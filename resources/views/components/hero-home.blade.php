{{--
    The home page hero is a different shape from the inner-page <x-hero>:
    centred, with a lead paragraph, two buttons and the wide image frame below.
--}}
<div class="mil-hero-1 mil-relative" id="top">
    <img src="{{ asset('img/ui/bg3.png') }}" alt="background" class="mil-bg mil-parallax-img-top" data-value-1="0" data-value-2="300">
    <div class="container">
        <div class="row mil-jcc">

            <div class="col-xl-10">
                <div class="mil-main-title mil-jcc mil-aic mil-column mil-mb-f mil-scale-img-top" data-value-1="1" data-value-2=".85">

                    <div class="mil-word-frame">
                        <x-star-burst class="mil-sm-hidden" style="width: 5vw; top: -20%; left: 24%" />
                        <h1 class="mil-fs-100 mil-sm-fs-52 mil-lh-110">{!! setting('home.hero_line_1', 'Carpets, Sofas') !!}</h1>
                    </div>

                    <div class="mil-word-frame">
                        <h1 class="mil-fs-100 mil-sm-fs-52 mil-lh-110">{!! setting('home.hero_line_2', '&amp; <img src="'.asset('img/ui/t3.jpg').'" alt="image" class="mil-text-image mil-sm-hidden"> Armchairs <img src="'.asset('img/ui/t2.jpg').'" alt="image" class="mil-text-image mil-long mil-sm-hidden">') !!}</h1>
                    </div>

                    <div class="mil-word-frame mil-mb-40">
                        <h1 class="mil-fs-100 mil-sm-fs-52 mil-lh-110">{!! setting('home.hero_line_3', 'Cleaned <img src="'.asset('img/ui/t1.jpg').'" alt="image" class="mil-text-image mil-sm-hidden"> in Chicago') !!}</h1>
                        <x-star-burst class="mil-sm-hidden" style="width: 2vw; bottom: -40%; right: 25%" />
                    </div>

                    <p class="mil-fs-20 mil-lh-160 mil-tac mil-mb-40" style="max-width: 62rem">{{ setting('home.hero_lead', 'Hot water extraction for area rugs, wall-to-wall carpet, sectionals, armchairs and mattresses. Stains, pet odors and dust mites gone in one visit — dry in 4-6 hours.') }}</p>

                    <div class="mil-buttons-frame mil-mb-60 mil-sm-mb-0">
                        <a href="{{ route('contact') }}" class="mil-btn mil-icon-btn mil-bg-a-1 mil-br-xl mil-hover-bri-105 mil-hover-scale">Book online<i class="far fa-plus mil-bg-m-4 mil-m-1"></i></a>
                        <a href="{{ route('services') }}" class="mil-btn mil-bg-m-2-light mil-br-xl mil-hover-bg-a-1 mil-hover-scale">All services</a>
                    </div>

                </div>
            </div>

            <div class="col-12">
                <div class="mil-image-frame">
                    <div class="mil-circle-text-position mil-absolute mil-mb-f">
                        <div class="mil-circle-text mil-rotate mil-accent-inside" data-value="360">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 300 300" enable-background="new 0 0 300 300" xml:space="preserve" aria-hidden="true" focusable="false">
                                <defs>
                                    <path id="circlePath" d="M 150, 150 m -60, 0 a 60,60 0 0,1 120,0 a 60,60 0 0,1 -120,0 " />
                                </defs>
                                <circle cx="150" cy="100" r="75" fill="none" />
                                <g>
                                    <use xlink:href="#circlePath" fill="none" />
                                    <text class="mil-link mil-dark" style="letter-spacing: .75rem">
                                        <textPath xlink:href="#circlePath">Scroll down - Scroll down - </textPath>
                                    </text>
                                </g>
                            </svg>
                        </div>
                        <a href="#scroll" class="far fa-arrow-down mil-bg-m-1 mil-a-1" aria-label="Scroll down"></a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
