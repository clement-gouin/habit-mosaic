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
        <x-menu class="col-2 col-md-1 col-lg-3 col-xxl-2">
            <x-menu.item route="dashboard" pattern="dashboard" icon="fa-solid fa-house-chimney">
                Dashboard
            </x-menu.item>
            <x-menu.item route="day" pattern="day" icon="fa-solid fa-calendar-check">
                Day to day
            </x-menu.item>
            <x-menu.item route="configuration" pattern="configuration" icon="fa-solid fa-wrench">
                Configuration
            </x-menu.item>
        </x-menu>
        <main style="background-color: #f5f5f5;" class="col-10 col-md-11 col-lg-9 col-xxl-10 h-100 overflow-scroll p-0 m-0">
            @yield('content')
        </main>
    </body>
</html>
