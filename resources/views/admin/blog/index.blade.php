@extends('layouts.admin')

@section('title', 'Blog posts')
@section('page_title', 'Blog posts')

@section('content')

    <div style="margin-bottom:16px;display:flex;justify-content:flex-end;">
        <a href="{{ route('admin.blog-posts.create') }}" class="admin-btn admin-btn-secondary"><i class="fas fa-plus"></i> Add post</a>
    </div>

    <div class="admin-card">
        <div class="admin-card-header"><h2><i class="fas fa-newspaper"></i>All posts</h2></div>
        <div class="admin-card-body" style="padding:0;">
            @if ($posts->isEmpty())
                <div style="padding:28px 24px;text-align:center;color:#9ca3af;">No posts yet.</div>
            @else
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width:60px;"></th>
                            <th>Title</th>
                            <th>Categories</th>
                            <th style="width:70px;">Featured</th>
                            <th>Status</th>
                            <th>Published</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($posts as $post)
                            <tr>
                                <td>
                                    @if ($post->cover_image)
                                        <img src="{{ $post->coverImageUrl() }}" alt="" style="width:44px;height:44px;object-fit:cover;border-radius:6px;">
                                    @endif
                                </td>
                                <td><strong>{{ $post->title }}</strong></td>
                                <td>
                                    @foreach ($post->categories as $category)
                                        <span class="badge-status badge-active" style="margin-right:4px;">{{ $category->title }}</span>
                                    @endforeach
                                </td>
                                <td>@if ($post->is_featured)<i class="fas fa-star" style="color:#f5b400;" title="Featured on Blog"></i>@endif</td>
                                <td><span class="badge-status {{ $post->is_published ? 'badge-active' : 'badge-inactive' }}">{{ $post->is_published ? 'Published' : 'Draft' }}</span></td>
                                <td>{{ $post->published_at?->format('j F Y') ?? '—' }}</td>
                                <td style="text-align:right;">
                                    @if ($post->is_published)
                                        <a href="{{ route('blog.show', $post) }}" target="_blank" class="admin-btn admin-btn-secondary admin-btn-sm" title="View on site"><i class="fas fa-external-link-alt"></i></a>
                                    @endif
                                    <a href="{{ route('admin.blog-posts.edit', $post) }}" class="admin-btn admin-btn-secondary admin-btn-sm"><i class="fas fa-pencil"></i> Edit</a>
                                    <form method="POST" action="{{ route('admin.blog-posts.destroy', $post) }}" style="display:inline;" onsubmit="return confirm('Delete this post?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

@endsection
