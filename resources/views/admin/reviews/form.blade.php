@extends('layouts.admin')

@section('title', 'Edit review')
@section('page_title', 'Edit review')

@section('content')

    <div style="margin-bottom:16px;">
        <a href="{{ route('admin.reviews.index') }}" class="admin-btn admin-btn-secondary admin-btn-sm"><i class="fas fa-arrow-left"></i> Back to reviews</a>
    </div>

    <div class="admin-card">
        <div class="admin-card-body">

            @unless ($review->isManual())
                <div class="admin-alert" style="background:#f3f4f6;color:#374151;">
                    <i class="fas fa-circle-info"></i>
                    Imported from <strong>{{ $review->source?->name }}</strong> — the author, rating and text are exactly
                    as fetched and aren't editable here.
                    @if ($review->source_url)
                        <a href="{{ $review->source_url }}" target="_blank" rel="noopener">View original</a>
                    @endif
                </div>
            @endunless

            <form method="POST" action="{{ route('admin.reviews.update', $review) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @if ($review->isManual())
                    <div class="mb-4">
                        <label class="admin-form-label" for="author_name">Name</label>
                        <input type="text" id="author_name" name="author_name" class="form-control @error('author_name') is-invalid @enderror" value="{{ old('author_name', $review->author_name) }}" required>
                        @error('author_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="admin-form-label" for="location">Location</label>
                        <input type="text" id="location" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $review->location) }}">
                        @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="admin-form-label" for="rating">Rating</label>
                        <select id="rating" name="rating" class="form-select @error('rating') is-invalid @enderror" required>
                            @for ($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" @selected(old('rating', $review->rating) == $i)>{{ $i }} star{{ $i === 1 ? '' : 's' }}</option>
                            @endfor
                        </select>
                        @error('rating')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="admin-form-label">Photo</label>
                        @if ($review->author_avatar)
                            <div style="margin-bottom:10px;"><img src="{{ $review->authorAvatarUrl() }}" alt="" style="width:64px;height:64px;object-fit:cover;border-radius:50%;"></div>
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="remove_image" name="remove_image" value="1">
                                <label class="form-check-label" for="remove_image">Remove current photo</label>
                            </div>
                        @else
                            <div class="admin-form-hint mb-2">None set — the review shows without a photo.</div>
                        @endif
                        <input type="file" id="author_avatar" name="author_avatar" class="form-control @error('author_avatar') is-invalid @enderror" accept="image/*">
                        <div class="admin-form-hint">Leave blank to keep the current one.</div>
                        @error('author_avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="admin-form-label" for="content">Review text</label>
                        <textarea id="content" name="content" class="form-control @error('content') is-invalid @enderror" rows="5">{{ old('content', $review->content) }}</textarea>
                        @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                @else
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="admin-form-label">Name</label>
                            <div class="form-control" style="background:#f9fafb;">{{ $review->author_name }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="admin-form-label">Rating</label>
                            <div class="form-control" style="background:#f9fafb;">{!! str_repeat('<i class="fas fa-star" style="color:#f5a623;"></i>', $review->rating ?? 0) !!}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="admin-form-label">Date</label>
                            <div class="form-control" style="background:#f9fafb;">{{ $review->review_date?->format('Y-m-d') }}</div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="admin-form-label">Review text</label>
                        <div class="form-control" style="background:#f9fafb;white-space:pre-wrap;">{{ $review->content }}</div>
                    </div>

                    <div class="mb-4">
                        <label class="admin-form-label" for="reply">Your reply</label>
                        <textarea id="reply" name="reply" class="form-control @error('reply') is-invalid @enderror" rows="3" placeholder="Shown alongside the review on your site">{{ old('reply', $review->reply) }}</textarea>
                        @error('reply')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="verified" name="verified" value="1" {{ old('verified', $review->verified) ? 'checked' : '' }}>
                        <label class="form-check-label" for="verified">Verified</label>
                    </div>
                @endif

                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="published" name="published" value="1" {{ old('published', $review->published) ? 'checked' : '' }}>
                    <label class="form-check-label" for="published">Published (visible on site)</label>
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="featured" name="featured" value="1" {{ old('featured', $review->featured) ? 'checked' : '' }}>
                    <label class="form-check-label" for="featured">Featured</label>
                </div>

                <button type="submit" class="admin-btn admin-btn-primary"><i class="fas fa-save"></i> Save</button>
            </form>
        </div>
    </div>

@endsection
