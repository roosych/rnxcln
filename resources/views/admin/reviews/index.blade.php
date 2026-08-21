@extends('layouts.admin')

@section('title', 'Reviews')
@section('page_title', 'Reviews')

@section('content')

    <div class="admin-card">
        <div class="admin-card-header">
            <h2><i class="fas fa-star"></i>All reviews</h2>
            <button type="button" class="admin-btn admin-btn-primary" data-bs-toggle="modal" data-bs-target="#addReviewModal"><i class="fas fa-plus"></i> Add review</button>
        </div>
        <div class="admin-card-body" style="padding:0;">
            @if ($reviews->isEmpty())
                <div style="padding:28px 24px;text-align:center;color:#9ca3af;">No reviews yet.</div>
            @else
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width:36px;"></th>
                            <th style="width:52px;"></th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Text</th>
                            <th>Status</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody data-sortable data-sortable-url="{{ route('admin.reviews.reorder') }}">
                        @foreach ($reviews as $review)
                            <tr data-id="{{ $review->id }}">
                                <td class="drag-handle" title="Drag to reorder"><i class="fas fa-grip-vertical"></i></td>
                                <td>
                                    @if ($review->image)
                                        <img src="{{ $review->imageUrl() }}" alt="" style="width:36px;height:36px;object-fit:cover;border-radius:50%;">
                                    @endif
                                </td>
                                <td><strong>{{ $review->name }}</strong></td>
                                <td>{{ $review->location }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($review->text, 80) }}</td>
                                <td><span class="badge-status {{ $review->is_active ? 'badge-active' : 'badge-inactive' }}">{{ $review->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td style="text-align:right;">
                                    <a href="{{ route('admin.reviews.edit', $review) }}" class="admin-btn admin-btn-secondary admin-btn-sm"><i class="fas fa-pencil"></i> Edit</a>
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

    <div class="modal fade" id="addReviewModal" tabindex="-1" aria-labelledby="addReviewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.reviews.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addReviewModalLabel">Add review</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="admin-form-label" for="name">Name</label>
                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-4">
                            <label class="admin-form-label" for="location">Location</label>
                            <input type="text" id="location" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location') }}">
                            @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-4">
                            <label class="admin-form-label">Photo</label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            <div class="admin-form-hint">Square photo works best — it's shown as a circle.</div>
                            @error('image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-4">
                            <label class="admin-form-label" for="text">Review text</label>
                            <textarea id="text" name="text" class="form-control @error('text') is-invalid @enderror" rows="5">{{ old('text') }}</textarea>
                            @error('text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-0 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active (visible on site)</label>
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
