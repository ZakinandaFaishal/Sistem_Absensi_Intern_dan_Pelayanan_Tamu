<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Sertifikat Magang</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 18mm;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            background: #ffffff;
        }

        /* ===== FRAME ===== */
        .outer {
            border: 4px solid #111827;
            padding: 12mm;
            box-sizing: border-box;
        }

        .inner {
            position: relative;
            border: 3px solid #b78b2a;
            /* emas */
            padding: 10mm 12mm;
            box-sizing: border-box;
            min-height: 160mm;
            /* supaya stabil */
        }

        /* ===== DECOR (corner emas sederhana) ===== */
        .corner {
            position: absolute;
            width: 26mm;
            height: 26mm;
            border: 2px solid #b78b2a;
            pointer-events: none;
        }

        .corner.tl {
            top: 6mm;
            left: 6mm;
            border-right: none;
            border-bottom: none;
        }

        .corner.tr {
            top: 6mm;
            right: 6mm;
            border-left: none;
            border-bottom: none;
        }

        .corner.bl {
            bottom: 6mm;
            left: 6mm;
            border-right: none;
            border-top: none;
        }

        .corner.br {
            bottom: 6mm;
            right: 6mm;
            border-left: none;
            border-top: none;
        }

        /* ===== TOP RIGHT RIBBON ===== */
        .ribbon1 {
            position: absolute;
            top: 0;
            right: 0;
            width: 0;
            height: 0;
            border-top: 34mm solid rgba(183, 139, 42, .95);
            border-left: 34mm solid transparent;
        }

        .ribbon2 {
            position: absolute;
            top: 0;
            right: 0;
            width: 0;
            height: 0;
            border-top: 24mm solid rgba(17, 24, 39, .85);
            border-left: 24mm solid transparent;
        }

        /* ===== HEADER ===== */
        .header {
            text-align: center;
            margin-top: 2mm;
        }

        .logo {
            margin: 0 auto 4mm;
            width: 18mm;
            height: 18mm;
        }

        .h1 {
            font-family: DejaVu Sans, sans-serif;
            font-weight: 800;
            font-size: 18px;
            letter-spacing: .5px;
            margin: 0;
            text-transform: uppercase;
        }

        .h2 {
            font-weight: 600;
            font-size: 13px;
            margin: 3px 0 0;
            text-transform: uppercase;
        }

        /* ===== TITLE ===== */
        .title {
            text-align: center;
            margin-top: 10mm;
        }

        .title .main {
            font-family: DejaVu Sans, sans-serif;
            font-weight: 900;
            font-size: 34px;
            letter-spacing: 2px;
            color: #b78b2a;
            margin: 0;
            text-transform: uppercase;
        }

        .title .no {
            margin-top: 2mm;
            font-size: 12px;
            font-weight: 700;
        }

        /* ===== CONTENT ===== */
        .content {
            text-align: center;
            margin-top: 10mm;
            padding: 0 10mm;
        }

        .give {
            font-size: 14px;
            margin: 0;
        }

        .name {
            margin: 6mm 0 2mm;
            font-family: DejaVu Sans, sans-serif;
            font-weight: 900;
            font-size: 34px;
            color: #111827;
        }

        .name-line {
            width: 120mm;
            border-top: 2px solid #111827;
            margin: 0 auto 3mm;
        }

        .sub {
            font-size: 13px;
            margin: 0;
            font-weight: 600;
        }

        .desc {
            margin-top: 5mm;
            font-size: 13px;
            line-height: 1.6;
        }

        /* ===== SIGNATURE BLOCK ===== */
        .sign {
            position: absolute;
            right: 14mm;
            bottom: 16mm;
            width: 90mm;
            text-align: center;
            font-size: 11px;
        }

        .sign .role {
            font-weight: 800;
            text-transform: uppercase;
            font-size: 10px;
            line-height: 1.3;
        }

        .ttd-space {
            height: 22mm;
        }

        .sign .sign-name {
            margin-top: 2mm;
            font-weight: 900;
            text-decoration: underline;
            font-size: 12px;
        }

        .sign .meta {
            margin-top: 2px;
            color: #374151;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="outer">
        <div class="inner">
            <!-- decorations -->
            <div class="corner tl"></div>
            <div class="corner tr"></div>
            <div class="corner bl"></div>
            <div class="corner br"></div>
            <div class="ribbon1"></div>
            <div class="ribbon2"></div>

            <!-- header -->
            <div class="header">
                {{-- Logo: ganti path sesuai file kamu --}}
                {{-- Jika mau pakai gambar: pastikan file ada di public/img/logo_kab_mgl.png --}}
                <img class="logo" src="{{ public_path('img/logo_kab_mgl.png') }}" alt="Logo">
                <p class="h1">PEMERINTAH KABUPATEN MAGELANG</p>
                <p class="h2">DINAS KOMUNIKASI DAN INFORMATIKA</p>
            </div>

            <!-- title -->
            <div class="title">
                <p class="main">SERTIFIKAT</p>
                <div class="no">NOMOR : {{ $certificateNo }}</div>
            </div>

            <!-- content -->
            <div class="content">
                <p class="give">Diberikan kepada :</p>

                <div class="name">{{ $user->name }}</div>
                <div class="name-line"></div>

                <p class="sub">
                    {{ $user->institution ?? 'Mahasiswa / Peserta Magang' }}
                </p>

                <div class="desc">
                    Telah menyelesaikan Magang di Dinas Komunikasi dan Informatika Kabupaten Magelang
                    @if ($user->internship_start_date && $user->internship_end_date)
                        selama
                        <strong>
                            {{ \Carbon\Carbon::parse($user->internship_start_date)->diffInDays(\Carbon\Carbon::parse($user->internship_end_date)) + 1 }}
                            hari
                        </strong>
                        dari tanggal
                        <strong>{{ optional($user->internship_start_date)->format('d F Y') }}</strong>
                        s/d
                        <strong>{{ optional($user->internship_end_date)->format('d F Y') }}</strong>
                    @else
                        sesuai periode yang berlaku.
                    @endif
                </div>
            </div>

            <!-- signature -->
            <div class="sign">
                <div class="role">
                    KEPALA DINAS KOMUNIKASI DAN INFORMATIKA<br>
                    KABUPATEN MAGELANG
                </div>

                <div class="ttd-space"></div>

                <div class="sign-name">
                    {{ $signatoryName }}
                </div>
                <div class="meta">
                    {{ $signatoryRank ?? 'Pembina Tingkat I' }}<br>
                    NIP. {{ $signatoryNip ?? '-' }}
                </div>
            </div>

        </div>
    </div>
</body>

</html>
