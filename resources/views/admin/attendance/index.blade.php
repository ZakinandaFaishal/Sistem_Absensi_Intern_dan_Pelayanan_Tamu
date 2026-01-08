@extends('layouts.admin')

@section('title', 'Log Presensi - Diskominfo Kab. Magelang')
@section('page_title', 'Log Presensi')

@section('content')

    {{-- HEADER PAGE --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Log Presensi</h2>
            <p class="mt-1 text-sm text-slate-600">Rekap check-in/check-out peserta magang.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('attendance.scan.show') }}"
                class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white
                  hover:bg-slate-800 transition">
                📷 Scan Presensi
            </a>

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700
                  hover:bg-slate-50 transition">
                ← Kembali
            </a>
        </div>
    </div>

    {{-- CONTENT WRAPPER --}}
    <div class="pt-5">
        <div class="max-w-7xl space-y-6">

            {{-- Card --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div
                    class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-6 py-5 border-b border-slate-200">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100">📌</span>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Daftar Presensi</p>
                            <p class="text-xs text-slate-500">Menampilkan data terbaru (paginasi).</p>
                        </div>
                    </div>

                    <div class="text-xs text-slate-500">
                        Total halaman: <span class="font-semibold text-slate-700">{{ $attendances->lastPage() }}</span>
                        • Total data: <span class="font-semibold text-slate-700">{{ $attendances->total() }}</span>
                    </div>
                </div>

                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-slate-600 border-b border-slate-200">
                                    <th class="py-3 pr-4 font-semibold whitespace-nowrap">Tanggal</th>
                                    <th class="py-3 pr-4 font-semibold whitespace-nowrap">Nama</th>
                                    <th class="py-3 pr-4 font-semibold whitespace-nowrap">Lokasi</th>
                                    <th class="py-3 pr-4 font-semibold whitespace-nowrap">Koordinat</th>
                                    <th class="py-3 pr-4 font-semibold whitespace-nowrap">Check-in</th>
                                    <th class="py-3 pr-4 font-semibold whitespace-nowrap">Check-out</th>
                                    <th class="py-3 pr-0 font-semibold">Catatan</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100">
                                @forelse($attendances as $attendance)
                                    <tr class="hover:bg-slate-50/70">
                                        <td class="py-3 pr-4 whitespace-nowrap text-slate-700">
                                            {{ optional($attendance->date)->format('d M Y') ?? '-' }}
                                        </td>

                                        <td class="py-3 pr-4 whitespace-nowrap">
                                            <div class="font-semibold text-slate-900">
                                                {{ $attendance->user?->name ?? '-' }}
                                            </div>
                                            <div class="text-xs text-slate-500">
                                                {{ $attendance->user?->email ?? '' }}
                                            </div>
                                        </td>

                                        <td class="py-3 pr-4 whitespace-nowrap text-slate-700">
                                            {{ $attendance->location?->name ?? '-' }}
                                        </td>

                                        <td class="py-3 pr-4 whitespace-nowrap">
                                            @if ($attendance->lat !== null && $attendance->lng !== null)
                                                <div class="inline-flex items-center gap-2">
                                                    <span
                                                        class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                                        {{ $attendance->lat }}, {{ $attendance->lng }}
                                                    </span>

                                                    <a class="text-xs font-semibold text-blue-700 hover:text-blue-800 underline"
                                                        href="https://www.google.com/maps?q={{ $attendance->lat }},{{ $attendance->lng }}"
                                                        target="_blank" rel="noreferrer">
                                                        Maps
                                                    </a>
                                                </div>
                                                <div class="mt-1 text-xs text-slate-500">
                                                    Akurasi: {{ $attendance->accuracy_m ?? '-' }} m
                                                </div>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>

                                        <td class="py-3 pr-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex rounded-lg bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                                {{ $attendance->check_in_at?->format('H:i:s') ?? '-' }}
                                            </span>
                                        </td>

                                        <td class="py-3 pr-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex rounded-lg bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">
                                                {{ $attendance->check_out_at?->format('H:i:s') ?? '-' }}
                                            </span>
                                        </td>

                                        <td class="py-3 pr-0 text-slate-700">
                                            <div class="max-w-[380px] whitespace-normal break-words">
                                                {{ $attendance->notes ?? '-' }}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-10 text-center text-slate-500">
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
            </div>

        </div>
    </div>

@endsection
