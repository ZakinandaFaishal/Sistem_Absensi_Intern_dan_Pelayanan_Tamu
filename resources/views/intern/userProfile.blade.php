@extends('layouts.userLayout')

@section('title', 'Profil Akun - User Panel')
@section('page_title', 'Profil')

@section('content')
@php
    $user = Auth::user();
    $user->loadMissing(['internshipLocation']);

    $initial = strtoupper(substr($user->name ?? 'U', 0, 1));

    $internStart = $user->internship_start_date
        ? \Carbon\Carbon::parse($user->internship_start_date)->translatedFormat('d F Y')
        : '-';

    $internEnd = $user->internship_end_date
        ? \Carbon\Carbon::parse($user->internship_end_date)->translatedFormat('d F Y')
        : '-';

    $internLocation = $user->internshipLocation?->name ?? '-';
    $internCode = $user->internshipLocation?->code;
    $internLocationLabel = $internCode ? "{$internLocation} ({$internCode})" : $internLocation;

    $role = $user->role ?? '-';
    $roleLabel = strtoupper($role);

    $status = $user->intern_status ?? null;
    $statusLabel = match ($status) {
        'aktif' => 'AKTIF',
        'tamat' => 'TAMAT',
        default => '-',
    };

    // ===== DARK GLASS (single accent: teal/emerald-ish) =====
    $pill = function (string $tone = 'neutral') {
        return match ($tone) {
            'accent'  => 'bg-teal-400/10 border border-teal-300/15 text-white',
            'danger'  => 'bg-rose-400/10 border border-rose-300/15 text-white',
            default   => 'bg-white/7 border border-white/12 text-white',
        };
    };

    $card = 'rounded-[22px] border border-white/10 bg-[#111827]/55 backdrop-blur-xl shadow-[0_18px_55px_rgba(0,0,0,.45)]';
    $panel = 'rounded-[28px] border border-white/10 bg-[#111827]/55 backdrop-blur-xl shadow-[0_22px_70px_rgba(0,0,0,.55)]';

    $iconMuted  = 'text-white/60';
    $iconAccent = 'text-teal-200/90';
    $iconWrap   = 'h-11 w-11 rounded-2xl bg-white/6 border border-white/12 flex items-center justify-center';
@endphp

<div class="max-w-6xl mx-auto space-y-6">

    {{-- HERO (dark glass, 1 accent only) --}}
    <section class="relative overflow-hidden {{ $panel }}">
        {{-- subtle glows (teal + white only) --}}
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-28 -left-28 h-72 w-72 rounded-full bg-teal-400/10 blur-3xl"></div>
            <div class="absolute -bottom-28 -right-28 h-72 w-72 rounded-full bg-white/6 blur-3xl"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-white/8 via-transparent to-white/4"></div>
        </div>

        <div class="relative p-6 sm:p-8">
            {{-- top accent (single tone) --}}
            <div class="mb-5 h-1 w-full rounded-full bg-gradient-to-r from-teal-300/55 via-white/10 to-transparent"></div>

            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    <p class="text-[11px] uppercase tracking-wider text-white/60">User Panel</p>
                    <h2 class="mt-1 text-xl sm:text-2xl font-extrabold tracking-tight text-white">
                        Profil Akun
                    </h2>
                    <p class="mt-1 text-sm text-white/70">
                        Ringkasan data diri dan pengaturan akun.
                    </p>

                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $pill('neutral') }}">
                            <span class="h-1.5 w-1.5 rounded-full bg-white/70"></span>
                            {{ $roleLabel }}
                        </span>

                        @if (($user->role ?? null) === 'intern')
                            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $pill('accent') }}">
                                <span class="h-1.5 w-1.5 rounded-full bg-teal-300/80"></span>
                                {{ $statusLabel }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    {{-- avatar --}}
                    <div class="relative shrink-0">
                        <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-teal-300/16 via-white/6 to-transparent blur-lg"></div>
                        <div class="relative h-14 w-14 sm:h-16 sm:w-16 rounded-3xl border border-white/10 bg-white/7 backdrop-blur flex items-center justify-center">
                            <span class="text-xl sm:text-2xl font-extrabold text-white">{{ $initial }}</span>
                        </div>
                    </div>

                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ $user->name }}</p>
                        <p class="text-xs text-white/70 truncate">{{ $user->email }}</p>
                        <p class="mt-1 text-[11px] text-white/55 truncate">
                            Terakhir login: {{ optional($user->last_login_at)->translatedFormat('d M Y, H:i') ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Alerts --}}
            <div class="mt-6 space-y-3">
                @if (session('status'))
                    <div class="rounded-2xl border border-white/12 bg-white/6 px-4 py-3 text-sm text-white flex items-start gap-2">
                        <x-icon name="check-circle" class="h-5 w-5 mt-0.5 {{ $iconAccent }}" />
                        <div class="leading-snug text-white/90">{{ session('status') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="rounded-2xl border border-rose-300/15 bg-rose-400/10 px-4 py-3 text-sm text-white">
                        <div class="font-semibold text-white">Terjadi kesalahan:</div>
                        <ul class="list-disc pl-5 mt-1 space-y-0.5 text-white/85">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

        </div>
    </section>

    {{-- QUICK CARDS (dark glass) --}}
    <section class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="relative overflow-hidden {{ $card }} p-5">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-teal-300/45 via-white/10 to-transparent"></div>
            <p class="text-[11px] uppercase tracking-wider text-white/55">Username</p>
            <p class="mt-1 text-sm font-semibold text-white break-all">{{ $user->username ?? '-' }}</p>
            <p class="mt-1 text-xs text-white/60">Digunakan untuk login.</p>
        </div>

        <div class="relative overflow-hidden {{ $card }} p-5">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-teal-300/35 via-white/10 to-transparent"></div>
            <p class="text-[11px] uppercase tracking-wider text-white/55">Nomor Telepon</p>
            <p class="mt-1 text-sm font-semibold text-white break-all">{{ $user->phone ?? '-' }}</p>
            <p class="mt-1 text-xs text-white/60">Kontak pengguna.</p>
        </div>

        <div class="relative overflow-hidden {{ $card }} p-5">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-teal-300/30 via-white/10 to-transparent"></div>
            <p class="text-[11px] uppercase tracking-wider text-white/55">NIK</p>
            <p class="mt-1 text-sm font-semibold text-white break-all">{{ $user->nik ?? '-' }}</p>
            <p class="mt-1 text-xs text-white/60">Identitas kependudukan.</p>
        </div>
    </section>

    {{-- DATA DIRI (dark glass) --}}
    <section class="relative overflow-hidden {{ $panel }}">
        <div class="h-1 w-full bg-gradient-to-r from-teal-300/45 via-white/10 to-transparent"></div>

        <div class="p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                <div>
                    <p class="text-[11px] uppercase tracking-wider text-white/60">Ringkasan</p>
                    <h3 class="mt-1 text-base sm:text-lg font-extrabold tracking-tight text-white">
                        Data Diri
                    </h3>
                    <p class="mt-1 text-xs sm:text-sm text-white/70">
                        Identitas dan detail magang yang tersimpan pada akun.
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-2 rounded-2xl px-3 py-2 text-xs font-semibold {{ $pill('accent') }}">
                        <x-icon name="shield-check" class="h-4 w-4 {{ $iconAccent }}" />
                        Data tersinkron
                    </span>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 lg:grid-cols-12 gap-4">

                {{-- Identitas --}}
                <div class="lg:col-span-5 relative overflow-hidden {{ $card }} p-5 sm:p-6">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-teal-300/35 via-white/10 to-transparent"></div>

                    <div class="flex items-center gap-3">
                        <div class="{{ $iconWrap }}">
                            <x-icon name="users" class="h-5 w-5 {{ $iconMuted }}" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-extrabold text-white truncate">Identitas</p>
                            <p class="text-xs text-white/60">Informasi dasar pengguna.</p>
                        </div>
                    </div>

                    <div class="mt-5 space-y-4">
                        <div class="rounded-2xl border border-white/10 bg-white/6 p-4">
                            <p class="text-[11px] uppercase tracking-wider text-white/55">Nama</p>
                            <p class="mt-1 text-sm font-semibold text-white break-words">{{ $user->name ?? '-' }}</p>
                        </div>

                        <div class="rounded-2xl border border-white/10 bg-white/6 p-4">
                            <p class="text-[11px] uppercase tracking-wider text-white/55">Email</p>
                            <p class="mt-1 text-sm font-semibold text-white break-all">{{ $user->email ?? '-' }}</p>
                        </div>

                        <div class="flex flex-wrap gap-2 pt-1">
                            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $pill('neutral') }}">
                                <x-icon name="users" class="h-4 w-4 {{ $iconMuted }}" />
                                Role: {{ $roleLabel }}
                            </span>

                            @if (($user->role ?? null) === 'intern')
                                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-semibold {{ $pill('accent') }}">
                                    <x-icon name="sparkles" class="h-4 w-4 {{ $iconAccent }}" />
                                    Status: {{ $statusLabel }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Magang --}}
                <div class="lg:col-span-7 relative overflow-hidden {{ $card }} p-5 sm:p-6">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-teal-300/30 via-white/10 to-transparent"></div>

                    <div class="flex items-center gap-3">
                        <div class="{{ $iconWrap }}">
                            <x-icon name="map-pin" class="h-5 w-5 {{ $iconMuted }}" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-extrabold text-white truncate">Masa & Lokasi Magang</p>
                            <p class="text-xs text-white/60">Dipakai untuk penentuan area presensi.</p>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-2xl border border-white/10 bg-white/6 p-4">
                            <p class="text-[11px] uppercase tracking-wider text-white/55">Mulai</p>
                            <p class="mt-1 text-sm font-semibold text-white">{{ $internStart }}</p>
                            <p class="mt-1 text-[12px] text-white/60">Tanggal mulai magang.</p>
                        </div>

                        <div class="rounded-2xl border border-white/10 bg-white/6 p-4">
                            <p class="text-[11px] uppercase tracking-wider text-white/55">Selesai</p>
                            <p class="mt-1 text-sm font-semibold text-white">{{ $internEnd }}</p>
                            <p class="mt-1 text-[12px] text-white/60">Tanggal akhir magang.</p>
                        </div>

                        <div class="sm:col-span-2 rounded-2xl border border-white/10 bg-white/6 p-4">
                            <p class="text-[11px] uppercase tracking-wider text-white/55">Lokasi Magang</p>
                            <p class="mt-1 text-sm font-semibold text-white break-words">{{ $internLocationLabel }}</p>
                            <p class="mt-1 text-[12px] text-white/60">Dinas/lokasi tempat bekerja.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="h-px w-full bg-gradient-to-r from-transparent via-white/12 to-transparent"></div>

        {{-- ACTIONS / SETTINGS --}}
        <div class="p-6 sm:p-8 space-y-6">
            <section class="space-y-6">
                @include('profile.partials.update-profile-and-password-form', ['user' => $user])
                @include('profile.partials.delete-user-form')
            </section>
        </div>
    </section>

</div>
@endsection
