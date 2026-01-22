<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Buku Tamu</title>

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

        .nowrap { white-space: nowrap; }
        .wrap { word-break: break-word; white-space: normal; }
        .members { margin-top: 2px; font-size: 9px; color: #6b7280; line-height: 1.35; }
    </style>
</head>

<body>
    <h1>Laporan Buku Tamu</h1>
    <div class="muted">Generated: {{ $generatedAt->format('Y-m-d H:i:s') }}</div>
    <div class="muted small" style="margin:6px 0 10px;">
        Menampilkan {{ $visits->count() }} data (maks {{ $maxRows }} baris).
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 13%">Waktu Datang</th>
                <th style="width: 19%">Nama</th>
                <th style="width: 15%">Email</th>
                <th style="width: 10%">Layanan</th>
                <th style="width: 23%">Keperluan</th>
                <th style="width: 8%">Status</th>
                <th style="width: 6%">Survey</th>
                <th style="width: 6%">Petugas</th>
            </tr>
        </thead>

        <tbody>
        @forelse($visits as $v)
            @php
                $status = $v->completed_at ? 'Selesai' : 'Pending';
                $surveyFilled = (bool) ($v->survey_exists ?? false);

                // deteksi kelompok
                $isGroup = (($v->visit_type ?? null) === 'group') || ((int)($v->group_count ?? 0) > 1);
                $groupCount = (int) ($v->group_count ?? 0);

                // ambil anggota dari group_names (CAST array di model)
                $members = $v->group_names;

                // normalisasi: pastikan array of string rapi
                if (!is_array($members)) $members = [];
                $members = array_values(array_unique(array_filter(array_map(fn($x) => trim((string)$x), $members))));

                // label nama
                $nameLabel = trim((string)($v->name ?? 'Tamu'));
                if ($isGroup) {
                    $suffix = $groupCount > 0 ? "Kelompok ({$groupCount} orang)" : "Kelompok";
                    $nameLabel .= " — {$suffix}";
                }

                $emailLabel = trim((string)($v->email ?? ''));
                $emailLabel = $emailLabel !== '' ? $emailLabel : '-';

                // keperluan: pakai purpose (yang sudah kamu pakai), wrap dan dibatasi agar rapi
                $purpose = \Illuminate\Support\Str::limit((string) $v->purpose, 140);
            @endphp

            <tr>
                <td class="nowrap">{{ optional($v->arrived_at)->format('Y-m-d H:i') }}</td>

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

                <td class="wrap">{{ $emailLabel }}</td>
                <td class="nowrap">{{ $v->service_type }}</td>
                <td class="wrap">{{ $purpose }}</td>
                <td class="nowrap">{{ $status }}</td>
                <td class="nowrap">{{ $surveyFilled ? 'Sudah' : 'Belum' }}</td>
                <td class="wrap">{{ $v->handler?->name ?? '-' }}</td>
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
