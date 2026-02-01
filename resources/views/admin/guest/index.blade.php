@extends('layouts.admin')

@section('title', 'Log Buku Tamu')
@section('page_title', 'Log Buku Tamu')

@section('content')

    @php
        // ===== FILTER STATE =====
        $q = request('q', '');
        $status = request('status', ''); // '', 'pending', 'done'
        $visitType = request('visit_type', ''); // '', 'single', 'group'
        $from = request('from', '');
        $to = request('to', '');

        $activeFilter = $q || $status !== '' || $visitType !== '' || $from || $to;

        $serviceLabel = function (?string $type) {
            return match ($type) {
                'layanan' => 'Layanan',
                'koordinasi' => 'Koordinasi',
                'berkas' => 'Pengantaran Berkas',
                'lainnya' => 'Lainnya',
                default => '—',
            };
        };
    @endphp

    {{-- HEADER PAGE --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Log Buku Tamu</h2>
            <p class="mt-1 text-sm text-slate-600">Rekap kunjungan tamu dan penyelesaian layanan.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            {{-- EXPORT --}}
            <div class="relative">
                <button type="button" id="btnExportGuest"
                    class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white
                           shadow-sm shadow-slate-900/10 hover:bg-slate-800 hover:shadow-md hover:shadow-slate-900/10
                           transition active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-slate-200">
                    <x-icon name="arrow-down-tray" class="h-5 w-5 text-white" />
                    <span>Export Laporan</span>
                    <x-icon id="exportGuestChevron" name="chevron-down" class="h-5 w-5 text-white/90 transition" />
                </button>

                <div id="menuExportGuest"
                    class="hidden absolute right-0 mt-2 w-56 rounded-xl border border-slate-200 bg-white shadow-lg overflow-hidden z-50 ring-1 ring-slate-200/60">
                    <button type="button"
                        class="export-guest-action w-full text-left px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center justify-between transition"
                        data-url="{{ route('admin.guest.export.excel', request()->query()) }}" data-label="Excel">
                        <span class="inline-flex items-center gap-2">
                            <x-icon name="table-cells" class="h-4 w-4 text-slate-600" />
                            <span>Export Excel (Buku Tamu)</span>
                        </span>
                        <span class="text-xs text-slate-400">.xlsx</span>
                    </button>

                    <button type="button"
                        class="export-guest-action w-full text-left px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center justify-between transition"
                        data-url="{{ route('admin.guest.export.pdf', request()->query()) }}" data-label="PDF">
                        <span class="inline-flex items-center gap-2">
                            <x-icon name="document-text" class="h-4 w-4 text-slate-600" />
                            <span>Export PDF (Buku Tamu)</span>
                        </span>
                        <span class="text-xs text-slate-400">.pdf</span>
                    </button>
                </div>
            </div>

            <iframe id="dlGuestFrame" class="hidden"></iframe>

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700
                       shadow-sm shadow-slate-900/5 hover:bg-slate-50 hover:shadow-md hover:shadow-slate-900/5
                       transition active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-slate-200">
                <x-icon name="chevron-left" class="h-5 w-5 text-slate-700" />
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <div class="pt-5 space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm shadow-emerald-900/5">
                <div class="font-semibold inline-flex items-center gap-2">
                    <x-icon name="check-circle" class="h-5 w-5 text-emerald-700" />
                    <span>Berhasil</span>
                </div>
                <div class="mt-0.5 text-emerald-700/90">{{ session('status') }}</div>
            </div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-6 py-5 border-b border-slate-200 bg-gradient-to-b from-white to-slate-50/30">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 ring-1 ring-slate-200/70">
                        <x-icon name="book-open" class="h-5 w-5 text-slate-700" />
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Daftar Kunjungan</p>
                        <p class="text-xs text-slate-500">Menampilkan data terbaru.</p>
                    </div>
                </div>

                <div class="text-xs text-slate-500">
                    Total data:
                    <span class="font-semibold text-slate-700">
                        {{ method_exists($visits, 'total') ? $visits->total() : $visits->count() }}
                    </span>
                </div>
            </div>

            {{-- FILTER BAR --}}
            <div class="px-6 pt-5">
                <form id="guestFilterForm" method="GET" action="{{ route('admin.guest.index') }}"
                    class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                    <input type="hidden" name="page" value="1">

                    <div class="sm:col-span-4">
                        <label class="block text-xs font-semibold text-slate-700">Cari</label>
                        <div class="relative mt-1">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                                <x-icon name="magnifying-glass" class="h-4 w-4" />
                            </span>
                            <input id="guestQInput" type="text" name="q" value="{{ $q }}"
                                placeholder="Nama tamu / keperluan…"
                                class="w-full rounded-xl border border-slate-200 bg-white pl-9 pr-3 py-2 text-sm
                                       shadow-sm shadow-slate-900/5
                                       focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition" />
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700">Status</label>
                        <select id="guestStatusSelect" name="status"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                   shadow-sm shadow-slate-900/5
                                   focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition">
                            <option value="" @selected($status === '')>Semua</option>
                            <option value="pending" @selected($status === 'pending')>Menunggu</option>
                            <option value="done" @selected($status === 'done')>Selesai</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700">Jenis Kunjungan</label>
                        <select id="guestVisitTypeSelect" name="visit_type"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                   shadow-sm shadow-slate-900/5
                                   focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition">
                            <option value="" @selected($visitType === '')>Semua</option>
                            <option value="single" @selected($visitType === 'single')>Sendiri</option>
                            <option value="group" @selected($visitType === 'group')>Kelompok</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700">Dari</label>
                        <input id="guestFromInput" type="date" name="from" value="{{ $from }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                   shadow-sm shadow-slate-900/5
                                   focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition" />
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700">Sampai</label>
                        <input id="guestToInput" type="date" name="to" value="{{ $to }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                   shadow-sm shadow-slate-900/5
                                   focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition" />
                    </div>

                    <div class="sm:col-span-8 flex flex-wrap items-end justify-between gap-2">
                        <div class="text-xs text-slate-500">
                            @if ($activeFilter)
                                <span class="font-semibold text-slate-700 inline-flex items-center gap-1">
                                    <x-icon name="funnel" class="h-4 w-4 text-slate-500" />
                                    <span>Filter aktif</span>
                                </span>
                                <span class="text-slate-400">•</span>
                                <a href="{{ url()->current() }}"
                                    class="font-semibold text-blue-700 hover:text-blue-800 underline underline-offset-2 inline-flex items-center gap-1">
                                    <x-icon name="arrow-path" class="h-4 w-4" />
                                    <span>Reset</span>
                                </a>
                            @else
                                Menampilkan data terbaru.
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <script>
                (function() {
                    const form = document.getElementById('guestFilterForm');
                    if (!form) return;

                    const qInput = document.getElementById('guestQInput');
                    const statusSelect = document.getElementById('guestStatusSelect');
                    const visitTypeSelect = document.getElementById('guestVisitTypeSelect');
                    const fromInput = document.getElementById('guestFromInput');
                    const toInput = document.getElementById('guestToInput');

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
                        qInput.addEventListener('compositionstart', () => { isComposing = true; });
                        qInput.addEventListener('compositionend', () => { isComposing = false; debounceSubmit(); });
                        qInput.addEventListener('input', debounceSubmit);
                    }

                    [statusSelect, visitTypeSelect, fromInput, toInput].forEach((el) => {
                        if (!el) return;
                        el.addEventListener('change', submit);
                    });
                })();
            </script>

            {{-- LIST --}}
            <div class="p-6 space-y-3">
                @forelse($visits as $visit)
                    @php
                        $isGroup = ($visit->visit_type ?? 'single') === 'group';
                        $groupNames = is_array($visit->group_names ?? null)
                            ? $visit->group_names
                            : (array) json_decode($visit->group_names ?? '[]', true);

                        $groupNames = array_values(array_filter($groupNames, fn($x) => trim((string) $x) !== ''));
                        $groupCount = $visit->group_count ?? null;

                        $statusText = $visit->completed_at ? 'Selesai' : 'Menunggu';
                        $statusPill = $visit->completed_at
                            ? 'bg-slate-100 text-slate-700 ring-slate-200'
                            : 'bg-amber-100 text-amber-800 ring-amber-200';

                        $visitPill = $isGroup
                            ? 'bg-indigo-50 text-indigo-700 border-indigo-100 ring-indigo-100'
                            : 'bg-emerald-50 text-emerald-700 border-emerald-100 ring-emerald-100';

                        $servicePill = match ($visit->service_type) {
                            'layanan' => 'bg-sky-50 text-sky-700 border-sky-100 ring-sky-100',
                            'koordinasi' => 'bg-violet-50 text-violet-700 border-violet-100 ring-violet-100',
                            'berkas' => 'bg-amber-50 text-amber-800 border-amber-100 ring-amber-100',
                            default => 'bg-slate-50 text-slate-700 border-slate-100 ring-slate-100',
                        };
                    @endphp

                    <details
                        class="group border border-slate-200 rounded-2xl bg-white shadow-sm shadow-slate-900/5
                               hover:bg-slate-50/60 hover:shadow-md hover:shadow-slate-900/5
                               transition overflow-hidden">
                        <summary class="cursor-pointer list-none">
                            <div class="p-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0 space-y-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <div class="font-semibold text-slate-900 truncate max-w-[22rem] sm:max-w-[28rem]">
                                            {{ $visit->name }}
                                        </div>

                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusPill }}">
                                            {{ $statusText }}
                                        </span>

                                        <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold ring-1 {{ $visitPill }}">
                                            <span class="inline-flex items-center gap-1.5">
                                                <x-icon name="{{ $isGroup ? 'user-group' : 'user' }}" class="h-4 w-4 opacity-80" />
                                                <span>{{ $isGroup ? 'Kelompok' : 'Sendiri' }}</span>
                                            </span>

                                            @if ($isGroup && $groupCount)
                                                <span class="ml-1 opacity-80">• {{ $groupCount }} org</span>
                                            @endif
                                        </span>

                                        <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold ring-1 {{ $servicePill }}">
                                            <span class="inline-flex items-center gap-1.5">
                                                <x-icon name="tag" class="h-4 w-4 opacity-80" />
                                                <span>{{ $serviceLabel($visit->service_type) }}</span>
                                            </span>
                                        </span>
                                    </div>

                                    <div class="text-sm text-slate-600 break-words">
                                        {{ $visit->purpose }}
                                    </div>

                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500">
                                        <div class="inline-flex items-center gap-1.5">
                                            <x-icon name="clock" class="h-4 w-4 text-slate-400" />
                                            <span>Datang:</span>
                                            <span class="font-semibold text-slate-700">
                                                {{ $visit->arrived_at ? $visit->arrived_at->timezone('Asia/Jakarta')->format('d M Y H:i') . ' WIB' : '-' }}
                                            </span>
                                        </div>

                                        @if ($visit->completed_at)
                                            <div class="inline-flex items-center gap-1.5">
                                                <x-icon name="check-circle" class="h-4 w-4 text-slate-400" />
                                                <span>Selesai:</span>
                                                <span class="font-semibold text-slate-700">
                                                    {{ $visit->completed_at ? $visit->completed_at->timezone('Asia/Jakarta')->format('d M Y H:i') . ' WIB' : '-' }}
                                                </span>
                                            </div>
                                        @endif

                                        @if ($isGroup && (count($groupNames) || $groupCount))
                                            <div class="inline-flex items-center gap-1 text-slate-400">
                                                <span class="hidden sm:inline">•</span>
                                                <span class="font-semibold text-slate-500">Detail kelompok</span>
                                                <x-icon name="chevron-down" class="h-4 w-4 transition group-open:rotate-180" />
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                        @if ($visit->institution)
                                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 font-semibold text-slate-700 ring-1 ring-slate-200">
                                                <x-icon name="building-office-2" class="h-4 w-4 text-slate-600" />
                                                <span class="truncate max-w-[18rem]">{{ $visit->institution }}</span>
                                            </span>
                                        @endif
                                        @if ($visit->email)
                                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 font-semibold text-slate-700 ring-1 ring-slate-200">
                                                <x-icon name="envelope" class="h-4 w-4 text-slate-600" />
                                                <span class="truncate max-w-[18rem]">{{ $visit->email }}</span>
                                            </span>
                                        @endif
                                        @if ($visit->phone)
                                            <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 font-semibold text-slate-700 ring-1 ring-slate-200">
                                                <x-icon name="phone" class="h-4 w-4 text-slate-600" />
                                                <span class="truncate max-w-[18rem]">{{ $visit->phone }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    @if ($visit->completed_at)
                                        <span class="inline-flex items-center gap-2 rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 ring-1 ring-slate-200">
                                            <x-icon name="check" class="h-5 w-5 text-slate-700" />
                                            <span>Selesai</span>
                                        </span>
                                    @else
                                        <form method="POST" action="{{ route('admin.guest.complete', $visit) }}">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white
                                                       shadow-sm shadow-slate-900/10 hover:bg-slate-800 hover:shadow-md hover:shadow-slate-900/10
                                                       transition active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-slate-200">
                                                <x-icon name="check" class="h-5 w-5 text-white" />
                                                <span>Tandai Selesai</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </summary>

                        @if ($isGroup)
                            <div class="border-t border-slate-200 bg-white">
                                <div class="p-4 sm:p-5 grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-4">
                                        <div class="flex items-start justify-between gap-2">
                                            <div>
                                                <div class="text-sm font-extrabold text-slate-900">Informasi Kelompok</div>
                                                <div class="text-xs text-slate-600">Data kunjungan berkelompok.</div>
                                            </div>
                                            <span class="inline-flex items-center gap-2 rounded-xl bg-white border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm shadow-slate-900/5">
                                                <x-icon name="user-group" class="h-4 w-4 text-slate-600" />
                                                <span>{{ $groupCount ?? max(1, count($groupNames)) }} orang</span>
                                            </span>
                                        </div>

                                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                            <div class="rounded-xl bg-white border border-slate-200 px-3 py-2 shadow-sm shadow-slate-900/5">
                                                <div class="text-[11px] font-semibold text-slate-500">Perwakilan/Ketua</div>
                                                <div class="font-semibold text-slate-900">{{ $visit->name }}</div>
                                            </div>

                                            <div class="rounded-xl bg-white border border-slate-200 px-3 py-2 shadow-sm shadow-slate-900/5">
                                                <div class="text-[11px] font-semibold text-slate-500">Jenis Kunjungan</div>
                                                <div class="font-semibold text-slate-900">Berkelompok</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm shadow-slate-900/5">
                                        <div class="flex items-center justify-between gap-2">
                                            <div>
                                                <div class="text-sm font-extrabold text-slate-900">Daftar Nama Anggota</div>
                                                <div class="text-xs text-slate-600">
                                                    @if (count($groupNames))
                                                        Total: <span class="font-semibold">{{ count($groupNames) }}</span> nama terisi.
                                                    @else
                                                        Belum ada nama anggota tersimpan.
                                                    @endif
                                                </div>
                                            </div>

                                            <span class="inline-flex items-center gap-2 rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                                                <x-icon name="clipboard-document-list" class="h-4 w-4 text-slate-600" />
                                                <span>Anggota</span>
                                            </span>
                                        </div>

                                        <div class="mt-3">
                                            @if (count($groupNames))
                                                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                    @foreach ($groupNames as $idx => $n)
                                                        <li class="rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-2">
                                                            <div class="text-[11px] text-slate-500 font-semibold">Anggota {{ $idx + 1 }}</div>
                                                            <div class="text-sm font-semibold text-slate-900 break-words">{{ $n }}</div>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-sm text-slate-600">
                                                    Tidak ada data anggota. Pastikan form kelompok menyimpan <span class="font-semibold">group_names</span>.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </details>
                @empty
                    <div class="py-10 text-center text-sm text-slate-600">
                        Belum ada data kunjungan.
                    </div>
                @endforelse

                @if (method_exists($visits, 'links'))
                    <div class="pt-3">
                        {{ $visits->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- EXPORT SCRIPT --}}
    <script>
        (function() {
            const btn = document.getElementById('btnExportGuest');
            const menu = document.getElementById('menuExportGuest');
            const chevron = document.getElementById('exportGuestChevron');
            const dlFrame = document.getElementById('dlGuestFrame');

            function openMenu() {
                if (!menu) return;
                menu.classList.remove('hidden');
                chevron?.classList.add('rotate-180');
            }

            function closeMenu() {
                if (!menu) return;
                menu.classList.add('hidden');
                chevron?.classList.remove('rotate-180');
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
