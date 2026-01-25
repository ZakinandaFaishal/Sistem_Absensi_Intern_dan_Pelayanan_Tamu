@extends('layouts.admin')

@section('title', 'Log Presensi - Diskominfo Kab. Magelang')
@section('page_title', 'Log Presensi')

@section('content')

    @php
        // ===== FILTER & SORT STATE =====
        $q = request('q', '');
        $dateFrom = request('date_from', '');
        $dateTo = request('date_to', '');
        $sort = request('sort', 'date');
        $dir = request('dir', 'desc');

        $mergeQuery = function (array $extra = []) {
            return url()->current() . '?' . http_build_query(array_merge(request()->query(), $extra));
        };

        $sortUrl = function (string $col) use ($sort, $dir, $mergeQuery) {
            $nextDir = $sort === $col && $dir === 'asc' ? 'desc' : 'asc';
            return $mergeQuery(['sort' => $col, 'dir' => $nextDir, 'page' => 1]);
        };

        $sortIcon = function (string $col) use ($sort, $dir) {
            if ($sort !== $col) {
                return '↕';
            }
            return $dir === 'asc' ? '↑' : '↓';
        };
    @endphp

    {{-- HEADER PAGE --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Log Presensi</h2>
            <p class="mt-1 text-sm text-slate-600">Rekap check-in/check-out peserta magang.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">

            {{-- EXPORT LAPORAN --}}
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
                        data-url="{{ route('admin.attendance.export.excel', request()->query()) }}" data-label="Excel">
                        <span>Export Excel (Presensi)</span>
                        <span class="text-xs text-slate-400">.xlsx</span>
                    </button>

                    <button type="button"
                        class="export-attendance-action w-full text-left px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center justify-between"
                        data-url="{{ route('admin.attendance.export.pdf', request()->query()) }}" data-label="PDF">
                        <span>Export PDF (Presensi)</span>
                        <span class="text-xs text-slate-400">.pdf</span>
                    </button>
                </div>
            </div>

            <iframe id="dlAttendanceFrame" class="hidden"></iframe>
        </div>
    </div>

    <div class="pt-5">
        <div class="w-full space-y-6">

            {{-- Card --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div
                    class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-6 py-5 border-b border-slate-200">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100">
                            <x-icon name="map-pin" class="h-5 w-5 text-slate-700" />
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Daftar Presensi</p>
                            <p class="text-xs text-slate-500">Menampilkan data terbaru.</p>
                        </div>
                    </div>

                    <div class="text-xs text-slate-500">
                        Total halaman: <span class="font-semibold text-slate-700">{{ $attendances->lastPage() }}</span>
                        • Total data: <span class="font-semibold text-slate-700">{{ $attendances->total() }}</span>
                    </div>
                </div>

                {{-- FILTER BAR --}}
                <div class="px-6 pt-5">
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
                    {{-- Filter + Sorting --}}
                    <form id="attendanceFilterForm" method="GET" action="{{ route('admin.attendance.index') }}"
                        class="mb-5 grid grid-cols-1 md:grid-cols-12 gap-3">
                        <input type="hidden" name="page" value="1">
                        <div class="md:col-span-4">
                            <label class="text-xs font-semibold text-slate-700">Cari Nama</label>
                            <input name="q" value="{{ $filters['q'] ?? '' }}"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                placeholder="contoh: Andi" />
                        </div>

                        <div class="md:col-span-3">
                            <label class="text-xs font-semibold text-slate-700">Tanggal Dari</label>
                            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                        </div>

                        <div class="md:col-span-3">
                            <label class="text-xs font-semibold text-slate-700">Tanggal Sampai</label>
                            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                        </div>

                        <div class="md:col-span-2">
                            <label class="text-xs font-semibold text-slate-700">Sort</label>
                            <div class="mt-1 grid grid-cols-2 gap-2">
                                <select name="sort"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                                    <option value="date" {{ ($filters['sort'] ?? 'date') === 'date' ? 'selected' : '' }}>
                                        Tanggal</option>
                                    <option value="name" {{ ($filters['sort'] ?? '') === 'name' ? 'selected' : '' }}>Nama
                                    </option>
                                </select>
                                <select name="dir"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                                    <option value="desc" {{ ($filters['dir'] ?? 'desc') === 'desc' ? 'selected' : '' }}>
                                        Desc</option>
                                    <option value="asc" {{ ($filters['dir'] ?? '') === 'asc' ? 'selected' : '' }}>Asc
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="md:col-span-12 flex items-center justify-end">
                        </div>
                    </form>

                    <script>
                        (function() {
                            const form = document.getElementById('attendanceFilterForm');
                            if (!form) return;

                            const qInput = form.querySelector('input[name="q"]');
                            const dateFrom = form.querySelector('input[name="date_from"]');
                            const dateTo = form.querySelector('input[name="date_to"]');
                            const sortSelect = form.querySelector('select[name="sort"]');
                            const dirSelect = form.querySelector('select[name="dir"]');

                            let timer = null;
                            let isComposing = false;

                            const submit = () => {
                                const pageInput = form.querySelector('input[name="page"]');
                                if (pageInput) pageInput.value = '1';
                                form.submit();
                            };

                            const debounceSubmit = () => {
                                if (isComposing) return;
                                if (timer) window.clearTimeout(timer);
                                timer = window.setTimeout(submit, 900);
                            };

                            if (qInput) {
                                qInput.addEventListener('compositionstart', () => {
                                    isComposing = true;
                                });
                                qInput.addEventListener('compositionend', () => {
                                    isComposing = false;
                                    debounceSubmit();
                                });
                                qInput.addEventListener('input', debounceSubmit);
                            }

                            [dateFrom, dateTo, sortSelect, dirSelect].forEach((el) => {
                                if (!el) return;
                                el.addEventListener('change', submit);
                            });
                        })();
                    </script>

                    @if (session('status'))
                        <div
                            class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-slate-600 border-b border-slate-200">
                                    <th class="py-3 pr-4 font-semibold">Tanggal</th>
                                    <th class="py-3 pr-4 font-semibold">Nama</th>
                                    <th class="py-3 pr-4 font-semibold">Lokasi</th>
                                    <th class="py-3 pr-4 font-semibold">Check-in</th>
                                    <th class="py-3 pr-4 font-semibold">Check-out</th>
                                    <th class="py-3 pr-4 font-semibold">Akurasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($attendances as $a)
                                    <tr class="hover:bg-slate-50/70">
                                        <td class="py-3 pr-4 whitespace-nowrap text-slate-700">
                                            {{ optional($a->date)->format('Y-m-d') ?? '-' }}</td>
                                        <td class="py-3 pr-4 whitespace-nowrap">
                                            <div class="font-semibold text-slate-900">{{ $a->user?->name ?? '-' }}</div>
                                            <div class="text-xs text-slate-500">{{ $a->user?->nik ?? '' }}</div>
                                        </td>
                                        <td class="py-3 pr-4 whitespace-nowrap text-slate-700">
                                            {{ $a->location?->name ?? '-' }}</td>
                                        <td class="py-3 pr-4 whitespace-nowrap text-slate-700">
                                            {{ $a->check_in_at ? $a->check_in_at->format('H:i') : '-' }}</td>
                                        <td class="py-3 pr-4 whitespace-nowrap text-slate-700">
                                            {{ $a->check_out_at ? $a->check_out_at->format('H:i') : '-' }}</td>
                                        <td class="py-3 pr-4 whitespace-nowrap text-slate-700">
                                            {{ $a->accuracy_m !== null ? number_format((float) $a->accuracy_m, 0, ',', '.') . ' m' : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-10 text-center text-slate-600">Belum ada data
                                            presensi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $attendances->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- EXPORT ATTENDANCE SCRIPT --}}
    <script>
        (function() {
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
