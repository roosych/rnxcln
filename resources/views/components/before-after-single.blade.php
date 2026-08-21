@props([
    'before',
    'after',
    'alt' => 'before and after',
])

{{--
    Native theme widget (public/js/main.js initBF(), public/css/style.css
    .mil-before-and-after) — drag anywhere on the image to reveal the after
    photo. JS grabs the first .mil-before-and-after/.mil-subject-scraper on
    the page via querySelector, so only one of these per page.
--}}
<div class="mil-before-and-after mil-up">
    <img class="mil-subject-before" src="{{ $before }}" alt="{{ $alt }} before cleaning">
    <div class="mil-subject-scraper">
        <img class="mil-subject-after" src="{{ $after }}" alt="{{ $alt }} after cleaning">
    </div>
</div>
