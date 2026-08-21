{{--
    Base layout. Everything outside #swupMain is "static content" in the original
    template's terms: swup swaps only the #swupMain block between page loads, so
    the header and the call widget must live outside it.
--}}
<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.head')
</head>

<body>

    <div class="mil-page-wrapper">

        @include('partials.header')
        @include('partials.back-to-top')

        <div id="swupMain" class="transition-fade">
            @yield('content')
            @include('partials.footer')
        </div>

    </div>

    @include('partials.scripts')

</body>

</html>
