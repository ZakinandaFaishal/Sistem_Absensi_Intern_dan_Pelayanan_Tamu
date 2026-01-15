@extends('layouts.admin')

@section('title', 'Log Buku Tamu - Diskominfo Kab. Magelang')
@section('page_title', 'Log Buku Tamu')

@section('content')

@php
    // ===== FILTER & SORT STATE =====
    $q      = request('q', '');
    $status = request('status', ''); // '', 'pending', 'done'
    $from   = request('from', '');
    $to     = request('to', '');
    $sort   = request('sort', 'arrived_at');
    $dir    = request('dir', 'desc');

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

    $activeFilter = ($q || $status !== '' || $from || $to);
@endphp

    {{-- HEADER PAGE --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Log Buku Tamu</h2>
            <p class="mt-1 text-sm text-slate-600">Rekap kunjungan tamu dan penyelesaian layanan.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">

            {{-- EXPORT LAPORAN (UI saja, route nanti) --}}
            <div class="relative">
                <button type="button" id="btnExportGuest"
                    class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white
                           hover:bg-slate-800 transition active:scale-[0.98]">
                    <span>Export Laporan</span>
                    <span class="inline-block transition" id="exportGuestChevron">▾</span>
                </button>

                <div id="menuExportGuest"
                    class="hidden absolute right-0 mt-2 w-56 rounded-xl border border-slate-200 bg-white shadow-lg overflow-hidden z-50">
                    <button type="button"
                        class="export-guest-action w-full text-left px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center justify-between"
                        data-url="{{ route('admin.guest.export.excel', request()->query()) }}"
                        data-label="Excel">
                        <span>Export Excel (Buku Tamu)</span>
                        <span class="text-xs text-slate-400">.xlsx</span>
                    </button>

                    <button type="button"
                        class="export-guest-action w-full text-left px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center justify-between"
                        data-url="{{ route('admin.guest.export.pdf', request()->query()) }}"
                        data-label="PDF">
                        <span>Export PDF (Buku Tamu)</span>
                        <span class="text-xs text-slate-400">.pdf</span>
                    </button>
                </div>
            </div>

            {{-- Hidden iframe download --}}
            <iframe id="dlGuestFrame" class="hidden"></iframe>

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700
                  hover:bg-slate-50 transition">
                ← Kembali
            </a>
        </div>
    </div>

    <div class="pt-5 space-y-6">

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        {{-- CARD --}}
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-6 py-5 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100">
                        <x-icon name="clipboard-document" class="h-5 w-5 text-slate-700" />
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Daftar Kunjungan</p>
                        <p class="text-xs text-slate-500">Menampilkan data terbaru.</p>
                    </div>
                </div>

                <div class="text-xs text-slate-500">
                    Total data: <span class="font-semibold text-slate-700">{{ $visits->total() }}</span>
                </div>
            </div>

            {{-- FILTER BAR --}}
            <div class="px-6 pt-5">
                <form method="GET" action="{{ route('admin.guest.index') }}"
                      class="grid grid-cols-1 sm:grid-cols-12 gap-3">

                    <div class="sm:col-span-5">
                        <label class="block text-xs font-semibold text-slate-600">Cari</label>
                        <input type="text" name="q" value="{{ $q }}"
                            placeholder="Nama tamu / keperluan…"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-slate-200">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold text-slate-600">Status</label>
                        <select name="status"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-slate-200">
                            <option value="" @selected($status === '')>Semua</option>
                            <option value="pending" @selected($status === 'pending')>Menunggu</option>
                            <option value="done" @selected($status === 'done')>Selesai</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600">Dari</label>
                        <input type="date" name="from" value="{{ $from }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-slate-200">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600">Sampai</label>
                        <input type="date" name="to" value="{{ $to }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-slate-200">
                    </div>

                    <div class="sm:col-span-12 flex flex-wrap items-center justify-between gap-2 pt-1">
                        <div class="text-xs text-slate-500">
                            Sort:
                            <span class="font-semibold text-slate-700">{{ $sort }}</span> ({{ $dir }})
                            @if($activeFilter)
                                <span class="mx-2 opacity-40">|</span> Filter aktif
                            @endif
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

                            <a href="{{ route('admin.guest.index') }}"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700
                                       hover:bg-slate-50 transition">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>

                {{-- SORT CHIPS --}}
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <a href="{{ $sortUrl('arrived_at') }}"
                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Datang <span class="text-slate-400">{{ $sortIcon('arrived_at') }}</span>
                    </a>
                    <a href="{{ $sortUrl('completed_at') }}"
                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Selesai <span class="text-slate-400">{{ $sortIcon('completed_at') }}</span>
                    </a>
                    <a href="{{ $sortUrl('name') }}"
                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Nama <span class="text-slate-400">{{ $sortIcon('name') }}</span>
                    </a>
                </div>
            </div>

            <div class="p-6 space-y-3">
                @forelse($visits as $visit)
                    <div
                        class="border border-slate-200 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 hover:bg-slate-50/60 transition">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <div class="font-semibold text-slate-900 truncate">{{ $visit->name }}</div>

                                @if ($visit->completed_at)
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">
                                        Menunggu
                                    </span>
                                @endif
                            </div>

                            <div class="mt-1 text-sm text-slate-600">
                                {{ $visit->purpose }}
                            </div>

                            <div class="mt-1 text-xs text-slate-500">
                                Datang: <span class="font-semibold text-slate-700">{{ $visit->arrived_at }}</span>
                                @if ($visit->completed_at)
                                    • Selesai: <span class="font-semibold text-slate-700">{{ $visit->completed_at }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if ($visit->completed_at)
                                <span class="inline-flex items-center rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">
                                    ✓ Selesai
                                </span>
                            @else
                                <form method="POST" action="{{ route('admin.guest.complete', $visit) }}">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white
                                           hover:bg-slate-800 transition">
                                        Tandai Selesai
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center text-sm text-slate-600">
                        Belum ada data kunjungan.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- EXPORT GUEST SCRIPT (UI only, route nanti) --}}
    <script>
        (function () {
            const btn = document.getElementById('btnExportGuest');
            const menu = document.getElementById('menuExportGuest');
            const chevron = document.getElementById('exportGuestChevron');
            const dlFrame = document.getElementById('dlGuestFrame');

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

            document.querySelectorAll('.export-guest-action').forEach(el => {
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
