@props([
    'count' => 4,
    'class' => '',
])

<ul class="mil-users-inline {{ $class }}">
    @for ($i = 1; $i <= $count; $i++)
        <li><img src="{{ asset("img/users/{$i}.jpg") }}" alt="User"></li>
    @endfor
</ul>
