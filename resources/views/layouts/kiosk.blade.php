<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title !== '' ? $title . ' - ' : '' }}{{ 'SIMANTA' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased bg-gradient-to-br from-blue-50 to-indigo-100">
    <main class="min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-3xl bg-white shadow-lg rounded-xl p-8 border border-gray-200">
            @if ($title !== '')
                <h1 class="text-2xl font-semibold text-gray-800 mb-6 text-center">{{ $title }}</h1>
            @endif
            {{ $slot }}
        </div>
    </main>
</body>

</html>
