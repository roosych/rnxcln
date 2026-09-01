<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlogPostRequest;
use App\Models\BlogPost;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    private const IMAGE_FIELDS = ['cover_image', 'og_image'];

    public function index(): View
    {
        return view('admin.blog.index', [
            'posts' => BlogPost::with('categories')->latest('created_at')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.blog.form', ['post' => new BlogPost, 'services' => $this->services()]);
    }

    public function store(BlogPostRequest $request): RedirectResponse
    {
        $data = $this->withDefaults($request, null);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);

        foreach (self::IMAGE_FIELDS as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('blog', 'public');
            }
        }

        $post = BlogPost::create($data);
        $post->categories()->sync($request->input('categories', []));

        return redirect()->route('admin.blog-posts.index')->with('status', 'Post created.');
    }

    public function edit(BlogPost $blogPost): View
    {
        return view('admin.blog.form', ['post' => $blogPost, 'services' => $this->services()]);
    }

    public function update(BlogPostRequest $request, BlogPost $blogPost): RedirectResponse
    {
        $data = $this->withDefaults($request, $blogPost);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);

        foreach (self::IMAGE_FIELDS as $field) {
            if ($request->hasFile($field)) {
                if ($blogPost->$field) {
                    Storage::disk('public')->delete($blogPost->$field);
                }
                $data[$field] = $request->file($field)->store('blog', 'public');
            }
        }

        $blogPost->update($data);

        // sync() keeps pivot rows for ids that already existed, so a reorder
        // (same ids, new tick order) wouldn't move — detach everything first
        // so the pivot's auto-increment id reflects this submission's order.
        $blogPost->categories()->detach();
        $blogPost->categories()->attach($request->input('categories', []));

        return redirect()->route('admin.blog-posts.index')->with('status', 'Post updated.');
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        foreach (self::IMAGE_FIELDS as $field) {
            if ($blogPost->$field) {
                Storage::disk('public')->delete($blogPost->$field);
            }
        }

        $blogPost->delete();

        return back()->with('status', 'Post deleted.');
    }

    private function services(): \Illuminate\Support\Collection
    {
        return Service::where('is_active', true)->orderBy('sort_order')->get();
    }

    private function withDefaults(BlogPostRequest $request, ?BlogPost $post): array
    {
        $data = $request->validated();

        $data['is_published'] = $request->boolean('is_published');
        $data['is_featured'] = $request->boolean('is_featured');

        // The form field is pre-filled with the post's existing published_at,
        // so it only comes through empty for a brand-new post (or one that
        // was never published) — default that case to "now" so a freshly
        // published post sorts as newest instead of null-first. A draft
        // always gets published_at cleared, regardless of what the (hidden)
        // field held.
        $data['published_at'] = $data['is_published']
            ? ($request->input('published_at') ?: ($post?->published_at ?? now()))
            : null;

        unset($data['cover_image'], $data['og_image'], $data['categories']);

        return $data;
    }
}
