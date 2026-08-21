@props([
    // Title lines, top to bottom. Raw HTML is allowed so a line can carry an
    // inline <img class="mil-text-image">. The first line gets the star,
    // the last one the bottom margin.
    'lines' => [],
    // [['label' => 'Home', 'url' => '/'], ...]
    'breadcrumbs' => [],
    // Small-screen type size: the home page runs three long lines, inner pages two.
    'size' => 'mil-sm-fs-72',
])

@php
    $last = count($lines) - 1;
@endphp

<div class="mil-hero-inner mil-relative" id="top">
    <img src="{{ asset('img/ui/bg.png') }}" alt="background" class="mil-bg" style="top: 0">
    <div class="container mil-column mil-jcb mil-h-100">
        <div class="row mil-jcb mil-h-100">

            <div class="col-lg-7 mil-mb-60">
                <div class="mil-main-title mil-jcc mil-ais mil-column">
                    <div class="mil-column mil-sm-mb-0 mil-w-100">

                        @foreach ($lines as $i => $line)
                            @if ($i === 0)
                                <div class="mil-aic">
                                    <div class="mil-word-frame">
                                        <x-star-burst style="top: -15%; right: -15%" />
                                        <h1 class="mil-fs-100 {{ $size }} mil-lh-110">{!! $line !!}</h1>
                                    </div>
                                </div>
                            @else
                                <div @class(['mil-word-frame', 'mil-mb-40' => $i === $last])>
                                    <h1 class="mil-fs-100 {{ $size }} mil-lh-110">{!! $line !!}</h1>
                                </div>
                            @endif
                        @endforeach

                        <div class="mil-aic">
                            {{ $slot }}
                        </div>

                    </div>
                </div>
            </div>

            <div class="mil-column col-lg-5 mil-jcb mil-aie mil-sm-ais mil-mb-60">
                <ul class="mil-breadcrumbs mil-mb-60">
                    @foreach ($breadcrumbs as $crumb)
                        <li><a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a></li>
                    @endforeach
                </ul>
                <x-scroll-hint />
            </div>

        </div>
    </div>
</div>

@if (! empty($breadcrumbs))
    @php
        $breadcrumbList = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($breadcrumbs)->values()->map(fn ($crumb, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => strip_tags((string) $crumb['label']),
                'item' => $crumb['url'],
            ])->all(),
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($breadcrumbList, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif
