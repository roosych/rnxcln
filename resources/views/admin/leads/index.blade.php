@extends('layouts.admin')

@section('title', 'Leads')
@section('page_title', 'Leads')

@section('content')

    <div class="admin-card">
        <div class="admin-card-header"><h2><i class="fas fa-filter"></i>Filter</h2></div>
        <div class="admin-card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="admin-form-label">Search</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Name or phone">
                </div>
                <div class="col-md-3">
                    <label class="admin-form-label">Source</label>
                    <select name="source" class="form-select">
                        <option value="">All</option>
                        <option value="contact_form" @selected(request('source') === 'contact_form')>Contact form</option>
                        <option value="callback" @selected(request('source') === 'callback')>Callback widget</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="admin-form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        @foreach (['new', 'contacted', 'booked', 'closed'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="admin-btn admin-btn-primary"><i class="fas fa-filter"></i> Filter</button>
                    <a href="{{ route('admin.leads.export', request()->query()) }}" class="admin-btn admin-btn-secondary"><i class="fas fa-file-csv"></i> Export</a>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Source</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Service</th>
                            <th>ZIP</th>
                            <th>Message</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($leads as $lead)
                            <tr>
                                <td>{{ $lead->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $lead->source === 'callback' ? 'Callback' : 'Contact form' }}</td>
                                <td><strong>{{ $lead->name }}</strong></td>
                                <td><a href="tel:{{ $lead->phone }}">{{ $lead->phone }}</a></td>
                                <td>{{ $lead->service }}</td>
                                <td>{{ $lead->zip }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($lead->message, 60) }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.leads.update', $lead) }}">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" class="form-select form-select-sm badge-status badge-{{ $lead->status }}" onchange="this.form.submit()" style="border:none;">
                                            @foreach (['new', 'contacted', 'booked', 'closed'] as $status)
                                                <option value="{{ $status }}" @selected($lead->status === $status)>{{ ucfirst($status) }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" style="text-align:center;color:#9ca3af;padding:28px 24px;">No leads yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="admin-pagination-nav">
        {{ $leads->links() }}
    </div>

@endsection
