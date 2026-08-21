@extends('layouts.admin')

@section('title', 'Edit step')
@section('page_title', 'Edit step')

@section('content')

    <div style="margin-bottom:16px;">
        <a href="{{ route('admin.process-steps.index') }}" class="admin-btn admin-btn-secondary admin-btn-sm"><i class="fas fa-arrow-left"></i> Back to process steps</a>
    </div>

    <div class="admin-card">
        <div class="admin-card-body">
            <form method="POST" action="{{ route('admin.process-steps.update', $step) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="admin-form-label" for="group">Page</label>
                    <select id="group" name="group" class="form-select">
                        @foreach ($groups as $group)
                            <option value="{{ $group }}" @selected(old('group', $step->group) === $group)>{{ ucfirst($group) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="admin-form-label" for="title">Title</label>
                    <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $step->title) }}" required>
                    <div class="admin-form-hint">A <code>&lt;br&gt;</code> can be used to control the line wrap, e.g. <code>Fabric test &lt;br&gt;and inspection</code></div>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="admin-form-label" for="text">Description</label>
                    <textarea id="text" name="text" class="form-control @error('text') is-invalid @enderror" rows="4">{{ old('text', $step->text) }}</textarea>
                    @error('text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="admin-btn admin-btn-primary"><i class="fas fa-save"></i> Save</button>
            </form>
        </div>
    </div>

@endsection
