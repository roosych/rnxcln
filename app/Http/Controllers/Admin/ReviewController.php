<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewRequest;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $reviews = Review::query()->orderBy('sort_order')->get();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function store(ReviewRequest $request): RedirectResponse
    {
        $data = $this->withImage($request, $request->validated());
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = Review::max('sort_order') + 1;

        Review::create($data);

        return redirect()->route('admin.reviews.index')->with('status', 'Review created.');
    }

    public function edit(Review $review): View
    {
        return view('admin.reviews.form', compact('review'));
    }

    public function update(ReviewRequest $request, Review $review): RedirectResponse
    {
        // sort_order isn't part of this form — only the drag-to-reorder list
        // on the index page changes it — so it's left out of the update data.
        $data = $this->withImage($request, $request->validated(), $review);
        $data['is_active'] = $request->boolean('is_active');
        unset($data['sort_order']);

        $review->update($data);

        return redirect()->route('admin.reviews.index')->with('status', 'Review updated.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        if ($review->image) {
            Storage::disk('public')->delete($review->image);
        }

        $review->delete();

        return back()->with('status', 'Review deleted.');
    }

    public function reorder(Request $request): void
    {
        $ids = $request->validate(['ids' => ['required', 'array']])['ids'];

        foreach (array_values($ids) as $order => $id) {
            Review::whereKey($id)->update(['sort_order' => $order]);
        }
    }

    /** @param  array<string, mixed>  $data */
    private function withImage(ReviewRequest $request, array $data, ?Review $review = null): array
    {
        $existing = $review?->image;

        if ($request->boolean('remove_image')) {
            if ($existing) {
                Storage::disk('public')->delete($existing);
            }
            $data['image'] = null;
        } elseif ($request->hasFile('image')) {
            if ($existing) {
                Storage::disk('public')->delete($existing);
            }
            $data['image'] = $request->file('image')->store('reviews', 'public');
        } else {
            unset($data['image']);
        }
        unset($data['remove_image']);

        return $data;
    }
}
