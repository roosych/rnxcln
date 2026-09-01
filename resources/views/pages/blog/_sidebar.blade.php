{{--
    Shared by pages/blog/index.blade.php and pages/blog/show.blade.php.
    Expects $categories (active services tagged on at least one published
    post) and, optionally, $activeCategory (the Service being filtered on).
--}}
@php
    // Random pick rather than a curated "featured" list — the site doesn't
    // have enough traffic data to genuinely rank "popular" posts, and a
    // fixed featured set went stale. Excludes the post currently being read
    // (only set when this partial is included from pages/blog/show.blade.php).
    $popularPosts = \App\Models\BlogPost::published()
        ->when(isset($post), fn ($q) => $q->where('id', '!=', $post->id))
        ->inRandomOrder()
        ->take(3)
        ->get();
@endphp

<div class="col-12 col-lg-4">
    <div class="mil-sidebar mil-mb-f">

        <div class="mil-text-side mil-auto mil-mb-15 mil-up">
            <div class="mil-box-bg">
                <img src="{{ asset('img/ui/t5.jpg') }}" alt="bg">
            </div>
            <div class="mil-text-frame mil-column mil-aic mil-w-100">
                <div class="mil-aic mil-mb-20">
                    <p class="mil-tac">We clean objects of any <br>complexity and size in 6-9 hours</p>
                </div>
                <h2 class="mil-fs-52 mil-m-3 mil-lh-110 mil-mb-40 mil-tac">Want to learn more about our services?</h2>
                <div class="mil-buttons-frame">
                    <a href="{{ route('services') }}" class="mil-btn mil-icon-btn mil-bg-a-1 mil-br-xl mil-hover-bri-105 mil-hover-scale">Our Services<i class="far fa-plus mil-bg-m-1 mil-a-1"></i></a>
                </div>
            </div>
        </div>

        @if ($categories->isNotEmpty())
            <div class="mil-sidebar-card mil-mb-15 mil-up">
                <h4 class="mil-mb-40 mil-fs-24">Categories</h4>
                <div class="mil-dots mil-mb-40"></div>
                <ul class="mil-categories">
                    @foreach ($categories as $category)
                        <li>
                            <a href="{{ route('blog.category', $category) }}" @class(['mil-a-1' => ($activeCategory ?? null)?->id === $category->id])>{!! $category->title !!}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($popularPosts->isNotEmpty())
            <div class="mil-sidebar-card mil-mb-15 mil-up">
                <h4 class="mil-fs-24 mil-mb-40">Popular</h4>
                <div class="mil-dots mil-mb-40"></div>
                @foreach ($popularPosts as $popular)
                    <a href="{{ route('blog.show', $popular) }}" class="mil-blog-card mil-type-2 mil-br-lg {{ $loop->last ? 'mil-mb-30' : 'mil-mb-15' }}">
                        <div class="mil-cover">
                            <img src="{{ $popular->cover_image ? $popular->coverImageUrl() : asset('img/blog/'.((($popular->id - 1) % 8) + 1).'.jpg') }}" alt="cover">
                        </div>
                        <div class="mil-descr">
                            <h5 class="mil-fs-20 mil-m-1 mil-lh-140">{{ $popular->title }}</h5>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        <div class="mil-sidebar-card mil-aic mil-mb-15 mil-jcb mil-bg-a-2 mil-up">
            <h5 class="mil-fs-24 mil-md-fs-32 mil-m-4 mil-mb-30 mil-md-mb-30">Get in touch</h5>
            <a href="{{ route('contact') }}" class="mil-link mil-m-4 mil-mb-30">Contact us<i class="far fa-arrow-right mil-bg-a-1 mil-m-1"></i></a>
        </div>

    </div>
</div>
