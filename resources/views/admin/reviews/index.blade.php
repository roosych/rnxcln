@extends('layouts.admin')

@section('title', 'Reviews')
@section('page_title', 'Reviews')

@section('content')

    <div class="admin-card">
        <div class="admin-card-header"><h2><i class="fas fa-filter"></i>Filter</h2></div>
        <div class="admin-card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="admin-form-label">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Author or text">
                </div>
                <div class="col-md-2">
                    <label class="admin-form-label">Source</label>
                    <select name="source" class="form-select">
                        <option value="">All</option>
                        @foreach ($sources as $source)
                            <option value="{{ $source->provider }}" @selected(request('source') === $source->provider)>{{ $source->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="admin-form-label">Rating</label>
                    <select name="rating" class="form-select">
                        <option value="">All</option>
                        @for ($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" @selected(request('rating') == $i)>{{ $i }} star{{ $i === 1 ? '' : 's' }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="admin-form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="published" @selected(request('status') === 'published')>Published</option>
                        <option value="hidden" @selected(request('status') === 'hidden')>Hidden</option>
                    </select>
                </div>
                <div class="col-md-1 form-check" style="padding-bottom:10px;">
                    <input type="checkbox" class="form-check-input" id="featured" name="featured" value="1" @checked(request()->boolean('featured'))>
                    <label class="form-check-label" for="featured">Featured</label>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="admin-btn admin-btn-primary"><i class="fas fa-filter"></i> Filter</button>
                    <a href="{{ route('admin.reviews.index') }}" class="admin-btn admin-btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h2><i class="fas fa-star"></i>All reviews</h2>
            <button type="button" class="admin-btn admin-btn-primary" data-bs-toggle="modal" data-bs-target="#addReviewModal"><i class="fas fa-plus"></i> Add review</button>
        </div>
        <div class="admin-card-body" style="padding:0;">
            @if ($reviews->isEmpty())
                <div style="padding:28px 24px;text-align:center;color:#9ca3af;">No reviews match these filters.</div>
            @else
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Author</th>
                            <th>Location</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Source</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reviews as $review)
                            <tr>
                                <td>
                                    @if ($review->authorAvatarUrl())
                                        <img src="{{ $review->authorAvatarUrl() }}" alt="" style="width:28px;height:28px;object-fit:cover;border-radius:50%;vertical-align:middle;margin-right:8px;">
                                    @endif
                                    <strong>{{ $review->author_name }}</strong>
                                    @if ($review->featured)
                                        <i class="fas fa-star mil-a-1" title="Featured" style="color:#f5a623;"></i>
                                    @endif
                                </td>
                                <td>{{ $review->location }}</td>
                                <td>{!! str_repeat('<i class="fas fa-star" style="color:#f5a623;"></i>', $review->rating ?? 0) !!}</td>
                                <td>{{ \Illuminate\Support\Str::limit($review->title ? $review->title.' — '.$review->content : $review->content, 70) }}</td>
                                <td><span class="badge-status">{{ $review->source?->name ?? 'Manual' }}</span></td>
                                <td>{{ $review->review_date?->format('Y-m-d') ?? $review->created_at->format('Y-m-d') }}</td>
                                <td><span class="badge-status {{ $review->published ? 'badge-active' : 'badge-inactive' }}">{{ $review->published ? 'Published' : 'Hidden' }}</span></td>
                                <td style="text-align:right;white-space:nowrap;">
                                    <form method="POST" action="{{ route('admin.reviews.publish', $review) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="admin-btn admin-btn-secondary admin-btn-sm" title="{{ $review->published ? 'Hide' : 'Publish' }}">
                                            <i class="fas {{ $review->published ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.reviews.feature', $review) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="admin-btn admin-btn-secondary admin-btn-sm" title="{{ $review->featured ? 'Unfeature' : 'Feature' }}">
                                            <i class="fa{{ $review->featured ? 's' : 'r' }} fa-star"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.reviews.edit', $review) }}" class="admin-btn admin-btn-secondary admin-btn-sm"><i class="fas fa-pencil"></i></a>
                                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" style="display:inline;" onsubmit="return confirm('Delete this review?')">
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

    <div class="admin-pagination-nav">
        {{ $reviews->links() }}
    </div>

    <div class="modal fade" id="addReviewModal" tabindex="-1" aria-labelledby="addReviewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.reviews.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addReviewModalLabel">Add review (Manual)</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="admin-form-label" for="author_name">Name</label>
                            <input type="text" id="author_name" name="author_name" class="form-control @error('author_name') is-invalid @enderror" value="{{ old('author_name') }}" required>
                            @error('author_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-4">
                            <label class="admin-form-label" for="location">Location</label>
                            <input type="text" id="location" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location') }}">
                            @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-4">
                            <label class="admin-form-label" for="rating">Rating</label>
                            <select id="rating" name="rating" class="form-select @error('rating') is-invalid @enderror" required>
                                @for ($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" @selected(old('rating', 5) == $i)>{{ $i }} star{{ $i === 1 ? '' : 's' }}</option>
                                @endfor
                            </select>
                            @error('rating')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-4">
                            <label class="admin-form-label">Photo</label>
                            <input type="file" name="author_avatar" class="form-control @error('author_avatar') is-invalid @enderror" accept="image/*">
                            <div class="admin-form-hint">Square photo works best — it's shown as a circle.</div>
                            @error('author_avatar')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-4">
                            <label class="admin-form-label" for="content">Review text</label>
                            <textarea id="content" name="content" class="form-control @error('content') is-invalid @enderror" rows="5">{{ old('content') }}</textarea>
                            @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-0 form-check">
                            <input type="checkbox" class="form-check-input" id="published" name="published" value="1" {{ old('published', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="published">Published (visible on site)</label>
                        </div>
                        <div class="mb-0 form-check">
                            <input type="checkbox" class="form-check-input" id="featured" name="featured" value="1" {{ old('featured') ? 'checked' : '' }}>
                            <label class="form-check-label" for="featured">Featured</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="admin-btn admin-btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="admin-btn admin-btn-primary"><i class="fas fa-save"></i> Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                new bootstrap.Modal(document.getElementById('addReviewModal')).show();
            });
        </script>
    @endif

@endsection
