@props([
    'before',
    'after',
    'alt' => 'before and after',
])

{{--
    img-comparison-slider (public/js/plugins/img-comparison-slider.js, a
    <img-comparison-slider> web component) — drag anywhere on the image, or
    the round handle, to compare. Handles mouse, touch and keyboard (focus
    it and use arrow keys) natively, so it works the same on mobile as on
    desktop — unlike the old mousemove/touchmove-on-the-whole-image version
    this replaced, which gave mobile users no visual hint that the photo
    was draggable at all.
--}}
<img-comparison-slider class="mil-ba-slider mil-br-lg mil-up">
    <img slot="before" src="{{ $before }}" alt="{{ $alt }} before cleaning">
    <img slot="after" src="{{ $after }}" alt="{{ $alt }} after cleaning">
    <div slot="handle" class="mil-ba-handle"><i class="far fa-arrows-alt-h"></i></div>
</img-comparison-slider>
