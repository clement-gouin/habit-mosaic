<!DOCTYPE html>
<html>
    <head>
        <title>{{ ucfirst(config('app.name')) }}</title>

        <script src="https://cdn.jsdelivr.net/npm/jdenticon@3.2.0/dist/jdenticon.min.js" async
                integrity="sha384-yBhgDqxM50qJV5JPdayci8wCfooqvhFYbIKhv0hTtLvfeeyJMJCscRfFNKIxt43M" crossorigin="anonymous">
        </script>

        @vite(['resources/js/app.js'])

        @include('layouts.head')

        @yield('head')
    </head>
    <body class="h-100 row" style="margin: auto">
        <x-menu class="d-none d-md-flex col-md-1 col-lg-3 col-xxl-2" style="z-index: 1002"/>
        <main style="background-color: #f5f5f5;" class="col-12 col-md-11 col-lg-9 col-xxl-10 h-100 overflow-auto p-0 m-0">
            @yield('content')
        </main>
        <div id="collapseMenu" class="collapse collapse-horizontal h-100 p-0 position-fixed start-0" style="z-index: 1002">
            <x-menu class="" style=""/>
        </div>
        <div  type="button" data-bs-toggle="collapse" data-bs-target="#collapseMenu" aria-expanded="false" aria-controls="collapseMenu"
             class="d-block d-md-none position-fixed start-0 bottom-0 rounded-bottom-0 rounded-start-0 rounded-top-3 rounded-end-3 p-2 py-1 text-center border-end border-top border-1 bg-white"
             style="width: 4em; z-index: 1002;"
        >
            <i class="fa-solid fa-bars"></i>
        </div>
        <footer class="position-fixed bottom-0 end-0 ps-2 w-fit user-select-none" style="color: #AAA">{{ config('app.version') }}</footer>
    </body>
</html>
