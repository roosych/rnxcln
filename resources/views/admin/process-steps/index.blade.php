@extends('layouts.admin')

@section('title', 'Process steps')
@section('page_title', 'Process steps')

@section('content')

    @foreach ($groups as $group)
        <div class="admin-card">
            <div class="admin-card-header">
                <h2><i class="fas fa-list-ol"></i>{{ ucfirst($group) }} page</h2>
                <button type="button" class="admin-btn admin-btn-primary admin-btn-sm" data-bs-toggle="modal" data-bs-target="#addStepModal-{{ $group }}"><i class="fas fa-plus"></i> Add step</button>
            </div>
            <div class="admin-card-body" style="padding:0;">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width:36px;"></th>
                                <th>Title</th>
                                <th style="text-align:right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody data-sortable data-sortable-url="{{ route('admin.process-steps.reorder', $group) }}">
                            @forelse (($steps[$group] ?? []) as $step)
                                <tr data-id="{{ $step->id }}">
                                    <td class="drag-handle" title="Drag to reorder"><i class="fas fa-grip-vertical"></i></td>
                                    <td>{!! $step->title !!}</td>
                                    <td style="text-align:right;">
                                        <a href="{{ route('admin.process-steps.edit', $step) }}" class="admin-btn admin-btn-secondary admin-btn-sm"><i class="fas fa-pencil"></i> Edit</a>
                                        <form method="POST" action="{{ route('admin.process-steps.destroy', $step) }}" style="display:inline;" onsubmit="return confirm('Delete this step?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" style="text-align:center;color:#9ca3af;padding:20px;">No steps yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addStepModal-{{ $group }}" tabindex="-1" aria-labelledby="addStepModalLabel-{{ $group }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('admin.process-steps.store') }}">
                        @csrf
                        <input type="hidden" name="group" value="{{ $group }}">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addStepModalLabel-{{ $group }}">Add step — {{ ucfirst($group) }} page</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-4">
                                <label class="admin-form-label" for="title-{{ $group }}">Title</label>
                                <input type="text" id="title-{{ $group }}" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('group') === $group ? old('title') : '' }}" required>
                                <div class="admin-form-hint">A <code>&lt;br&gt;</code> can be used to control the line wrap, e.g. <code>Fabric test &lt;br&gt;and inspection</code></div>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-0">
                                <label class="admin-form-label" for="text-{{ $group }}">Description</label>
                                <textarea id="text-{{ $group }}" name="text" class="form-control @error('text') is-invalid @enderror" rows="4">{{ old('group') === $group ? old('text') : '' }}</textarea>
                                @error('text')<div class="invalid-feedback">{{ $message }}</div>@enderror
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

        @if ($errors->any() && old('group') === $group)
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    new bootstrap.Modal(document.getElementById('addStepModal-{{ $group }}')).show();
                });
            </script>
        @endif
    @endforeach

@endsection
