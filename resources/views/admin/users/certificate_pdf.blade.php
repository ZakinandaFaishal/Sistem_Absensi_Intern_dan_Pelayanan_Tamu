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
            padding: 10mm;
            box-sizing: border-box;
        }

        .inner {
            position: relative;
            border: 3px solid #caa24a; /* emas */
            padding: 12mm 14mm;
            min-height: 165mm;
            box-sizing: border-box;
            overflow: hidden;
        }

        /* ===== WATERMARK ===== */
        .watermark {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.05;
            pointer-events: none;
            z-index: 0;
        }

        .watermark img {
            width: 120mm;
            height: auto;
        }

        /* ===== CORNER ORNAMENT ===== */
        .corner {
            position: absolute;
            width: 24mm;
            height: 24mm;
            border: 2px solid #caa24a;
            pointer-events: none;
            z-index: 2;
        }

        .corner.tl { top: 6mm; left: 6mm; border-right: none; border-bottom: none; }
        .corner.tr { top: 6mm; right: 6mm; border-left: none; border-bottom: none; }
        .corner.bl { bottom: 6mm; left: 6mm; border-right: none; border-top: none; }
        .corner.br { bottom: 6mm; right: 6mm; border-left: none; border-top: none; }

        /* ===== RIBBON (TOP RIGHT) ===== */
        .ribbon {
            position: absolute;
            top: 0;
            right: 0;
            width: 0;
            height: 0;
            border-top: 36mm solid #caa24a;
            border-left: 36mm solid transparent;
            z-index: 1;
            pointer-events: none;
        }

        .ribbon.dark {
            border-top-color: #111827;
            border-top-width: 26mm;
            border-left-width: 26mm;
        }

        /* ===== CONTENT LAYER ===== */
        .layer {
            position: relative;
            z-index: 3; /* di atas watermark */
        }

        /* ===== HEADER ===== */
        .header {
            text-align: center;
            margin-top: 2mm;
        }

        .logo {
            width: 18mm;
            height: auto;
            margin: 0 auto 4mm;
            display: block;
            /* Dompdf tidak selalu support filter, jadi aman tanpa efek */
        }

        .org {
            font-weight: 800;
            font-size: 18px;
            letter-spacing: 0.8px;
            margin: 0;
            text-transform: uppercase;
        }

        .dept {
            font-weight: 600;
            font-size: 13px;
            margin: 2px 0 0;
            text-transform: uppercase;
        }

        /* ===== MEDAL ===== */
        .medal {
            margin: 8mm auto 0;
            width: 22mm;
            height: 22mm;
            border-radius: 50%;
            border: 3px solid #111827;
            background: #caa24a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 900;
            color: #111827;
        }

        /* ===== TITLE ===== */
        .title {
            text-align: center;
            margin-top: 6mm;
        }

        .title-main {
            font-size: 36px;
            font-weight: 900;
            letter-spacing: 3px;
            color: #caa24a;
            margin: 0;
            text-transform: uppercase;
        }

        .title-no {
            margin-top: 2mm;
            font-size: 12px;
            font-weight: 700;
        }

        /* ===== CONTENT ===== */
        .content {
            text-align: center;
            margin-top: 10mm;
            padding: 0 12mm;
        }

        .given {
            font-size: 14px;
            margin: 0 0 3mm;
        }

        .name {
            font-size: 36px;
            font-weight: 900;
            margin: 3mm 0;
        }

        .name-line {
            width: 120mm;
            margin: 0 auto 3mm;
            border-top: 2px solid #111827;
        }

        .sub {
            font-size: 13px;
            font-weight: 600;
            margin: 0 0 4mm;
        }

        .desc {
            font-size: 13px;
            line-height: 1.7;
            margin: 0;
        }

        /* ===== SIGNATURE ===== */
        .sign {
            position: absolute;
            right: 16mm;
            bottom: 18mm;
            width: 90mm;
            text-align: center;
            font-size: 11px;
            z-index: 3;
        }

        .role {
            font-weight: 800;
            font-size: 10px;
            text-transform: uppercase;
            line-height: 1.4;
        }

        .space {
            height: 22mm;
        }

        .sign-name {
            font-size: 12px;
            font-weight: 900;
            text-decoration: underline;
        }

        .meta {
            margin-top: 2px;
            font-size: 10px;
            color: #374151;
        }
    </style>
</head>

<body>
<div class="outer">
    <div class="inner">

        {{-- Ornament --}}
        <div class="corner tl"></div>
        <div class="corner tr"></div>
        <div class="corner bl"></div>
        <div class="corner br"></div>
        <div class="ribbon"></div>
        <div class="ribbon dark"></div>

        {{-- Watermark (logo transparan) --}}
        <div class="watermark">
            <img src="{{ public_path('img/logo kab.mgl.png') }}" alt="Watermark Logo Kabupaten Magelang">
        </div>

        <div class="layer">
            {{-- Header --}}
            <div class="header">
                <img class="logo"
                     src="{{ public_path('img/logo kab.mgl.png') }}"
                     alt="Logo Kabupaten Magelang">

                <p class="org">PEMERINTAH KABUPATEN MAGELANG</p>
                <p class="dept">DINAS KOMUNIKASI DAN INFORMATIKA</p>
            </div>

            {{-- Medal --}}
            <div class="medal">★</div>

            {{-- Title --}}
            <div class="title">
                <p class="title-main">SERTIFIKAT</p>
                <div class="title-no">NOMOR : {{ $certificateNo }}</div>
            </div>

            {{-- Content --}}
            <div class="content">
                <p class="given">Diberikan kepada :</p>

                <div class="name">{{ $user->name }}</div>
                <div class="name-line"></div>

                <div class="sub">
                    {{ $user->institution ?? 'Mahasiswa / Peserta Magang' }}
                </div>

                <p class="desc">
                    Telah menyelesaikan kegiatan Magang di
                    <strong>Dinas Komunikasi dan Informatika Kabupaten Magelang</strong>
                    @if ($user->internship_start_date && $user->internship_end_date)
                        selama
                        <strong>
                            {{ \Carbon\Carbon::parse($user->internship_start_date)->diffInDays(\Carbon\Carbon::parse($user->internship_end_date)) + 1 }}
                            hari
                        </strong>
                        terhitung sejak
                        <strong>{{ optional($user->internship_start_date)->format('d F Y') }}</strong>
                        sampai dengan
                        <strong>{{ optional($user->internship_end_date)->format('d F Y') }}</strong>.
                    @else
                        sesuai dengan periode yang berlaku.
                    @endif
                </p>
            </div>

            {{-- Signature --}}
            <div class="sign">
                <div class="role">
                    KEPALA DINAS KOMUNIKASI DAN INFORMATIKA<br>
                    KABUPATEN MAGELANG
                </div>

                <div class="space"></div>

                <div class="sign-name">{{ $signatoryName }}</div>
                <div class="meta">
                    {{ $signatoryRank ?? 'Pembina Tingkat I' }}<br>
                    NIP. {{ $signatoryNip ?? '-' }}
                </div>
            </div>
        </div>

    </div>
</div>
</body>
</html>
