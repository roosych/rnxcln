@extends('layouts.app')

@section('content')

    <x-hero-home />

    @php $n = 0; @endphp

    {{-- featured services --}}
    <div class="mil-p-f-30" id="scroll">
        <div class="container">
            @php $n++; @endphp
            <x-section-title
                :number="$n"
                :title="setting('home.section_1_title', 'Our most-requested work')"
                :lead="setting('home.section_1_lead', 'A mix of what people book us for most — from truck-grade carpet and upholstery extraction to a full home reset.')" />

            <div class="row">
                @foreach (\App\Models\Service::where('is_active', true)->where('is_featured', true)->orderBy('sort_order')->get() as $featuredService)
                    @if ($featuredService->image)
                        <div class="col-12">
                            <x-service-long :service="$featuredService" />
                        </div>
                    @else
                        <x-service-wide
                            :title="$featuredService->title"
                            :count="count($featuredService->items ?? [])"
                            noun="item"
                            :url="$featuredService->url()"
                            action="Learn more"
                            width="col-12" />
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- method --}}
    <div class="mil-p-0-15">
        <div class="container">
            @php $n++; @endphp
            <x-section-title :number="$n" :title="setting('home.section_2_title', 'How it works')" />

            <div class="row">
                @foreach (\App\Models\ProcessStep::where('group', 'home')->where('is_active', true)->orderBy('sort_order')->get() as $step)
                    <x-step-card :number="$loop->iteration" :title="$step->title" :line="$loop->iteration % 4 !== 0 && ! $loop->last">{{ $step->text }}</x-step-card>
                @endforeach
            </div>
        </div>
    </div>

    {{-- other services — everything active and not featured, random order; hidden if nothing's left over --}}
    @php
        $otherServices = \App\Models\Service::where('is_active', true)->where('is_featured', false)->inRandomOrder()->get();
    @endphp
    @if ($otherServices->isNotEmpty())
        <div class="mil-p-0-15">
            <div class="container">
                @php $n++; @endphp
                <x-section-title
                    :number="$n"
                    :title="setting('home.section_3_title', 'Other services')"
                    :lead="setting('home.section_3_lead', 'Carpet and upholstery cleaning is our main line of work, but the same crew can take care of the rest of the place too.')" />

                <div class="row">
                    @foreach ($otherServices as $otherService)
                        <x-service-wide
                            :title="$otherService->title"
                            :count="count($otherService->items ?? [])"
                            noun="item"
                            :url="$otherService->url()"
                            action="Learn more" />
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- about --}}
    @php $n++; @endphp
    <x-about-boxes :number="$n" />

    {{-- reviews --}}
    @php $n++; @endphp
    <x-reviews :number="$n" />

    {{-- faq --}}
    @php $n++; @endphp
    <x-faq :number="$n" :items="\App\Models\FaqItem::query()->orderBy('sort_order')->get()" />

@endsection
