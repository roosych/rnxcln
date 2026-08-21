@extends('layouts.admin')

@section('title', 'Services')
@section('page_title', 'Services')

@section('content')

    <div class="admin-alert admin-alert-info">Whether a service shows on the Home page no longer depends on its folder — check "Featured" on a service's own form to pin it to Home's top section; everything else active shows further down, in random order. Drag order below sets both the Services-page order within a folder and the Home featured order.</div>

    <div style="margin-bottom:16px;display:flex;justify-content:flex-end;">
        <a href="{{ route('admin.services.create') }}" class="admin-btn admin-btn-secondary"><i class="fas fa-plus"></i> Add service</a>
    </div>

    <div class="admin-card">
        <div class="admin-card-header"><h2><i class="fas fa-pencil"></i>Folder names</h2></div>
        <div class="admin-card-body">
            <form method="POST" action="{{ route('admin.services.folders.update') }}">
                @csrf @method('PUT')
                <div class="row">
                    @foreach ($folders as $key => $name)
                        <div class="col-md-4 mb-3">
                            <label class="admin-form-label" for="folder-{{ $key }}">{{ $name }}</label>
                            <input type="text" id="folder-{{ $key }}" name="{{ $key }}" class="form-control @error($key) is-invalid @enderror" value="{{ old($key, $name) }}">
                            @if (isset($folderHints[$key]))
                                <div class="admin-form-hint">{{ $folderHints[$key] }}</div>
                            @endif
                            @error($key)<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    @endforeach
                </div>
                <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm"><i class="fas fa-save"></i> Save</button>
            </form>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header"><h2><i class="fas fa-list"></i>All services</h2></div>
        <div class="admin-card-body" style="padding:0;">
            @if ($services->isEmpty())
                <div style="padding:28px 24px;text-align:center;color:#9ca3af;">No services yet.</div>
            @else
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width:36px;"></th>
                            <th style="width:60px;"></th>
                            <th>Title</th>
                            <th>Folder</th>
                            <th style="width:70px;">Featured</th>
                            <th>Slug</th>
                            <th>Status</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody data-sortable data-sortable-url="{{ route('admin.services.reorder') }}">
                        @foreach ($services as $service)
                            <tr data-id="{{ $service->id }}">
                                <td class="drag-handle" title="Drag to reorder"><i class="fas fa-grip-vertical"></i></td>
                                <td>
                                    @if ($service->image)
                                        <img src="{{ $service->imageUrl() }}" alt="" style="width:44px;height:44px;object-fit:cover;border-radius:6px;">
                                    @endif
                                </td>
                                <td><strong>{!! $service->title !!}</strong></td>
                                <td>{{ $service->section ? ($folders[$service->section] ?? $service->section) : '—' }}</td>
                                <td>@if ($service->is_featured)<i class="fas fa-star" style="color:#f5b400;" title="Featured on Home"></i>@endif</td>
                                <td><code style="font-size:0.78rem;background:#f3f4f6;padding:2px 6px;border-radius:4px;">{{ $service->slug }}</code></td>
                                <td><span class="badge-status {{ $service->is_active ? 'badge-active' : 'badge-inactive' }}">{{ $service->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td style="text-align:right;">
                                    <a href="{{ route('services.show', $service) }}" target="_blank" class="admin-btn admin-btn-secondary admin-btn-sm" title="View on site"><i class="fas fa-external-link-alt"></i></a>
                                    <a href="{{ route('admin.services.edit', $service) }}" class="admin-btn admin-btn-secondary admin-btn-sm"><i class="fas fa-pencil"></i> Edit</a>
                                    <form method="POST" action="{{ route('admin.services.destroy', $service) }}" style="display:inline;" onsubmit="return confirm('Delete this service?')">
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
