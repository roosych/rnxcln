<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
{{-- chrome mobile panel color --}}
<meta name="theme-color" content="#EDCE29">

@if ($favicon = setting('site.favicon'))
    <link rel="icon" href="{{ asset('storage/'.$favicon) }}">
@else
    <link rel="icon" type="image/png" href="{{ asset('favicon-96x96.png') }}" sizes="96x96">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
@endif

<link rel="stylesheet" href="{{ asset('css/plugins/bootstrap-grid.css') }}">
<link rel="stylesheet" href="{{ asset('css/plugins/swiper.css') }}">
<link rel="stylesheet" href="{{ asset('css/plugins/fancybox.css') }}">
<link rel="stylesheet" href="{{ asset('css/plugins/fontawesome.css') }}">
<link rel="stylesheet" href="{{ asset('css/plugins/flatpickr.css') }}">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">

@php
    // The per-service page (services.show) has no PageSeo row — its title/
    // description come straight from the Service record instead. Read it off
    // the route binding itself, not a `$service` Blade variable — this
    // partial is @included into the parent layout's <head>, which shares
    // variable scope with whatever the child @section('content') last
    // assigned, so a `$service` left over from an unrelated @foreach
    // elsewhere on the page (e.g. home.blade.php's featured-services loop)
    // would otherwise silently leak in and give the page the wrong title.
    $service = request()->route('service');
    $seo = \App\Models\PageSeo::forRoute(request()->route()?->getName() ?? '');
    // Some service titles carry their own <br class="mil-sm-hidden"> for
    // wrap control elsewhere (see e.g. config/catalog.php's home-office
    // entries) — strip that HTML out before it lands in <title>/og:title,
    // which are plain text, not markup.
    // Null (not the site name) when there's genuinely no page-specific title
    // to show — e.g. the 404 page, which has no Service and no PageSeo row —
    // so <title> doesn't end up showing the site name twice.
    $pageTitle = $service?->meta_title ?: ($service ? trim(preg_replace('#\s+#', ' ', strip_tags($service->title))) : null) ?: $seo?->meta_title;
    $metaTitle = $pageTitle ?? setting('site.name');
    $metaDescription = $service?->meta_description ?: ($seo?->meta_description ?? '');
    $canonical = $seo?->canonical_url ?? url()->current();
    // Priority: an explicitly uploaded per-page OG image, then the service's
    // own photo on its detail page (services.show has no PageSeo row so
    // $seo is always null there), then the site logo as a last resort.
    $ogImage = $seo?->og_image
        ? asset('storage/'.$seo->og_image)
        : ($service?->image ? $service->imageUrl() : asset('img/ui/logo2.png'));
@endphp

<title>{{ $pageTitle ? $pageTitle.' — '.setting('site.name') : setting('site.name') }}</title>
<meta name="description" content="{{ $metaDescription }}">
<link rel="canonical" href="{{ $canonical }}">
@if ($seo?->robots === 'noindex')
    <meta name="robots" content="noindex, nofollow">
@endif

<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ setting('site.name') }}">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $ogImage }}">

@include('partials.schema')
@include('partials.analytics')

{{-- Raw code pasted in Admin > SEO > Custom head code. Trusted-admin-only, unescaped by design. --}}
@if ($customHeadCode = setting('seo.custom_head_code'))
    {!! $customHeadCode !!}
@endif
