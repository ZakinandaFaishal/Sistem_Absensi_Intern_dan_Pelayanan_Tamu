<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-indigo-50">
        <div class="min-h-screen flex flex-col items-center justify-center px-4 py-10">
            <div class="w-full max-w-lg">
                <div class="flex flex-col items-center">
                    <a href="/" class="inline-flex items-center gap-3">
                        <x-application-logo class="w-14 h-14 fill-current text-gray-700" />
                        <div class="text-center sm:text-left">
                            <div class="text-sm font-semibold text-gray-900">{{ config('app.name', 'Laravel') }}</div>
                            <div class="text-xs text-gray-500">Absensi internal & buku tamu</div>
                        </div>
                    </a>
                </div>

                <div
                    class="mt-8 rounded-2xl bg-white/80 backdrop-blur border border-gray-200 shadow-xl shadow-gray-200/40">
                    <div class="px-6 py-6 sm:px-8">
                        {{ $slot }}
                    </div>
                </div>

                <p class="mt-6 text-center text-xs text-gray-500">
                    © {{ now()->year }} {{ config('app.name', 'Laravel') }}
                </p>
            </div>
        </div>
    </div>
</body>

</html>
