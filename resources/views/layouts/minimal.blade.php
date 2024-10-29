<!DOCTYPE html>
<html>
<head>
    <title>{{ isset($title) ? config('app.name') . ' - ' . $title : config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="minimal-body">
@yield('content')
@stack('scripts')
</body>
</html>
