@extends('layouts.admin')

@section('title', 'My profile')
@section('page_title', 'My profile')

@section('content')

    <div class="admin-card">
        <div class="admin-card-body">
            <form method="POST" action="{{ route('admin.profile.update') }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="admin-form-label" for="name">Name</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', auth()->user()->name) }}">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="admin-form-label">Email</label>
                    <input type="email" class="form-control" value="{{ auth()->user()->email }}" disabled>
                </div>

                <button type="submit" class="admin-btn admin-btn-primary"><i class="fas fa-save"></i> Save</button>
            </form>
        </div>
    </div>

    <div class="admin-card" style="margin-top:24px;">
        <div class="admin-card-body">
            <h3 style="margin-top:0;margin-bottom:20px;font-size:1.1rem;">Change password</h3>

            <form method="POST" action="{{ route('admin.profile.password.update') }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="admin-form-label" for="current_password">Current password</label>
                    <input type="password" id="current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" autocomplete="current-password">
                    @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="admin-form-label" for="password">New password</label>
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="admin-form-label" for="password_confirmation">Confirm new password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password">
                </div>

                <button type="submit" class="admin-btn admin-btn-primary"><i class="fas fa-save"></i> Update password</button>
            </form>
        </div>
    </div>

@endsection
