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

        // badge style helper
        $badge = function (string $tone = 'neutral') {
            return match ($tone) {
                'sky' => 'bg-sky-400/10 text-sky-100 border-sky-300/15',
                'emerald' => 'bg-emerald-400/10 text-emerald-100 border-emerald-300/15',
                'fuchsia' => 'bg-fuchsia-400/10 text-fuchsia-100 border-fuchsia-300/15',
                default => 'bg-white/10 text-white/85 border-white/15',
            };
        };
    @endphp

    <div class="max-w-6xl mx-auto space-y-6">

        {{-- HERO --}}
        <section class="relative overflow-hidden rounded-[28px] border border-white/12 bg-white/10 backdrop-blur-xl shadow-[0_18px_60px_rgba(0,0,0,.28)]">
            {{-- glow layer --}}
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute -top-24 -left-24 h-56 w-56 rounded-full bg-sky-500/20 blur-3xl"></div>
                <div class="absolute -bottom-24 -right-24 h-56 w-56 rounded-full bg-fuchsia-500/20 blur-3xl"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-white/10 via-transparent to-white/5"></div>
            </div>

            <div class="relative p-6 sm:p-8">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">

                    <div class="min-w-0">
                        <p class="text-[11px] uppercase tracking-wider text-white/55">User Panel</p>
                        <h2 class="mt-1 text-xl sm:text-2xl font-extrabold tracking-tight text-white">
                            Profil Akun
                        </h2>
                        <p class="mt-1 text-sm text-white/70">
                            Ringkasan data diri dan pengaturan akun.
                        </p>

                        <div class="mt-4 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold {{ $badge('neutral') }}">
                                <span class="h-1.5 w-1.5 rounded-full bg-white/70"></span>
                                {{ $roleLabel }}
                            </span>

                            @if (($user->role ?? null) === 'intern')
                                <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold {{ ($status === 'tamat') ? $badge('emerald') : $badge('sky') }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ ($status === 'tamat') ? 'bg-emerald-400' : 'bg-sky-400' }}"></span>
                                    {{ $statusLabel }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        {{-- avatar --}}
                        <div class="relative">
                            <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-sky-400/35 via-white/10 to-fuchsia-400/35 blur-lg"></div>
                            <div class="relative h-14 w-14 sm:h-16 sm:w-16 rounded-3xl border border-white/15 bg-white/10 backdrop-blur flex items-center justify-center">
                                <span class="text-xl sm:text-2xl font-extrabold text-white">{{ $initial }}</span>
                            </div>
                        </div>

                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-white truncate">{{ $user->name }}</p>
                            <p class="text-xs text-white/60 truncate">{{ $user->email }}</p>
                            <p class="mt-1 text-[11px] text-white/45 truncate">
                                Terakhir login: {{ optional($user->last_login_at)->translatedFormat('d M Y, H:i') ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- alerts --}}
                <div class="mt-6 space-y-3">
                    @if (session('status'))
                        <div class="rounded-2xl border border-emerald-300/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100 flex items-start gap-2">
                            <x-icon name="check-circle" class="h-5 w-5 mt-0.5" />
                            <div class="leading-snug">{{ session('status') }}</div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="rounded-2xl border border-rose-300/20 bg-rose-400/10 px-4 py-3 text-sm text-rose-100">
                            <div class="font-semibold">Terjadi kesalahan:</div>
                            <ul class="list-disc pl-5 mt-1 space-y-0.5">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- GRID: QUICK STATS (optional nice touch) --}}
        <section class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-[22px] border border-white/12 bg-white/8 backdrop-blur-xl p-5 shadow-[0_14px_40px_rgba(0,0,0,.22)]">
                <p class="text-[11px] uppercase tracking-wider text-white/45">Username</p>
                <p class="mt-1 text-sm font-semibold text-white break-all">{{ $user->username ?? '-' }}</p>
                <p class="mt-1 text-xs text-white/55">Digunakan untuk login.</p>
            </div>

            <div class="rounded-[22px] border border-white/12 bg-white/8 backdrop-blur-xl p-5 shadow-[0_14px_40px_rgba(0,0,0,.22)]">
                <p class="text-[11px] uppercase tracking-wider text-white/45">Nomor Telepon</p>
                <p class="mt-1 text-sm font-semibold text-white break-all">{{ $user->phone ?? '-' }}</p>
                <p class="mt-1 text-xs text-white/55">Kontak pengguna.</p>
            </div>

            <div class="rounded-[22px] border border-white/12 bg-white/8 backdrop-blur-xl p-5 shadow-[0_14px_40px_rgba(0,0,0,.22)]">
                <p class="text-[11px] uppercase tracking-wider text-white/45">NIK</p>
                <p class="mt-1 text-sm font-semibold text-white break-all">{{ $user->nik ?? '-' }}</p>
                <p class="mt-1 text-xs text-white/55">Identitas kependudukan.</p>
            </div>
        </section>

        {{-- DATA Diri --}}
        <section class="rounded-[28px] border border-white/12 bg-white/10 backdrop-blur-xl shadow-[0_18px_60px_rgba(0,0,0,.26)] overflow-hidden">
            <div class="p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                    <div>
                        <p class="text-[11px] uppercase tracking-wider text-white/55">Ringkasan</p>
                        <h3 class="mt-1 text-base sm:text-lg font-extrabold tracking-tight text-white">
                            Data Diri
                        </h3>
                        <p class="mt-1 text-xs sm:text-sm text-white/65">
                            Identitas dan detail magang yang tersimpan pada akun.
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-2 rounded-2xl border border-white/12 bg-white/8 px-3 py-2 text-xs font-semibold text-white/85">
                            <x-icon name="shield-check" class="h-4 w-4 text-white/70" />
                            Data tersinkron
                        </span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 lg:grid-cols-12 gap-4">

                    {{-- Identity card (bigger) --}}
                    <div class="lg:col-span-5 rounded-[22px] border border-white/12 bg-white/8 p-5 sm:p-6">
                        <div class="flex items-center gap-3">
                            <div class="h-11 w-11 rounded-2xl bg-white/10 border border-white/12 flex items-center justify-center">
                                <x-icon name="users" class="h-5 w-5 text-white/80" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-extrabold text-white truncate">Identitas</p>
                                <p class="text-xs text-white/55">Informasi dasar pengguna.</p>
                            </div>
                        </div>

                        <div class="mt-5 space-y-4">
                            <div>
                                <p class="text-[11px] uppercase tracking-wider text-white/45">Nama</p>
                                <p class="mt-1 text-sm font-semibold text-white break-words">{{ $user->name ?? '-' }}</p>
                            </div>

                            <div>
                                <p class="text-[11px] uppercase tracking-wider text-white/45">Email</p>
                                <p class="mt-1 text-sm font-semibold text-white break-all">{{ $user->email ?? '-' }}</p>
                            </div>

                            <div class="flex flex-wrap gap-2 pt-1">
                                <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold {{ $badge('neutral') }}">
                                    <x-icon name="users" class="h-4 w-4 text-white/70" />
                                    Role: {{ $roleLabel }}
                                </span>
                                @if (($user->role ?? null) === 'intern')
                                    <span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold {{ ($status === 'tamat') ? $badge('emerald') : $badge('sky') }}">
                                        <x-icon name="sparkles" class="h-4 w-4 text-white/70" />
                                        Status: {{ $statusLabel }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Internship card (bigger) --}}
                    <div class="lg:col-span-7 rounded-[22px] border border-white/12 bg-white/8 p-5 sm:p-6">
                        <div class="flex items-center gap-3">
                            <div class="h-11 w-11 rounded-2xl bg-white/10 border border-white/12 flex items-center justify-center">
                                <x-icon name="map-pin" class="h-5 w-5 text-white/80" />
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-extrabold text-white truncate">Masa & Lokasi Magang</p>
                                <p class="text-xs text-white/55">Dipakai untuk penentuan area presensi.</p>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <p class="text-[11px] uppercase tracking-wider text-white/45">Mulai</p>
                                <p class="mt-1 text-sm font-semibold text-white">{{ $internStart }}</p>
                                <p class="mt-1 text-[12px] text-white/55">Tanggal mulai magang.</p>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                <p class="text-[11px] uppercase tracking-wider text-white/45">Selesai</p>
                                <p class="mt-1 text-sm font-semibold text-white">{{ $internEnd }}</p>
                                <p class="mt-1 text-[12px] text-white/55">Tanggal akhir magang.</p>
                            </div>

                            <div class="sm:col-span-2 rounded-2xl border border-white/10 bg-white/5 p-4">
                                <p class="text-[11px] uppercase tracking-wider text-white/45">Lokasi Magang</p>
                                <p class="mt-1 text-sm font-semibold text-white break-words">{{ $internLocationLabel }}</p>
                                <p class="mt-1 text-[12px] text-white/55">Dinas/lokasi tempat bekerja.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="h-px w-full bg-gradient-to-r from-transparent via-white/15 to-transparent"></div>

            {{-- ACTIONS / SETTINGS --}}
            <div class="p-6 sm:p-8 space-y-6">
                <section class="space-y-6">
                    @include('profile.partials.update-profile-information-form', ['user' => $user])
                    @include('profile.partials.update-password-form')
                    @include('profile.partials.delete-user-form')
                </section>
            </div>
        </section>

    </div>
@endsection
