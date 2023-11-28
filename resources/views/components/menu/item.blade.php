@props(['route', 'pattern', 'icon'])

<li class="nav-item d-none d-lg-block">
    <a href="{{ isset($route) ? route($route) : '#' }}" class="nav-link fs-5 {{ (isset($pattern) && request()->is($pattern)) ? 'active' : 'link-dark' }}">
        <i class="bi-{{ $icon }} me-1" title="{{ $slot }}"></i> {{ $slot }}
    </a>
</li>
<li class="nav-item d-block d-lg-none">
    <a href="{{ isset($route) ? route($route) : '#' }}" class="nav-link fs-4 rounded-0 py-3 px-0 w-100 d-flex justify-content-center {{ (isset($pattern) && request()->is($pattern)) ? 'active' : 'link-dark' }}">
        <i class="bi-{{ $icon }}" title="{{ $slot }}"></i>
    </a>
</li>
