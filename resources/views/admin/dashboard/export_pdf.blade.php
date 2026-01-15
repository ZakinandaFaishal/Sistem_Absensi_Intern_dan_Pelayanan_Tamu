<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Export Dashboard</title>
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
    <h1>Export Dashboard</h1>
    <div class="muted">Generated: {{ $generatedAt->format('Y-m-d H:i:s') }}</div>

    <h2>Ringkasan Hari Ini</h2>
    <table>
        <tr>
            <th style="width: 45%">Presensi hari ini</th>
            <td class="right">{{ (int) ($stats['attendance_today'] ?? 0) }}</td>
        </tr>
        <tr>
            <th>Intern masih open (belum checkout)</th>
            <td class="right">{{ (int) ($stats['intern_open'] ?? 0) }}</td>
        </tr>
        <tr>
            <th>Buku tamu hari ini</th>
            <td class="right">{{ (int) ($stats['guest_today'] ?? 0) }}</td>
        </tr>
        <tr>
            <th>Survey hari ini</th>
            <td class="right">{{ (int) ($stats['survey_today'] ?? 0) }}</td>
        </tr>
        <tr>
            <th>Total users</th>
            <td class="right">{{ (int) ($stats['users_total'] ?? 0) }}</td>
        </tr>
    </table>

    <h2>Aktivitas 7 Hari Terakhir</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 20%">Tanggal</th>
                <th class="right" style="width: 20%">Tamu</th>
                <th class="right" style="width: 20%">Survey</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($chart as $row)
                <tr>
                    <td class="nowrap">{{ $row['date'] }}</td>
                    <td class="right">{{ (int) ($row['guest'] ?? 0) }}</td>
                    <td class="right">{{ (int) ($row['survey'] ?? 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
