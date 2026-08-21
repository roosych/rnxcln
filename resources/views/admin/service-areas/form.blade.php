@extends('layouts.admin')

@section('title', 'Edit service area')
@section('page_title', 'Edit service area')

@section('content')

    <div style="margin-bottom:16px;">
        <a href="{{ route('admin.service-areas.index') }}" class="admin-btn admin-btn-secondary admin-btn-sm"><i class="fas fa-arrow-left"></i> Back to service areas</a>
    </div>

    <div class="admin-card">
        <div class="admin-card-body">
            <form method="POST" action="{{ route('admin.service-areas.update', $area) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="admin-form-label" for="zip">ZIP code</label>
                    <input type="text" id="zip" name="zip" class="form-control @error('zip') is-invalid @enderror" value="{{ old('zip', $area->zip) }}" required>
                    @error('zip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="admin-form-label" for="area">Area name</label>
                    <input type="text" id="area" name="area" class="form-control @error('area') is-invalid @enderror" value="{{ old('area', $area->area) }}" required>
                    @error('area')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $area->is_active ?? true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active (shown in the contact form's ZIP dropdown)</label>
                </div>

                <button type="submit" class="admin-btn admin-btn-primary"><i class="fas fa-save"></i> Save</button>
            </form>
        </div>
    </div>

@endsection
