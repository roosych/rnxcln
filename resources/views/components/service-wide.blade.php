@props([
    // Raw HTML: titles carry a <br class="mil-sm-hidden"> to control wrapping.
    'title',
    'url',
    'action' => 'Learn more',
    // A plain count instead of the item list itself — keeps every card the
    // same shape no matter how many items the underlying service/category has.
    'count' => null,
    'noun' => 'service',
    // 'col-12' for a full-width card next to x-service-long in a mixed grid.
    'width' => 'col-md-6',
])

<div class="{{ $width }} mil-mb-15 mil-up">
    <a href="{{ $url }}" class="mil-service-card mil-column mil-jcb mil-bg-m-4 mil-br-md">
        <div>
            <h3 class="mil-fs-30 mil-mb-15 mil-lh-110">{!! $title !!}</h3>
            @if ($count)
                <div class="mil-fs-16 mil-a-2">{{ $count }} {{ Str::plural($noun, $count) }}</div>
            @endif
        </div>
        <div class="mil-mt-30">
            <div class="mil-btn mil-sm mil-bg-m-3 mil-br-xl mil-hover-bri-105 mil-hover-scale">{{ $action }}</div>
        </div>
    </a>
</div>
