<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ setting('site.name') }} Admin</title>

    <link rel="stylesheet" href="{{ asset('css/plugins/fontawesome.css') }}">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="admin-body">

<div class="admin-sidebar">
    <div class="admin-sidebar-logo">
        <a href="{{ route('admin.dashboard') }}">
            {{-- Light variant: this sidebar is a dark background, same as the public site's footer. --}}
            <img src="{{ logo_url('light') }}" alt="{{ setting('site.name') }}" style="max-width: 100%; height: auto;">
        </a>
    </div>

    <nav class="admin-nav">
        <div class="admin-nav-section">Main</div>
        <ul>
            <li class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </li>
        </ul>

        <div class="admin-nav-section">Content</div>
        <ul>
            <li class="{{ request()->routeIs('admin.leads*') ? 'active' : '' }}">
                <a href="{{ route('admin.leads.index') }}"><i class="fas fa-inbox"></i> Leads</a>
            </li>
            <li class="{{ request()->routeIs('admin.services*') ? 'active' : '' }}">
                <a href="{{ route('admin.services.index') }}"><i class="fas fa-broom"></i> Services</a>
            </li>
            <li class="{{ request()->routeIs('admin.blog-posts*') ? 'active' : '' }}">
                <a href="{{ route('admin.blog-posts.index') }}"><i class="fas fa-newspaper"></i> Blog posts</a>
            </li>
            <li class="{{ request()->routeIs('admin.faq*') ? 'active' : '' }}">
                <a href="{{ route('admin.faq.index') }}"><i class="fas fa-question-circle"></i> FAQ</a>
            </li>
            <li class="{{ request()->routeIs('admin.process-steps*') ? 'active' : '' }}">
                <a href="{{ route('admin.process-steps.index') }}"><i class="fas fa-list-ol"></i> Process steps</a>
            </li>
            <li class="{{ request()->routeIs('admin.reviews.index') || request()->routeIs('admin.reviews.edit') ? 'active' : '' }}">
                <a href="{{ route('admin.reviews.index') }}"><i class="fas fa-star"></i> Reviews</a>
            </li>
            @can('manage-settings')
                <li class="{{ request()->routeIs('admin.reviews.sources*') ? 'active' : '' }}">
                    <a href="{{ route('admin.reviews.sources.index') }}"><i class="fas fa-plug"></i> Review sources</a>
                </li>
            @endcan
            <li class="{{ request()->routeIs('admin.service-areas*') ? 'active' : '' }}">
                <a href="{{ route('admin.service-areas.index') }}"><i class="fas fa-map-marker-alt"></i> Service areas</a>
            </li>
        </ul>

        @canany(['manage-settings', 'manage-users'])
            <div class="admin-nav-section">Manage</div>
            <ul>
                @can('manage-settings')
                    <li class="{{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                        <a href="{{ route('admin.settings.index') }}"><i class="fas fa-cog"></i> Site settings</a>
                    </li>
                    <li class="{{ request()->routeIs('admin.seo*') ? 'active' : '' }}">
                        <a href="{{ route('admin.seo.index') }}"><i class="fas fa-search"></i> SEO</a>
                    </li>
                @endcan
                @can('manage-users')
                    <li class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                        <a href="{{ route('admin.users.index') }}"><i class="fas fa-users"></i> Users</a>
                    </li>
                @endcan
            </ul>
        @endcanany

        <div class="admin-nav-section">Site</div>
        <ul>
            <li>
                <a href="{{ route('home') }}" target="_blank">
                    <i class="fas fa-external-link-alt"></i> View website
                </a>
            </li>
        </ul>
    </nav>

    <div class="admin-sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="background:none;border:none;padding:0;width:100%;text-align:left;">
                <span style="color:rgba(255,255,255,0.5);display:flex;align-items:center;gap:10px;font-size:0.85rem;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </span>
            </button>
        </form>
    </div>
</div>

<div class="admin-main">
    <div class="admin-topbar">
        <div style="display:flex;align-items:center;">
            <button type="button" class="admin-sidebar-toggle" data-sidebar-toggle><i class="fas fa-bars"></i></button>
            <div class="admin-topbar-title">@yield('page_title', 'Dashboard')</div>
        </div>
        <a href="{{ route('admin.profile.edit') }}" class="admin-topbar-user" style="text-decoration:none;color:inherit;">
            <i class="fas fa-user-circle" style="color:#9ca3af;font-size:1.4rem;"></i>
            <span>{{ auth()->user()->name }} <span class="badge-status badge-{{ auth()->user()->role }}">{{ ucfirst(auth()->user()->role) }}</span></span>
        </a>
    </div>

    <div class="admin-content">
        @if (session('status'))
            <div class="admin-alert admin-alert-success"><i class="fas fa-check-circle"></i> {{ session('status') }}</div>
        @endif

        @if (session('error'))
            <div class="admin-alert admin-alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="admin-alert admin-alert-error">
                <i class="fas fa-exclamation-circle"></i>
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
        @endif

        @yield('content')
    </div>
</div>

@stack('scripts')
</body>
</html>
