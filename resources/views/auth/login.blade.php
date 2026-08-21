<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — {{ setting('site.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/plugins/fontawesome.css') }}">
    @vite(['resources/css/admin.css'])
    <style>
        body {
            background: var(--admin-sidebar-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            border-radius: 16px;
            padding: 48px 44px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
        }
        .login-title { text-align: center; font-size: 1.35rem; font-weight: 700; color: var(--admin-ink); margin-bottom: 6px; }
        .login-subtitle { text-align: center; font-size: 0.88rem; color: var(--admin-muted); margin-bottom: 32px; }
        .btn-login {
            width: 100%;
            background: var(--accent-color);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .btn-login:hover { opacity: 0.9; }
        .back-link { text-align: center; margin-top: 20px; font-size: 0.85rem; }
        .back-link a { color: var(--admin-muted); text-decoration: none; }
        .back-link a:hover { color: var(--accent-color); }
        .login-logo { display: block; max-width: 18rem; height: auto; margin: 0 auto 24px; }
    </style>
</head>
<body>

<div class="login-card">
    <img src="{{ logo_url('dark') }}" alt="{{ setting('site.name') }}" class="login-logo">
    <div class="login-subtitle">Sign in to manage your content</div>

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-4">
            <label for="email" class="admin-form-label">Email address</label>
            <input type="email" id="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" autocomplete="email" autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label for="password" class="admin-form-label">Password</label>
            <input type="password" id="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   autocomplete="current-password">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4 form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember">
            <label class="form-check-label" for="remember" style="font-size:0.88rem;color:var(--admin-muted);">Remember me</label>
        </div>

        <button type="submit" class="btn-login">Sign in</button>
    </form>

    <div class="back-link">
        <a href="{{ route('password.request') }}">Forgot your password?</a>
    </div>
    <div class="back-link">
        <a href="{{ route('home') }}"><i class="fas fa-arrow-left" style="font-size:0.8em;"></i> Back to website</a>
    </div>
</div>

</body>
</html>
