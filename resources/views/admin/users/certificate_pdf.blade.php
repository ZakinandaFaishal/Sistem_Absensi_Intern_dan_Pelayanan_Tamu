<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sertifikat Magang</title>
    <style>
        @page {
            margin: 36px 42px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #0f172a;
        }

        .border {
            border: 2px solid #0f172a;
            padding: 22px;
        }

        .topline {
            border-top: 1px solid #cbd5e1;
            margin: 18px 0;
        }

        .title {
            text-align: center;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .subtitle {
            text-align: center;
            font-size: 14px;
            color: #334155;
            margin-top: 4px;
        }

        .no {
            text-align: center;
            font-size: 12px;
            color: #475569;
            margin-top: 10px;
        }

        .content {
            margin-top: 18px;
            font-size: 14px;
            line-height: 1.6;
        }

        .name {
            text-align: center;
            font-size: 24px;
            font-weight: 800;
            margin: 16px 0 6px;
        }

        .muted {
            color: #475569;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        .table td {
            padding: 6px 0;
            vertical-align: top;
        }

        .label {
            width: 180px;
            color: #334155;
        }

        .sign {
            margin-top: 26px;
            width: 100%;
        }

        .sign td {
            vertical-align: top;
        }

        .sign-box {
            text-align: right;
        }

        .sign-name {
            margin-top: 60px;
            font-weight: 700;
            text-decoration: underline;
        }

        .footer {
            margin-top: 18px;
            font-size: 10px;
            color: #64748b;
        }
    </style>
</head>

<body>
    <div class="border">
        <div class="title">SERTIFIKAT MAGANG</div>
        <div class="subtitle">Sistem Absensi Intern dan Pelayanan Tamu</div>
        <div class="no">Nomor: {{ $certificateNo }}</div>

        <div class="topline"></div>

        <div class="content">
            Dengan ini menerangkan bahwa:
            <div class="name">{{ $user->name }}</div>

            <table class="table">
                <tr>
                    <td class="label">NIK</td>
                    <td>: {{ $user->nik ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Lokasi / Dinas</td>
                    <td>: {{ $user->internshipLocation?->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Periode Magang</td>
                    <td>:
                        @if ($user->internship_start_date && $user->internship_end_date)
                            {{ optional($user->internship_start_date)->format('d M Y') }} s/d
                            {{ optional($user->internship_end_date)->format('d M Y') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </table>

            <p class="muted" style="margin-top: 14px;">
                Sertifikat ini diberikan sebagai bukti telah menyelesaikan kegiatan magang sesuai ketentuan yang
                berlaku.
            </p>
        </div>

        <table class="sign">
            <tr>
                <td></td>
                <td class="sign-box">
                    <div>{{ $issuedAt->format('d M Y') }}</div>
                    <div class="muted" style="margin-top: 4px;">{{ $signatoryTitle }}</div>
                    <div class="sign-name">{{ $signatoryName }}</div>
                </td>
            </tr>
        </table>

        <div class="footer">
            Dokumen ini dihasilkan otomatis oleh sistem.
        </div>
    </div>
</body>

</html>
