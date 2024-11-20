<!DOCTYPE html>
<html>
    <head>
        <title>{{ ucfirst(config('app.name')) }}</title>

        <script src="https://cdn.jsdelivr.net/npm/jdenticon@3.2.0/dist/jdenticon.min.js" async
                integrity="sha384-yBhgDqxM50qJV5JPdayci8wCfooqvhFYbIKhv0hTtLvfeeyJMJCscRfFNKIxt43M" crossorigin="anonymous">
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @include('layouts.head')

        @yield('head')
    </head>
    <body class="antialiased text-gray-800 min-h-screen drawer md:drawer-open">
        <input id="menu-drawer" type="checkbox" class="drawer-toggle" />
        <main class="drawer-content h-full bg-gray-50 z-0">
            @yield('content')
            <label for="menu-drawer"
                 class="drawer-button cursor-pointer z-40 block md:hidden fixed start-0 bottom-0 rounded-tr-md p-2 py-1 text-center shadow border border-e-gray-50 border-t-gray-50 bg-white"
                 style="width: 4em;"
            >
                <i class="fa-solid fa-bars"></i>
            </label>
        </main>
        <div class="drawer-side">
            <label for="menu-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
            <x-menu />
        </div>
        <footer class="fixed bottom-0 end-1 ps-2 w-fit select-none" style="color: #AAA">{{ config('app.version') }}</footer>
    </body>
</html>
