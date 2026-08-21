<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewRequest;
use App\Models\Review;
use App\Models\ReviewSource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $reviews = Review::query()
            ->with('source')
            ->when($request->filled('source'), fn ($q) => $q->whereHas('source', fn ($q) => $q->where('provider', $request->string('source'))))
            ->when($request->filled('rating'), fn ($q) => $q->where('rating', (int) $request->input('rating')))
            ->when($request->input('status') === 'published', fn ($q) => $q->where('published', true))
            ->when($request->input('status') === 'hidden', fn ($q) => $q->where('published', false))
            ->when($request->boolean('featured'), fn ($q) => $q->where('featured', true))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.mb_strtolower($request->string('q')).'%';
                $q->where(fn ($q) => $q->whereRaw('LOWER(author_name) LIKE ?', [$term])->orWhereRaw('LOWER(content) LIKE ?', [$term]));
            })
            ->orderBy('sort_order')
            ->orderByDesc('review_date')
            ->paginate(25)
            ->withQueryString();

        $sources = ReviewSource::query()->orderBy('name')->get();

        return view('admin.reviews.index', compact('reviews', 'sources'));
    }

    public function store(ReviewRequest $request): RedirectResponse
    {
        $manual = $this->manualSource();
        $data = $this->withImage($request, $request->validated());
        $data['published'] = $request->boolean('published');
        $data['featured'] = $request->boolean('featured');
        $data['review_source_id'] = $manual->id;
        $data['review_date'] ??= now()->toDateString();
        $data['sort_order'] = (int) Review::max('sort_order') + 1;

        Review::create($data);

        return redirect()->route('admin.reviews.index')->with('status', 'Review created.');
    }

    public function edit(Review $review): View
    {
        return view('admin.reviews.form', compact('review'));
    }

    /**
     * Not type-hinted as ReviewRequest: that would trigger its `rules()`
     * (author_name/rating/content required) on every edit, including the
     * imported-review branch below which never sends those fields.
     */
    public function update(Request $request, Review $review): RedirectResponse
    {
        if (! $review->isManual()) {
            // Imported reviews are real customer feedback from an external
            // platform — only the reply and moderation flags are ours to
            // change, the author/content/rating stay exactly as fetched.
            $review->update($request->validate([
                'reply' => ['nullable', 'string'],
                'published' => ['nullable', 'boolean'],
                'featured' => ['nullable', 'boolean'],
                'verified' => ['nullable', 'boolean'],
            ]) + [
                'published' => $request->boolean('published'),
                'featured' => $request->boolean('featured'),
                'verified' => $request->boolean('verified'),
                'reply_date' => $request->filled('reply') ? now() : null,
            ]);

            return redirect()->route('admin.reviews.index')->with('status', 'Review updated.');
        }

        $validated = $request->validate((new ReviewRequest)->rules());
        $data = $this->withImage($request, $validated, $review);
        $data['published'] = $request->boolean('published');
        $data['featured'] = $request->boolean('featured');

        $review->update($data);

        return redirect()->route('admin.reviews.index')->with('status', 'Review updated.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        if ($review->isManual() && $review->author_avatar) {
            Storage::disk('public')->delete($review->author_avatar);
        }

        $review->delete();

        return back()->with('status', 'Review deleted.');
    }

    public function publish(Review $review): RedirectResponse
    {
        $review->update(['published' => ! $review->published]);

        return back()->with('status', $review->published ? 'Review published.' : 'Review hidden.');
    }

    public function feature(Review $review): RedirectResponse
    {
        $review->update(['featured' => ! $review->featured]);

        return back()->with('status', $review->featured ? 'Review featured.' : 'Review unfeatured.');
    }

    private function manualSource(): ReviewSource
    {
        return ReviewSource::query()->where('provider', ReviewSource::PROVIDER_MANUAL)->firstOrFail();
    }

    /** @param  array<string, mixed>  $data */
    private function withImage(Request $request, array $data, ?Review $review = null): array
    {
        $existing = $review?->author_avatar;

        if ($request->boolean('remove_image')) {
            if ($existing) {
                Storage::disk('public')->delete($existing);
            }
            $data['author_avatar'] = null;
        } elseif ($request->hasFile('author_avatar')) {
            if ($existing) {
                Storage::disk('public')->delete($existing);
            }
            $data['author_avatar'] = $request->file('author_avatar')->store('reviews', 'public');
        } else {
            unset($data['author_avatar']);
        }
        unset($data['remove_image']);

        return $data;
    }
}
