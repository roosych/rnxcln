@props([
    'post',
    // 'grid' (square cover, used in 3-up rows) or 'wide' (mil-type-2, cover
    // beside the text) — see public/css/style.css .mil-blog-card.mil-type-2.
    'type' => 'grid',
    // True for the "You may also like" cards on the publication page, which
    // sit inside a white .mil-publication card and need the mil-bg-m-3 tint
    // (plus a lighter button) to stand out from it — see publication.html.
    'nested' => false,
])

@php
    $cover = $post->cover_image ? $post->coverImageUrl() : asset('img/blog/'.((($post->id - 1) % 8) + 1).'.jpg');
    // Two alternating pill colors, same pairing the reference theme cycles
    // through its demo categories with.
    $pillClasses = ['mil-bg-m-1 mil-up-text mil-m-3', 'mil-bg-a-2 mil-up-text mil-m-3'];
@endphp

<a href="{{ route('blog.show', $post) }}" class="mil-blog-card mil-br-lg @if ($type === 'wide') mil-type-2 @endif">
    <div class="mil-cover">
        <img src="{{ $cover }}" alt="{{ $post->alt ?: $post->title }}">
        @if ($post->categories->isNotEmpty())
            <ul class="mil-category">
                @foreach ($post->categories->take(2) as $i => $category)
                    <li class="{{ $pillClasses[$i % 2] }} mil-mr-5">{!! $category->title !!}</li>
                @endforeach
            </ul>
        @endif
    </div>
    <div class="mil-descr @if ($nested) mil-bg-m-3 @endif">
        <p class="mil-mb-20"><span class="mil-date">{{ $post->published_at?->format('j F Y') }}</span> &nbsp; by &nbsp; <span class="mil-a-2">{{ $post->author_name ?: setting('site.name') }}</span></p>
        <h5 class="mil-fs-28 mil-m-1 mil-mb-15 mil-lh-140">{{ $post->title }}</h5>
        @if ($post->excerpt)
            <p class="mil-lh-180 mil-mb-30">{{ $post->excerpt }}</p>
        @endif
        <div class="mil-btn mil-sm @if ($nested) mil-bg-m-2-light @else mil-bg-m-3 @endif mil-br-xl mil-hover-bri-105 mil-hover-scale">Read more</div>
    </div>
</a>
