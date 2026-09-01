@extends('layouts.app')

@section('content')

    @php
        // Some titles carry their own <br class="mil-sm-hidden"> for wrap
        // control elsewhere (see e.g. config/catalog.php's home-office
        // entries) — strip that HTML out before splitting on spaces, or the
        // space inside the tag's attribute gets treated as a word boundary
        // and breaks the tag in half. The hero below has its own line-break
        // mechanism (separate $heroLines), so the plain-text title is all it needs.
        //
        // Same decorative-image-in-title treatment as every other page's
        // hero (About, Services, Contact): break off the last word of the
        // title and splice in a photo. Uses the service's own image where
        // it's a real photo; falls back to the generic accent photo for
        // services with no image (the four home/office ones).
        $heroTitle = trim(preg_replace('#\s+#', ' ', strip_tags($service->title)));
        $heroWords = explode(' ', $heroTitle);
        $heroLastWord = array_pop($heroWords);
        $heroFirstLine = trim(implode(' ', $heroWords));
        $heroImage = $service->image ? $service->imageUrl() : asset('img/ui/t8.jpg');
        $heroLastLine = '<img src="'.$heroImage.'" alt="image" class="mil-text-image mil-long"> '.$heroLastWord;
        $heroLines = $heroFirstLine !== '' ? [$heroFirstLine, $heroLastLine] : [$heroLastLine];
    @endphp

    <x-hero
        :lines="$heroLines"
        :breadcrumbs="[
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Services', 'url' => route('services')],
            ['label' => $heroTitle, 'url' => route('services.show', $service)],
        ]">
        <a href="{{ route('contact') }}" class="mil-btn mil-icon-btn mil-bg-a-1 mil-br-xl mil-hover-bri-105 mil-hover-scale">Book online<i class="far fa-plus mil-bg-m-4 mil-m-1"></i></a>
    </x-hero>

    @php
        $serviceSchema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $heroTitle,
            'description' => $service->meta_description ?: $service->text ?: $service->tagline,
            'url' => route('services.show', $service),
            'image' => $service->image ? $service->imageUrl() : null,
            'areaServed' => setting('site.address')['city'] ?? null,
            'provider' => [
                '@type' => 'LocalBusiness',
                'name' => setting('site.name'),
                'url' => url('/'),
                'telephone' => setting('site.phone_e164'),
            ],
        ]);
    @endphp
    <script type="application/ld+json">{!! json_encode($serviceSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

    {{-- Empty anchor for the hero's "scroll down" hint — no section here is
         guaranteed to render (they're all conditional on the service having
         that content), so this can't just live on the first content block. --}}
    <div id="scroll"></div>

    @php $n = 0; @endphp

    @if (filled($service->text))
        <div class="mil-p-0-15">
            <div class="container">
                @php $n++; @endphp
                <x-section-title :number="$n" title="About this service" />
                <div class="row mil-mb-30">
                    <div class="col-12 mil-up">
                        {{-- Plain-text field from the admin form (not Trix) — escape it,
                             then turn the author's line breaks into paragraph spacing. --}}
                        <p class="mil-fs-24 mil-lh-180 mil-m-1">{!! nl2br(e($service->text)) !!}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($service->steps->isNotEmpty())
        <div class="mil-p-0-15">
            <div class="container">
                @php $n++; @endphp
                <x-section-title :number="$n" title="How we clean it" />
                <div class="row">
                    @foreach ($service->steps as $step)
                        <x-step-card :number="$loop->iteration" :title="$step->title" :line="$loop->iteration % 4 !== 0 && ! $loop->last">{{ $step->text }}</x-step-card>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if (! empty($service->items))
        <div class="mil-p-0-15">
            <div class="container">
                @php $n++; @endphp
                <x-section-title :number="$n" title="What we clean" />
                {{-- mil-includes has generous padding meant for the 3-column layout
                     (see the old carpet page's checklist) — a single column of 5
                     short items left half the box empty, so split across 2 columns
                     to fill it evenly instead. --}}
                @php $checklistColumns = collect($service->items)->chunk((int) ceil(count($service->items) / 2)); @endphp
                <div class="mil-includes mil-bg-m-4 mil-br-md mil-up">
                    <div class="row">
                        @foreach ($checklistColumns as $column)
                            <div class="col-lg-6">
                                <x-check-list variant="spaced" :items="$column" />
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($service->before_image && $service->after_image)
        <div class="mil-p-0-15">
            <div class="container">
                @php $n++; @endphp
                <x-section-title
                    :number="$n"
                    title="Results of our work"
                    lead="Drag across the photo to see the same piece before and after a single visit." />
                <x-before-after-single
                    :before="$service->beforeImageUrl()"
                    :after="$service->afterImageUrl()"
                    :alt="$service->alt ?: $service->title" />
            </div>
        </div>
    @endif

    @if ($otherServices->isNotEmpty())
        <div class="mil-p-0-15">
            <div class="container">
                @php $n++; @endphp
                <x-section-title :number="$n" title="Other services" />
                <div class="row">
                    @foreach ($otherServices as $other)
                        <x-service-wide
                            :title="$other->title"
                            :count="count($other->items ?? [])"
                            noun="item"
                            :url="route('services.show', $other)"
                            action="Learn more" />
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    @if ($articles->isNotEmpty())
        <div class="mil-p-0-15">
            <div class="container">
                @php $n++; @endphp
                <x-section-title :number="$n" title="From our blog" />
                <div class="row">
                    @foreach ($articles as $article)
                        <div class="col-12 mil-mb-15 mil-up">
                            <x-blog-card :post="$article" type="wide" />
                        </div>
                    @endforeach
                </div>
                <div class="mil-up">
                    <a href="{{ route('blog.category', $service) }}" class="mil-link mil-m-1 mil-reverse">More articles about {{ $heroTitle }}<i class="far fa-arrow-right mil-bg-a-2 mil-m-4" style="padding: .2rem 0 0 .2rem"></i></a>
                </div>
            </div>
        </div>
    @endif

    <x-cta headline="Not sure if this is the right fit? Send us a photo and we'll tell you." />

    @if ($faqItems->isNotEmpty())
        @php $n++; @endphp
        <x-faq :number="$n" :items="$faqItems" />
    @endif

@endsection
