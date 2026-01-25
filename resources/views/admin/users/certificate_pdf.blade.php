<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Sertifikat Magang</title>

    <style>
        /* ===== PAGE (Dompdf friendly, force 1 page) ===== */
        @page {
            size: A4 landscape;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 297mm;
            height: 210mm;
        }

        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            color: #0f172a;
            background: #ffffff;
            -webkit-print-color-adjust: exact;
        }

        :root {
            --gold: #caa24a;
            --ink: #0f172a;
            --muted: #475569;
        }

        .page {
            width: 297mm;
            height: 210mm;
            position: relative;
            overflow: hidden;
            background: #ffffff;
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        /* Border ala contoh (absolute inset) */
        .border-frame {
            position: absolute;
            top: 6mm;
            right: 6mm;
            bottom: 6mm;
            left: 6mm;
            border: 3px solid var(--ink);
            z-index: 2;
            pointer-events: none;
        }

        /* Isi page (mirip p-16) */
        .inner {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 3;
            padding: 14mm 18mm;
        }

        /* Watermark (opsional) */
        .watermark {
            position: absolute;
            left: 14mm;
            top: 24mm;
            width: 120mm;
            height: auto;
            opacity: .06;
            z-index: 1;
            pointer-events: none;
        }

        /* Header */
        .header {
            text-align: center;
            margin-top: 6mm;
        }

        .header__logo {
            width: 18mm;
            height: auto;
            display: inline-block;
            margin-bottom: 3mm;
        }

        .header__org {
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 1.1px;
            text-transform: uppercase;
            color: var(--ink);
        }

        .header__dept {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .7px;
            text-transform: uppercase;
            color: var(--ink);
            margin-top: 1.5mm;
        }

        /* Title */
        .title {
            text-align: center;
            margin-top: 7mm;
        }

        .title__main {
            font-family: "DejaVu Serif", "Times New Roman", serif;
            font-size: 34px;
            font-weight: 900;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: #111827;
        }

        .title__sub {
            margin-top: 2mm;
            font-size: 12px;
            font-weight: 700;
            color: #111827;
        }

        /* Content */
        .content {
            text-align: center;
            margin-top: 14mm;
            padding: 0 18mm;
        }

        .content__label {
            font-size: 12px;
            color: var(--muted);
        }

        .content__name {
            margin-top: 5mm;
            font-family: "DejaVu Serif", "Times New Roman", serif;
            font-size: 28px;
            font-weight: 900;
            text-transform: uppercase;
            color: #111827;
            word-break: break-word;
        }

        .content__line {
            width: 170mm;
            margin: 5mm auto 0;
            border-top: 2px solid #111827;
        }

        .content__desc {
            margin-top: 8mm;
            font-size: 12px;
            line-height: 1.7;
            color: #111827;
        }

        .content__desc strong {
            font-weight: 900;
        }

        /* Footer (dua kolom ala contoh) */
        .footer {
            position: absolute;
            left: 18mm;
            right: 18mm;
            bottom: 16mm;
            height: 55mm;
        }

        .footer__col {
            position: absolute;
            bottom: 0;
            width: 48%;
            text-align: center;
        }

        .footer__col--left {
            left: 0;
        }

        .footer__col--right {
            right: 0;
        }

        .footer__text {
            font-size: 11px;
            color: #111827;
        }

        .footer__role {
            margin-top: 2mm;
            font-size: 11px;
            font-weight: 700;
            color: #111827;
        }

        .footer__space {
            height: 18mm;
        }

        .footer__name {
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            text-decoration: underline;
            text-underline-offset: 2px;
            color: #111827;
        }

        .footer__meta {
            margin-top: 1.5mm;
            font-size: 10px;
            color: var(--muted);
            line-height: 1.35;
        }
    </style>
</head>

<body>
    @php
        // ===== LOGO BASE64 (AMANKAN UNTUK DOMPDF) =====
        $logoPath = public_path('img/logo_kab_mgl.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $ext = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
            $mime = in_array($ext, ['jpg', 'jpeg']) ? 'image/jpeg' : 'image/png';
            $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
        }

        // ===== TANGGAL MAGANG =====
        $start = $user->internship_start_date ? \Carbon\Carbon::parse($user->internship_start_date) : null;
        $end = $user->internship_end_date ? \Carbon\Carbon::parse($user->internship_end_date) : null;
        $days = $start && $end ? $start->diffInDays($end) + 1 : null;

        $printDate = now()->translatedFormat('d F Y');
    @endphp

    <div class="page">
        <div class="border-frame"></div>

        @if ($logoBase64)
            <img class="watermark" src="{{ $logoBase64 }}" alt="">
        @endif

        <div class="inner">
            <div class="header">
                @if ($logoBase64)
                    <img class="header__logo" src="{{ $logoBase64 }}" alt="Logo">
                @endif
                <div class="header__org">PEMERINTAH KABUPATEN MAGELANG</div>
                <div class="header__dept">DINAS KOMUNIKASI DAN INFORMATIKA</div>
            </div>

            <div class="title">
                <div class="title__main">SERTIFIKAT MAGANG</div>
                <div class="title__sub">Nomor: {{ $certificateNo }}</div>
            </div>

            <div class="content">
                <div class="content__label">Diberikan kepada:</div>
                <div class="content__name">{{ $user->name }}</div>
                <div class="content__line"></div>

                <div class="content__desc">
                    Telah melaksanakan kegiatan <strong>Program Magang</strong> di
                    <strong>Dinas Komunikasi dan Informatika Kabupaten Magelang</strong>
                    @if ($start && $end)
                        selama <strong>{{ $days }} hari</strong>, terhitung sejak
                        <strong>{{ $start->translatedFormat('d F Y') }}</strong>
                        sampai dengan
                        <strong>{{ $end->translatedFormat('d F Y') }}</strong>.
                    @else
                        sesuai dengan periode yang berlaku.
                    @endif
                </div>
            </div>

            <div class="footer">
                <div class="footer__col footer__col--left">
                    <div class="footer__text">Mengetahui,</div>
                    <div class="footer__role">Pembimbing Magang</div>
                    <div class="footer__space"></div>
                    <div class="footer__name">(........................)</div>
                </div>

                <div class="footer__col footer__col--right">
                    <div class="footer__text">Magelang, {{ $printDate }}</div>
                    <div class="footer__role">
                        Kepala Dinas Komunikasi dan Informatika<br>
                        Kabupaten Magelang
                    </div>
                    <div class="footer__space"></div>
                    <div class="footer__name">{{ $signatoryName }}</div>
                    <div class="footer__meta">
                        {{ $signatoryRank ?? 'Pembina Tingkat I' }}<br>
                        NIP. {{ $signatoryNip ?? '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
