@props([
    // An App\Models\Service instance.
    'service',
])

<div class="mil-service-card-long mil-mb-15">
    <div class="row mil-jcb">

        <div class="col-lg-4 mil-mb-15 mil-up">
            <div class="mil-inner-frame mil-bg-m-4 mil-br-md mil-column mil-jcc">
                <h2 class="mil-fs-30 mil-mb-30">{{ $service['title'] }}</h2>
                <div class="mil-up-text mil-a-2 mil-mb-30">{{ $service['tagline'] }}</div>
                <p class="mil-fs-18 mil-lh-160 mil-mb-30">{{ $service['text'] }}</p>
                <a href="{{ route('services.show', $service) }}" class="mil-link mil-m-1 mil-icon-btn">Read more<i class="far fa-arrow-right mil-bg-a-1 mil-m-1"></i></a>
            </div>
        </div>

        <div class="col-lg-4 mil-mb-15 mil-up">
            <div class="mil-img-frame">
                <img src="{{ $service->imageUrl() }}" alt="{{ $service['alt'] }}" class="mil-scale-img" data-value-1="1.15" data-value-2="1">
            </div>
        </div>

        <div class="col-lg-4 mil-mb-15 mil-up">
            <div class="mil-inner-frame mil-bg-m-4 mil-br-md mil-column mil-jcc">
                <x-check-list :items="$service['items']" />
            </div>
        </div>

    </div>
</div>
