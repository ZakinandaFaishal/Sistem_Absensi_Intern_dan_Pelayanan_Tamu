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

    {{-- SCAN ONLY: rapikan UI bawaan scanner + hilangkan upload file --}}
    <style>
        /* scope khusus halaman ini */
        .scan-only-shell #attendance-qr-reader {
            color: rgba(255, 255, 255, .92);
        }

        /* rapikan elemen bawaan yang kadang berupa tabel */
        .scan-only-shell #attendance-qr-reader table,
        .scan-only-shell #attendance-qr-reader tr,
        .scan-only-shell #attendance-qr-reader td {
            border: 0 !important;
        }

        /* dropdown kamera */
        .scan-only-shell #attendance-qr-reader select {
            width: 100% !important;
            border-radius: 14px !important;
            border: 1px solid rgba(255, 255, 255, .16) !important;
            background: rgba(255, 255, 255, .08) !important;
            color: rgba(255, 255, 255, .92) !important;
            padding: 10px 12px !important;
            font-size: 14px !important;
            outline: none !important;
        }

        .scan-only-shell #attendance-qr-reader select:focus {
            box-shadow: 0 0 0 3px rgba(255, 255, 255, .18) !important;
        }

        /* tombol start/stop */
        .scan-only-shell #attendance-qr-reader button {
            width: 100% !important;
            border-radius: 14px !important;
            border: 1px solid rgba(255, 255, 255, .16) !important;
            background: rgba(255, 255, 255, .92) !important;
            color: rgba(15, 23, 42, .95) !important;
            padding: 10px 12px !important;
            font-weight: 800 !important;
            font-size: 14px !important;
            transition: 180ms ease !important;
        }

        .scan-only-shell #attendance-qr-reader button:hover {
            transform: translateY(-1px);
            background: rgba(255, 255, 255, .86) !important;
        }

        .scan-only-shell #attendance-qr-reader button:active {
            transform: translateY(0);
        }

        /* video/canvas rounded */
        .scan-only-shell #attendance-qr-reader video,
        .scan-only-shell #attendance-qr-reader canvas {
            border-radius: 14px !important;
        }

        /* ✅ HILANGKAN MODE UPLOAD FILE (html5-qrcode) */
        .scan-only-shell #attendance-qr-reader #html5-qrcode-anchor-scan-type-change,
        .scan-only-shell #attendance-qr-reader #html5-qrcode-button-file-selection,
        .scan-only-shell #attendance-qr-reader input[type="file"] {
            display: none !important;
        }
    </style>

    <div class="max-w-3xl mx-auto space-y-5 sm:space-y-6">

        {{-- Header --}}
        <section class="relative overflow-hidden rounded-3xl border border-white/15 bg-white/10 shadow-xl">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-white/10 via-transparent to-transparent"></div>

            <div class="relative p-6 sm:p-7">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-white">
                            Scan QR Absensi
                        </h2>
                        <p class="mt-1 text-sm text-white/70">
                            Arahkan kamera HP ke QR yang tampil di layar kiosk.
                        </p>
                    </div>

                    <div class="shrink-0 h-12 w-12 rounded-2xl bg-white/10 border border-white/15 flex items-center justify-center">
                        <x-icon name="camera" class="h-6 w-6" />
                    </div>
                </div>

                <div class="mt-5 h-px w-full bg-white/15"></div>

                @if ($errors->any())
                    <div class="mt-5 rounded-2xl border border-rose-300/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-100">
                        <span class="font-semibold">Gagal:</span> {{ $errors->first() }}
                    </div>
                @endif
            </div>
        </section>

        {{-- Reader --}}
        <section class="relative overflow-hidden rounded-3xl border border-white/15 bg-white/10 shadow-xl">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-white/10 via-transparent to-transparent"></div>

            <div class="relative p-6 sm:p-7 space-y-4 sm:space-y-5">

                {{-- Frame --}}
                <div class="rounded-2xl border border-white/15 bg-black/20 p-3 sm:p-4">
                    <div class="rounded-xl border border-white/10 bg-black/30 p-3 sm:p-4 scan-only-shell">
                        <div id="attendance-qr-reader" class="w-full"></div>
                    </div>
                </div>

                {{-- Hint --}}
                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
                    <p id="attendance-qr-hint" class="text-xs sm:text-sm text-white/70 leading-relaxed">
                        Jika kamera tidak muncul, pastikan izin kamera diaktifkan pada browser dan gunakan HTTPS (atau localhost).
                    </p>
                </div>

                {{-- Actions --}}
                <div class="flex flex-wrap gap-2 pt-1">
                    <a href="{{ $backUrl }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl
                              bg-white/10 px-4 py-2.5 text-sm font-semibold text-white
                              border border-white/20 hover:bg-white/20 transition
                              focus:outline-none focus:ring-2 focus:ring-white/30">
                        <span class="text-base">←</span>
                        <span>Selesai</span>
                    </a>
                </div>

            </div>
        </section>

    </div>
@endsection
