@props(['class', 'style'])

<nav style="{{ $style }}" class="{{ $class }} w-22 lg:w-60 bg-white h-full border-r-2 border-gray-200 px-2 py-2 pb-4 lg:pt-8 flex flex-col top-0 left-0 fixed">
    <div class="text-center hidden lg:block text-3xl">
        <x-full-logo />
    </div>
    <div class="text-center block my-2 lg:hidden text-3xl">
        <x-logo />
    </div>
    <hr class="my-6 hidden lg:block">
    <div class="flex-grow text-gray-700">
        <ul class="menu bg-base-0 menu-lg rounded-lg w-full p-0">
            <x-menu.item route="dashboard" pattern="dashboard" icon="fa-solid fa-grip">
                Mosaic
            </x-menu.item>
            <x-menu.item route="day" pattern="day" icon="fa-solid fa-calendar-check">
                Day to day
            </x-menu.item>
            <x-menu.item route="graph" pattern="graph" icon="fa-solid fa-chart-column">
                Graphics
            </x-menu.item>
            <x-menu.item route="table" pattern="table" icon="fa-solid fa-table-cells">
                Table view
            </x-menu.item>
            <x-menu.item route="configuration" pattern="configuration" icon="fa-solid fa-wrench">
                Configuration
            </x-menu.item>
        </ul>
    </div>
    <div class="relative w-full dropdown dropdown-top">
        <div tabindex="0" role="button" type="button" class="btn gap-x-1 lg:gap-x-2 px-0 lg:px-auto flex flex-row w-full bg-white hover:bg-gray-50" id="menu-button" aria-expanded="true" aria-haspopup="true" style="align-items: center">
            <div class="avatar align-middle">
                <div class="w-7 lg:w-8 rounded-full">
                    <svg data-jdenticon-value="{{ auth()->user()->email }}" width="32" height="32" />
                </div>
            </div>
            <span class="hidden lg:block align-middle">{{ auth()->user()->name }}</span>
            <svg class="h-5 w-5 text-gray-400 block align-middle" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
            </svg>
        </div>
        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-[1] p-2 shadow">
            <li class="border-b border-b-gray-200">
                <span class="border-b-1 border-b-gray-200 inline pointer-events-none">
                    <span>{{ auth()->user()->name }}</span><br /><small class="italic">{{ auth()->user()->email }}</small>
                </span>
            </li>
            <li>
                <a href="/logout">Sign out</a>
            </li>
        </ul>
    </div>
</nav>
