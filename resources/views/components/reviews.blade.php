@props([
    'number',
    'title' => 'What clients say',
    // Defaults to the active reviews in the database.
    'items' => null,
    'padding' => 'mil-p-0-f',
])

@php
    $reviews = $items ?? \App\Models\Review::query()->where('is_active', true)->orderBy('sort_order')->get();
@endphp

<div class="{{ $padding }}" id="reviews">
    <div class="container">

        <x-section-title :title="$title" :number="$number" />

        <div class="mil-revi-pagination mil-up mil-mb-60"></div>

        <div class="row mil-jcc">
            <div class="col-lg-8 mil-relative">

                <div class="mil-slider-nav mil-soft mil-reviews-nav mil-up">
                    <div class="mil-slider-arrow mil-prev mil-revi-prev"><i class="far fa-arrow-right"></i></div>
                    <div class="mil-slider-arrow mil-revi-next"><i class="far fa-arrow-right"></i></div>
                </div>

                <div class="swiper-container mil-reviews-slider mil-mb-60">
                    <div class="swiper-wrapper">
                        @foreach ($reviews as $review)
                            <div class="swiper-slide">
                                <div class="mil-review-frame mil-tac" data-swiper-parallax="-600" data-swiper-parallax-opacity="0" data-swiper-parallax-scale=".6">
                                    <h5 class="mil-fs-28 mil-m-1 mil-up mil-mb-15">{{ $review['name'] }}</h5>
                                    <p class="mil-m-1 mil-up-text mil-up mil-mb-40">{{ $review['location'] }}</p>
                                    <div class="mil-fs-72 mil-a-2 mil-lh-100 mil-up" style="transform: translateX(-1rem)"><i>"</i></div>
                                    <p class="mil-fs-22 mil-m-1 mil-lh-200 mil-up">{{ $review['text'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mil-aic mil-jcc mil-up">
                    <p class="mil-mr-15">Overall:</p>
                    @for ($i = 0; $i < 5; $i++)
                        <i @class(['fas fa-star mil-fs-20 mil-a-1', 'mil-mr-5' => $i < 4, 'mil-mr-15' => $i === 4])></i>
                    @endfor
                    <p>{{ setting('site.stats')['rating'] }}</p>
                </div>

            </div>
        </div>

    </div>
</div>
