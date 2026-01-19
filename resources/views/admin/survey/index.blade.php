@extends('layouts.admin')

@section('title', 'Survey Pelayanan - Diskominfo Kab. Magelang')
@section('page_title', 'Survey Pelayanan')

@section('content')

    @php
        // ===== FILTER & SORT STATE =====
        $q = request('q', '');
        $avgMin = request('avg_min', ''); // '', '4','3.5','3','2.5','2','1.5','1'
        $from = request('from', '');
        $to = request('to', '');
        $sort = request('sort', 'submitted_at'); // kalau pakai created_at, ganti default ini
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

        // ===== PERTANYAAN SURVEY (SAMA PERSIS DARI FORM TAMU) =====
        $questions = [
            'q1' => [
                'title' =>
                    'Bagaimana pendapat Saudara tentang kesesuaian persyaratan pelayanan dengan jenis pelayanannya?',
                'options' => [1 => 'Tidak sesuai', 2 => 'Kurang sesuai', 3 => 'Sesuai', 4 => 'Sangat sesuai'],
            ],
            'q2' => [
                'title' => 'Bagaimana pemahaman Saudara tentang kemudahan prosedur pelayanan di unit ini?',
                'options' => [1 => 'Tidak mudah', 2 => 'Kurang mudah', 3 => 'Mudah', 4 => 'Sangat mudah'],
            ],
            'q3' => [
                'title' => 'Bagaimana pendapat Saudara tentang kecepatan waktu dalam memberikan pelayanan?',
                'options' => [1 => 'Tidak cepat', 2 => 'Kurang cepat', 3 => 'Cepat', 4 => 'Sangat cepat'],
            ],
            'q4' => [
                'title' => 'Bagaimana pendapat Saudara tentang kewajaran biaya/tarif dalam pelayanan?',
                'options' => [1 => 'Sangat mahal', 2 => 'Cukup mahal', 3 => 'Murah', 4 => 'Gratis'],
            ],
            'q5' => [
                'title' =>
                    'Bagaimana pendapat Saudara tentang kesesuaian produk pelayanan antara yang tercantum dalam standar pelayanan dengan hasil yang diberikan?',
                'options' => [1 => 'Tidak sesuai', 2 => 'Kurang sesuai', 3 => 'Sesuai', 4 => 'Sangat sesuai'],
            ],
            'q6' => [
                'title' => 'Bagaimana pendapat Saudara tentang kompetensi/kemampuan petugas dalam pelayanan?',
                'options' => [1 => 'Tidak kompeten', 2 => 'Kurang kompeten', 3 => 'Kompeten', 4 => 'Sangat kompeten'],
            ],
            'q7' => [
                'title' =>
                    'Bagaimana pendapat Saudara tentang perilaku petugas dalam pelayanan terkait kesopanan dan keramahan?',
                'options' => [
                    1 => 'Tidak sopan dan ramah',
                    2 => 'Kurang sopan dan ramah',
                    3 => 'Sopan dan ramah',
                    4 => 'Sangat sopan dan ramah',
                ],
            ],
            'q8' => [
                'title' => 'Bagaimana pendapat Saudara tentang kualitas sarana dan prasarana?',
                'options' => [1 => 'Buruk', 2 => 'Cukup', 3 => 'Baik', 4 => 'Sangat baik'],
            ],
            'q9' => [
                'title' => 'Bagaimana pendapat Saudara tentang penanganan pengaduan pengguna layanan?',
                'options' => [
                    1 => 'Tidak ada',
                    2 => 'Ada tetapi tidak berfungsi',
                    3 => 'Berfungsi kurang maksimal',
                    4 => 'Dikelola dengan baik',
                ],
            ],
        ];
    @endphp

    {{-- HEADER PAGE --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Survey Pelayanan</h2>
            <p class="mt-1 text-sm text-slate-600">Rekap jawaban Q1–Q9 dan komentar dari tamu.</p>
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

                    <button type="button"
                        class="export-survey-action w-full text-left px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center justify-between"
                        data-url="{{ route('admin.survey.export.ikm.pdf', request()->query()) }}" data-label="IKM PDF">
                        <span>Export IKM (PDF)</span>
                        <span class="text-xs text-slate-400">.pdf</span>
                    </button>

                    <button type="button"
                        class="export-survey-action w-full text-left px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center justify-between"
                        data-url="{{ route('admin.survey.export.detail.csv', request()->query()) }}" data-label="Detail">
                        <span>Export Detail Survey</span>
                        <span class="text-xs text-slate-400">.csv</span>
                    </button>

                    <button type="button"
                        class="export-survey-action w-full text-left px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center justify-between"
                        data-url="{{ route('admin.survey.export.ikm.csv', request()->query()) }}" data-label="IKM">
                        <span>Export IKM</span>
                        <span class="text-xs text-slate-400">.csv</span>
                    </button>
                </div>
            </div>

            <iframe id="dlSurveyFrame" class="hidden"></iframe>

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700
                  hover:bg-slate-50 transition">
                ← Kembali
            </a>
        </div>
    </div>

    <div class="pt-5 space-y-6">

        {{-- RINGKASAN IKM (PermenPANRB No. 14 Tahun 2017) --}}
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div
                class="px-6 py-5 border-b border-slate-200 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="text-sm font-semibold text-slate-900">Ringkasan IKM</div>
                    <div class="text-xs text-slate-500">NRR (skala 1–4) → IKM = NRR × 25 (skala 25–100)</div>
                </div>

                <div class="text-xs text-slate-500">
                    Total respon (sesuai filter):
                    <span class="font-semibold text-slate-700">{{ (int) ($ikmSummary['n'] ?? 0) }}</span>
                </div>
            </div>

            @php
                $ikmN = (int) ($ikmSummary['n'] ?? 0);
                $avgByKey = (array) ($ikmSummary['avg_by_key'] ?? []);
                $overallNrr = (float) ($ikmSummary['overall_nrr'] ?? 0);
                $overallIkm = (float) ($ikmSummary['overall_ikm'] ?? 0);
                $mutu = (string) ($ikmSummary['mutu'] ?? '-');
                $kinerja = (string) ($ikmSummary['kinerja'] ?? '-');

                $mutuBadge = match ($mutu) {
                    'A' => 'bg-emerald-100 text-emerald-800',
                    'B' => 'bg-sky-100 text-sky-800',
                    'C' => 'bg-amber-100 text-amber-800',
                    'D' => 'bg-rose-100 text-rose-800',
                    default => 'bg-slate-100 text-slate-700',
                };

                $labels = [
                    'q1' => 'Kesesuaian persyaratan pelayanan',
                    'q2' => 'Kemudahan prosedur',
                    'q3' => 'Kecepatan waktu pelayanan',
                    'q4' => 'Kewajaran biaya/tarif',
                    'q5' => 'Kesesuaian produk pelayanan',
                    'q6' => 'Kompetensi petugas',
                    'q7' => 'Perilaku petugas (sopan/ramah)',
                    'q8' => 'Kualitas sarana & prasarana',
                    'q9' => 'Penanganan pengaduan',
                ];

                $fmt2 = fn($v) => number_format((float) $v, 2, ',', '.');
            @endphp

            <div class="px-6 py-5">
                @if ($ikmN <= 0)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                        Belum ada data survey (Q1–Q9) pada filter saat ini.
                    </div>
                @else
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <div class="text-xs font-semibold text-slate-500">NRR (rata-rata Q1–Q9)</div>
                            <div class="mt-1 text-2xl font-extrabold text-slate-900 tabular-nums">
                                {{ $fmt2($overallNrr) }} <span class="text-sm font-semibold text-slate-500">/4</span>
                            </div>
                            <div class="mt-1 text-xs text-slate-500">Skala 1–4</div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <div class="text-xs font-semibold text-slate-500">Nilai IKM</div>
                            <div class="mt-1 text-2xl font-extrabold text-slate-900 tabular-nums">
                                {{ $fmt2($overallIkm) }}
                            </div>
                            <div class="mt-1 text-xs text-slate-500">Rumus: IKM = NRR × 25</div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <div class="text-xs font-semibold text-slate-500">Mutu Pelayanan</div>
                            <div class="mt-2 inline-flex items-center gap-2">
                                <span
                                    class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold {{ $mutuBadge }}">
                                    {{ $mutu }}
                                </span>
                                <span class="text-sm font-semibold text-slate-800">{{ $kinerja }}</span>
                            </div>
                            <div class="mt-1 text-xs text-slate-500">Kategori: A/B/C/D (PermenPANRB 14/2017)</div>
                        </div>
                    </div>

                    <div class="mt-5 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-slate-600 border-b border-slate-200">
                                    <th class="py-3 pr-4 font-semibold whitespace-nowrap">Unsur</th>
                                    <th class="py-3 pr-4 font-semibold whitespace-nowrap">NRR (1–4)</th>
                                    <th class="py-3 pr-0 font-semibold whitespace-nowrap">Konversi (×25)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($labels as $key => $label)
                                    @php
                                        $avg = $avgByKey[$key] ?? null;
                                        $nrr = $avg !== null ? (float) $avg : 0.0;
                                        $conv = $nrr * 25.0;
                                    @endphp
                                    <tr>
                                        <td class="py-3 pr-4 text-slate-700">
                                            <div class="font-semibold text-slate-900">{{ strtoupper($key) }}</div>
                                            <div class="text-xs text-slate-500">{{ $label }}</div>
                                        </td>
                                        <td class="py-3 pr-4 text-slate-700 tabular-nums">
                                            {{ $avg !== null ? $fmt2($nrr) : '-' }}</td>
                                        <td class="py-3 pr-0 text-slate-700 tabular-nums">
                                            {{ $avg !== null ? $fmt2($conv) : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-600">
                        Interval mutu umum: 88,31–100 (A), 76,61–88,30 (B), 65,00–76,60 (C), 25,00–64,99 (D).
                    </div>
                @endif
            </div>
        </section>

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
                <form method="GET" action="{{ route('admin.survey.index') }}"
                    class="grid grid-cols-1 sm:grid-cols-12 gap-3">

                    <div class="sm:col-span-5">
                        <label class="block text-xs font-semibold text-slate-600">Cari</label>
                        <input type="text" name="q" value="{{ $q }}"
                            placeholder="Nama tamu / keperluan / komentar…"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-slate-200">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold text-slate-600">Minimal Rata-rata (Q1–Q9)</label>
                        <select name="avg_min"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-slate-200">
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

                    <div class="sm:col-span-12 flex items-center justify-between gap-2">
                        <div class="text-xs text-slate-500">
                            Sort:
                            <span class="font-semibold text-slate-700">{{ $sort }}</span> ({{ $dir }})
                            @if ($activeFilter)
                                <span class="mx-2 opacity-40">|</span> Filter aktif
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <input type="hidden" name="sort" value="{{ $sort }}">
                            <input type="hidden" name="dir" value="{{ $dir }}">

                            <button type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white
                                       hover:bg-slate-800 transition">
                                Terapkan
                            </button>

                            <a href="{{ route('admin.survey.index') }}"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700
                                       hover:bg-slate-50 transition">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>

                {{-- SORT CHIPS --}}
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

            {{-- LIST --}}
            <div class="p-6 space-y-3">
                @forelse($surveys as $survey)
                    @php
                        // Ambil skor Q1..Q9
                        $vals = [];
                        foreach (array_keys($questions) as $k) {
                            $v = data_get($survey, $k);
                            if ($v !== null && $v !== '') {
                                $vals[] = (int) $v;
                            }
                        }
                        $avg = count($vals) ? array_sum($vals) / count($vals) : 0; // 1..4
                        $avgLabel = $avg ? number_format($avg, 2, ',', '.') : '-';

                        $avgBadge =
                            $avg >= 3.5
                                ? 'bg-emerald-100 text-emerald-800'
                                : ($avg >= 2.75
                                    ? 'bg-amber-100 text-amber-800'
                                    : 'bg-rose-100 text-rose-800');

                        // kalau submitted_at tidak ada, pakai created_at:
                        $submittedAt = $survey->submitted_at ?? ($survey->created_at ?? null);
                        $submitted = $submittedAt?->format('d M Y H:i') ?? '-';

                        $guestName = $survey->visit?->name ?? 'Tamu';
                        $purpose = $survey->visit?->purpose ?? '-';

                        $accordionId = 'detail-' . ($survey->id ?? spl_object_id($survey));
                    @endphp

                    <div class="border border-slate-200 rounded-2xl p-4 hover:bg-slate-50/60 transition">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <div class="font-semibold text-slate-900 truncate">{{ $guestName }}</div>
                                    <span class="text-slate-300">•</span>
                                    <span
                                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $avgBadge }}">
                                        Rata-rata (Q1–Q9): {{ $avgLabel }}/4
                                    </span>
                                </div>

                                <div class="mt-1 text-sm text-slate-600">{{ $purpose }}</div>

                                <div class="mt-1 text-xs text-slate-500">
                                    Dikirim: <span class="font-semibold text-slate-700">{{ $submitted }}</span>
                                </div>
                            </div>

                            <div class="shrink-0 flex flex-wrap items-center gap-2">
                                <button type="button"
                                    class="btnToggleDetail inline-flex items-center gap-2 rounded-xl bg-white border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700
                                           hover:bg-slate-50 transition"
                                    data-target="{{ $accordionId }}">
                                    <span>Detail Jawaban</span>
                                    <span class="chev inline-block transition">▾</span>
                                </button>
                            </div>
                        </div>

                        {{-- DETAIL --}}
                        <div id="{{ $accordionId }}" class="hidden mt-4">
                            <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
                                <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                                    <div class="text-sm font-semibold text-slate-900">Jawaban Survey (Skala 1–4)</div>
                                    <div class="text-xs text-slate-500">Nilai dan label sesuai pilihan responden.</div>
                                </div>

                                <div class="p-4 space-y-2">
                                    @foreach ($questions as $key => $qmeta)
                                        @php
                                            $v = (int) (data_get($survey, $key) ?? 0);
                                            $label = $v && isset($qmeta['options'][$v]) ? $qmeta['options'][$v] : '-';
                                            $pill =
                                                $v >= 4
                                                    ? 'bg-emerald-50 text-emerald-700'
                                                    : ($v === 3
                                                        ? 'bg-amber-50 text-amber-700'
                                                        : 'bg-rose-50 text-rose-700');
                                        @endphp

                                        <div
                                            class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 rounded-xl border border-slate-200 px-3 py-3">
                                            <div class="min-w-0">
                                                <div class="text-xs font-semibold text-slate-500">{{ strtoupper($key) }}
                                                </div>
                                                <div class="text-sm font-semibold text-slate-900 leading-snug">
                                                    {{ $qmeta['title'] }}
                                                </div>
                                                <div class="text-sm text-slate-700 mt-1">
                                                    {{ $label }}
                                                </div>
                                            </div>

                                            <div class="shrink-0">
                                                <span
                                                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $pill }}">
                                                    Skor: {{ $v ?: '-' }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach

                                    @if ($survey->comment)
                                        <div class="mt-3 rounded-xl border border-slate-200 bg-white px-4 py-3">
                                            <div class="text-xs font-semibold text-slate-500">Komentar</div>
                                            <div class="mt-1 text-sm text-slate-800">{{ $survey->comment }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                @empty
                    <div class="py-10 text-center text-sm text-slate-600">
                        Belum ada survey masuk.
                    </div>
                @endforelse

                <div class="pt-3">
                    {{ $surveys->links() }}
                </div>
            </div>
        </section>
    </div>

    {{-- EXPORT + ACCORDION SCRIPT --}}
    <script>
        (function() {
            // ===== Export dropdown =====
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

            // ===== Accordion =====
            document.querySelectorAll('.btnToggleDetail').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-target');
                    const panel = id ? document.getElementById(id) : null;
                    if (!panel) return;

                    const chev = btn.querySelector('.chev');
                    const isHidden = panel.classList.contains('hidden');

                    if (isHidden) {
                        panel.classList.remove('hidden');
                        if (chev) chev.style.transform = 'rotate(180deg)';
                    } else {
                        panel.classList.add('hidden');
                        if (chev) chev.style.transform = 'rotate(0deg)';
                    }
                });
            });
        })();
    </script>

@endsection
