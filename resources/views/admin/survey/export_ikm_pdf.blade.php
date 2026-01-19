<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Rekap IKM</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
        }

        .muted {
            color: #6b7280;
        }

        h1 {
            font-size: 16px;
            margin: 0 0 6px;
        }

        h2 {
            font-size: 13px;
            margin: 14px 0 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
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
    </style>
</head>

<body>
    @php
        $n = (int) ($ikmSummary['n'] ?? 0);
        $avgByKey = (array) ($ikmSummary['avg_by_key'] ?? []);
        $overallNrr = (float) ($ikmSummary['overall_nrr'] ?? 0);
        $overallIkm = (float) ($ikmSummary['overall_ikm'] ?? 0);
        $mutu = (string) ($ikmSummary['mutu'] ?? '-');
        $kinerja = (string) ($ikmSummary['kinerja'] ?? '-');

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
    @endphp

    <h1>Rekap IKM Survey Pelayanan</h1>
    <div class="muted">PermenPANRB No. 14 Tahun 2017 · Generated: {{ $generatedAt->format('Y-m-d H:i:s') }}</div>

    <h2>Filter</h2>
    <table>
        <tr>
            <th style="width: 22%">q</th>
            <td>{{ $filters['q'] !== '' ? $filters['q'] : '-' }}</td>
        </tr>
        <tr>
            <th>avg_min</th>
            <td>{{ $filters['avg_min'] !== '' ? $filters['avg_min'] : '-' }}</td>
        </tr>
        <tr>
            <th>from</th>
            <td>{{ $filters['from'] !== '' ? $filters['from'] : '-' }}</td>
        </tr>
        <tr>
            <th>to</th>
            <td>{{ $filters['to'] !== '' ? $filters['to'] : '-' }}</td>
        </tr>
    </table>

    <h2>Ringkasan</h2>
    <table>
        <tr>
            <th style="width: 22%">Total respon (Q1..Q9 lengkap)</th>
            <td class="right">{{ $n }}</td>
        </tr>
        <tr>
            <th>NRR (1–4)</th>
            <td class="right">{{ number_format($overallNrr, 4, '.', '') }}</td>
        </tr>
        <tr>
            <th>IKM (25–100) = NRR × 25</th>
            <td class="right">{{ number_format($overallIkm, 4, '.', '') }}</td>
        </tr>
        <tr>
            <th>Mutu</th>
            <td class="right nowrap">{{ $mutu }}</td>
        </tr>
        <tr>
            <th>Kinerja</th>
            <td class="right">{{ $kinerja }}</td>
        </tr>
    </table>

    <h2>NRR per Unsur</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 10%">Unsur</th>
                <th>Label</th>
                <th class="right" style="width: 16%">NRR (1–4)</th>
                <th class="right" style="width: 16%">Konversi × 25</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($labels as $key => $label)
                @php
                    $nrr = $avgByKey[$key] ?? null;
                    $conv = $nrr !== null ? ((float) $nrr) * 25.0 : null;
                @endphp
                <tr>
                    <td class="nowrap">{{ strtoupper($key) }}</td>
                    <td>{{ $label }}</td>
                    <td class="right">{{ $nrr !== null ? number_format((float) $nrr, 4, '.', '') : '-' }}</td>
                    <td class="right">{{ $conv !== null ? number_format((float) $conv, 4, '.', '') : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
