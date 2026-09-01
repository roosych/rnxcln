<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        return view('pages.blog.index', [
            'posts' => BlogPost::published()->with('categories')->latest('published_at')->paginate(6),
            'categories' => $this->categories(),
            'activeCategory' => null,
        ]);
    }

    public function category(Service $service): View
    {
        abort_unless($service->is_active, 404);

        return view('pages.blog.index', [
            'posts' => BlogPost::published()
                ->whereHas('categories', fn ($q) => $q->whereKey($service->id))
                ->with('categories')
                ->latest('published_at')
                ->paginate(6),
            'categories' => $this->categories(),
            'activeCategory' => $service,
        ]);
    }

    public function show(BlogPost $post): View
    {
        abort_unless($post->is_published, 404);
        $post->load('categories');

        return view('pages.blog.show', [
            'post' => $post,
            'related' => $post->relatedPosts(),
            'categories' => $this->categories(),
        ]);
    }

    /** Active services that at least one published post is tagged with, for the sidebar list. */
    private function categories(): \Illuminate\Support\Collection
    {
        $usedIds = DB::table('blog_post_service')
            ->join('blog_posts', 'blog_posts.id', '=', 'blog_post_service.blog_post_id')
            ->where('blog_posts.is_published', true)
            ->pluck('service_id');

        return Service::where('is_active', true)->whereIn('id', $usedIds)->orderBy('sort_order')->get();
    }
}
