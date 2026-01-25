@extends('layouts.admin')

@section('title', 'Ringkasan IKM')
@section('page_title', 'Ringkasan IKM')

@section('content')

    {{-- HEADER PAGE --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Ringkasan IKM</h2>
            <p class="mt-1 text-sm text-slate-600">NRR (skala 1–4) → IKM = NRR × 25 (skala 25–100).</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.survey.index') }}"
                class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                ← Ke Daftar Survey
            </a>
        </div>
    </div>

    <div class="pt-5 space-y-6">

        {{-- FILTER BAR --}}
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200">
                <p class="text-sm font-semibold text-slate-900">Filter Ringkasan</p>
                <p class="mt-0.5 text-xs text-slate-500">Filter ini mempengaruhi perhitungan NRR/IKM.</p>
            </div>

            <div class="p-6">
                <form method="GET" action="{{ route('admin.survey.ikm') }}"
                    class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                    <div class="sm:col-span-5">
                        <label class="block text-xs font-semibold text-slate-600">Cari</label>
                        <input type="text" name="q" value="{{ request('q', '') }}"
                            placeholder="Nama tamu / keperluan / komentar…"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-200">
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold text-slate-600">Minimal Rata-rata (Q1–Q9)</label>
                        @php($avgMin = (string) request('avg_min', ''))
                        <select name="avg_min"
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
                        <input type="date" name="from" value="{{ request('from', '') }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-200">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600">Sampai</label>
                        <input type="date" name="to" value="{{ request('to', '') }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-200">
                    </div>

                    <div class="sm:col-span-12 flex items-center justify-end gap-2">
                        <a href="{{ route('admin.survey.ikm') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                            Reset
                        </a>
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">
                            Terapkan
                        </button>
                    </div>
                </form>
            </div>
        </section>

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

    </div>

@endsection
