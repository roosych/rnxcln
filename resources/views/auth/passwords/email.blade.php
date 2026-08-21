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
        .login-title { text-align: center; font-size: 1.35rem; font-weight: 700; color: var(--admin-ink); margin-bottom: 6px; }
        .login-subtitle { text-align: center; font-size: 0.88rem; color: var(--admin-muted); margin-bottom: 32px; }
        .btn-login { width: 100%; background: var(--accent-color); color: #fff; border: none; border-radius: 8px; padding: 12px; font-weight: 700; font-size: 1rem; cursor: pointer; }
        .back-link { text-align: center; margin-top: 20px; font-size: 0.85rem; }
        .back-link a { color: var(--admin-muted); text-decoration: none; }
        .back-link a:hover { color: var(--accent-color); }
        .login-logo { display: block; max-width: 18rem; height: auto; margin: 0 auto 24px; }
    </style>
</head>
<body>

<div class="login-card">
    <img src="{{ logo_url('dark') }}" alt="{{ setting('site.name') }}" class="login-logo">
    <div class="login-subtitle">We'll email you a reset link</div>

    @if (session('status'))
        <div class="admin-alert admin-alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-4">
            <label for="email" class="admin-form-label">Email address</label>
            <input type="email" id="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" autocomplete="email" autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn-login">Send reset link</button>
    </form>

    <div class="back-link"><a href="{{ route('login') }}"><i class="fas fa-arrow-left" style="font-size:0.8em;"></i> Back to login</a></div>
</div>

</body>
</html>
