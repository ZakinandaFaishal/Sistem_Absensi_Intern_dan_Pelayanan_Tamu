@extends('layouts.kiosk_mode')

@section('title', 'Mode Kiosk')

@section('content')
    <main class="relative min-h-screen w-full overflow-hidden">
        {{-- Background --}}
        <div class="absolute inset-0">
            <img src="{{ asset('img/background.png') }}" alt="Kabupaten Magelang"
                class="h-full w-full object-cover scale-[1.03]">
            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/35 to-black/70"></div>
            <div
                class="absolute inset-0 [background:radial-gradient(ellipse_at_center,rgba(0,0,0,0.08)_0%,rgba(0,0,0,0.55)_70%,rgba(0,0,0,0.8)_100%)]">
            </div>
        </div>

        {{-- Header (logo + clock only) --}}
        <header class="relative z-10 flex items-center justify-between px-6 py-5 sm:px-10">
            <div class="flex items-center gap-3">
                <div class="rounded-2xl bg-white/10 border border-white/20 backdrop-blur-md shadow-lg p-2">
                    <img src="{{ asset('img/logo kab.mgl.png') }}" alt="Logo Kabupaten Magelang"
                        class="h-12 w-12 sm:h-14 sm:w-14 object-contain">
                </div>
                <div class="hidden sm:block text-left">
                    <div class="text-white font-semibold">Diskominfo</div>
                    <div class="text-white/70 text-sm">Kabupaten Magelang</div>
                </div>
            </div>

            <div class="text-right">
                <div id="kioskClock" class="text-white font-semibold text-lg sm:text-2xl tabular-nums">--:--:--</div>
                <div id="kioskDate" class="text-white/70 text-xs sm:text-sm">—</div>
            </div>
        </header>

        {{-- Content --}}
        <section class="relative z-10 min-h-[calc(100vh-84px)] px-6 sm:px-10">
            <div class="mx-auto flex min-h-[calc(100vh-84px)] max-w-3xl flex-col items-center justify-center text-center">
                <h1 class="font-serif text-3xl sm:text-5xl md:text-6xl text-white drop-shadow tracking-wide">
                    Selamat Datang
                </h1>

                <p class="mt-2 text-white/90 text-sm sm:text-base tracking-wide">
                    Silakan pilih layanan
                </p>

                <div class="mt-6 h-[2px] w-24 rounded-full bg-white/40"></div>

                @php
                    $btnBase = 'w-72 max-w-full rounded-2xl bg-white/15 px-6 py-5 text-base font-semibold text-white
                                backdrop-blur-md border border-white/25 shadow-xl
                                hover:bg-white/25 hover:-translate-y-0.5 hover:shadow-2xl
                                focus:outline-none focus:ring-2 focus:ring-white/50
                                transition duration-200';
                @endphp

                <div class="mt-10 w-full flex flex-col items-center gap-6">
                    <a href="{{ route('kiosk.absensi') }}" class="{{ $btnBase }}">
                        📌 Absensi Magang
                    </a>

                    <a href="{{ route('guest.create') }}" class="{{ $btnBase }}">
                        📝 Buku Tamu
                    </a>
                </div>
            </div>
        </section>
    </main>

    <script>
        (function() {
            const clockEl = document.getElementById('kioskClock');
            const dateEl = document.getElementById('kioskDate');

            function pad(n) {
                return String(n).padStart(2, '0');
            }

            function tick() {
                const now = new Date();
                const hh = pad(now.getHours());
                const mm = pad(now.getMinutes());
                const ss = pad(now.getSeconds());

                if (clockEl) clockEl.textContent = `${hh}:${mm}:${ss}`;
                if (dateEl) {
                    dateEl.textContent = now.toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                    });
                }
            }

            tick();
            setInterval(tick, 1000);
        })();
    </script>
@endsection
