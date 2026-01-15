<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Presensi</title>
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
    <h1>Laporan Presensi</h1>
    <div class="muted">Generated: {{ $generatedAt->format('Y-m-d H:i:s') }}</div>

    <h2>Filter</h2>
    <table>
        <tr>
            <th style="width: 14%">q</th>
            <td>{{ $filters['q'] !== '' ? $filters['q'] : '-' }}</td>
            <th style="width: 14%">date_from</th>
            <td class="nowrap">{{ $filters['date_from'] !== '' ? $filters['date_from'] : '-' }}</td>
            <th style="width: 14%">date_to</th>
            <td class="nowrap">{{ $filters['date_to'] !== '' ? $filters['date_to'] : '-' }}</td>
        </tr>
    </table>

    <div class="muted small">Menampilkan {{ $attendances->count() }} data (maks {{ $maxRows }} baris).</div>

    <table>
        <thead>
            <tr>
                <th style="width: 18%">Nama</th>
                <th style="width: 10%">Tanggal</th>
                <th style="width: 11%">Check-in</th>
                <th style="width: 11%">Check-out</th>
                <th style="width: 14%">Lokasi</th>
                <th style="width: 10%">Status</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $a)
                @php
                    $status = '-';
                    if (!empty($a->check_in_at) && empty($a->check_out_at)) {
                        $status = 'Open';
                    }
                    if (!empty($a->check_in_at) && !empty($a->check_out_at)) {
                        $status = 'Selesai';
                    }
                    $note = (string) ($a->notes ?? '');
                @endphp
                <tr>
                    <td>{{ $a->user?->name ?? '-' }}</td>
                    <td class="nowrap">{{ optional($a->date)->format('Y-m-d') }}</td>
                    <td class="nowrap">{{ optional($a->check_in_at)->format('H:i') }}</td>
                    <td class="nowrap">{{ optional($a->check_out_at)->format('H:i') }}</td>
                    <td>{{ $a->location?->name ?? '-' }}</td>
                    <td class="nowrap">{{ $status }}</td>
                    <td>{{ $note !== '' ? \Illuminate\Support\Str::limit($note, 60) : '-' }}</td>
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
