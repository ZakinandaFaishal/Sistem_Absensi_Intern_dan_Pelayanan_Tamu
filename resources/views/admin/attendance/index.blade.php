@extends('layouts.admin')

@section('title', 'Log Presensi - Diskominfo Kab. Magelang')
@section('page_title', 'Log Presensi')

@section('content')

    @php
        // ===== FILTER & SORT STATE =====
        $q = request('q', '');
        $location = request('location', '');
        $range = request('range', ''); // '', 'today', 'week', 'month'
        $from = request('from', '');
        $to = request('to', '');
        $status = request('status', ''); // '', 'checked_in', 'checked_out'
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

        $activeFilter = $q || $location !== '' || $range !== '' || $from || $to || $status !== '';
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

            {{-- Hidden iframe download --}}
            <iframe id="dlAttendanceFrame" class="hidden"></iframe>
        </div>
    </div>

    {{-- CONTENT WRAPPER --}}
    <div class="pt-5">
        <div class="w-full space-y-6">

            {{-- Settings: Aturan Presensi --}}
            <div id="aturan-presensi" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200">
                    <p class="text-sm font-semibold text-slate-900">Aturan Presensi</p>
                    <p class="mt-0.5 text-xs text-slate-500">Geofence radius + pembatasan jam check-in/check-out.</p>
                </div>

                <form method="POST" action="{{ route('admin.attendance.settings') }}" class="p-6 space-y-5">
                    @csrf

                    @if (session('status'))
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-slate-700">Office Latitude</label>
                            <input name="office_lat" value="{{ old('office_lat', $settings['office_lat'] ?? '') }}"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                placeholder="contoh: -7.479xxx" />
                            @error('office_lat')
                                <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-slate-700">Office Longitude</label>
                            <input name="office_lng" value="{{ old('office_lng', $settings['office_lng'] ?? '') }}"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                placeholder="contoh: 110.217xxx" />
                            @error('office_lng')
                                <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-slate-700">Radius (meter)</label>
                            <input name="radius_m" type="number" min="1" max="50"
                                value="{{ old('radius_m', $settings['radius_m'] ?? 50) }}"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                            @error('radius_m')
                                <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-slate-700">Maks Akurasi GPS (meter)</label>
                            <input name="max_accuracy_m" type="number" min="1" max="5000"
                                value="{{ old('max_accuracy_m', $settings['max_accuracy_m'] ?? 100) }}"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                            @error('max_accuracy_m')
                                <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-slate-700">Check-in Mulai</label>
                                <input name="checkin_start" type="time"
                                    value="{{ old('checkin_start', $settings['checkin_start'] ?? '08:00') }}"
                                    class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                                @error('checkin_start')
                                    <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-700">Check-in Sampai</label>
                                <input name="checkin_end" type="time"
                                    value="{{ old('checkin_end', $settings['checkin_end'] ?? '12:00') }}"
                                    class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                                @error('checkin_end')
                                    <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-slate-700">Check-out Mulai</label>
                                <input name="checkout_start" type="time"
                                    value="{{ old('checkout_start', $settings['checkout_start'] ?? '13:00') }}"
                                    class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                                @error('checkout_start')
                                    <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-700">Check-out Sampai</label>
                                <input name="checkout_end" type="time"
                                    value="{{ old('checkout_end', $settings['checkout_end'] ?? '16:30') }}"
                                    class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                                @error('checkout_end')
                                    <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit"
                            class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">
                            Simpan Aturan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Settings: Lokasi / Dinas --}}
            <div id="lokasi-dinas" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200">
                    <p class="text-sm font-semibold text-slate-900">Lokasi / Dinas</p>
                    <p class="mt-0.5 text-xs text-slate-500">Kelola titik koordinat untuk penugasan peserta magang.</p>
                </div>

                <div class="p-6 space-y-5">
                    @if (session('status'))
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.attendance.locations.store') }}"
                        class="grid grid-cols-1 md:grid-cols-12 gap-3">
                        @csrf
                        <div class="md:col-span-3">
                            <label class="text-xs font-semibold text-slate-700">Nama</label>
                            <input name="name" value="{{ old('name') }}"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                placeholder="Contoh: Diskominfo" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs font-semibold text-slate-700">Kode (opsional)</label>
                            <input name="code" value="{{ old('code') }}"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                placeholder="KOMINFO">
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs font-semibold text-slate-700">Latitude</label>
                            <input name="lat" value="{{ old('lat') }}"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                placeholder="-7.59..." required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-xs font-semibold text-slate-700">Longitude</label>
                            <input name="lng" value="{{ old('lng') }}"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                placeholder="110.21..." required>
                        </div>
                        <div class="md:col-span-3">
                            <label class="text-xs font-semibold text-slate-700">Alamat (opsional)</label>
                            <input name="address" value="{{ old('address') }}"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                placeholder="Alamat singkat">
                        </div>
                        <div class="md:col-span-12 flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">
                                Tambah Lokasi
                            </button>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-slate-600 border-b border-slate-200">
                                    <th class="py-3 pr-4 font-semibold whitespace-nowrap">Nama</th>
                                    <th class="py-3 pr-4 font-semibold whitespace-nowrap">Kode</th>
                                    <th class="py-3 pr-4 font-semibold whitespace-nowrap">Lat</th>
                                    <th class="py-3 pr-4 font-semibold whitespace-nowrap">Lng</th>
                                    <th class="py-3 pr-4 font-semibold whitespace-nowrap">Alamat</th>
                                    <th class="py-3 pr-0 font-semibold whitespace-nowrap text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse(($locations ?? []) as $loc)
                                    <tr class="hover:bg-slate-50/70">
                                        <td class="py-3 pr-4">
                                            <input form="locForm{{ $loc->id }}" name="name"
                                                value="{{ $loc->name }}"
                                                class="w-56 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                                        </td>
                                        <td class="py-3 pr-4">
                                            <input form="locForm{{ $loc->id }}" name="code"
                                                value="{{ $loc->code }}"
                                                class="w-32 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                                        </td>
                                        <td class="py-3 pr-4">
                                            <input form="locForm{{ $loc->id }}" name="lat"
                                                value="{{ $loc->lat }}"
                                                class="w-40 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                                        </td>
                                        <td class="py-3 pr-4">
                                            <input form="locForm{{ $loc->id }}" name="lng"
                                                value="{{ $loc->lng }}"
                                                class="w-40 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                                        </td>
                                        <td class="py-3 pr-4">
                                            <input form="locForm{{ $loc->id }}" name="address"
                                                value="{{ $loc->address }}"
                                                class="w-72 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                                        </td>
                                        <td class="py-3 pr-0 text-right whitespace-nowrap">
                                            <form id="locForm{{ $loc->id }}" method="POST"
                                                action="{{ route('admin.attendance.locations.update', $loc) }}"
                                                class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition">
                                                    Simpan
                                                </button>
                                            </form>

                                            <form method="POST"
                                                action="{{ route('admin.attendance.locations.destroy', $loc) }}"
                                                class="inline-block" onsubmit="return confirm('Hapus lokasi ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="rounded-xl bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-700 transition">
                                                    Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 text-center text-slate-500">Belum ada lokasi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Card --}}
            <div id="daftar-presensi" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div
                    class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-6 py-5 border-b border-slate-200">
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
                                    <option value="date"
                                        {{ ($filters['sort'] ?? 'date') === 'date' ? 'selected' : '' }}>Tanggal</option>
                                    <option value="name" {{ ($filters['sort'] ?? '') === 'name' ? 'selected' : '' }}>
                                        Nama</option>
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
                            <div class="text-xs text-slate-500">Filter diterapkan otomatis.</div>
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
                        {{-- nanti di controller: $attendances->appends(request()->query())->links() --}}
                        {{ $attendances->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- EXPORT ATTENDANCE SCRIPT (UI only, route nanti) --}}
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
