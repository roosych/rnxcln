@extends('layouts.admin')

@section('title', 'SEO')
@section('page_title', 'SEO')

@section('content')

    <div class="admin-alert admin-alert-info">
        Structured data (JSON-LD) for <strong>breadcrumbs</strong> (every inner page), <strong>FAQPage</strong> (any page with an FAQ block) and the business's <strong>aggregate rating</strong> (from Reviews + the rating in Company &amp; contacts) is generated automatically — nothing to configure. Each service's own page also gets <strong>Service</strong> schema from its title/description/photo. "Custom JSON-LD" on a page's own edit screen is only for something beyond that.
    </div>

    <div class="admin-card">
        <div class="admin-card-header"><h2><i class="fas fa-search"></i>Per-page meta</h2></div>
        <div class="admin-card-body" style="padding:0;">
            @if ($pages->isEmpty())
                <div style="padding:28px 24px;text-align:center;color:#9ca3af;">No pages yet.</div>
            @else
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width:52px;"></th>
                            <th>Route</th>
                            <th>Meta title</th>
                            <th>Indexing</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pages as $page)
                            <tr>
                                <td>
                                    @if ($page->og_image)
                                        <img src="{{ asset('storage/'.$page->og_image) }}" alt="" style="width:40px;height:40px;object-fit:cover;border-radius:6px;">
                                    @endif
                                </td>
                                <td><code style="font-size:0.85rem;">{{ $page->route_name }}</code></td>
                                <td>{{ \Illuminate\Support\Str::limit($page->meta_title, 60) }}</td>
                                <td><span class="badge-status {{ $page->robots === 'index' ? 'badge-active' : 'badge-inactive' }}">{{ $page->robots === 'index' ? 'Index' : 'Noindex' }}</span></td>
                                <td style="text-align:right;">
                                    @if (\Illuminate\Support\Facades\Route::has($page->route_name))
                                        <a href="{{ route($page->route_name) }}" target="_blank" class="admin-btn admin-btn-secondary admin-btn-sm" title="View on site"><i class="fas fa-external-link-alt"></i></a>
                                    @endif
                                    <a href="{{ route('admin.seo.edit', $page) }}" class="admin-btn admin-btn-secondary admin-btn-sm"><i class="fas fa-pencil"></i> Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header"><h2><i class="fas fa-chart-line"></i>Analytics & robots.txt</h2></div>
        <div class="admin-card-body">
            <form method="POST" action="{{ route('admin.seo.analytics.update') }}">
                @csrf
                @method('PUT')
                <div class="mb-4"><label class="admin-form-label">Google Analytics 4 measurement ID</label><input type="text" name="ga4_id" class="form-control" value="{{ old('ga4_id', $ga4Id) }}" placeholder="G-XXXXXXX"></div>
                <div class="mb-4"><label class="admin-form-label">Yandex Metrika counter ID</label><input type="text" name="yandex_metrika_id" class="form-control" value="{{ old('yandex_metrika_id', $yandexId) }}"></div>

                <div class="mb-4">
                    <label class="admin-form-label" for="custom_head_code">Custom head code</label>
                    <textarea id="custom_head_code" name="custom_head_code" class="form-control @error('custom_head_code') is-invalid @enderror" rows="8" style="font-family:monospace;font-size:0.85rem;" placeholder="<!-- Google Tag Manager, Meta Pixel, verification meta tags, etc. -->">{{ old('custom_head_code', $customHeadCode) }}</textarea>
                    <div class="admin-form-hint">Pasted <strong>as-is</strong> into every page's <code>&lt;head&gt;</code>, right before <code>&lt;/head&gt;</code> — full snippets (Google Tag Manager, Meta Pixel, site verification tags, etc.), not just an ID. This runs unescaped on every visitor's browser, so only paste code from providers you trust, and keep access to this screen limited to trusted admins.</div>
                    @error('custom_head_code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="mb-4"><label class="admin-form-label">robots.txt content</label><textarea name="robots_txt" class="form-control" rows="6" style="font-family:monospace;">{{ old('robots_txt', $robots) }}</textarea></div>
                <p class="admin-form-hint">Sitemap: <code>{{ url('/sitemap.xml') }}</code></p>
                <button type="submit" class="admin-btn admin-btn-primary"><i class="fas fa-save"></i> Save</button>
            </form>
        </div>
    </div>

@endsection
