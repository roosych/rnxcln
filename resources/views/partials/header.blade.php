@php
    $logo = config('site.logo');
    $navItems = [
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'About', 'route' => 'about'],
        ['label' => 'Services', 'route' => 'services'],
        ['label' => 'Blog', 'route' => 'blog.index'],
        ['label' => 'Contact', 'route' => 'contact'],
    ];
@endphp

<div class="mil-top-panel">
    <div class="container">
        <div class="mil-tp-content">

            <div class="mil-left">
                {{-- lifted so the wordmark lines up with the menu, see config/site.php --}}
                <a href="{{ route('home') }}" class="mil-logo mil-hover-bri-105"
                   style="width: 20rem; transform: translateY(-{{ $logo['lift']['header'] }})">
                    <img src="{{ logo_url('dark') }}" alt="{{ setting('site.name') }}"
                         width="{{ $logo['width'] }}" height="{{ $logo['height'] }}" style="height: auto">
                    <img src="{{ logo_url('light') }}" alt="{{ setting('site.name') }}" class="mil-light"
                         width="{{ $logo['width'] }}" height="{{ $logo['height'] }}" style="height: auto">
                </a>
            </div>

            <nav class="mil-main-menu">
                <ul id="swupMenu" class="mil-menu-transition mil-aic mil-m-4">
                    @foreach ($navItems as $item)
                        @php
                            // Only "Services" ever has a dropdown, built from the live
                            // catalogue (each one has its own page) rather than a fixed list.
                            $children = $item['route'] === 'services'
                                ? \App\Models\Service::where('is_active', true)->orderBy('sort_order')->get()
                                    ->map(fn ($service) => [
                                        'label' => trim(preg_replace('#\s+#', ' ', preg_replace('#<br[^>]*>#', ' ', $service->title))),
                                        'url' => route('services.show', $service),
                                    ])
                                : collect();
                            $hasChildren = $children->isNotEmpty();
                            $active = request()->routeIs($item['route'])
                                || ($item['route'] === 'services' && request()->routeIs('services.show'))
                                || ($item['route'] === 'blog.index' && request()->routeIs('blog.*'));
                        @endphp
                        <li @class(['mil-has-children' => $hasChildren, 'mil-current' => $active])>
                            {{-- No href when it has a dropdown — it's not a page link, the
                                 submenu (opened on :hover of this <li>) is the navigation.
                                 data-no-swup matters here too: swup's Link.getAddress() turns
                                 an empty pathname into "/", so without this a click here gets
                                 hijacked into a SPA-navigation to the homepage. --}}
                            @if ($hasChildren)
                                <a data-no-swup>{{ $item['label'] }}</a>
                            @else
                                <a href="{{ route($item['route']) }}">{{ $item['label'] }}</a>
                            @endif
                            @if ($hasChildren)
                                <ul>
                                    @foreach ($children as $child)
                                        <li>
                                            <a href="{{ $child['url'] }}">{{ $child['label'] }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="mil-right mil-jce">
                <div class="mil-tp-phone mil-aic mil-mr-60 mil-sm-mr-15">
                    <i class="far fa-mobile mil-icon mil-sm mil-bg-a-2 mil-m-4 mil-mr-15 mil-sm-hidden" style="padding-top: .2rem"></i>
                    <a href="tel:{{ setting('site.phone_e164') }}" class="mil-fw-600" data-no-swup>{{ setting('site.phone') }}</a>
                </div>
                <a href="{{ route('contact') }}" class="mil-btn mil-lg mil-bg-a-1 mil-br-xl mil-hover-bri-105 mil-icon-btn mil-hover-scale mil-md-hidden"><span>Book online</span><i class="far fa-magic mil-bg-m-1 mil-a-1"></i></a>
                <div class="mil-menu-btn">
                    <span></span>
                </div>
            </div>

        </div>
    </div>
</div>
