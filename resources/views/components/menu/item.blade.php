@props(['route', 'pattern', 'icon'])

<li class="m-auto lg:m-0">
    <a href="{{ isset($route) ? route($route) : '#' }}" class="{{ (isset($pattern) && request()->is($pattern)) ? 'active' : '' }}">
        <i class="{{ $icon }}" title="{{ $slot }}"></i><span class="hidden lg:inline">{{ $slot }}</span>
    </a>
</li>
