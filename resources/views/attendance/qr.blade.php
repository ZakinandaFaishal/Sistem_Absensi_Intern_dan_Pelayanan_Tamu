@extends('layouts.userLayout')

@section('title', 'Scan QR Absensi - User Panel')
@section('page_title', 'Scan QR Absensi')

@section('content')
@php
    $backUrl = \Illuminate\Support\Facades\Route::has('intern.userProfile')
        ? route('intern.userProfile')
        : (\Illuminate\Support\Facades\Route::has('profile.edit')
            ? route('profile.edit')
            : url('/userProfile'));

    $panel = 'relative overflow-hidden rounded-[28px] border border-white/10 bg-[#111827]/55 backdrop-blur-xl shadow-[0_22px_70px_rgba(0,0,0,.55)]';
@endphp

{{-- SCAN ONLY STYLE (match profil) --}}
<style>
    .scan-only-shell #attendance-qr-reader { color: rgba(255,255,255,.92); }

    .scan-only-shell video,
    .scan-only-shell canvas { border-radius: 18px !important; }

    .scan-only-shell select,
    .scan-only-shell button {
        width: 100% !important;
        border-radius: 14px !important;
    }

    .scan-only-shell select {
        background: rgba(255,255,255,.06) !important;
        border: 1px solid rgba(255,255,255,.12) !important;
        color: rgba(255,255,255,.95) !important;
        padding: 10px 12px !important;
    }

    .scan-only-shell button {
        background: rgba(16,185,129,.22) !important;       /* emerald */
        border: 1px solid rgba(16,185,129,.25) !important;
        color: rgba(236,253,245,.98) !important;
        font-weight: 800 !important;
        padding: 10px 12px !important;
        transition: .18s ease;
    }

    .scan-only-shell button:hover {
        transform: translateY(-1px);
        background: rgba(16,185,129,.28) !important;
    }

    .scan-only-shell #html5-qrcode-anchor-scan-type-change,
    .scan-only-shell #html5-qrcode-button-file-selection,
    .scan-only-shell input[type="file"] { display: none !important; }
</style>

<div class="max-w-4xl mx-auto space-y-6">

    {{-- HERO (same as profil) --}}
    <section class="{{ $panel }}">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-28 -left-28 h-72 w-72 rounded-full bg-emerald-400/10 blur-3xl"></div>
            <div class="absolute -bottom-28 -right-28 h-72 w-72 rounded-full bg-white/6 blur-3xl"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-white/8 via-transparent to-white/4"></div>
        </div>

        <div class="relative p-6 sm:p-8">
            <div class="mb-5 h-1 w-full rounded-full bg-gradient-to-r from-emerald-300/55 via-white/10 to-transparent"></div>

            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-[11px] uppercase tracking-wider text-white/60">Presensi</p>
                    <h2 class="mt-1 text-xl sm:text-2xl font-extrabold tracking-tight text-white">
                        Scan QR Absensi
                    </h2>
                    <p class="mt-1 text-sm text-white/70 max-w-md">
                        Arahkan kamera ke QR Code yang ditampilkan pada layar kiosk untuk melakukan presensi.
                    </p>
                </div>

                <div class="shrink-0">
                    <div class="relative">
                        <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-emerald-300/16 via-white/6 to-transparent blur-lg"></div>
                        <div class="relative h-12 w-12 rounded-3xl border border-white/10 bg-white/7 backdrop-blur
                                    flex items-center justify-center">
                            <x-icon name="camera" class="h-6 w-6 text-white" />
                        </div>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="mt-5 rounded-2xl border border-rose-300/20 bg-rose-400/10 px-4 py-3 text-sm text-rose-100">
                    <span class="font-semibold">Gagal:</span> {{ $errors->first() }}
                </div>
            @endif
        </div>
    </section>

    {{-- SCANNER (same as profil panel) --}}
    <section class="{{ $panel }}">
        <div class="h-1 w-full bg-gradient-to-r from-emerald-300/45 via-white/10 to-transparent"></div>

        <div class="p-6 sm:p-8 space-y-5">
            {{-- FRAME --}}
            <div class="rounded-2xl border border-white/10 bg-black/25 p-3 sm:p-4">
                <div class="rounded-2xl border border-white/10 bg-black/35 p-3 sm:p-4 scan-only-shell">
                    <div id="attendance-qr-reader" class="w-full"></div>
                </div>
            </div>

            {{-- HINT --}}
            <div class="rounded-2xl border border-white/10 bg-white/6 px-4 py-3">
                <p class="text-xs sm:text-sm text-white/70 leading-relaxed">
                    Jika kamera tidak muncul, pastikan izin kamera aktif dan gunakan browser modern
                    (Chrome / Edge / Safari).
                </p>
            </div>

            {{-- ACTION --}}
            <div class="pt-1">
                <a href="{{ $backUrl }}"
                   class="inline-flex items-center gap-2 rounded-xl
                          bg-white/10 px-4 py-2.5
                          text-sm font-semibold text-white
                          border border-white/12
                          hover:bg-white/15 hover:-translate-y-0.5 transition
                          focus:outline-none focus:ring-2 focus:ring-white/20">
                    <x-icon name="chevron-left" class="h-5 w-5" />
                    Kembali
                </a>
            </div>
        </div>
    </section>

</div>
@endsection
