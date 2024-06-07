<html>
<head>
    <title>{{ ucfirst(config('app.name')) }}</title>

    @vite(['resources/js/app.js'])

    @include('layouts.head')

    @yield('head')
</head>
    <body class="text-center">
        @yield('content')
        <footer class="position-fixed bottom-0 start-0 ps-2" style="color: #AAA">{{ config('app.version') }}</footer>
    </body>
</html>
