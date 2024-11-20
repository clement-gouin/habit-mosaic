<!DOCTYPE html>
<html>
<head>
    <title>{{ isset($title) ? config('app.name') . ' - ' . $title : config('app.name') }}</title>

    @include('layouts.head')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased text-gray-800 bg-gray-100 min-h-screen">
@yield('content')
@stack('scripts')
</body>
</html>
