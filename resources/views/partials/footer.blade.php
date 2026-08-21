@php
    $logo = config('site.logo');
    $address = setting('site.address');
    $footerNav = [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Services', 'url' => route('services')],
        ['label' => 'Contact', 'url' => route('contact')],
    ];
    // /services is one flat list now (no more folder-specific anchors to link
    // to), so this footer column points at a few real services directly.
    $footerServices = \App\Models\Service::where('is_active', true)->orderBy('sort_order')->limit(3)->get()
        ->map(fn ($service) => ['label' => $service->title, 'url' => $service->url()])
        ->push(['label' => 'All services', 'url' => route('services')])
        ->all();
    $legalLinks = [
        ['label' => 'Privacy Policy', 'url' => route('privacy-policy')],
        ['label' => 'Terms and conditions', 'url' => route('terms-and-conditions')],
        ['label' => 'Cookie Policy', 'url' => route('cookie-policy')],
    ];
@endphp

<footer>
    <div class="mil-footer-bg">
        <video class="mil-footer-bg mil-scale-img" data-value-1="1" data-value-2="1.3" autoplay="autoplay" loop="loop" muted playsinline oncontextmenu="return false;" preload="auto">
            <source src="{{ asset('img/ui/footer.mp4') }}">
        </video>
    </div>
    <div class="mil-footer-content">
        <div class="container">

            <div class="row mil-p-f-60">
                <div class="col-lg-7 col-md-6 col-sm-12 mil-mb-60">
                    <div class="row">
                        <div class="col-8 col-md-6">
                            <a href="{{ route('home') }}" class="mil-logo mil-hover-bri-105 mil-mb-30 mil-up" style="width: 20rem">
                                {{-- Lift goes on the img, not the anchor: GSAP animates y on .mil-up
                                     and would overwrite a transform set on the anchor itself. --}}
                                <img src="{{ logo_url('light') }}" alt="{{ setting('site.name') }}" class="mil-light"
                                     width="{{ $logo['width'] }}" height="{{ $logo['height'] }}"
                                     style="height: auto; transform: translateY(-{{ $logo['lift']['footer'] }})">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 mil-mb-60">
                    <div class="row">
                        <div class="col-6">
                            <ul>
                                @foreach ($footerNav as $item)
                                    <li class="mil-fs-24 mil-fw-600 mil-m-3 mil-hover-a-1 mil-mb-20 mil-up"><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-6">
                            <ul>
                                @foreach ($footerServices as $item)
                                    <li class="mil-up-text mil-hover-m-4 mil-mb-20 mil-up"><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mil-dots mil-up"></div>

            <div class="row mil-p-f-60 mil-sm-column-r">
                <div class="col-lg-7 col-md-6 col-sm-12 mil-column mil-jcb mil-mb-60">
                    <ul class="mil-aic mil-mb-30 mil-up">
                        @foreach (setting('site.socials') as $social)
                            <li class="mil-mr-20 mil-fs-20 mil-m-3">
                                <a href="{{ $social['url'] }}" target="_blank" data-no-swup class="mil-hover-a-1">
                                    <i class="{{ $social['icon'] }}"></i>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <ul class="mil-aic mil-up">
                        @foreach ($legalLinks as $item)
                            <li class="mil-hover-m-4 mil-mr-20">
                                <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 mil-mb-60">
                    <div class="row mil-jcb">
                        <div class="col-12 mil-up">
                            <h5 class="mil-fs-22 mil-fw-600 mil-m-3 mil-mb-20">{{ $address['city'] }}</h5>
                            <p class="mil-mb-20">{{ $address['line_1'] }}, <br>{{ $address['line_2'] }}</p>
                            <p class="mil-up-text mil-a-1 mil-mb-20"><a href="tel:{{ setting('site.phone_e164') }}" data-no-swup>{{ setting('site.phone') }}</a></p>
                            <p><a href="mailto:{{ setting('site.email') }}" data-no-swup>{{ setting('site.email') }}</a></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mil-footer-bottom mil-aic mil-jcb mil-up">
                <p>© {{ date('Y') }} {{ setting('site.footer_seo_text', 'Carpet, rug & upholstery cleaning in Chicago, IL') }}. All rights reserved.</p>
            </div>

        </div>
    </div>
</footer>
