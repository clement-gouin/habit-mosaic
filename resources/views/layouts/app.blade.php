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
    <body class="antialiased text-gray-800 bg-gray-50 relative" x-data="{open: false}">
        <x-menu style="z-index: 1002" class="hidden md:block"/>
        <main style="background-color: #f5f5f5;min-height: 100vh" class="ml-0 md:ml-20 lg:ml-60 flex-grow" x-on:click="open = false">
            @yield('content')
        </main>
        <div class="h-100 p-0 fixed start-0" style="z-index: 1002" x-show="open">
            <x-menu class="" style=""/>
        </div>
        <div x-on:click="open = true" x-show="!open" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMenu" aria-expanded="false" aria-controls="collapseMenu"
             class="cursor-pointer block md:hidden fixed start-0 bottom-0 rounded-tr-md p-2 py-1 text-center border border-e-gray-500 border-t-gray-500 bg-white"
             style="width: 4em; z-index: 1002;"
        >
            <i class="fa-solid fa-bars"></i>
        </div>
        <footer class="fixed bottom-0 end-1 ps-2 w-fit select-none" style="color: #AAA">{{ config('app.version') }}</footer>
    </body>
</html>
