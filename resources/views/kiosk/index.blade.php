<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dinas Komunikasi & Informatika Kabupaten Magelang</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
<main class="relative min-h-screen w-full overflow-hidden">

    {{-- BACKGROUND --}}
    <div class="absolute inset-0">
        <img
            src="{{ asset('img/background.png') }}"
            class="h-full w-full object-cover scale-[1.03]"
            alt="Kabupaten Magelang"
        >

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/35 to-black/70"></div>
        <div class="absolute inset-0 [background:radial-gradient(ellipse_at_center,rgba(0,0,0,0.08)_0%,rgba(0,0,0,0.55)_70%,rgba(0,0,0,0.8)_100%)]"></div>
    </div>

    {{-- HEADER --}}
    <header class="relative z-10 flex items-start justify-between px-6 py-5 sm:px-10">
        {{-- LOGO --}}
        <div class="flex items-center gap-3">
            <div class="rounded-2xl bg-white/10 border border-white/20 backdrop-blur-md shadow-lg p-2">
                <img
                    src="{{ asset('img/logo kab.mgl.png') }}"
                    alt="Logo Kabupaten Magelang"
                    class="h-14 w-14 object-contain"
                >
            </div>
        </div>

        {{-- LOGIN --}}
        <a
            href="{{ route('login') }}"
            class="rounded-xl bg-white/15 px-5 py-2.5 text-sm font-semibold text-white
                   backdrop-blur-md border border-white/25 shadow-lg
                   hover:bg-white/25 hover:-translate-y-0.5 transition duration-200"
        >
            Login
        </a>
    </header>

    {{-- CONTENT --}}
    <section class="relative z-10 flex min-h-screen flex-col items-center justify-center px-6 text-center">
        <h1 class="font-serif text-3xl sm:text-5xl md:text-6xl text-white drop-shadow tracking-wide">
            Dinas Komunikasi &amp; Informatika
        </h1>

        <p class="mt-2 text-white/90 text-sm sm:text-base tracking-wide">
            Kabupaten Magelang
        </p>

        {{-- Divider --}}
        <div class="mt-5 h-[2px] w-24 rounded-full bg-white/40"></div>

        {{-- BUTTONS --}}
        <div class="mt-10 flex flex-col items-center gap-4">
            {{-- ABSENSI --}}
            <a
                href="{{ route('kiosk.absensi') }}"
                class="w-56 rounded-xl bg-white/15 px-6 py-4 text-base font-semibold text-white
                       backdrop-blur-md border border-white/25 shadow-xl
                       hover:bg-white/25 hover:-translate-y-0.5 hover:shadow-2xl
                       focus:outline-none focus:ring-2 focus:ring-white/50
                       transition duration-200"
            >
                📌 Absensi Magang
            </a>

            {{-- BUKU TAMU --}}
            <a
                href="{{ route('guest.create') }}"
                class="w-56 rounded-xl bg-white/15 px-6 py-4 text-base font-semibold text-white
                       backdrop-blur-md border border-white/25 shadow-xl
                       hover:bg-white/25 hover:-translate-y-0.5 hover:shadow-2xl
                       focus:outline-none focus:ring-2 focus:ring-white/50
                       transition duration-200"
            >
                📝 Buku Tamu
            </a>

            <p class="mt-2 text-xs text-white/70">
                Portal layanan absensi dan buku tamu magang
            </p>
        </div>
    </section>

</main>
</body>
</html>
