@props(['class', 'style'])

<div style="{{ $style }}" class="{{ $class }} user-select-none flex-column flex-shrink-0 p-0 p-lg-4 bg-light h-100 border-end border-dark-subtle position-relative">
    <div>
        <div class="d-none d-lg-block align-items-center me-md-auto text-decoration-none text-center w-100 text-primary">
            <x-full-logo />
        </div>
        <div class="d-block d-lg-none align-items-center my-2 me-md-auto text-decoration-none text-center w-100 text-primary">
            <x-logo />
        </div>
        <hr class="my-0 my-lg-3">
        <ul class="nav nav-pills nav-flush flex-column mb-auto text-center text-lg-start">
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
    <hr class="mt-0 mt-lg-3">
    <div class="dropdown mx-auto mx-lg-0">
        <a href="#" class="d-flex align-items-center w-fit mx-auto mx-lg-0 link-dark text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
            <svg data-jdenticon-value="{{ auth()->user()->email }}" width="32" height="32" />
            <span class="ms-2 d-none d-lg-inline-block">{{ auth()->user()->name }}</span>
        </a>
        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser">
            <li><a class="dropdown-item" href="/logout"><i class="bi-door-open"></i> Sign out</a></li>
        </ul>
    </div>
</div>
