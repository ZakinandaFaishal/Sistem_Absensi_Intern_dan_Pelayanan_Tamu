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
@endphp

{{-- SCAN ONLY STYLE --}}
<style>
    .scan-only-shell #attendance-qr-reader {
        color: rgba(255,255,255,.92);
    }

    .scan-only-shell video,
    .scan-only-shell canvas {
        border-radius: 18px !important;
    }

    .scan-only-shell select,
    .scan-only-shell button {
        width: 100% !important;
        border-radius: 14px !important;
    }

    .scan-only-shell select {
        background: rgba(255,255,255,.08) !important;
        border: 1px solid rgba(255,255,255,.18) !important;
        color: rgba(255,255,255,.95) !important;
        padding: 10px 12px !important;
    }

    .scan-only-shell button {
        background: rgba(255,255,255,.92) !important;
        color: rgba(15,23,42,.95) !important;
        font-weight: 800 !important;
        padding: 10px 12px !important;
        transition: .18s ease;
    }

    .scan-only-shell button:hover {
        transform: translateY(-1px);
        background: rgba(255,255,255,.86) !important;
    }

    .scan-only-shell
    #html5-qrcode-anchor-scan-type-change,
    .scan-only-shell
    #html5-qrcode-button-file-selection,
    .scan-only-shell input[type="file"] {
        display: none !important;
    }
</style>

<div class="max-w-4xl mx-auto space-y-6">

    {{-- HERO --}}
    <section
        class="relative overflow-hidden rounded-[28px]
               border border-white/12 bg-white/10 backdrop-blur-xl
               shadow-[0_18px_60px_rgba(0,0,0,.28)]"
    >
        {{-- glow --}}
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-24 -left-24 h-56 w-56 rounded-full bg-sky-500/20 blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 h-56 w-56 rounded-full bg-fuchsia-500/20 blur-3xl"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-white/10 via-transparent to-white/5"></div>
        </div>

        <div class="relative p-6 sm:p-8">
            {{-- accent --}}
            <div class="mb-5 h-1 w-full rounded-full bg-gradient-to-r from-sky-400/60 via-fuchsia-400/50 to-emerald-400/55"></div>

            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-[11px] uppercase tracking-wider text-white/70">Presensi</p>
                    <h2 class="mt-1 text-xl sm:text-2xl font-extrabold tracking-tight text-white">
                        Scan QR Absensi
                    </h2>
                    <p class="mt-1 text-sm text-white/80 max-w-md">
                        Arahkan kamera ke QR Code yang ditampilkan pada layar kiosk untuk melakukan presensi.
                    </p>
                </div>

                <div class="shrink-0">
                    <div class="relative">
                        <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-sky-400/35 to-fuchsia-400/35 blur-lg"></div>
                        <div class="relative h-12 w-12 rounded-3xl border border-white/15 bg-white/10 backdrop-blur
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

    {{-- SCANNER --}}
    <section
        class="relative overflow-hidden rounded-[28px]
               border border-white/12 bg-white/10 backdrop-blur-xl
               shadow-[0_18px_60px_rgba(0,0,0,.26)]"
    >
        <div class="h-1 w-full bg-gradient-to-r from-emerald-400/55 via-sky-400/50 to-fuchsia-400/50"></div>

        <div class="p-6 sm:p-8 space-y-5">

            {{-- FRAME --}}
            <div class="rounded-2xl border border-white/15 bg-black/25 p-3 sm:p-4">
                <div class="rounded-2xl border border-white/10 bg-black/35 p-3 sm:p-4 scan-only-shell">
                    <div id="attendance-qr-reader" class="w-full"></div>
                </div>
            </div>

            {{-- HINT --}}
            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                <p class="text-xs sm:text-sm text-white/75 leading-relaxed">
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
                          border border-white/20
                          hover:bg-white/20 hover:-translate-y-0.5 transition
                          focus:outline-none focus:ring-2 focus:ring-white/30">
                    <x-icon name="chevron-left" class="h-5 w-5" />
                    Kembali
                </a>
            </div>

        </div>
    </section>

</div>
@endsection
