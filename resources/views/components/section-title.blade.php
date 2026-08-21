@props([
    'title',
    // Section counter shown on the right; padded to two digits.
    'number',
    // Optional intro paragraph rendered under the rule.
    'lead' => null,
])

<div class="mil-section-title mil-mb-f mil-up">
    <h2 class="mil-fs-36">{{ $title }}</h2>
    <div class="mil-dots"></div>
    <b class="mil-fs-24">{{ str_pad((string) $number, 2, '0', STR_PAD_LEFT) }}</b>
</div>

@if ($lead)
    <div class="row mil-mb-30">
        <div class="col-lg-8 mil-up">
            <p class="mil-fs-18 mil-lh-160">{!! $lead !!}</p>
        </div>
    </div>
@endif
