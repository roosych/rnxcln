@extends('layouts.admin')

@section('title', 'FAQ')
@section('page_title', 'FAQ')

@section('content')

    <div class="admin-alert admin-alert-info">One universal FAQ, shown at the bottom of Home, Services, and every individual service page.</div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h2><i class="fas fa-question-circle"></i>All questions</h2>
            <button type="button" class="admin-btn admin-btn-primary" data-bs-toggle="modal" data-bs-target="#addFaqModal"><i class="fas fa-plus"></i> Add question</button>
        </div>
        <div class="admin-card-body" style="padding:0;">
            @if ($items->isEmpty())
                <div style="padding:28px 24px;text-align:center;color:#9ca3af;">No FAQ items yet.</div>
            @else
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width:36px;"></th>
                            <th>Question</th>
                            <th>Answer</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody data-sortable data-sortable-url="{{ route('admin.faq.reorder') }}">
                        @foreach ($items as $item)
                            <tr data-id="{{ $item->id }}">
                                <td class="drag-handle" title="Drag to reorder"><i class="fas fa-grip-vertical"></i></td>
                                <td><strong>{{ $item->question }}</strong></td>
                                <td>{{ \Illuminate\Support\Str::limit($item->answer, 80) }}</td>
                                <td style="text-align:right;">
                                    <a href="{{ route('admin.faq.edit', $item) }}" class="admin-btn admin-btn-secondary admin-btn-sm"><i class="fas fa-pencil"></i> Edit</a>
                                    <form method="POST" action="{{ route('admin.faq.destroy', $item) }}" style="display:inline;" onsubmit="return confirm('Delete this question?')">
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

    <div class="modal fade" id="addFaqModal" tabindex="-1" aria-labelledby="addFaqModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.faq.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addFaqModalLabel">Add question</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="admin-form-label" for="question">Question</label>
                            <input type="text" id="question" name="question" class="form-control @error('question') is-invalid @enderror" value="{{ old('question') }}" required>
                            @error('question')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-0">
                            <label class="admin-form-label" for="answer">Answer</label>
                            <textarea id="answer" name="answer" class="form-control @error('answer') is-invalid @enderror" rows="5">{{ old('answer') }}</textarea>
                            @error('answer')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                new bootstrap.Modal(document.getElementById('addFaqModal')).show();
            });
        </script>
    @endif

@endsection
