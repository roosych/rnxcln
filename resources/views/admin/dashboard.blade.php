@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')

    @php
        $today = \App\Models\Lead::whereDate('created_at', today())->count();
        $week = \App\Models\Lead::where('created_at', '>=', now()->subDays(7))->count();
        $total = \App\Models\Lead::count();
        $new = \App\Models\Lead::where('status', 'new')->count();
    @endphp

    <div class="row">
        <div class="col-lg-3 col-md-6">
            <a href="{{ route('admin.leads.index') }}" class="admin-stat-card">
                <div class="admin-stat-icon"><i class="fas fa-inbox"></i></div>
                <div>
                    <div class="admin-stat-value">{{ $today }}</div>
                    <div class="admin-stat-label">Leads today</div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6">
            <a href="{{ route('admin.leads.index') }}" class="admin-stat-card">
                <div class="admin-stat-icon" style="background:#8cd3f3;"><i class="fas fa-calendar-week"></i></div>
                <div>
                    <div class="admin-stat-value">{{ $week }}</div>
                    <div class="admin-stat-label">Leads this week</div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6">
            <a href="{{ route('admin.leads.index', ['status' => 'new']) }}" class="admin-stat-card">
                <div class="admin-stat-icon" style="background:#f59e0b;"><i class="fas fa-exclamation-circle"></i></div>
                <div>
                    <div class="admin-stat-value">{{ $new }}</div>
                    <div class="admin-stat-label">Unhandled leads</div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6">
            <a href="{{ route('admin.leads.index') }}" class="admin-stat-card">
                <div class="admin-stat-icon" style="background:#8792a4;"><i class="fas fa-archive"></i></div>
                <div>
                    <div class="admin-stat-value">{{ $total }}</div>
                    <div class="admin-stat-label">Leads total</div>
                </div>
            </a>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header"><h2><i class="fas fa-bolt"></i>Quick links</h2></div>
        <div class="admin-card-body">
            <div class="row g-3">
                <div class="col-md-4"><a href="{{ route('admin.services.index') }}" class="admin-btn admin-btn-secondary w-100 justify-content-center">Manage services</a></div>
                <div class="col-md-4"><a href="{{ route('admin.faq.index') }}" class="admin-btn admin-btn-secondary w-100 justify-content-center">Manage FAQ</a></div>
                <div class="col-md-4"><a href="{{ route('admin.reviews.index') }}" class="admin-btn admin-btn-secondary w-100 justify-content-center">Manage reviews</a></div>
            </div>
        </div>
    </div>

@endsection
