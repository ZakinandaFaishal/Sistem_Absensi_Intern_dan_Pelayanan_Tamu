@extends('layouts.userLayout')

@section('title', 'Riwayat Presensi - User Panel')
@section('page_title', 'Riwayat Presensi')

@section('content')
    @php
        $u = auth()->user();
    @endphp

    <div class="max-w-6xl mx-auto space-y-6">

        {{-- Header card --}}
        <section
            class="relative rounded-3xl border border-white/15 bg-white/10 backdrop-blur-xl shadow-xl p-6 sm:p-7 overflow-hidden">
            <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-white/10 via-transparent to-transparent">
            </div>

            <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="min-w-0">
                    <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-white drop-shadow">
                        Riwayat Presensi
                    </h2>
                    <p class="mt-1 text-sm text-white/70">
                        Rekap check-in/check-out Anda berdasarkan data yang tersimpan.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <div
                        class="h-12 w-12 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center shadow">
                        <span class="text-xl font-extrabold text-white">
                            {{ strtoupper(substr($u->name ?? 'U', 0, 1)) }}
                        </span>
                    </div>
                    <div class="leading-tight">
                        <p class="text-sm font-semibold text-white">{{ $u->name }}</p>
                        <p class="text-xs text-white/60">{{ $u->email }}</p>
                    </div>
                </div>
            </div>

            <div
                class="relative mt-6 h-[2px] w-full rounded-full bg-gradient-to-r from-transparent via-white/30 to-transparent">
            </div>

            <div class="relative mt-4 flex flex-wrap items-center gap-2">
                <a href="{{ route('attendance.qr') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl
                      bg-white/25 px-4 py-2 text-sm font-semibold text-white
                      border border-white/25 shadow
                      hover:bg-white/35 hover:-translate-y-0.5 transition duration-200
                      focus:outline-none focus:ring-2 focus:ring-white/40">
                    <x-icon name="camera" class="h-5 w-5" /> Scan QR Presensi
                </a>

                <!-- <a href="{{ route('intern.userProfile') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-xl
                      bg-white/10 px-4 py-2 text-sm font-semibold text-white
                      border border-white/20 shadow
                      hover:bg-white/20 hover:-translate-y-0.5 transition duration-200
                      focus:outline-none focus:ring-2 focus:ring-white/30">
                        ← Kembali
                    </a> -->
            </div>
        </section>

        {{-- Table card --}}
        <section class="relative rounded-3xl border border-white/15 bg-white/10 backdrop-blur-xl shadow-xl overflow-hidden">
            <div
                class="pointer-events-none absolute inset-0 bg-gradient-to-br from-white/10 via-transparent to-transparent">
            </div>

            <div class="relative px-6 py-5 border-b border-white/10">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div
                            class="h-10 w-10 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center">
                            <x-icon name="map-pin" class="h-5 w-5" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-white">Daftar Presensi</p>
                            <p class="text-xs text-white/60">Menampilkan data terbaru.</p>
                        </div>
                    </div>

                    <div class="text-xs text-white/60">
                        Total halaman:
                        <span class="font-semibold text-white/90">{{ $attendances->lastPage() }}</span>
                        • Total data:
                        <span class="font-semibold text-white/90">{{ $attendances->total() }}</span>
                    </div>
                </div>
            </div>

            <div class="relative p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-white/70 border-b border-white/10">
                                <th class="py-3 pr-4 font-semibold">Tanggal</th>
                                <th class="py-3 pr-4 font-semibold">Lokasi</th>
                                <th class="py-3 pr-4 font-semibold">Koordinat</th>
                                <th class="py-3 pr-4 font-semibold">Check-in</th>
                                <th class="py-3 pr-0 font-semibold">Check-out</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-white/10">
                            @forelse($attendances as $attendance)
                                <tr class="hover:bg-white/5 transition">
                                    <td class="py-3 pr-4 whitespace-nowrap text-white/90">
                                        {{ optional($attendance->date)->format('d M Y') ?? '-' }}
                                    </td>

                                    <td class="py-3 pr-4 whitespace-nowrap text-white/80">
                                        {{ $attendance->location?->name ?? '-' }}
                                    </td>

                                    <td class="py-3 pr-4 whitespace-nowrap">
                                        @if ($attendance->lat !== null && $attendance->lng !== null)
                                            <div class="inline-flex flex-col gap-1">
                                                <span
                                                    class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-white/90 border border-white/15">
                                                    {{ $attendance->lat }}, {{ $attendance->lng }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-white/50">-</span>
                                        @endif
                                    </td>

                                    <td class="py-3 pr-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex rounded-lg bg-emerald-400/15 px-3 py-1 text-xs font-semibold text-emerald-200 border border-emerald-300/20">
                                            {{ $attendance->check_in_at?->format('H:i:s') ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="py-3 pr-0 whitespace-nowrap">
                                        <span
                                            class="inline-flex rounded-lg bg-rose-400/15 px-3 py-1 text-xs font-semibold text-rose-200 border border-rose-300/20">
                                            {{ $attendance->check_out_at?->format('H:i:s') ?? '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-white/70">
                                        Belum ada data presensi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $attendances->links() }}
                </div>
            </div>
        </section>

    </div>
@endsection
