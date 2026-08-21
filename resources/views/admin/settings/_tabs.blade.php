@php
    $tabs = [
        'company' => ['label' => 'Company & contacts', 'icon' => 'fa-building'],
        'hours' => ['label' => 'Hours', 'icon' => 'fa-clock'],
        'socials' => ['label' => 'Socials', 'icon' => 'fa-share-alt'],
        'home' => ['label' => 'Home page', 'icon' => 'fa-house'],
        'services-page' => ['label' => 'Services page', 'icon' => 'fa-broom'],
        'contact-page' => ['label' => 'Contact page', 'icon' => 'fa-envelope'],
        'about' => ['label' => 'About page', 'icon' => 'fa-info-circle'],
    ];
@endphp

<div class="admin-tabs">
    @foreach ($tabs as $key => $tab)
        <a href="{{ route('admin.settings.index', $key) }}"
           class="admin-btn admin-btn-sm {{ $active === $key ? 'admin-btn-primary' : 'admin-btn-secondary' }}">
            <i class="fas {{ $tab['icon'] }}"></i> {{ $tab['label'] }}
        </a>
    @endforeach
</div>
