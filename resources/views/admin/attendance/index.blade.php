@extends('layouts.admin')

@section('title', 'Log Presensi - Diskominfo Kab. Magelang')
@section('page_title', 'Log Presensi')

@section('content')

@php
    // ===== FILTER & SORT STATE =====
    $q        = request('q', '');
    $location = request('location', '');
    $range    = request('range', ''); // '', 'today', 'week', 'month'
    $from     = request('from', '');
    $to       = request('to', '');
    $status   = request('status', ''); // '', 'checked_in', 'checked_out'
    $sort     = request('sort', 'date');
    $dir      = request('dir', 'desc');

    $mergeQuery = function(array $extra = []) {
        return url()->current() . '?' . http_build_query(array_merge(request()->query(), $extra));
    };

    $sortUrl = function(string $col) use ($sort, $dir, $mergeQuery) {
        $nextDir = ($sort === $col && $dir === 'asc') ? 'desc' : 'asc';
        return $mergeQuery(['sort' => $col, 'dir' => $nextDir, 'page' => 1]);
    };

    $sortIcon = function(string $col) use ($sort, $dir) {
        if ($sort !== $col) return '↕';
        return $dir === 'asc' ? '↑' : '↓';
    };

    $activeFilter = ($q || $location !== '' || $range !== '' || $from || $to || $status !== '');
@endphp

    {{-- HEADER PAGE --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Log Presensi</h2>
            <p class="mt-1 text-sm text-slate-600">Rekap check-in/check-out peserta magang.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">

            {{-- EXPORT LAPORAN (UI saja, route nanti) --}}
            <div class="relative">
                <button type="button" id="btnExportAttendance"
                    class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white
                           hover:bg-slate-800 transition active:scale-[0.98]">
                    <span>Export Laporan</span>
                    <span class="inline-block transition" id="exportAttendanceChevron">▾</span>
                </button>

                <div id="menuExportAttendance"
                    class="hidden absolute right-0 mt-2 w-56 rounded-xl border border-slate-200 bg-white shadow-lg overflow-hidden z-50">
                    <button type="button"
                        class="export-attendance-action w-full text-left px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center justify-between"
                        data-url="{{ route('admin.attendance.export.excel', request()->query()) }}"
                        data-label="Excel">
                        <span>Export Excel (Presensi)</span>
                        <span class="text-xs text-slate-400">.xlsx</span>
                    </button>

                    <button type="button"
                        class="export-attendance-action w-full text-left px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center justify-between"
                        data-url="{{ route('admin.attendance.export.pdf', request()->query()) }}"
                        data-label="PDF">
                        <span>Export PDF (Presensi)</span>
                        <span class="text-xs text-slate-400">.pdf</span>
                    </button>
                </div>
            </div>

            {{-- Hidden iframe download --}}
            <iframe id="dlAttendanceFrame" class="hidden"></iframe>

            <a href="{{ route('attendance.scan.show') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white
                  hover:bg-slate-800 transition">
                <x-icon name="camera" class="h-5 w-5" /> Scan Presensi
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
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-6 py-5 border-b border-slate-200">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100">
                            <x-icon name="map-pin" class="h-5 w-5 text-slate-700" />
                        </span>
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

                {{-- FILTER BAR --}}
                <div class="px-6 pt-5">
                    <form method="GET" action="{{ route('admin.attendance.index') }}"
                          class="grid grid-cols-1 sm:grid-cols-12 gap-3">

                        <div class="sm:col-span-4">
                            <label class="block text-xs font-semibold text-slate-600">Cari</label>
                            <input type="text" name="q" value="{{ $q }}"
                                placeholder="Nama / email…"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-slate-200">
                        </div>

                        <div class="sm:col-span-3">
                            <label class="block text-xs font-semibold text-slate-600">Lokasi</label>
                            <input type="text" name="location" value="{{ $location }}"
                                placeholder="Nama lokasi…"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-slate-200">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-600">Range</label>
                            <select name="range"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-slate-200">
                                <option value="" @selected($range === '')>Custom</option>
                                <option value="today" @selected($range === 'today')>Hari ini</option>
                                <option value="week" @selected($range === 'week')>7 hari</option>
                                <option value="month" @selected($range === 'month')>30 hari</option>
                            </select>
                        </div>

                        <div class="sm:col-span-3 grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600">Dari</label>
                                <input type="date" name="from" value="{{ $from }}"
                                    class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                           focus:outline-none focus:ring-2 focus:ring-slate-200">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600">Sampai</label>
                                <input type="date" name="to" value="{{ $to }}"
                                    class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                           focus:outline-none focus:ring-2 focus:ring-slate-200">
                            </div>
                        </div>

                        <div class="sm:col-span-12 flex flex-wrap items-center justify-between gap-2 pt-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600">
                                    Status:
                                </label>
                                <select name="status"
                                    class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                           focus:outline-none focus:ring-2 focus:ring-slate-200">
                                    <option value="" @selected($status === '')>Semua</option>
                                    <option value="checked_in" @selected($status === 'checked_in')>Sudah check-in (belum checkout)</option>
                                    <option value="checked_out" @selected($status === 'checked_out')>Sudah checkout</option>
                                </select>

                                <span class="text-xs text-slate-500">
                                    Sort: <span class="font-semibold text-slate-700">{{ $sort }}</span> ({{ $dir }})
                                    @if($activeFilter)
                                        <span class="mx-2 opacity-40">|</span> Filter aktif
                                    @endif
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                {{-- keep sort state --}}
                                <input type="hidden" name="sort" value="{{ $sort }}">
                                <input type="hidden" name="dir" value="{{ $dir }}">

                                <button type="submit"
                                    class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white
                                           hover:bg-slate-800 transition">
                                    Terapkan
                                </button>

                                <a href="{{ route('admin.attendance.index') }}"
                                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700
                                           hover:bg-slate-50 transition">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    {{-- SORT CHIPS --}}
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <a href="{{ $sortUrl('date') }}"
                            class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                            Tanggal <span class="text-slate-400">{{ $sortIcon('date') }}</span>
                        </a>
                        <a href="{{ $sortUrl('check_in_at') }}"
                            class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                            Check-in <span class="text-slate-400">{{ $sortIcon('check_in_at') }}</span>
                        </a>
                        <a href="{{ $sortUrl('check_out_at') }}"
                            class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                            Check-out <span class="text-slate-400">{{ $sortIcon('check_out_at') }}</span>
                        </a>
                        <a href="{{ $sortUrl('name') }}"
                            class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                            Nama <span class="text-slate-400">{{ $sortIcon('name') }}</span>
                        </a>
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
                                            <span class="inline-flex rounded-lg bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                                {{ $attendance->check_in_at?->format('H:i:s') ?? '-' }}
                                            </span>
                                        </td>

                                        <td class="py-3 pr-4 whitespace-nowrap">
                                            <span class="inline-flex rounded-lg bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">
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
                        {{-- nanti di controller: $attendances->appends(request()->query())->links() --}}
                        {{ $attendances->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- EXPORT ATTENDANCE SCRIPT (UI only, route nanti) --}}
    <script>
        (function () {
            const btn = document.getElementById('btnExportAttendance');
            const menu = document.getElementById('menuExportAttendance');
            const chevron = document.getElementById('exportAttendanceChevron');
            const dlFrame = document.getElementById('dlAttendanceFrame');

            function openMenu() {
                if (!menu) return;
                menu.classList.remove('hidden');
                if (chevron) chevron.style.transform = 'rotate(180deg)';
            }
            function closeMenu() {
                if (!menu) return;
                menu.classList.add('hidden');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }

            btn?.addEventListener('click', (e) => {
                e.stopPropagation();
                if (!menu) return;
                menu.classList.contains('hidden') ? openMenu() : closeMenu();
            });

            document.addEventListener('click', closeMenu);
            menu?.addEventListener('click', (e) => e.stopPropagation());

            document.querySelectorAll('.export-attendance-action').forEach(el => {
                el.addEventListener('click', () => {
                    const url = el.getAttribute('data-url');
                    const label = el.getAttribute('data-label') || 'Export';

                    const original = el.innerHTML;
                    el.disabled = true;
                    el.innerHTML = `
                        <span class="inline-flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full border-2 border-slate-300 border-t-slate-700 animate-spin"></span>
                            <span>Mengekspor ${label}...</span>
                        </span>
                        <span class="text-xs text-slate-400">harap tunggu</span>
                    `;

                    closeMenu();

                    if (url && dlFrame) dlFrame.src = url;

                    setTimeout(() => {
                        el.disabled = false;
                        el.innerHTML = original;
                    }, 1800);
                });
            });
        })();
    </script>

@endsection
