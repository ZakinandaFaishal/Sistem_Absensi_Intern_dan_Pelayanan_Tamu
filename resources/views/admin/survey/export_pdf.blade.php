<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Survey Pelayanan</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #111827;
        }

        .muted {
            color: #6b7280;
        }

        h1 {
            font-size: 15px;
            margin: 0 0 6px;
        }

        h2 {
            font-size: 12px;
            margin: 12px 0 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            padding: 5px 6px;
            vertical-align: top;
        }

        th {
            background: #f9fafb;
            text-align: left;
        }

        .right {
            text-align: right;
        }

        .nowrap {
            white-space: nowrap;
        }

        .small {
            font-size: 9px;
        }
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

    <h2>Filter</h2>
    <table>
        <tr>
            <th style="width: 16%">q</th>
            <td>{{ $filters['q'] !== '' ? $filters['q'] : '-' }}</td>
            <th style="width: 16%">avg_min</th>
            <td>{{ $filters['avg_min'] !== '' ? $filters['avg_min'] : '-' }}</td>
        </tr>
        <tr>
            <th>from</th>
            <td>{{ $filters['from'] !== '' ? $filters['from'] : '-' }}</td>
            <th>to</th>
            <td>{{ $filters['to'] !== '' ? $filters['to'] : '-' }}</td>
        </tr>
    </table>

    <h2>Ringkasan IKM</h2>
    <table>
        <tr>
            <th style="width: 16%">Total respon (Q1..Q9 lengkap)</th>
            <td class="right">{{ $n }}</td>
            <th style="width: 16%">NRR (1–4)</th>
            <td class="right">{{ number_format($overallNrr, 4, '.', '') }}</td>
            <th style="width: 16%">IKM (×25)</th>
            <td class="right">{{ number_format($overallIkm, 4, '.', '') }}</td>
            <th style="width: 10%">Mutu</th>
            <td class="right nowrap">{{ $mutu }}</td>
            <th style="width: 12%">Kinerja</th>
            <td class="right">{{ $kinerja }}</td>
        </tr>
    </table>

    <h2>Detail Respon</h2>
    <div class="muted small">Menampilkan {{ $showing }} data terbaru (maks {{ $maxRows }} baris) untuk
        menjaga ukuran PDF.</div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%">Waktu</th>
                <th style="width: 14%">Nama</th>
                <th style="width: 10%">Layanan</th>
                <th style="width: 14%">Keperluan</th>
                <th class="right" style="width: 6%">NRR</th>
                <th class="right" style="width: 6%">IKM</th>
                <th>Komentar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($surveys as $s)
                @php
                    $visit = $s->visit;
                    $nums = [];
                    foreach (range(1, 9) as $i) {
                        $k = "q{$i}";
                        if ($s->$k === null) {
                            $nums = [];
                            break;
                        }
                        $nums[] = (float) $s->$k;
                    }
                    $avg = count($nums) === 9 ? array_sum($nums) / 9.0 : null;
                    $ikm = $avg !== null ? $avg * 25.0 : null;
                    $comment = (string) ($s->comment ?? '');
                @endphp
                <tr>
                    <td class="nowrap">{{ optional($s->submitted_at)->format('Y-m-d H:i') }}</td>
                    <td>{{ $visit?->name ?? '-' }}</td>
                    <td>{{ $visit?->service_type ?? '-' }}</td>
                    <td>{{ $visit?->purpose ?? '-' }}</td>
                    <td class="right">{{ $avg !== null ? number_format($avg, 2, '.', '') : '-' }}</td>
                    <td class="right">{{ $ikm !== null ? number_format($ikm, 2, '.', '') : '-' }}</td>
                    <td>{{ $comment !== '' ? \Illuminate\Support\Str::limit($comment, 140) : '-' }}</td>
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
