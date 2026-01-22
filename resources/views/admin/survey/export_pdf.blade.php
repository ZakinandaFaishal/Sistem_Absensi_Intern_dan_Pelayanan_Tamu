<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Survey Pelayanan</title>

    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        h1 { font-size: 15px; margin: 0 0 6px; }
        h2 { font-size: 12px; margin: 12px 0 6px; }
        .muted { color: #6b7280; }
        .small { font-size: 9px; }

        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 7px; vertical-align: top; }
        th { background: #f3f4f6; text-align: left; font-weight: 700; }
        tr:nth-child(even) td { background: #fafafa; }

        .right { text-align: right; }
        .nowrap { white-space: nowrap; }
        .wrap { word-break: break-word; white-space: normal; }
        .members { margin-top: 2px; font-size: 9px; color: #6b7280; line-height: 1.35; }
    </style>
</head>

<body>
@php
    $n = (int) ($ikmSummary['n'] ?? 0);
    $overallNrr = (float) ($ikmSummary['overall_nrr'] ?? 0);
    $overallIkm = (float) ($ikmSummary['overall_ikm'] ?? 0);
    $mutu = (string) ($ikmSummary['mutu'] ?? '-');
    $kinerja = (string) ($ikmSummary['kinerja'] ?? '-');

    $showing = $surveys->count();
@endphp

    <h1>Laporan Survey Pelayanan</h1>
    <div class="muted">Generated: {{ $generatedAt->format('Y-m-d H:i:s') }}</div>
    <div class="muted small" style="margin:6px 0 10px;">
        Menampilkan {{ $showing }} data terbaru (maks {{ $maxRows }} baris) untuk menjaga ukuran PDF.
    </div>

    <h2>Ringkasan IKM</h2>
    <table>
        <tbody>
            <tr>
                <th style="width: 16%">Total respon (Q1..Q9 lengkap)</th>
                <td class="right nowrap">{{ $n }}</td>
                <th style="width: 12%">NRR (1–4)</th>
                <td class="right nowrap">{{ number_format($overallNrr, 4, '.', '') }}</td>
                <th style="width: 12%">IKM (×25)</th>
                <td class="right nowrap">{{ number_format($overallIkm, 4, '.', '') }}</td>
                <th style="width: 10%">Mutu</th>
                <td class="right nowrap">{{ $mutu }}</td>
                <th style="width: 12%">Kinerja</th>
                <td class="right nowrap">{{ $kinerja }}</td>
            </tr>
        </tbody>
    </table>

    <h2>Detail Respon</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 11%">Waktu</th>
                <th style="width: 22%">Nama</th>
                <th style="width: 10%">Layanan</th>
                <th style="width: 20%">Keperluan</th>
                <th class="right" style="width: 7%">NRR</th>
                <th class="right" style="width: 7%">IKM</th>
                <th>Komentar</th>
            </tr>
        </thead>

        <tbody>
        @forelse($surveys as $s)
            @php
                $visit = $s->visit;

                // ===== hitung NRR/IKM =====
                $nums = [];
                foreach (range(1, 9) as $i) {
                    $k = "q{$i}";
                    if ($s->$k === null) { $nums = []; break; }
                    $nums[] = (float) $s->$k;
                }
                $avg = count($nums) === 9 ? array_sum($nums) / 9.0 : null;
                $ikm = $avg !== null ? $avg * 25.0 : null;

                $comment = trim((string) ($s->comment ?? ''));

                // ===== deteksi kelompok (mengikuti GuestVisit) =====
                $isGroup = (($visit?->visit_type ?? null) === 'group') || ((int)($visit?->group_count ?? 0) > 1);
                $groupCount = (int) ($visit?->group_count ?? 0);

                // ===== ambil anggota dari group_names (cast array) =====
                $members = $visit?->group_names ?? [];
                if (!is_array($members)) $members = [];
                $members = array_values(array_unique(array_filter(array_map(fn($x) => trim((string)$x), $members))));

                // ===== label nama =====
                $nameLabel = trim((string)($visit?->name ?? 'Tamu'));
                if ($isGroup) {
                    $suffix = $groupCount > 0 ? "Kelompok ({$groupCount} orang)" : "Kelompok";
                    $nameLabel .= " — {$suffix}";
                }

                $serviceLabel = trim((string)($visit?->service_type ?? ''));
                $serviceLabel = $serviceLabel !== '' ? $serviceLabel : '-';

                $purposeLabel = trim((string)($visit?->purpose ?? ''));
                $purposeLabel = $purposeLabel !== '' ? $purposeLabel : '-';
                $purposeLabel = \Illuminate\Support\Str::limit($purposeLabel, 140);

                $commentLabel = $comment !== '' ? \Illuminate\Support\Str::limit($comment, 140) : '-';
            @endphp

            <tr>
                <td class="nowrap">{{ optional($s->submitted_at)->format('Y-m-d H:i') }}</td>

                <td class="wrap">
                    <div><strong>{{ $nameLabel }}</strong></div>

                    @if($isGroup)
                        <div class="members">
                            @if(count($members))
                                Anggota:
                                {{ implode(', ', array_slice($members, 0, 12)) }}
                                @if(count($members) > 12)
                                    , dan {{ count($members) - 12 }} lainnya
                                @endif
                            @else
                                Anggota: -
                            @endif
                        </div>
                    @endif
                </td>

                <td class="nowrap">{{ $serviceLabel }}</td>
                <td class="wrap">{{ $purposeLabel }}</td>
                <td class="right nowrap">{{ $avg !== null ? number_format($avg, 2, '.', '') : '-' }}</td>
                <td class="right nowrap">{{ $ikm !== null ? number_format($ikm, 2, '.', '') : '-' }}</td>
                <td class="wrap">{{ $commentLabel }}</td>
            </tr>

        @empty
            <tr>
                <td colspan="7" class="muted">Tidak ada data.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
