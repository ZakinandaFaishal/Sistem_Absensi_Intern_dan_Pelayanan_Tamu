<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ 'SIMANTA' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <main class="relative min-h-screen w-full overflow-hidden">

        {{-- Background (FIXED) --}}
        <div id="kiosk-bg" class="fixed inset-0 -z-10 overflow-hidden">
            <video class="absolute inset-0 h-full w-full object-cover object-center" autoplay muted loop playsinline
                preload="auto" poster="{{ asset('img/background.png') }}">
                <source src="{{ asset('img/vid_bg_kab.mp4') }}" type="video/mp4">
            </video>

            {{-- overlays --}}
            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/35 to-black/70"></div>
            <div
                class="absolute inset-0 [background:radial-gradient(ellipse_at_center,rgba(0,0,0,0.08)_0%,rgba(0,0,0,0.55)_70%,rgba(0,0,0,0.8)_100%)]">
            </div>
        </div>


        {{-- Top bar --}}
        <header class="relative z-10 flex items-start justify-between px-6 py-5 sm:px-10">
            <a href="{{ route('kiosk.index') }}" class="flex items-center gap-3">
                <div class="rounded-2xl bg-white/10 border border-white/20 backdrop-blur-md shadow-lg p-2">
                    <img src="{{ asset('img/logo_kab_mgl.png') }}" alt="Logo Kabupaten Magelang"
                        class="h-12 w-12 object-contain">
                </div>

                <div class="hidden sm:block text-left">
                    <div class="text-sm font-semibold text-white drop-shadow leading-tight">SIMANTA</div>
                    <div class="text-[11px] text-white/75 leading-tight">Sistem Informasi Manajemen Magang &amp; Tamu
                    </div>
                    <div class="text-xs text-white/75">Kabupaten Magelang</div>
                </div>
            </a>

            <a href="{{ route('kiosk.index') }}"
                class="rounded-xl bg-white/15 px-5 py-2.5 text-sm font-semibold text-white
                       backdrop-blur-md border border-white/25 shadow-lg
                       hover:bg-white/25 hover:-translate-y-0.5 transition duration-200">
                ← Kembali
            </a>
        </header>

        {{-- Content wrapper --}}
        <div class="relative z-10 min-h-[calc(100vh-88px)] flex flex-col items-center justify-center px-4 py-10">
            <div class="w-full max-w-3xl">

                {{-- Glass card container --}}
                <div class="rounded-3xl border border-white/20 bg-white/10 backdrop-blur-xl shadow-2xl overflow-hidden">
                    <div class="px-6 py-6 sm:px-8 sm:py-8">
                        {{ $slot }}
                    </div>
                </div>

                <p class="mt-6 text-center text-xs text-white/70">
                    © {{ now()->year }} • Kabupaten Magelang
                </p>
            </div>
        </div>

    </main>
</body>

</html>
