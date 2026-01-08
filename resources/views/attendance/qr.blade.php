@extends('layouts.userLayout')

@section('title', 'Scan QR Absensi - User Panel')
@section('page_title', 'Scan QR Absensi')

@section('content')
@php
    $backUrl =
        \Illuminate\Support\Facades\Route::has('intern.dashboard')
            ? route('intern.dashboard')
            : (\Illuminate\Support\Facades\Route::has('profile.edit')
                ? route('profile.edit')
                : url('/dashboard'));
@endphp

<div class="max-w-3xl mx-auto space-y-6">

    {{-- Header card --}}
    <section class="relative rounded-3xl border border-white/15 bg-white/10 backdrop-blur-xl shadow-xl p-6 sm:p-7 overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-white/10 via-transparent to-transparent"></div>

        <div class="relative">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-white drop-shadow">
                        Scan QR Absensi
                    </h2>
                    <p class="mt-1 text-sm text-white/70">
                        Arahkan kamera HP ke QR yang tampil di layar kiosk.
                    </p>
                </div>

                <div class="h-12 w-12 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center shadow">
                    <span class="text-xl">📷</span>
                </div>
            </div>

            <div class="mt-5 h-[2px] w-full rounded-full bg-gradient-to-r from-transparent via-white/30 to-transparent"></div>

            @if ($errors->any())
                <div class="mt-5 rounded-2xl border border-rose-300/20 bg-rose-400/10 px-4 py-3 text-sm text-rose-100">
                    ❌ {{ $errors->first() }}
                </div>
            @endif
        </div>
    </section>

    {{-- Reader card --}}
    <section class="relative rounded-3xl border border-white/15 bg-white/10 backdrop-blur-xl shadow-xl overflow-hidden">
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-white/10 via-transparent to-transparent"></div>

        <div class="relative p-6 sm:p-7 space-y-4">

            {{-- QR Reader container --}}
            <div class="rounded-2xl border border-white/15 bg-white/10 p-3 sm:p-4">
                <div id="attendance-qr-reader" class="w-full"></div>
            </div>

            <div id="attendance-qr-hint" class="text-xs sm:text-sm text-white/65">
                Jika kamera tidak muncul, pastikan izin kamera diaktifkan pada browser dan gunakan HTTPS (atau localhost).
            </div>

            <div class="flex flex-wrap gap-2 pt-2">
                <a href="{{ $backUrl }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl
                          bg-white/10 px-4 py-2 text-sm font-semibold text-white
                          border border-white/20 shadow
                          hover:bg-white/20 hover:-translate-y-0.5 transition duration-200
                          focus:outline-none focus:ring-2 focus:ring-white/30">
                    ← Selesai
                </a>
            </div>
        </div>
    </section>

</div>
@endsection
