@extends('layouts.userLayout')

@section('title', 'Presensi Magang - User Panel')
@section('page_title', 'Presensi Magang')

@section('content')
    @php
        $backUrl = \Illuminate\Support\Facades\Route::has('attendance.qr')
            ? route('attendance.qr')
            : url('/presensi/scan-qr');
    @endphp

    <div class="max-w-3xl mx-auto space-y-6">

        {{-- Header --}}
        <section class="relative overflow-hidden rounded-3xl border border-white/15 bg-white/10 backdrop-blur-xl shadow-xl">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-white/10 via-transparent to-transparent"></div>

            <div class="relative p-6 sm:p-7">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-white drop-shadow">
                            Presensi Magang
                        </h1>
                        <p class="mt-1 text-sm text-white/70">
                            Lokasi akan diambil dari GPS HP saat tombol presensi ditekan.
                        </p>

                        {{-- Status --}}
                        <div class="mt-4">
                            <span id="geo-status"
                                class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-semibold text-white/80">
                                <span id="geo-dot" class="inline-block h-2 w-2 rounded-full bg-white/40"></span>
                                Mengecek lokasi…
                            </span>
                        </div>
                    </div>

                    <div class="shrink-0 h-12 w-12 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center shadow">
                        <span class="text-xl">📍</span>
                    </div>
                </div>

                <div class="mt-5 h-[2px] w-full rounded-full bg-gradient-to-r from-transparent via-white/25 to-transparent"></div>

                @if ($errors->any())
                    <div class="mt-5 rounded-2xl border border-rose-300/20 bg-rose-400/10 px-4 py-3 text-sm text-rose-100">
                        <span class="font-semibold">Gagal:</span> {{ $errors->first() }}
                    </div>
                @endif
            </div>
        </section>

        {{-- Form --}}
        <section class="relative overflow-hidden rounded-3xl border border-white/15 bg-white/10 backdrop-blur-xl shadow-xl">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-white/10 via-transparent to-transparent"></div>

            <form method="POST" action="{{ route('attendance.scan.store') }}" id="attendance-scan-form"
                class="relative p-6 sm:p-7 space-y-4">
                @csrf

                <input type="hidden" name="k" value="{{ $token }}">
                <input type="hidden" name="lat" id="geo-lat" value="">
                <input type="hidden" name="lng" id="geo-lng" value="">
                <input type="hidden" name="accuracy_m" id="geo-accuracy" value="">

                {{-- Actions --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <button type="submit" name="action" value="in"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl px-4 py-3
                               bg-emerald-500/20 border border-emerald-300/20 text-emerald-50
                               text-sm font-semibold shadow
                               hover:bg-emerald-500/30 transition
                               focus:outline-none focus:ring-2 focus:ring-emerald-200/30
                               active:scale-[0.99]">
                        <x-icon name="check-circle" class="h-5 w-5" />
                        <span>Check-in</span>
                    </button>

                    <button type="submit" name="action" value="out"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl px-4 py-3
                               bg-sky-500/20 border border-sky-300/20 text-sky-50
                               text-sm font-semibold shadow
                               hover:bg-sky-500/30 transition
                               focus:outline-none focus:ring-2 focus:ring-sky-200/30
                               active:scale-[0.99]">
                        <span class="text-base">⏱️</span>
                        <span>Check-out</span>
                    </button>
                </div>

                {{-- Info --}}
                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-xs sm:text-sm text-white/70 leading-relaxed">
                    Tips: Pastikan GPS aktif dan izin lokasi untuk browser diizinkan. Jika lokasi belum tersedia, tunggu
                    beberapa detik lalu coba lagi.
                </div>

                {{-- Links --}}
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <a href="{{ $backUrl }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl
                               bg-white/10 px-4 py-2 text-sm font-semibold text-white
                               border border-white/20 shadow
                               hover:bg-white/20 transition
                               focus:outline-none focus:ring-2 focus:ring-white/30">
                        ← Scan ulang QR
                    </a>

                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl
                               bg-white/5 px-4 py-2 text-sm font-semibold text-white/85
                               border border-white/15
                               hover:bg-white/10 hover:text-white transition
                               focus:outline-none focus:ring-2 focus:ring-white/20">
                        Dashboard
                    </a>
                </div>
            </form>
        </section>

    </div>

    <script>
        (function () {
            const statusEl = document.getElementById('geo-status');
            const latEl = document.getElementById('geo-lat');
            const lngEl = document.getElementById('geo-lng');
            const accEl = document.getElementById('geo-accuracy');
            const form = document.getElementById('attendance-scan-form');

            function classesFor(variant) {
                const base = ['inline-flex','items-center','gap-2','rounded-full','border','px-3','py-1','text-xs','font-semibold'];

                if (variant === 'success') {
                    return base.concat(['border-emerald-300/20','bg-emerald-500/15','text-emerald-50']);
                }
                if (variant === 'error') {
                    return base.concat(['border-rose-300/20','bg-rose-500/15','text-rose-50']);
                }
                // loading/idle
                return base.concat(['border-white/15','bg-white/10','text-white/80']);
            }

            function dotClass(variant) {
                if (variant === 'success') return 'inline-block h-2 w-2 rounded-full bg-emerald-400';
                if (variant === 'error') return 'inline-block h-2 w-2 rounded-full bg-rose-400';
                return 'inline-block h-2 w-2 rounded-full bg-white/40';
            }

            function setStatus(text, variant = 'idle') {
                if (!statusEl) return;

                statusEl.className = classesFor(variant).join(' ');
                statusEl.innerHTML = `<span class="${dotClass(variant)}"></span><span>${text}</span>`;
            }

            function getLocation() {
                if (!('geolocation' in navigator)) {
                    setStatus('Browser tidak mendukung GPS.', 'error');
                    return;
                }

                setStatus('Meminta izin lokasi…', 'loading');

                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        latEl.value = String(pos.coords.latitude);
                        lngEl.value = String(pos.coords.longitude);
                        accEl.value = pos.coords.accuracy != null ? String(Math.round(pos.coords.accuracy)) : '';
                        setStatus('Lokasi terdeteksi', 'success');
                    },
                    (err) => {
                        console.warn(err);
                        setStatus('Gagal mengambil lokasi. Aktifkan GPS & izinkan akses lokasi.', 'error');
                    },
                    { enableHighAccuracy: true, maximumAge: 0, timeout: 10000 }
                );
            }

            // initial fetch
            getLocation();

            // guard submit
            form?.addEventListener('submit', (e) => {
                if (!latEl.value || !lngEl.value) {
                    e.preventDefault();
                    setStatus('Lokasi belum tersedia. Coba lagi…', 'error');
                    getLocation();
                }
            });
        })();
    </script>
@endsection
