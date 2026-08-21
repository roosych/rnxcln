@extends('layouts.app')

@section('content')

    <x-hero
        :lines="['Page not', 'found']"
        :breadcrumbs="[
            ['label' => 'Home', 'url' => route('home')],
        ]">
        <a href="{{ route('home') }}" class="mil-btn mil-icon-btn mil-bg-a-1 mil-br-xl mil-hover-bri-105 mil-hover-scale mil-mr-40">Back to home<i class="far fa-arrow-right mil-bg-m-4 mil-m-1"></i></a>
        <a href="{{ route('services') }}" class="mil-link mil-m-1 mil-reverse">Browse services<i class="far fa-arrow-right mil-bg-a-2 mil-m-4"></i></a>
    </x-hero>

@endsection
