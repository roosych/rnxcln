@extends('layouts.admin')

@section('title', 'Review sources')
@section('page_title', 'Review sources')

@section('content')

    <div style="margin-bottom:16px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
        <a href="{{ route('admin.reviews.index') }}" class="admin-btn admin-btn-secondary admin-btn-sm"><i class="fas fa-arrow-left"></i> Back to reviews</a>

        <div style="display:flex;gap:10px;align-items:center;">
            <form method="PUT" action="{{ route('admin.reviews.sources.interval') }}" style="display:flex;gap:8px;align-items:center;">
                @csrf @method('PUT')
                <label class="admin-form-label" style="margin:0;">Auto-sync every</label>
                <input type="number" name="sync_interval_hours" value="{{ $syncIntervalHours }}" min="1" max="168" class="form-control form-control-sm" style="width:70px;">
                <span>hours</span>
                <button class="admin-btn admin-btn-secondary admin-btn-sm">Save</button>
            </form>
            <form method="POST" action="{{ route('admin.reviews.sources.sync-all') }}">
                @csrf
                <button class="admin-btn admin-btn-primary"><i class="fas fa-rotate"></i> Sync All</button>
            </form>
        </div>
    </div>

    <div class="row g-3">
        @foreach ($sources as $source)
            <div class="col-md-6">
                <div class="admin-card" style="height:100%;">
                    <div class="admin-card-header">
                        <h2>
                            <i class="fas {{ ['manual' => 'fa-pen', 'google' => 'fa-map-marker-alt', 'yelp' => 'fa-utensils', 'facebook' => 'fa-thumbs-up'][$source->provider] ?? 'fa-plug' }}"></i>
                            {{ $source->name }}
                        </h2>
                        <div style="display:flex;gap:6px;">
                            <span class="badge-status {{ $source->connected ? 'badge-active' : 'badge-inactive' }}">{{ $source->connected ? 'Connected' : 'Not connected' }}</span>
                            <span class="badge-status {{ $source->enabled ? 'badge-active' : 'badge-inactive' }}">{{ $source->enabled ? 'Enabled' : 'Disabled' }}</span>
                        </div>
                    </div>
                    <div class="admin-card-body">
                        <p class="admin-form-hint">
                            Last sync: {{ $source->last_synced_at?->diffForHumans() ?? 'never' }}
                            @if ($source->sync_status === \App\Models\ReviewSource::STATUS_ERROR)
                                <span style="color:#dc2626;">— {{ $source->sync_error }}</span>
                            @elseif ($source->sync_status === \App\Models\ReviewSource::STATUS_UNSUPPORTED)
                                <span style="color:#b45309;">— {{ $source->sync_error }}</span>
                            @endif
                        </p>

                        @if ($source->isManual())
                            <p>Reviews are added directly from the <a href="{{ route('admin.reviews.index') }}">Reviews</a> page — nothing to connect here.</p>

                        @elseif ($source->provider === \App\Models\ReviewSource::PROVIDER_GOOGLE)
                            @if (! $source->connected)
                                <a href="{{ route('admin.reviews.sources.google.connect') }}" class="admin-btn admin-btn-primary"><i class="fab fa-google"></i> Connect with Google</a>
                                @unless (config('services.google_business.client_id'))
                                    <p class="admin-form-hint mt-2">Set <code>GOOGLE_BUSINESS_CLIENT_ID</code> / <code>GOOGLE_BUSINESS_CLIENT_SECRET</code> in .env first.</p>
                                @endunless
                            @else
                                <p><strong>Location:</strong> {{ $source->configValue('location_title') ?? 'None selected' }}</p>

                                <form method="POST" action="{{ route('admin.reviews.sources.google.locations.refresh') }}" class="d-inline">
                                    @csrf
                                    <button class="admin-btn admin-btn-secondary admin-btn-sm"><i class="fas fa-rotate"></i> Refresh locations</button>
                                </form>

                                @php $locations = $source->configValue('available_locations', []); @endphp
                                @if (! empty($locations))
                                    <form method="POST" action="{{ route('admin.reviews.sources.google.location') }}" style="margin-top:10px;display:flex;gap:8px;">
                                        @csrf
                                        <select name="location" class="form-select form-select-sm">
                                            @foreach ($locations as $location)
                                                <option value="{{ $location['account_name'] }}::{{ $location['location_name'] }}::{{ $location['title'] }}">{{ $location['title'] }}</option>
                                            @endforeach
                                        </select>
                                        <button class="admin-btn admin-btn-secondary admin-btn-sm">Use this location</button>
                                    </form>
                                @endif

                                <div style="margin-top:12px;display:flex;gap:8px;">
                                    <form method="POST" action="{{ route('admin.reviews.sources.sync', $source) }}">
                                        @csrf
                                        <button class="admin-btn admin-btn-primary admin-btn-sm" @disabled(! $source->configValue('location_name'))><i class="fas fa-rotate"></i> Sync Reviews</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.reviews.sources.disconnect', $source) }}" onsubmit="return confirm('Disconnect Google?')">
                                        @csrf
                                        <button class="admin-btn admin-btn-secondary admin-btn-sm">Disconnect</button>
                                    </form>
                                </div>

                                <p class="admin-form-hint mt-2">
                                    Google restricts the Business Profile reviews endpoint to approved projects —
                                    sync will report an access error until Google grants this OAuth client access.
                                </p>
                            @endif

                        @elseif ($source->provider === \App\Models\ReviewSource::PROVIDER_YELP)
                            <form method="POST" action="{{ route('admin.reviews.sources.yelp.update', $source) }}">
                                @csrf @method('PUT')
                                <div class="mb-2">
                                    <label class="admin-form-label">API Key</label>
                                    <input type="text" name="api_key" class="form-control form-control-sm" value="{{ $source->maskedCredential('api_key') ? '' : old('api_key') }}" placeholder="{{ $source->maskedCredential('api_key') ?? 'Yelp Fusion API key' }}">
                                </div>
                                <div class="mb-2">
                                    <label class="admin-form-label">Business ID</label>
                                    <input type="text" name="business_id" class="form-control form-control-sm" value="{{ old('business_id', $source->configValue('business_id')) }}" placeholder="your-business-chicago">
                                </div>
                                <div class="mb-2">
                                    <label class="admin-form-label">Business URL</label>
                                    <input type="url" name="business_url" class="form-control form-control-sm" value="{{ old('business_url', $source->configValue('business_url')) }}" placeholder="https://www.yelp.com/biz/...">
                                </div>
                                <button class="admin-btn admin-btn-primary admin-btn-sm">Save &amp; Test Connection</button>
                            </form>

                            @if ($source->connected)
                                <div style="margin-top:12px;">
                                    <form method="POST" action="{{ route('admin.reviews.sources.sync', $source) }}">
                                        @csrf
                                        <button class="admin-btn admin-btn-secondary admin-btn-sm"><i class="fas fa-rotate"></i> Sync Reviews</button>
                                    </form>
                                </div>
                            @endif

                            <p class="admin-form-hint mt-2">
                                Yelp's public Fusion API returns up to 3 review excerpts per business — full review
                                sync requires Yelp's separate partner-only Reviews API access.
                            </p>

                        @elseif ($source->provider === \App\Models\ReviewSource::PROVIDER_FACEBOOK)
                            @if (! $source->connected)
                                <a href="{{ route('admin.reviews.sources.facebook.connect') }}" class="admin-btn admin-btn-primary"><i class="fab fa-facebook"></i> Connect Facebook</a>
                                @unless (config('services.facebook.client_id'))
                                    <p class="admin-form-hint mt-2">Set <code>FACEBOOK_CLIENT_ID</code> / <code>FACEBOOK_CLIENT_SECRET</code> in .env first.</p>
                                @endunless
                            @else
                                <p><strong>Page:</strong> {{ $source->configValue('page_name') ?? 'None selected' }}</p>

                                @php $pages = $source->configValue('available_pages', []); @endphp
                                @if (! empty($pages))
                                    <form method="POST" action="{{ route('admin.reviews.sources.facebook.page') }}" style="display:flex;gap:8px;">
                                        @csrf
                                        <select name="page" class="form-select form-select-sm">
                                            @foreach ($pages as $page)
                                                <option value="{{ $page['id'] }}::{{ $page['name'] }}::{{ $page['access_token'] }}">{{ $page['name'] }}</option>
                                            @endforeach
                                        </select>
                                        <button class="admin-btn admin-btn-secondary admin-btn-sm">Use this Page</button>
                                    </form>
                                @endif

                                <div style="margin-top:12px;display:flex;gap:8px;">
                                    <form method="POST" action="{{ route('admin.reviews.sources.sync', $source) }}">
                                        @csrf
                                        <button class="admin-btn admin-btn-primary admin-btn-sm" @disabled(! $source->configValue('page_id'))><i class="fas fa-rotate"></i> Sync Reviews</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.reviews.sources.disconnect', $source) }}" onsubmit="return confirm('Disconnect Facebook?')">
                                        @csrf
                                        <button class="admin-btn admin-btn-secondary admin-btn-sm">Disconnect</button>
                                    </form>
                                </div>

                                <p class="admin-form-hint mt-2">
                                    Meta gates Page ratings behind App Review (`pages_read_engagement`) and Business
                                    Verification — sync will report an access error until this app is approved.
                                </p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

@endsection
