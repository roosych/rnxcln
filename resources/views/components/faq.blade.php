@props([
    'number',
    // FaqItem[] (or [['question' => '...', 'answer' => '...'], ...]) — split across two columns automatically.
    'items' => [],
])

@php
    $items = collect($items)->values();
    $columns = $items->chunk((int) ceil($items->count() / 2));

    $faqSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $items->map(fn ($item) => [
            '@type' => 'Question',
            'name' => strip_tags((string) $item['question']),
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => strip_tags((string) $item['answer']),
            ],
        ])->all(),
    ];
@endphp

<div class="mil-p-0-30" id="faq">
    <div class="container">

        <x-section-title title="FAQ" :number="$number" />

        <div class="row mil-mb-60">
            @foreach ($columns as $column)
                <div class="col-lg-6">
                    @foreach ($column as $item)
                        <x-faq-item :question="$item['question']">{{ $item['answer'] }}</x-faq-item>
                    @endforeach
                </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-lg-6 mil-mb-30 mil-up">
                <p class="mil-lh-160 mil-fs-18">Didn't find your answer? Send us a photo of the rug, sofa or armchair at {{ setting('site.email') }}, or call {{ setting('site.phone') }}. We reply to every message within one business hour, seven days a week.</p>
            </div>
            <div class="col-lg-6 mil-jce mil-sm-jcs mil-mb-30 mil-up">
                <a href="{{ route('contact') }}" class="mil-btn mil-bg-m-2-light mil-br-xl mil-hover-bg-a-1 mil-hover-scale mil-icon-btn">Contact us<i class="far fa-envelope mil-bg-m-4 mil-m-1"></i></a>
            </div>
        </div>

    </div>
</div>

@if ($items->isNotEmpty())
    <script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif
