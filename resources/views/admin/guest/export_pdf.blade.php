<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Buku Tamu</title>
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

        .nowrap {
            white-space: nowrap;
        }

        .small {
            font-size: 9px;
        }
    </style>
</head>

<body>
    <h1>Laporan Buku Tamu</h1>
    <div class="muted">Generated: {{ $generatedAt->format('Y-m-d H:i:s') }}</div>

    <h2>Filter</h2>
    <table>
        <tr>
            <th style="width: 14%">q</th>
            <td>{{ $filters['q'] !== '' ? $filters['q'] : '-' }}</td>
            <th style="width: 14%">status</th>
            <td>{{ $filters['status'] !== '' ? $filters['status'] : '-' }}</td>
            <th style="width: 14%">from</th>
            <td class="nowrap">{{ $filters['from'] !== '' ? $filters['from'] : '-' }}</td>
            <th style="width: 14%">to</th>
            <td class="nowrap">{{ $filters['to'] !== '' ? $filters['to'] : '-' }}</td>
        </tr>
    </table>

    <div class="muted small">Menampilkan {{ $visits->count() }} data (maks {{ $maxRows }} baris).</div>

    <table>
        <thead>
            <tr>
                <th style="width: 14%">Waktu Datang</th>
                <th style="width: 14%">Nama</th>
                <th style="width: 14%">Email</th>
                <th style="width: 10%">Layanan</th>
                <th style="width: 22%">Keperluan</th>
                <th style="width: 10%">Status</th>
                <th style="width: 10%">Survey</th>
                <th style="width: 12%">Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($visits as $v)
                @php
                    $status = $v->completed_at ? 'Selesai' : 'Pending';
                    $surveyFilled = (bool) ($v->survey_exists ?? false);
                @endphp
                <tr>
                    <td class="nowrap">{{ optional($v->arrived_at)->format('Y-m-d H:i') }}</td>
                    <td>{{ $v->name }}</td>
                    <td>{{ $v->email }}</td>
                    <td class="nowrap">{{ $v->service_type }}</td>
                    <td>{{ \Illuminate\Support\Str::limit((string) $v->purpose, 80) }}</td>
                    <td class="nowrap">{{ $status }}</td>
                    <td class="nowrap">{{ $surveyFilled ? 'Sudah' : 'Belum' }}</td>
                    <td>{{ $v->handler?->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="muted">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
