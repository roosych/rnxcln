{{-- Pagination re-skinned to the theme's .mil-page-pagination markup (see
     public/css/style.css) instead of Bootstrap's default classes. --}}
@if ($paginator->hasPages())
    <ul class="mil-page-pagination">
        @foreach ($elements as $element)
            @if (is_string($element))
                <li><a href="#" style="pointer-events: none;">{{ $element }}</a></li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    <li class="{{ $page == $paginator->currentPage() ? 'mil-current' : '' }}">
                        <a href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endforeach
            @endif
        @endforeach
    </ul>
@endif
