@extends('layouts.app')

@section('content')

    @php
        $heroLines = [
            setting('contact-page.hero_line_1', "Let's get in"),
            setting('contact-page.hero_line_2', '<img src="'.asset('img/ui/t10.jpg').'" alt="image" class="mil-text-image mil-long"> touch'),
        ];
        $address = setting('site.address');
    @endphp

    <x-hero
        :lines="$heroLines"
        :breadcrumbs="[
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Contact', 'url' => route('contact')],
        ]">
        <a href="#form" class="mil-btn mil-icon-btn mil-bg-a-1 mil-br-xl mil-hover-bri-105 mil-hover-scale">Order now<i class="far fa-plus mil-bg-m-4 mil-m-1"></i></a>
    </x-hero>

    {{-- 01 — form and map --}}
    <div class="mil-p-0-15" id="scroll">
        <div class="container">
            <x-section-title number="1" :title="setting('contact-page.section_1_title', 'Write to us')" />

            <div class="row">
                <div class="col-lg-6 mil-mb-15 mil-up">
                    <div class="mil-hero-form-frame mil-bg-m-4 mil-br-md" id="form">

                        <div class="mil-aic mil-column">
                            <h3 class="mil-fs-32 mil-tac mil-lh-140 mil-mb-60">{!! setting('contact-page.form_heading', 'We accept your requests <br>24 hours a day, 7 days a week') !!}</h3>
                        </div>

                        <p class="mil-fs-18 mil-a-1 mil-mb-30" data-form-status @class(['mil-hidden' => ! session('status')])>{{ session('status') }}</p>

                        <form action="{{ route('contact.send') }}" method="POST" data-ajax>
                            @csrf
                            <x-honeypot />
                            <div class="row">

                                <div class="col-md-6 mil-mb-15">
                                    <div class="mil-input-frame">
                                        <input type="text" id="user-name" name="name" placeholder="Name" class="mil-br-md mil-bg-m-3" autocomplete="name" value="{{ old('name') }}">
                                        <i class="far fa-user mil-a-2"></i>
                                    </div>
                                    <p class="mil-fs-14 mil-error-1 mil-mt-5" data-error-for="name">@error('name'){{ $message }}@enderror</p>
                                </div>

                                <div class="col-md-6 mil-mb-15">
                                    <div class="mil-input-frame">
                                        <input type="tel" id="user-phone" name="phone" class="mil-phone-input mil-br-md mil-bg-m-3" placeholder="+1 (___) ___-____" autocomplete="tel" value="{{ old('phone') }}">
                                        <i class="far fa-mobile mil-a-2"></i>
                                    </div>
                                    <p class="mil-fs-14 mil-error-1 mil-mt-5" data-error-for="phone">@error('phone'){{ $message }}@enderror</p>
                                </div>

                                <div class="col-lg-12 mil-mb-15">
                                    <div class="mil-input-frame mil-custom-select">
                                        <div class="mil-select-button mil-br-md mil-bg-m-3 mil-m-1">
                                            <span class="mil-selected-value" data-default-label="Service type">Service type</span>
                                        </div>
                                        <ul class="mil-select-dropdown mil-br-md">
                                            {{-- Mirrors whatever's active in Services — see admin.services.index. --}}
                                            @foreach (\App\Models\Service::where('is_active', true)->orderBy('sort_order')->get() as $i => $enquiryService)
                                                <li>
                                                    <input type="radio" id="service-{{ $i }}" name="service" value="{{ $enquiryService->title }}">
                                                    <label for="service-{{ $i }}">{{ $enquiryService->title }}</label>
                                                </li>
                                            @endforeach
                                        </ul>
                                        <i class="far fa-chevron-down mil-a-2"></i>
                                    </div>
                                    <p class="mil-fs-14 mil-error-1 mil-mt-5" data-error-for="service">@error('service'){{ $message }}@enderror</p>
                                </div>

                                <div class="col-md-6 mil-mb-15">
                                    <div class="mil-input-frame mil-custom-select">
                                        <div class="mil-select-button mil-br-md mil-bg-m-3 mil-m-1">
                                            <span class="mil-selected-value" data-default-label="ZIP code">ZIP code</span>
                                        </div>
                                        <ul class="mil-select-dropdown mil-scrollable mil-br-md">
                                            @foreach (\App\Models\ServiceArea::where('is_active', true)->orderBy('area')->get() as $serviceArea)
                                                <li>
                                                    <input type="radio" id="zip-{{ $serviceArea->zip }}" name="zip" value="{{ $serviceArea->zip }}">
                                                    <label for="zip-{{ $serviceArea->zip }}">{{ $serviceArea->zip }} — {{ $serviceArea->area }}</label>
                                                </li>
                                            @endforeach
                                            <li>
                                                <input type="radio" id="zip-other" name="zip" value="Other">
                                                <label for="zip-other">Other — not listed</label>
                                            </li>
                                        </ul>
                                        <i class="far fa-chevron-down mil-a-2"></i>
                                    </div>
                                    <p class="mil-fs-14 mil-error-1 mil-mt-5" data-error-for="zip">@error('zip'){{ $message }}@enderror</p>
                                </div>

                                <div class="col-md-6 mil-mb-15">
                                    <div class="mil-input-frame">
                                        <input type="text" id="user-date" name="preferred_date" placeholder="Preferred date" class="mil-date-input mil-br-md mil-bg-m-3" autocomplete="off" value="{{ old('preferred_date') }}">
                                        <i class="far fa-calendar-alt mil-a-2"></i>
                                    </div>
                                    <p class="mil-fs-14 mil-error-1 mil-mt-5" data-error-for="preferred_date">@error('preferred_date'){{ $message }}@enderror</p>
                                </div>

                                <div class="col-lg-12 mil-mb-30">
                                    <div class="mil-input-frame mil-type-2">
                                        <textarea id="message" name="message" placeholder="Tell us what needs cleaning — how many rugs, sofas or armchairs, the fabric, any stains or pet odors" class="mil-br-md mil-bg-m-3" autocomplete="off">{{ old('message') }}</textarea>
                                        <i class="far fa-comment-alt mil-a-2"></i>
                                    </div>
                                    <p class="mil-fs-14 mil-error-1 mil-mt-5" data-error-for="message">@error('message'){{ $message }}@enderror</p>
                                </div>

                                <div class="col-lg-12 mil-aic">
                                    <p class="mil-fs-14 mil-m-2">By clicking the send button, you agree to the rules for processing personal data</p>
                                    <button class="mil-btn mil-bg-a-1 mil-br-xl mil-hover-bri-105 mil-hover-scale mil-ml-60 mil-sm-ml-30" type="submit">Send requests</button>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-lg-6 mil-mb-15 mil-up">
                    <div class="mil-map-frame">
                        {{-- Built from the address setting, so the pin follows the office. --}}
                        <iframe
                            src="https://www.google.com/maps?q={{ urlencode($address['line_1'].', '.$address['line_2']) }}&output=embed"
                            width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade" title="Office location"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 02 — contact info --}}
    <div class="mil-p-0-f">
        <div class="container">
            <x-section-title number="2" :title="setting('contact-page.section_2_title', 'Contact info')" />

            <x-features :items="[
                [
                    'icon'  => 'img/ui/icons/14.png',
                    'title' => 'Phone',
                    'lines' => ['<a href=\'tel:'.setting('site.phone_e164').'\' data-no-swup>'.setting('site.phone').'</a>'],
                ],
                [
                    'icon'  => 'img/ui/icons/15.png',
                    'title' => 'Address',
                    'lines' => [$address['line_1'].', <br>'.$address['line_2']],
                ],
                [
                    'icon'  => 'img/ui/icons/13.png',
                    'title' => 'Working hours',
                    'lines' => setting('site.hours'),
                ],
            ]" />
        </div>
    </div>

@endsection
