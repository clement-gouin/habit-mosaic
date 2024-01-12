<!DOCTYPE html>
<html>
    <head>
        <title>{{ ucfirst(config('app.name')) }}</title>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <script src="https://cdn.jsdelivr.net/npm/jdenticon@3.2.0/dist/jdenticon.min.js" async
                integrity="sha384-yBhgDqxM50qJV5JPdayci8wCfooqvhFYbIKhv0hTtLvfeeyJMJCscRfFNKIxt43M" crossorigin="anonymous">
        </script>

        @vite(['resources/js/app.js'])

        <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon-16x16.png">
        <link rel="manifest" href="/images/site.webmanifest">
        <link rel="shortcut icon" href="/images/favicon.ico">

        <style>
            html,
            body {
                height: 100%;
            }
        </style>

        @yield('head')
    </head>
    <body class="h-100 row" style="margin: auto">
        <x-menu class="d-none d-md-flex col-md-1 col-lg-3 col-xxl-2" style=""/>
        <div id="collapseMenu" class="collapse collapse-horizontal h-100 p-0 position-fixed start-0 z-2">
            <x-menu class="" style=""/>
        </div>
        <div type="button" data-bs-toggle="collapse" data-bs-target="#collapseMenu" aria-expanded="false" aria-controls="collapseMenu"
             class="d-block d-md-none position-fixed start-0 bottom-0 rounded-bottom-0 rounded-start-0 rounded-top-3 rounded-end-3 z-3 p-2 py-1 text-center border-end border-top border-1 bg-white"
             style="width: 4em"
        >
            <i class="fa-solid fa-bars"></i>
        </div>
        <main style="background-color: #f5f5f5;" class="col-12 col-md-11 col-lg-9 col-xxl-10 h-100 overflow-auto p-0 m-0">
            @yield('content')
        </main>
        <footer class="position-fixed bottom-0 end-0 ps-2 w-fit user-select-none" style="color: #AAA">{{ config('app.version') }}</footer>
    </body>
</html>
