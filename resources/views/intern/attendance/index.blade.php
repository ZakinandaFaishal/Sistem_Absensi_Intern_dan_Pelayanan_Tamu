@extends('layouts.userLayout')

@section('title', 'Riwayat Presensi - User Panel')
@section('page_title', 'Riwayat Presensi')

@section('content')
@php
    $user = Auth::user();
    $initial = strtoupper(substr($user->name ?? 'U', 0, 1));

    $badge = function (string $tone = 'neutral') {
        return match ($tone) {
            'emerald' => 'bg-emerald-400/10 text-emerald-100 border-emerald-300/15',
            'rose' => 'bg-rose-400/10 text-rose-100 border-rose-300/15',
            default => 'bg-white/10 text-white/85 border-white/15',
        };
    };
@endphp

<div class="max-w-6xl mx-auto space-y-6">

    {{-- HERO --}}
    <section class="relative overflow-hidden rounded-[28px] border border-white/12 bg-white/10 backdrop-blur-xl
                    shadow-[0_18px_60px_rgba(0,0,0,.28)]">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-24 -left-24 h-56 w-56 rounded-full bg-emerald-500/20 blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 h-56 w-56 rounded-full bg-sky-500/20 blur-3xl"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-white/10 via-transparent to-white/5"></div>
        </div>

        <div class="relative p-6 sm:p-8">
            <div class="mb-5 h-1 w-full rounded-full bg-gradient-to-r from-emerald-400/60 via-sky-400/55 to-fuchsia-400/55"></div>

            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-[11px] uppercase tracking-wider text-white/70">User Panel</p>
                    <h2 class="mt-1 text-xl sm:text-2xl font-extrabold text-white">
                        Riwayat Presensi
                    </h2>
                    <p class="mt-1 text-sm text-white/85">
                        Riwayat kehadiran berdasarkan tanggal, lokasi, dan waktu.
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div class="absolute inset-0 rounded-3xl bg-gradient-to-br from-emerald-400/35 to-sky-400/35 blur-lg"></div>
                        <div class="relative h-14 w-14 rounded-3xl border border-white/15 bg-white/10 backdrop-blur
                                    flex items-center justify-center">
                            <span class="text-xl font-extrabold text-white">{{ $initial }}</span>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-white">{{ $user->name }}</p>
                        <p class="text-xs text-white/70">{{ $user->email }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- DATA --}}
    <section class="relative overflow-hidden rounded-[28px] border border-white/12 bg-white/10 backdrop-blur-xl
                    shadow-[0_18px_60px_rgba(0,0,0,.26)]">

        <div class="h-1 w-full bg-gradient-to-r from-emerald-400/55 via-sky-400/50 to-fuchsia-400/50"></div>

        <div class="p-6 sm:p-8">

            {{-- MOBILE --}}
            <div class="space-y-4 sm:hidden">
                @if ($attendances->count())
                    @foreach ($attendances as $a)
                        <div class="rounded-[22px] border border-white/12 bg-white/8 p-5 space-y-4">
                            <div>
                                <p class="text-[11px] uppercase tracking-wider text-white/60">Tanggal</p>
                                <p class="text-sm font-semibold text-white">
                                    {{ optional($a->date)->format('d M Y') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-[11px] uppercase tracking-wider text-white/60">Lokasi</p>
                                <p class="text-sm font-semibold text-white">
                                    {{ $a->location?->name ?? '-' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-[11px] uppercase tracking-wider text-white/60">Koordinat</p>
                                <p class="text-xs font-semibold text-white/85">
                                    {{ ($a->lat && $a->lng) ? "{$a->lat}, {$a->lng}" : '-' }}
                                </p>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                                    <p class="text-[11px] uppercase text-white/60">Check-in</p>
                                    <p class="mt-1 text-sm font-semibold text-emerald-200">
                                        {{ $a->check_in_at?->format('H:i:s') ?? '-' }}
                                    </p>
                                </div>

                                <div class="rounded-xl border border-white/10 bg-white/5 p-3">
                                    <p class="text-[11px] uppercase text-white/60">Check-out</p>
                                    <p class="mt-1 text-sm font-semibold text-rose-200">
                                        {{ $a->check_out_at?->format('H:i:s') ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    {{-- EMPTY MOBILE --}}
                    <div class="rounded-[22px] border border-white/15 bg-white/5 p-6 text-center">
                        <p class="text-sm font-semibold text-white">Belum ada data presensi</p>
                        <p class="mt-1 text-xs text-white/70">
                            Data presensi Anda akan muncul di sini setelah melakukan check-in.
                        </p>
                    </div>
                @endif
            </div>

            {{-- DESKTOP --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/10 text-white/70">
                            <th class="py-3 pr-4">Tanggal</th>
                            <th class="py-3 pr-4">Lokasi</th>
                            <th class="py-3 pr-4">Koordinat</th>
                            <th class="py-3 pr-4">Check-in</th>
                            <th class="py-3">Check-out</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($attendances as $a)
                            <tr class="hover:bg-white/5 transition">
                                <td class="py-3 pr-4 text-white">
                                    {{ optional($a->date)->format('d M Y') }}
                                </td>
                                <td class="py-3 pr-4 text-white/85">
                                    {{ $a->location?->name ?? '-' }}
                                </td>
                                <td class="py-3 pr-4 text-xs text-white/80">
                                    {{ ($a->lat && $a->lng) ? "{$a->lat}, {$a->lng}" : '-' }}
                                </td>
                                <td class="py-3 pr-4">
                                    <span class="inline-flex rounded-lg px-3 py-1 text-xs font-semibold {{ $badge('emerald') }}">
                                        {{ $a->check_in_at?->format('H:i:s') ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <span class="inline-flex rounded-lg px-3 py-1 text-xs font-semibold {{ $badge('rose') }}">
                                        {{ $a->check_out_at?->format('H:i:s') ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            {{-- EMPTY DESKTOP --}}
                            <tr>
                                <td colspan="5" class="py-10 text-center">
                                    <p class="text-sm font-semibold text-white">Belum ada data presensi</p>
                                    <p class="mt-1 text-xs text-white/70">
                                        Silakan lakukan presensi untuk melihat riwayat di halaman ini.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $attendances->links() }}
            </div>
        </div>
    </section>
</div>
@endsection
