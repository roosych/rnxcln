@extends('layouts.app')

@section('content')

    @php
        $heroLines = [
            setting('services-page.hero_line_1', 'Our'),
            setting('services-page.hero_line_2', '<img src="'.asset('img/ui/t8.jpg').'" alt="image" class="mil-text-image mil-long"> services'),
        ];
    @endphp

    <x-hero
        :lines="$heroLines"
        :breadcrumbs="[
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Services', 'url' => route('services')],
        ]">
        <a href="{{ route('contact') }}" class="mil-btn mil-icon-btn mil-bg-a-1 mil-br-xl mil-hover-bri-105 mil-hover-scale">Book online<i class="far fa-plus mil-bg-m-4 mil-m-1"></i></a>
    </x-hero>

    {{-- 01 — all services, flat --}}
    <div class="mil-p-f-30" id="scroll">
        <div class="container">
            <x-section-title
                number="1"
                :title="setting('services-page.section_1_title', 'All services')"
                :lead="setting('services-page.section_1_lead', 'Everything we clean, in one list — carpets, upholstery, mattresses, and the rest of the home or office. Pick a card to see what\'s included.')" />

            <div class="row">
                @foreach (\App\Models\Service::where('is_active', true)->orderBy('sort_order')->get() as $item)
                    <x-service-wide :title="$item->title" :count="count($item->items ?? [])" noun="item" :url="$item->url()" action="Learn more" />
                @endforeach
            </div>
        </div>
    </div>

    {{-- 02 — how a visit works --}}
    <div class="mil-p-0-15">
        <div class="container">
            <x-section-title number="2" :title="setting('services-page.section_2_title', 'How a visit works')" />

            <div class="row">
                @foreach (\App\Models\ProcessStep::where('group', 'services')->where('is_active', true)->orderBy('sort_order')->get() as $step)
                    <x-step-card :number="$loop->iteration" :title="$step->title" :line="$loop->iteration % 4 !== 0 && ! $loop->last">{{ $step->text }}</x-step-card>
                @endforeach
            </div>
        </div>
    </div>

    <x-cta :headline="setting('services-page.cta_headline', \"Not sure which service you need? Send us a photo and we'll tell you.\")" />

    <x-faq number="3" :items="\App\Models\FaqItem::query()->orderBy('sort_order')->get()" />

@endsection
