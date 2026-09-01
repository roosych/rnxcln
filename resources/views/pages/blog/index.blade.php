@extends('layouts.app')

@section('content')

    @php
        $breadcrumbs = [['label' => 'Home', 'url' => route('home')], ['label' => 'Blog', 'url' => route('blog.index')]];
        $heroImage = '<img src="'.asset('img/ui/t7.jpg').'" alt="image" class="mil-text-image mil-long">';
        if ($activeCategory) {
            $categoryTitle = trim(strip_tags($activeCategory->title));
            $breadcrumbs[] = ['label' => $categoryTitle, 'url' => route('blog.category', $activeCategory)];
            $heroLines = [$categoryTitle, $heroImage.' Category'];
        } else {
            $heroLines = ['Tips &amp; Tricks', $heroImage.' In Our Blog'];
        }
    @endphp

    <x-hero :lines="$heroLines" :breadcrumbs="$breadcrumbs">
        <a href="{{ route('services') }}" class="mil-btn mil-icon-btn mil-bg-a-1 mil-br-xl mil-hover-bri-105 mil-hover-scale mil-mr-40">Our services<i class="far fa-plus mil-bg-m-4 mil-m-1"></i></a>
    </x-hero>

    <div id="scroll"></div>

    <div class="mil-relative">
        <div class="container">
            <x-section-title :number="1" :title="$activeCategory ? trim(strip_tags($activeCategory->title)).' articles' : 'Latest publications'" />
            <div class="row mil-jcb">
                <div class="col-12 col-lg-8">
                    @if ($posts->isEmpty())
                        <p class="mil-lh-180">No articles here yet — check back soon.</p>
                    @else
                        <div class="row">
                            @foreach ($posts as $post)
                                <div class="col-12 mil-mb-15 mil-up">
                                    <x-blog-card :post="$post" type="wide" />
                                </div>
                            @endforeach
                        </div>
                        <div class="mil-up mil-p-0-f mil-mt-40">
                            {{ $posts->links('vendor.pagination.mil') }}
                        </div>
                    @endif
                </div>

                @include('pages.blog._sidebar')
            </div>
        </div>
    </div>

@endsection
