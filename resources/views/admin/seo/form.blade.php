@extends('layouts.admin')

@section('title', 'SEO — '.$page->route_name)
@section('page_title', 'Edit SEO: '.$page->route_name)

@section('content')

    <div style="margin-bottom:16px;display:flex;justify-content:space-between;align-items:center;">
        <a href="{{ route('admin.seo.index') }}" class="admin-btn admin-btn-secondary admin-btn-sm"><i class="fas fa-arrow-left"></i> Back to SEO</a>
        @if (\Illuminate\Support\Facades\Route::has($page->route_name))
            <a href="{{ route($page->route_name) }}" target="_blank" class="admin-btn admin-btn-secondary admin-btn-sm"><i class="fas fa-external-link-alt"></i> View page</a>
        @endif
    </div>

    <div class="admin-card">
        <div class="admin-card-header"><h2><i class="fas fa-search"></i>{{ $page->route_name }}</h2></div>
        <div class="admin-card-body">
            <form method="POST" action="{{ route('admin.seo.update', $page) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <h3 class="admin-form-subheading">Search</h3>
                <div class="mb-3">
                    <label class="admin-form-label" for="meta_title">Meta title</label>
                    <input type="text" id="meta_title" name="meta_title" class="form-control @error('meta_title') is-invalid @enderror" value="{{ old('meta_title', $page->meta_title) }}">
                    @error('meta_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="admin-form-label" for="meta_description">Meta description</label>
                    <textarea id="meta_description" name="meta_description" class="form-control @error('meta_description') is-invalid @enderror" rows="2">{{ old('meta_description', $page->meta_description) }}</textarea>
                    @error('meta_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="admin-form-label" for="canonical_url">Canonical URL</label>
                    <input type="text" id="canonical_url" name="canonical_url" class="form-control @error('canonical_url') is-invalid @enderror" value="{{ old('canonical_url', $page->canonical_url) }}">
                    <div class="admin-form-hint">Blank uses this page's own URL.</div>
                    @error('canonical_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-4">
                    <label class="admin-form-label" for="robots">Indexing</label>
                    <select id="robots" name="robots" class="form-select">
                        <option value="index" @selected($page->robots === 'index')>Index (default — show in search results)</option>
                        <option value="noindex" @selected($page->robots === 'noindex')>Noindex (hide from search results)</option>
                    </select>
                </div>

                <h3 class="admin-form-subheading">Social sharing</h3>
                <div class="mb-4">
                    <label class="admin-form-label">Image (Open Graph / Twitter card)</label>
                    @if ($page->og_image)
                        <div style="margin-bottom:10px;"><img src="{{ asset('storage/'.$page->og_image) }}" alt="" style="max-width:280px;border-radius:8px;"></div>
                        <div class="form-check mb-2">
                            <input type="checkbox" class="form-check-input" id="remove_og_image" name="remove_og_image" value="1">
                            <label class="form-check-label" for="remove_og_image">Remove current image</label>
                        </div>
                    @else
                        <div class="admin-form-hint mb-2">None set — shares of this page fall back to the site logo.</div>
                    @endif
                    <input type="file" name="og_image" class="form-control @error('og_image') is-invalid @enderror" accept="image/*">
                    <div class="admin-form-hint">Recommended 1200×630px. Leave blank to keep the current image.</div>
                    @error('og_image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <h3 class="admin-form-subheading">Advanced</h3>
                <div class="mb-4">
                    <label class="admin-form-label">Custom JSON-LD</label>
                    <textarea name="schema_json" class="form-control @error('schema_json') is-invalid @enderror" rows="4" style="font-family:monospace;font-size:0.85rem;" placeholder='{"@@context": "https://schema.org", "@@type": "Thing", ...}'>{{ old('schema_json', $page->schema_json ? json_encode($page->schema_json, JSON_PRETTY_PRINT) : '') }}</textarea>
                    <div class="admin-form-hint">Raw JSON-LD object, added to the page as its own <code>&lt;script type="application/ld+json"&gt;</code> block alongside the automatic breadcrumbs/FAQ/rating markup. Leave blank unless you specifically need this. Must be valid JSON.</div>
                    @error('schema_json')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="admin-btn admin-btn-primary"><i class="fas fa-save"></i> Save</button>
            </form>
        </div>
    </div>

@endsection
