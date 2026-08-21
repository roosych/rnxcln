<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset password — {{ setting('site.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/plugins/fontawesome.css') }}">
    @vite(['resources/css/admin.css'])
    <style>
        body { background: var(--admin-sidebar-bg); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: #fff; border-radius: 16px; padding: 48px 44px; width: 100%; max-width: 420px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35); }
        .login-title { text-align: center; font-size: 1.35rem; font-weight: 700; color: var(--admin-ink); margin-bottom: 32px; }
        .btn-login { width: 100%; background: var(--accent-color); color: #fff; border: none; border-radius: 8px; padding: 12px; font-weight: 700; font-size: 1rem; cursor: pointer; }
        .login-logo { display: block; max-width: 18rem; height: auto; margin: 0 auto 24px; }
    </style>
</head>
<body>

<div class="login-card">
    <img src="{{ logo_url('dark') }}" alt="{{ setting('site.name') }}" class="login-logo">
    <div class="login-title">Set a new password</div>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-4">
            <label for="email" class="admin-form-label">Email address</label>
            <input type="email" id="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $email) }}" autocomplete="email" autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label for="password" class="admin-form-label">New password</label>
            <input type="password" id="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   autocomplete="new-password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="admin-form-label">Confirm new password</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   class="form-control" autocomplete="new-password">
        </div>

        <button type="submit" class="btn-login">Reset password</button>
    </form>
</div>

</body>
</html>
