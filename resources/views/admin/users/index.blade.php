@extends('layouts.admin')

@section('title', 'Users')
@section('page_title', 'Users')

@section('content')

    <div class="admin-card">
        <div class="admin-card-header">
            <h2><i class="fas fa-users"></i>All users</h2>
            <a href="{{ route('admin.users.create') }}" class="admin-btn admin-btn-primary"><i class="fas fa-plus"></i> Add user</a>
        </div>
        <div class="admin-card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td><strong>{{ $user->name }}</strong></td>
                                <td>{{ $user->email }}</td>
                                <td><span class="badge-status badge-{{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
                                <td style="text-align:right;">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="admin-btn admin-btn-secondary admin-btn-sm"><i class="fas fa-pencil"></i> Edit</a>
                                    @unless (auth()->user()->is($user))
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:inline;" onsubmit="return confirm('Delete this user?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="admin-btn admin-btn-danger admin-btn-sm"><i class="fas fa-trash"></i></button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
