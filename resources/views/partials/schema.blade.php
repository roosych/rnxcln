@php
    $address = setting('site.address');
    $socials = collect(setting('site.socials', []))
        ->pluck('url')
        ->reject(fn ($url) => empty($url) || $url === '#.')
        ->values();

    $localBusiness = [
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        'name' => setting('site.name'),
        'image' => logo_url('dark'),
        'url' => url('/'),
        'telephone' => setting('site.phone_e164'),
        'email' => setting('site.email'),
        'address' => [
            '@type' => 'PostalAddress',
            'addressLocality' => $address['city'] ?? null,
            'streetAddress' => $address['line_1'] ?? null,
            'addressRegion' => 'IL',
            'addressCountry' => 'US',
        ],
    ];

    if ($socials->isNotEmpty()) {
        $localBusiness['sameAs'] = $socials->all();
    }

    // Only valid with real reviews behind it — an AggregateRating with zero
    // reviews is both meaningless and against Google's structured-data rules.
    $reviewCount = \App\Models\Review::where('is_active', true)->count();
    // The Company tab's "Rating" field is a free-text display string like
    // "4.9/5.0" — schema.org's ratingValue/bestRating each need a plain
    // number, so split it instead of passing the display string through.
    $ratingParts = array_map('trim', explode('/', (string) (setting('site.stats')['rating'] ?? '')));
    $ratingValue = is_numeric($ratingParts[0] ?? null) ? $ratingParts[0] : null;
    $bestRating = is_numeric($ratingParts[1] ?? null) ? $ratingParts[1] : '5';
    if ($reviewCount > 0 && $ratingValue) {
        $localBusiness['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => $ratingValue,
            'reviewCount' => $reviewCount,
            'bestRating' => $bestRating,
        ];
    }
@endphp

<script type="application/ld+json">{!! json_encode($localBusiness, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

@if (! empty($seo?->schema_json))
    <script type="application/ld+json">{!! json_encode($seo->schema_json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif
