<html>
<head>
    <title>{{ ucfirst(config('app.name')) }}</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/js/app.js'])

    <link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon-16x16.png">
    <link rel="manifest" href="/images/site.webmanifest">

    <style>
        html,
        body {
            height: 100%;
        }
    </style>

    @yield('head')
</head>
    <body class="text-center">
        @yield('content')
    </body>
</html>
