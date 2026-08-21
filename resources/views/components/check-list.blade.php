@props([
    'items' => [],
    // 'plain' for the compact card list, 'spaced' for the big "what we clean" columns.
    'variant' => 'plain',
])

<ul @class(['mil-check-list', 'mil-has-last-space' => $variant === 'spaced'])>
    @foreach ($items as $item)
        <li @class(['mil-fs-20 mil-mb-15' => $variant === 'spaced'])>{{ $item }}</li>
    @endforeach
</ul>
