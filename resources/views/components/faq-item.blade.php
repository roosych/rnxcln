@props(['question'])

<div class="mil-mb-15 mil-up">
    <button class="mil-accordion mil-fw-600 mil-fs-20">{{ $question }}<span class="mil-icon mil-sm mil-bg-m-3">+</span></button>
    <div class="mil-panel">
        <p class="mil-lh-160 mil-fs-18">{{ $slot }}</p>
    </div>
</div>
