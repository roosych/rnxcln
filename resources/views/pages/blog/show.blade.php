@extends('layouts.app')

@section('content')

    @php
        $heroTitle = trim(preg_replace('#\s+#', ' ', strip_tags($post->title)));
        $heroWords = explode(' ', $heroTitle);
        $heroLastWord = array_pop($heroWords);
        $heroFirstLine = trim(implode(' ', $heroWords));
        $heroLastLine = '<img src="'.asset('img/ui/t7.jpg').'" alt="image" class="mil-text-image mil-long"> '.$heroLastWord;
        $heroLines = $heroFirstLine !== '' ? [$heroFirstLine, $heroLastLine] : [$heroLastLine];
        $pillClasses = ['mil-bg-m-1 mil-up-text mil-m-3', 'mil-bg-a-2 mil-up-text mil-m-3'];
        $cover = $post->cover_image ? $post->coverImageUrl() : asset('img/blog/'.((($post->id - 1) % 8) + 1).'.jpg');
    @endphp

    <x-hero
        :lines="$heroLines"
        :breadcrumbs="[
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Blog', 'url' => route('blog.index')],
        ]">
        <ul class="mil-category">
            @foreach ($post->categories->take(2) as $i => $category)
                <li class="{{ $pillClasses[$i % 2] }} mil-mr-5">{!! $category->title !!}</li>
            @endforeach
            <li class="mil-date">{{ $post->published_at?->format('j F Y') }}</li>
        </ul>
    </x-hero>

    @php
        $articleSchema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $heroTitle,
            'description' => $post->meta_description ?: $post->excerpt,
            'image' => $post->cover_image ? $post->coverImageUrl() : null,
            'datePublished' => $post->published_at?->toAtomString(),
            'dateModified' => $post->updated_at->toAtomString(),
            'author' => [
                '@type' => 'Organization',
                'name' => $post->author_name ?: setting('site.name'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => setting('site.name'),
            ],
            'mainEntityOfPage' => route('blog.show', $post),
        ]);
    @endphp
    <script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

    <div id="scroll"></div>

    <div class="mil-p-0-f">
        <div class="container">
            <div class="mil-port-cover">
                <img src="{{ $cover }}" alt="{{ $post->alt ?: $heroTitle }}" class="mil-scale-img" data-value-1="1.2" data-value-2="1">
            </div>
        </div>
    </div>

    <div class="mil-p-0-15 mil-relative">
        <div class="container">
            <div class="row mil-jcb">
                <div class="col-12 col-lg-8">

                    <div class="mil-publication mil-up mil-mb-15">
                        <h4 class="mil-fs-36 mil-mb-30 mil-lh-120 mil-up">{{ $post->title }}</h4>
                        @if ($post->excerpt)
                            <p class="mil-fs-18 mil-mb-60 mil-lh-160 mil-up">{{ $post->excerpt }}</p>
                        @endif
                        {{-- Trix-authored HTML from a logged-in admin/editor — same trust
                             boundary as Admin > SEO > custom head code in partials/head.blade.php. --}}
                        <div class="mil-article-body mil-fs-18 mil-lh-160 mil-up">{!! $post->body !!}</div>
                    </div>

                    @if ($related->isNotEmpty())
                        <div class="mil-publication mil-mb-15">
                            <h2 class="mil-fs-36 mil-mb-60 mil-up">You may also like</h2>
                            <div class="row">
                                @foreach ($related as $other)
                                    <div class="col-12 col-lg-6 mil-mb-15 mil-up">
                                        <x-blog-card :post="$other" :nested="true" />
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                @include('pages.blog._sidebar', ['activeCategory' => null])
            </div>
        </div>
    </div>

@endsection
