{{--
    Decorative four-point star. Position is always caller-specific, so pass it in:
    <x-star-burst style="top: -15%; right: -15%" />
    <x-star-burst class="mil-sm-hidden" style="width: 5vw; top: -20%; left: 24%" />
--}}
<svg width="89" height="89" viewBox="0 0 89 89" fill="none" xmlns="http://www.w3.org/2000/svg"
     aria-hidden="true" focusable="false"
     {{ $attributes->merge(['class' => 'mil-text-star']) }}>
    <rect x="42.1406" y="13.0017" width="3.46874" height="62.7841" fill="#F6D62D" />
    <rect x="13.0078" y="45.2607" width="3.46874" height="62.7841" transform="rotate(-90 13.0078 45.2607)" fill="#F6D62D" />
    <rect x="21.5859" y="22.8096" width="3.46874" height="62.7841" transform="rotate(-45 21.5859 22.8096)" fill="#F6D62D" />
    <rect x="22.8125" y="67.2046" width="3.46874" height="62.7841" transform="rotate(-135 22.8125 67.2046)" fill="#F6D62D" />
</svg>
