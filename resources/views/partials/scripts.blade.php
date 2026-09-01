<script src="{{ asset('js/plugins/swup.min.js') }}"></script>
<script src="{{ asset('js/plugins/SwupBodyClassPlugin.min.js') }}"></script>
<script src="{{ asset('js/plugins/gsap.js') }}"></script>
<script src="{{ asset('js/plugins/scroll-trigger.js') }}"></script>
<script src="{{ asset('js/plugins/lenis.js') }}"></script>
<script src="{{ asset('js/plugins/cleave.js') }}"></script>
<script src="{{ asset('js/plugins/swiper.min.js') }}"></script>
<script src="{{ asset('js/plugins/fancybox.js') }}"></script>
<script src="{{ asset('js/plugins/flatpickr.min.js') }}"></script>
<script src="{{ asset('js/plugins/img-comparison-slider.js') }}"></script>
<script src="{{ asset('js/main.js') }}?v={{ filemtime(public_path('js/main.js')) }}"></script>
@stack('scripts')
