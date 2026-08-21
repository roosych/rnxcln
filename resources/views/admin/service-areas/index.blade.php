@extends('layouts.admin')

@section('title', 'Service areas')
@section('page_title', 'Service areas')

@section('content')

    <div class="admin-alert admin-alert-info">Shown in the ZIP code dropdown on the contact form, sorted alphabetically by area. Only active areas appear.</div>

    <div class="admin-card">
        <div class="admin-card-header"><h2><i class="fas fa-filter"></i>Search</h2></div>
        <div class="admin-card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="admin-form-label">ZIP or area name</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="e.g. 60601 or Loop">
                </div>
                <div class="col-md-3">
                    <button class="admin-btn admin-btn-primary"><i class="fas fa-filter"></i> Search</button>
                    @if (request('q'))
                        <a href="{{ route('admin.service-areas.index') }}" class="admin-btn admin-btn-secondary">Clear</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h2><i class="fas fa-map-marker-alt"></i>Service areas</h2>
            <button type="button" class="admin-btn admin-btn-primary" data-bs-toggle="modal" data-bs-target="#addAreaModal"><i class="fas fa-plus"></i> Add area</button>
        </div>
        <div class="admin-card-body" style="padding:0;">
            @if ($areas->isEmpty())
                <div style="padding:28px 24px;text-align:center;color:#9ca3af;">No service areas found.</div>
            @else
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ZIP</th>
                            <th>Area</th>
                            <th>Status</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($areas as $area)
                            <tr>
                                <td><code style="font-size:0.78rem;background:#f3f4f6;padding:2px 6px;border-radius:4px;">{{ $area->zip }}</code></td>
                                <td><strong>{{ $area->area }}</strong></td>
                                <td><span class="badge-status {{ $area->is_active ? 'badge-active' : 'badge-inactive' }}">{{ $area->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td style="text-align:right;">
                                    <a href="{{ route('admin.service-areas.edit', $area) }}" class="admin-btn admin-btn-secondary admin-btn-sm"><i class="fas fa-pencil"></i> Edit</a>
                                    <form method="POST" action="{{ route('admin.service-areas.destroy', $area) }}" style="display:inline;" onsubmit="return confirm('Delete this service area?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="padding:16px 24px;">
                {{ $areas->links() }}
            </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="addAreaModal" tabindex="-1" aria-labelledby="addAreaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.service-areas.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAreaModalLabel">Add service area</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="admin-form-label" for="zip">ZIP code</label>
                            <input type="text" id="zip" name="zip" class="form-control @error('zip') is-invalid @enderror" value="{{ old('zip') }}" required>
                            @error('zip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-4">
                            <label class="admin-form-label" for="area">Area name</label>
                            <input type="text" id="area" name="area" class="form-control @error('area') is-invalid @enderror" value="{{ old('area') }}" required>
                            @error('area')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-0 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active (shown in the contact form's ZIP dropdown)</label>
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
                new bootstrap.Modal(document.getElementById('addAreaModal')).show();
            });
        </script>
    @endif

@endsection
