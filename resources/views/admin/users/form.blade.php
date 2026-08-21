@extends('layouts.admin')

@section('title', $user->exists ? 'Edit user' : 'Add user')
@section('page_title', $user->exists ? 'Edit user' : 'Add user')

@section('content')

    <div style="margin-bottom:16px;">
        <a href="{{ route('admin.users.index') }}" class="admin-btn admin-btn-secondary admin-btn-sm"><i class="fas fa-arrow-left"></i> Back to users</a>
    </div>

    <div class="admin-card">
        <div class="admin-card-body">
            <form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">
                @csrf
                @if ($user->exists) @method('PUT') @endif

                <div class="mb-4">
                    <label class="admin-form-label" for="name">Name</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="admin-form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="admin-form-label" for="password">{{ $user->exists ? 'New password (leave blank to keep current)' : 'Password' }}</label>
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4">
                    <label class="admin-form-label" for="role">Role</label>
                    <select id="role" name="role" class="form-select">
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin — full access</option>
                        <option value="editor" @selected(old('role', $user->role ?? 'editor') === 'editor')>Editor — leads & content only</option>
                    </select>
                </div>

                <button type="submit" class="admin-btn admin-btn-primary"><i class="fas fa-save"></i> Save</button>
            </form>
        </div>
    </div>

@endsection
