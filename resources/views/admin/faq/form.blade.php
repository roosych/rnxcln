@extends('layouts.admin')

@section('title', 'Edit question')
@section('page_title', 'Edit question')

@section('content')

    <div style="margin-bottom:16px;">
        <a href="{{ route('admin.faq.index') }}" class="admin-btn admin-btn-secondary admin-btn-sm"><i class="fas fa-arrow-left"></i> Back to FAQ</a>
    </div>

    <div class="admin-card">
        <div class="admin-card-body">
            <form method="POST" action="{{ route('admin.faq.update', $faqItem) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="admin-form-label" for="question">Question</label>
                    <input type="text" id="question" name="question" class="form-control @error('question') is-invalid @enderror" value="{{ old('question', $faqItem->question) }}" required>
                    @error('question')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="admin-form-label" for="answer">Answer</label>
                    <textarea id="answer" name="answer" class="form-control @error('answer') is-invalid @enderror" rows="5">{{ old('answer', $faqItem->answer) }}</textarea>
                    @error('answer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="admin-btn admin-btn-primary"><i class="fas fa-save"></i> Save</button>
            </form>
        </div>
    </div>

@endsection
