@props([
    'number',
    'title' => null,
    'id' => null,
    'padding' => 'mil-p-0-15',
])

{{-- The identical mosaic appeared on both the home page and the about page. --}}
@php
    $stats = setting('site.stats');
    $title ??= setting('about.section_title', 'A few words about us');
@endphp
<div class="{{ $padding }} mil-relative" @if ($id) id="{{ $id }}" @endif>
    <img src="{{ asset('img/ui/bg.png') }}" alt="background" class="mil-bg" style="top: -10rem">
    <div class="container">

        <x-section-title :title="$title" :number="$number" />

        <div class="row">

            <div class="col-md-6 col-xl-4 mil-mb-15 mil-up">
                <div class="mil-about-box mil-br-md">
                    <img src="{{ asset('img/ui/a1.jpg') }}" alt="cover" class="mil-box-cover mil-scale-img" data-value-1="1.2" data-value-2="1">
                </div>
            </div>

            <div class="col-md-6 col-xl-4 mil-mb-15 mil-up">
                <div class="mil-about-box mil-br-md mil-bg-m-1">
                    <img src="{{ asset('img/ui/bg-i1.png') }}" alt="icons" class="mil-box-icons mil-opacity-05 mil-parallax-img" data-value-1="0" data-value-2="-60">
                    <div class="mil-box-content mil-column mil-jcb mil-tar">
                        <x-user-avatars class="mil-dark-border mil-sm mil-jce mil-mb-20" />
                        <div>
                            <div class="mil-fs-72 mil-fw-600 mil-m-3 mil-mb-20"><span class="mil-counter" data-number="{{ $stats['jobs'] }}">{{ $stats['jobs'] }}</span><span class="mil-a-1">+</span></div>
                            <p class="mil-fs-20 mil-m-3 mil-opacity-4">{!! setting('about.stats_caption', 'rugs, sofas and armchairs <br>cleaned by our team last year') !!}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4 mil-mb-15 mil-up">
                <div class="mil-about-box mil-br-md">
                    <img src="{{ asset('img/ui/a2.jpg') }}" alt="cover" class="mil-box-cover mil-scale-img" data-value-1="1.2" data-value-2="1">
                </div>
            </div>

            <div class="col-md-6 col-xl-4 mil-mb-15 mil-up">
                <div class="mil-about-box mil-br-md mil-bg-a-2">
                    <div class="mil-box-content mil-column mil-jcb">
                        <p class="mil-fs-38 mil-fw-600 mil-m-4"><span class="mil-counter" data-number="{{ $stats['years'] }}">{{ $stats['years'] }}</span> years of <br>{!! setting('about.years_caption', 'carpet and sofa <br>cleaning.') !!}</p>
                        <div>
                            <p class="mil-fs-20 mil-m-3 mil-opacity-7">{{ setting('about.since_prefix', 'From the Loop to Naperville, since') }} {{ $stats['since'] }}</p>
                        </div>
                        <div class="mil-bg-number mil-scale-img" data-value-1="1.2" data-value-2="1">{{ $stats['years'] }}</div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4 mil-mb-15 mil-up mil-relative">
                <div class="mil-about-box mil-br-md">
                    <img src="{{ asset('img/ui/a3.jpg') }}" alt="cover" class="mil-box-cover mil-scale-img" data-value-1="1.2" data-value-2="1" style="object-position: bottom">
                </div>
                <img src="{{ asset('img/ui/s1.png') }}" alt="star" class="mil-deco-star">
            </div>

            <div class="col-md-6 col-xl-4 mil-mb-15 mil-up">
                <div class="mil-about-box mil-br-md mil-bg-a-1">
                    <img src="{{ asset('img/ui/bg-i2.png') }}" alt="icons" class="mil-box-icons mil-scale-img" data-value-1="1.3" data-value-2="1" style="right: -2rem; top: 10%">
                    <div class="mil-box-content mil-column mil-jcb">
                        <p class="mil-fs-38 mil-fw-600 mil-m-1">{!! setting('about.safe_headline', 'Safe for wool, <br>velvet, kids <br>and pets.') !!}</p>
                        <div>
                            <p class="mil-fs-20 mil-m-1">{!! setting('about.safe_body', 'Fabric-tested eco-certified solutions, <br>HEPA extraction and no sticky <br>residue left in the pile.') !!}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
