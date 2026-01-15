<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Users</title>
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
    <h1>Laporan Users</h1>
    <div class="muted">Generated: {{ $generatedAt->format('Y-m-d H:i:s') }}</div>
    <div class="muted small">Total: {{ $users->count() }} user</div>

    <table>
        <thead>
            <tr>
                <th style="width: 18%">Nama</th>
                <th style="width: 12%">Username</th>
                <th style="width: 18%">Email</th>
                <th style="width: 10%">Role</th>
                <th style="width: 10%">Status</th>
                <th class="right" style="width: 8%">Hadir</th>
                <th class="right" style="width: 8%">Nilai</th>
                <th style="width: 16%">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $u)
                @php
                    $active = (bool) ($u->active ?? true);
                    $role = (string) ($u->role ?? 'intern');
                    $internStatus = (string) ($u->intern_status ?? 'aktif');
                    $attended = (int) ($u->attended_days ?? 0);
                    $score = $u->computed_score;
                    $note = '';
                    if ($role === 'intern') {
                        $note = $u->computed_score_is_override
                            ? ((string) ($u->score_override_note ?? 'Override'))
                            : 'Auto';
                        if (!empty($u->internship_start_date) && !empty($u->internship_end_date)) {
                            $note .= ' · ' . $u->internship_start_date . ' s/d ' . $u->internship_end_date;
                        }
                    }
                @endphp
                <tr>
                    <td>{{ $u->name }}</td>
                    <td class="nowrap">{{ $u->username }}</td>
                    <td>{{ $u->email }}</td>
                    <td class="nowrap">{{ strtoupper($role) }}</td>
                    <td class="nowrap">
                        {{ $active ? 'Aktif' : 'Nonaktif' }}
                        @if ($role === 'intern')
                            · {{ $internStatus }}
                        @endif
                    </td>
                    <td class="right">{{ $role === 'intern' ? $attended : '-' }}</td>
                    <td class="right">{{ $role === 'intern' && $score !== null ? $score : '-' }}</td>
                    <td>{{ $note !== '' ? \Illuminate\Support\Str::limit($note, 60) : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
