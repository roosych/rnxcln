@props([
    'number',
    // Raw HTML so the heading can keep its <br> break.
    'title',
    'width' => 'col-md-6 col-lg-3',
    // The last card in a row drops the connecting line.
    'line' => true,
])

<div class="{{ $width }} mil-mb-15 mil-up">
    <div @class(['mil-step-card', 'mil-step-line' => $line, 'mil-bg-m-4 mil-br-md'])>
        <div class="mil-fs-40 mil-ff-3 mil-a-2 mil-mb-20">{{ str_pad((string) $number, 2, '0', STR_PAD_LEFT) }}</div>
        <h3 class="mil-fs-30 mil-mb-20 mil-lh-110 mil-mb-15">{!! $title !!}</h3>
        <p>{{ $slot }}</p>
    </div>
</div>
