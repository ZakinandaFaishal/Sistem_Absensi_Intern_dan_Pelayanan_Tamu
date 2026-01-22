@extends('layouts.admin')

@section('title', 'Survey Pelayanan - Diskominfo Kab. Magelang')
@section('page_title', 'Survey Pelayanan')

@section('content')

    @php
        // ===== FILTER & SORT STATE =====
        $q = request('q', '');
        $avgMin = request('avg_min', '');
        $from = request('from', '');
        $to = request('to', '');
        $sort = request('sort', 'submitted_at');
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

        $activeFilter = $q || $avgMin !== '' || $from || $to;
    @endphp

    {{-- HEADER PAGE --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Survey Pelayanan</h2>
            <p class="mt-1 text-sm text-slate-600">Daftar jawaban Q1–Q9 dan komentar dari tamu.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            {{-- EXPORT LAPORAN --}}
            <div class="relative">
                <button type="button" id="btnExportSurvey"
                    class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white
                           hover:bg-slate-800 transition active:scale-[0.98]">
                    <span>Export Laporan</span>
                    <span class="inline-block transition" id="exportSurveyChevron">▾</span>
                </button>

                <div id="menuExportSurvey"
                    class="hidden absolute right-0 mt-2 w-56 rounded-xl border border-slate-200 bg-white shadow-lg overflow-hidden z-50">
                    <button type="button"
                        class="export-survey-action w-full text-left px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center justify-between"
                        data-url="{{ route('admin.survey.export.excel', request()->query()) }}" data-label="Excel">
                        <span>Export Excel (Survey)</span>
                        <span class="text-xs text-slate-400">.xlsx</span>
                    </button>

                    <button type="button"
                        class="export-survey-action w-full text-left px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center justify-between"
                        data-url="{{ route('admin.survey.export.pdf', request()->query()) }}" data-label="PDF">
                        <span>Export PDF (Survey)</span>
                        <span class="text-xs text-slate-400">.pdf</span>
                    </button>
                </div>
            </div>

            <iframe id="dlSurveyFrame" class="hidden"></iframe>

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                ← Kembali
            </a>
        </div>
    </div>

    <div class="pt-5 space-y-6">

        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-6 py-5 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100">
                        <x-icon name="star" class="h-5 w-5 text-slate-700" />
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Daftar Survey</p>
                        <p class="text-xs text-slate-500">Menampilkan data terbaru (paginasi).</p>
                    </div>
                </div>

                <div class="text-xs text-slate-500">
                    Total halaman: <span class="font-semibold text-slate-700">{{ $surveys->lastPage() }}</span>
                    • Total data: <span class="font-semibold text-slate-700">{{ $surveys->total() }}</span>
                </div>
            </div>

            {{-- FILTER BAR --}}
            <div class="px-6 pt-5">
                <form id="surveyFilterForm" method="GET" action="{{ route('admin.survey.index') }}"
                    class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                    <input type="hidden" name="page" value="1">

                    <div class="sm:col-span-5">
                        <label class="block text-xs font-semibold text-slate-600">Cari</label>
                        <input id="surveyQInput" type="text" name="q" value="{{ $q }}"
                            placeholder="Nama tamu / keperluan / komentar…"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-200">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold text-slate-600">Minimal Rata-rata (Q1–Q9)</label>
                        <select id="surveyAvgMinSelect" name="avg_min"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-200">
                            <option value="" @selected($avgMin === '')>Semua</option>
                            <option value="4" @selected($avgMin === '4')>≥ 4.00</option>
                            <option value="3.5" @selected($avgMin === '3.5')>≥ 3.50</option>
                            <option value="3" @selected($avgMin === '3')>≥ 3.00</option>
                            <option value="2.5" @selected($avgMin === '2.5')>≥ 2.50</option>
                            <option value="2" @selected($avgMin === '2')>≥ 2.00</option>
                            <option value="1.5" @selected($avgMin === '1.5')>≥ 1.50</option>
                            <option value="1" @selected($avgMin === '1')>≥ 1.00</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600">Dari</label>
                        <input id="surveyFromInput" type="date" name="from" value="{{ $from }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-200">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600">Sampai</label>
                        <input id="surveyToInput" type="date" name="to" value="{{ $to }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-200">
                    </div>

                    <div class="sm:col-span-12 flex items-center justify-between gap-2">
                        <div class="text-xs text-slate-500">
                            @if ($activeFilter)
                                <span class="font-semibold text-slate-600">Filter aktif</span>
                            @else
                                Menampilkan data terbaru (paginasi).
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <input type="hidden" name="sort" value="{{ $sort }}">
                            <input type="hidden" name="dir" value="{{ $dir }}">

                            <div class="text-xs text-slate-500">Filter diterapkan otomatis.</div>
                        </div>
                    </div>
                </form>

                <script>
                    (function() {
                        const form = document.getElementById('surveyFilterForm');
                        if (!form) return;

                        const qInput = document.getElementById('surveyQInput');
                        const avgMinSelect = document.getElementById('surveyAvgMinSelect');
                        const fromInput = document.getElementById('surveyFromInput');
                        const toInput = document.getElementById('surveyToInput');

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

                        [avgMinSelect, fromInput, toInput].forEach((el) => {
                            if (!el) return;
                            el.addEventListener('change', submit);
                        });
                    })();
                </script>

                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <a href="{{ $sortUrl('submitted_at') }}"
                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Tanggal <span class="text-slate-400">{{ $sortIcon('submitted_at') }}</span>
                    </a>
                    <a href="{{ $sortUrl('avg') }}"
                        class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Rata-rata <span class="text-slate-400">{{ $sortIcon('avg') }}</span>
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
                                <th class="py-3 pr-4 font-semibold whitespace-nowrap">Keperluan</th>
                                <th class="py-3 pr-4 font-semibold whitespace-nowrap">Avg</th>
                                <th class="py-3 pr-4 font-semibold whitespace-nowrap">Komentar</th>
                                <th class="py-3 pr-0 font-semibold whitespace-nowrap">Detail</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($surveys as $s)
                                @php
                                    $avg = null;
                                    if (
                                        $s->q1 !== null &&
                                        $s->q2 !== null &&
                                        $s->q3 !== null &&
                                        $s->q4 !== null &&
                                        $s->q5 !== null &&
                                        $s->q6 !== null &&
                                        $s->q7 !== null &&
                                        $s->q8 !== null &&
                                        $s->q9 !== null
                                    ) {
                                        $avg =
                                            ((int) $s->q1 +
                                                (int) $s->q2 +
                                                (int) $s->q3 +
                                                (int) $s->q4 +
                                                (int) $s->q5 +
                                                (int) $s->q6 +
                                                (int) $s->q7 +
                                                (int) $s->q8 +
                                                (int) $s->q9) /
                                            9.0;
                                    }

                                    $surveyPayload = [
                                        'submitted_at' => $s->submitted_at?->format('Y-m-d H:i'),
                                        'name' => $s->visit?->name,
                                        'email' => $s->visit?->email,
                                        'purpose' => $s->visit?->purpose,
                                        'avg' => $avg !== null ? number_format((float) $avg, 2, ',', '.') : null,
                                        'comment' => $s->comment,
                                        'answers' => [
                                            'Q1' => $s->q1,
                                            'Q2' => $s->q2,
                                            'Q3' => $s->q3,
                                            'Q4' => $s->q4,
                                            'Q5' => $s->q5,
                                            'Q6' => $s->q6,
                                            'Q7' => $s->q7,
                                            'Q8' => $s->q8,
                                            'Q9' => $s->q9,
                                        ],
                                    ];
                                @endphp
                                <tr class="hover:bg-slate-50/70">
                                    <td class="py-3 pr-4 whitespace-nowrap text-slate-700">
                                        {{ $s->submitted_at ? $s->submitted_at->format('Y-m-d H:i') : '-' }}</td>
                                    <td class="py-3 pr-4 whitespace-nowrap">
                                        <div class="font-semibold text-slate-900">{{ $s->visit?->name ?? '-' }}</div>
                                        <div class="text-xs text-slate-500">{{ $s->visit?->email ?? '' }}</div>
                                    </td>
                                    <td class="py-3 pr-4 whitespace-nowrap text-slate-700">
                                        {{ $s->visit?->purpose ?? '-' }}</td>
                                    <td class="py-3 pr-4 whitespace-nowrap text-slate-700 tabular-nums">
                                        {{ $avg !== null ? number_format((float) $avg, 2, ',', '.') : '-' }}
                                    </td>
                                    <td class="py-3 pr-4 text-slate-700">
                                        <div class="max-w-xl break-words">{{ $s->comment ?? '-' }}</div>
                                    </td>
                                    <td class="py-3 pr-0 whitespace-nowrap">
                                        <button type="button"
                                            class="btnSurveyDetail inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition"
                                            data-survey='@json($surveyPayload)'>
                                            Lihat
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-slate-600">Belum ada data survey.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- DETAIL MODAL --}}
                <div id="surveyDetailModal" class="hidden fixed inset-0 z-50">
                    <div class="absolute inset-0 bg-slate-900/40" data-modal-close></div>

                    <div class="absolute inset-0 flex items-center justify-center p-4">
                        <div class="w-full max-w-2xl rounded-2xl border border-slate-200 bg-white shadow-xl overflow-hidden">
                            <div class="flex items-center justify-between gap-3 px-5 py-4 border-b border-slate-200 bg-slate-50">
                                <div class="min-w-0">
                                    <div class="text-sm font-extrabold tracking-tight text-slate-900">Detail Survey</div>
                                    <div class="text-xs text-slate-500" id="surveyDetailMeta">-</div>
                                </div>
                                <button type="button" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50" data-modal-close>
                                    Tutup
                                </button>
                            </div>

                            <div class="p-5 space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="rounded-xl border border-slate-200 px-4 py-3">
                                        <div class="text-xs font-semibold text-slate-500">Nama</div>
                                        <div class="mt-1 text-sm font-semibold text-slate-900" id="surveyDetailName">-</div>
                                        <div class="mt-1 text-xs text-slate-500" id="surveyDetailEmail"></div>
                                    </div>
                                    <div class="rounded-xl border border-slate-200 px-4 py-3">
                                        <div class="text-xs font-semibold text-slate-500">Keperluan</div>
                                        <div class="mt-1 text-sm text-slate-800" id="surveyDetailPurpose">-</div>
                                        <div class="mt-2 text-xs text-slate-500">Avg: <span class="font-semibold text-slate-700" id="surveyDetailAvg">-</span></div>
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-slate-200 overflow-hidden">
                                    <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                                        <div class="text-xs font-semibold text-slate-600">Jawaban (Skala 1–4)</div>
                                    </div>
                                    <div class="p-4 overflow-x-auto">
                                        <table class="min-w-full text-sm">
                                            <thead>
                                                <tr class="text-left text-slate-600 border-b border-slate-200">
                                                    <th class="py-2 pr-4 font-semibold">Pertanyaan</th>
                                                    <th class="py-2 pr-0 font-semibold">Skor</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100" id="surveyDetailAnswers"></tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="rounded-xl border border-slate-200 px-4 py-3">
                                    <div class="text-xs font-semibold text-slate-500">Komentar</div>
                                    <div class="mt-1 text-sm text-slate-800" id="surveyDetailComment">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    {{ $surveys->links() }}
                </div>
            </div>
        </section>
    </div>

    {{-- EXPORT SURVEY SCRIPT --}}
    <script>
        (function() {
            // ===== Detail modal =====
            const modal = document.getElementById('surveyDetailModal');
            const metaEl = document.getElementById('surveyDetailMeta');
            const nameEl = document.getElementById('surveyDetailName');
            const emailEl = document.getElementById('surveyDetailEmail');
            const purposeEl = document.getElementById('surveyDetailPurpose');
            const avgEl = document.getElementById('surveyDetailAvg');
            const commentEl = document.getElementById('surveyDetailComment');
            const answersTbody = document.getElementById('surveyDetailAnswers');

            const openModal = () => {
                if (!modal) return;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            };

            const closeModal = () => {
                if (!modal) return;
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            };

            document.querySelectorAll('[data-modal-close]').forEach((el) => {
                el.addEventListener('click', closeModal);
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeModal();
            });

            document.querySelectorAll('.btnSurveyDetail').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const raw = btn.getAttribute('data-survey');
                    if (!raw) return;

                    let data = null;
                    try {
                        data = JSON.parse(raw);
                    } catch (e) {
                        return;
                    }

                    if (metaEl) metaEl.textContent = data.submitted_at ? `Dikirim: ${data.submitted_at}` : '-';
                    if (nameEl) nameEl.textContent = data.name || '-';
                    if (emailEl) emailEl.textContent = data.email || '';
                    if (purposeEl) purposeEl.textContent = data.purpose || '-';
                    if (avgEl) avgEl.textContent = data.avg || '-';
                    if (commentEl) commentEl.textContent = data.comment || '-';

                    if (answersTbody) {
                        answersTbody.innerHTML = '';
                        const answers = data.answers || {};
                        Object.keys(answers).forEach((key) => {
                            const value = answers[key];
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td class="py-2 pr-4 text-slate-700 font-semibold">${key}</td>
                                <td class="py-2 pr-0 text-slate-700 tabular-nums">${value ?? '-'}</td>
                            `;
                            answersTbody.appendChild(tr);
                        });
                    }

                    openModal();
                });
            });

            const btn = document.getElementById('btnExportSurvey');
            const menu = document.getElementById('menuExportSurvey');
            const chevron = document.getElementById('exportSurveyChevron');
            const dlFrame = document.getElementById('dlSurveyFrame');

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

            document.querySelectorAll('.export-survey-action').forEach(el => {
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
