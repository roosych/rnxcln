@extends('layouts.admin')

@section('title', 'Edit review')
@section('page_title', 'Edit review')

@section('content')

    <div style="margin-bottom:16px;">
        <a href="{{ route('admin.reviews.index') }}" class="admin-btn admin-btn-secondary admin-btn-sm"><i class="fas fa-arrow-left"></i> Back to reviews</a>
    </div>

    <div class="admin-card">
        <div class="admin-card-body">
            <form method="POST" action="{{ route('admin.reviews.update', $review) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="admin-form-label" for="name">Name</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $review->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="admin-form-label" for="location">Location</label>
                    <input type="text" id="location" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $review->location) }}">
                    @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="admin-form-label">Photo</label>
                    @if ($review->image)
                        <div style="margin-bottom:10px;"><img src="{{ $review->imageUrl() }}" alt="" style="width:64px;height:64px;object-fit:cover;border-radius:50%;"></div>
                        <div class="form-check mb-2">
                            <input type="checkbox" class="form-check-input" id="remove_image" name="remove_image" value="1">
                            <label class="form-check-label" for="remove_image">Remove current photo</label>
                        </div>
                    @else
                        <div class="admin-form-hint mb-2">None set — the review shows without a photo.</div>
                    @endif
                    <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                    <div class="admin-form-hint">Square photo works best — it's shown as a circle. Leave blank to keep the current one.</div>
                    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="admin-form-label" for="text">Review text</label>
                    <textarea id="text" name="text" class="form-control @error('text') is-invalid @enderror" rows="5">{{ old('text', $review->text) }}</textarea>
                    @error('text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $review->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active (visible on site)</label>
                </div>

                <div class="admin-form-hint mb-4">Order is set by dragging rows on the <a href="{{ route('admin.reviews.index') }}">reviews list</a>.</div>

                <button type="submit" class="admin-btn admin-btn-primary"><i class="fas fa-save"></i> Save</button>
            </form>
        </div>
    </div>

@endsection
