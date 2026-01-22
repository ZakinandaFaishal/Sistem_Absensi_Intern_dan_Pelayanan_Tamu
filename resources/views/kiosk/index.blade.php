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
    @php
        $isAuthed = auth()->check();
        $role = auth()->user()->role ?? null;

        if (!$isAuthed) {
            $absensiUrl = route('login');
            $absensiLabel = 'Login untuk Presensi';
            $absensiHint = 'Presensi hanya untuk magang / admin dinas / super admin.';
        } elseif (in_array($role, ['super_admin', 'admin_dinas'], true)) {
            $absensiUrl = route('kiosk.absensi');
            $absensiLabel = 'Tampilkan QR Absensi';
            $absensiHint = 'Buka halaman ini di monitor untuk ditampilkan.';
        } else {
            $absensiUrl = route('attendance.qr');
            $absensiLabel = 'Scan QR Absensi';
            $absensiHint = 'Arahkan kamera HP ke QR di monitor.';
        }

        $btnBase = 'w-72 max-w-full inline-flex items-center justify-center gap-3 rounded-2xl bg-white/15 px-6 py-4 text-base font-semibold text-white
                backdrop-blur-md border border-white/25 shadow-xl
                hover:bg-white/25 hover:-translate-y-0.5 hover:shadow-2xl
                focus:outline-none focus:ring-2 focus:ring-white/50
                transition duration-200';
        $btnTop = 'rounded-xl bg-white/15 px-4 py-2 text-sm font-semibold text-white
                backdrop-blur-md border border-white/25 shadow-lg
                hover:bg-white/25 hover:-translate-y-0.5 transition duration-200';
    @endphp

    <main class="relative min-h-screen w-full overflow-hidden">
        {{-- Background --}}
        <div id="kiosk-bg" class="absolute inset-0">
            <video
                class="h-full w-full object-cover scale-[1.03]"
                autoplay
                muted
                loop
                playsinline
                preload="auto"
            >
                <source src="{{ asset('img/vid_bg_kab.mp4') }}" type="video/mp4">
            </video>

            {{-- overlays --}}
            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/35 to-black/70"></div>
            <div class="absolute inset-0 [background:radial-gradient(ellipse_at_center,rgba(0,0,0,0.08)_0%,rgba(0,0,0,0.55)_70%,rgba(0,0,0,0.8)_100%)]"></div>
        </div>

        {{-- Header --}}
        <header class="relative z-10 flex items-center justify-between px-6 py-5 sm:px-10">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <div class="rounded-2xl bg-white/10 border border-white/20 backdrop-blur-md shadow-lg p-2">
                    <img src="{{ asset('img/logo kab.mgl.png') }}" alt="Logo Kabupaten Magelang"
                        class="h-12 w-12 sm:h-14 sm:w-14 object-contain">
                </div>
            </a>

            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}" class="{{ $btnTop }}">
                        Dashboard
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="{{ $btnTop }}">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="{{ $btnTop }}">
                        Login
                    </a>
                @endauth
            </div>
        </header>

        {{-- Content --}}
        <section class="relative z-10 min-h-[calc(100vh-84px)] px-6 sm:px-10">
            <div
                class="mx-auto flex min-h-[calc(100vh-84px)] max-w-3xl flex-col items-center justify-center text-center">
                <h1 class="font-serif text-3xl sm:text-5xl md:text-6xl text-white drop-shadow tracking-wide">
                    Dinas Komunikasi &amp; Informatika
                </h1>

                <p class="mt-2 text-white/90 text-sm sm:text-base tracking-wide">
                    Kabupaten Magelang
                </p>

                <div class="mt-6 h-[2px] w-24 rounded-full bg-white/40"></div>

                <div class="mt-8 w-full flex flex-col items-center gap-5">
                    <div class="w-full flex flex-col items-center gap-2">
                        <a href="{{ $absensiUrl }}" class="{{ $btnBase }}">
                            <x-icon name="map-pin" class="h-7 w-7 shrink-0" /> {{ $absensiLabel }}
                        </a>
                        <p class="text-xs text-white/70 max-w-md">
                            {{ $absensiHint }}
                        </p>
                    </div>

                    <div class="w-full flex flex-col items-center gap-2">
                        <a href="{{ route('guest.create') }}" class="{{ $btnBase }}">
                            <x-icon name="clipboard-document" class="h-7 w-7 shrink-0" /> Buku Tamu
                        </a>
                        <p class="text-xs text-white/70 max-w-md">
                            Isi buku tamu untuk pencatatan kunjungan.
                        </p>
                    </div>

                    <p class="pt-2 text-xs text-white/60">
                        Portal layanan buku tamu &amp; absensi magang
                    </p>
                </div>
            </div>
        </section>
    </main>
</body>

</html>
