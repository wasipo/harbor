<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Harbor') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/js/app/app.tsx'])
    @inertiaHead
    
    <style>
    /* Critical CSS for FOUC prevention */
    html, body {
        height: 100%;
        margin: 0;
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
    }
    </style>
</head>
<body class="font-sans antialiased">
    @inertia
</body>
</html>