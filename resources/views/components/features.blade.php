@props([
    // [['icon' => '...', 'title' => 'raw html', 'lines' => ['...']], ...]
    'items' => [],
])

<div class="mil-features mil-bg-m-4 mil-br-md mil-up">
    <div class="row">
        @foreach ($items as $item)
            <div class="col-lg-4 mil-tac mil-mb-60 mil-up">
                <img src="{{ asset($item['icon']) }}" alt="icon" class="mil-card-icon mil-mb-30">
                <h3 class="mil-fs-30 mil-mb-20 mil-lh-110">{!! $item['title'] !!}</h3>
                @foreach ($item['lines'] ?? [] as $line)
                    <p class="mil-fs-18 mil-lh-160">{!! $line !!}</p>
                @endforeach
            </div>
        @endforeach
    </div>
</div>
