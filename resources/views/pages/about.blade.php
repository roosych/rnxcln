@extends('layouts.app')

@section('content')

    @php
        $heroLines = [
            'A few words',
            '<img src="'.asset('img/ui/t10.jpg').'" alt="image" class="mil-text-image mil-long"> about us',
        ];
    @endphp

    <x-hero
        :lines="$heroLines"
        :breadcrumbs="[
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'About', 'url' => route('about')],
        ]">
        <a href="{{ route('contact') }}" class="mil-btn mil-icon-btn mil-bg-a-1 mil-br-xl mil-hover-bri-105 mil-hover-scale">Book online<i class="far fa-plus mil-bg-m-4 mil-m-1"></i></a>
    </x-hero>

    <x-about-boxes number="1" id="scroll" />

@endsection
