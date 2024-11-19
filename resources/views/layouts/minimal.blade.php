<!DOCTYPE html>
<html>
<head>
    <title>{{ isset($title) ? config('app.name') . ' - ' . $title : config('app.name') }}</title>

    @include('layouts.head')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="minimal-body antialiased text-gray-800 bg-gray-50 relative" style="background-color: #f5f5f5;min-height: 100vh">
@yield('content')
@stack('scripts')
</body>
</html>
