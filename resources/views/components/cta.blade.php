@props([
    // Headline in the yellow panel.
    'headline',
])

@php
    $stats = setting('site.stats');
@endphp

<div class="container">
    <div class="mil-dots mil-up"></div>
</div>

<div class="mil-p-ff mil-relative">
    <img src="{{ asset('img/ui/bg.png') }}" alt="background" class="mil-bg" style="top: -10rem">
    <div class="container">

        <div class="row">
            <div class="col-lg-7 mil-mb-15">
                <div class="mil-cta mil-br-lg mil-bg-a-1 mil-aicb mil-up">
                    <div class="row mil-jcb">
                        <div class="col-lg-6 mil-mb-30">
                            <h5 class="mil-fs-32 mil-m-1">{{ $headline }}</h5>
                        </div>
                        <div class="col-lg-6 mil-jce mil-md-jcs mil-mb-30">
                            <a href="{{ route('contact') }}" class="mil-btn mil-icon-btn mil-bg-m-1 mil-m-4 mil-br-xl mil-hover-bri-105 mil-hover-scale">Book online<i class="far fa-arrow-right mil-bg-a-1 mil-m-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 mil-mb-15">
                <div class="mil-cta mil-br-lg mil-bg-m-4 mil-jcc mil-column mil-up">
                    <h5 class="mil-fs-22 mil-md-fs-32 mil-m-1 mil-mb-15 mil-md-mb-30">We accept your requests 24 hours a day, 7 days a week</h5>
                    <a href="{{ route('contact') }}" class="mil-link mil-m-1 mil-mb-15">Get in touch<i class="far fa-arrow-right mil-bg-m-3 mil-m-1"></i></a>
                </div>
            </div>
        </div>

        <div class="mil-hero-cards-frame">
            <div class="row">
                <div class="col-lg-7 col-xxl-7 mil-mb-15 mil-up">
                    <div class="mil-hero-users-card mil-bg-a-2 mil-br-md">
                        <div class="mil-aic mil-mb-30">
                            <x-user-avatars />
                            <span class="mil-fs-32 mil-fw-700 mil-m-4 mil-ml-30">{{ $stats['jobs'] }}+</span>
                        </div>
                        <p class="mil-fs-18 mil-m-4 mil-mr-15 mil-sm-mr-0" style="opacity: .7">More than {{ $stats['jobs'] }} rugs, sofas and armchairs cleaned across Chicago last year. Yours can be next.</p>
                    </div>
                </div>
                <div class="col-lg-5 col-xxl-5 mil-mb-15 mil-up">
                    <div class="mil-hero-reviews-card mil-bg-m-4 mil-br-md">
                        <h3 class="mil-fs-32 mil-mb-30 mil-m-1">Do you have any questions?</h3>
                        <a href="#faq" class="mil-link mil-m-1">Yes, I do <i class="far fa-arrow-right mil-bg-a-1 mil-m-1" style="padding: .2rem 0 0 .2rem"></i></a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
