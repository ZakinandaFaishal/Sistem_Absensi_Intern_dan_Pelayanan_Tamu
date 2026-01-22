<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Magang</title>

    <style>
        /* ===== PAGE ===== */
        @page {
            size: A4 landscape;
            margin: 14mm; /* lebih kecil biar ruang konten aman */
        }

        html, body {
            height: 100%;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            color: #111827;
            background: #ffffff;
        }

        /* ===== FRAME ===== */
        .certificate-outer {
            height: 100%;
            border: 4px solid #111827;
            padding: 7mm;              /* diperkecil supaya tidak overflow */
        }

        .certificate-inner {
            position: relative;
            height: 100%;
            border: 3px solid #caa24a;
            padding: 10mm 12mm;        /* diperkecil */
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
            width: 110mm;
            height: auto;
        }

        /* ===== CORNERS ===== */
        .corner {
            position: absolute;
            width: 22mm;
            height: 22mm;
            border: 2px solid #caa24a;
            pointer-events: none;
            z-index: 2;
        }
        .corner--top-left    { top: 6mm; left: 6mm;  border-right: none; border-bottom: none; }
        .corner--top-right   { top: 6mm; right: 6mm; border-left: none;  border-bottom: none; }
        .corner--bottom-left { bottom: 6mm; left: 6mm; border-right: none; border-top: none; }
        .corner--bottom-right{ bottom: 6mm; right: 6mm; border-left: none;  border-top: none; }

        /* ===== RIBBON ===== */
        .ribbon {
            position: absolute;
            top: 0;
            right: 0;
            width: 0;
            height: 0;
            border-top: 34mm solid #caa24a;
            border-left: 34mm solid transparent;
            pointer-events: none;
            z-index: 1;
        }
        .ribbon--dark {
            border-top-color: #111827;
            border-top-width: 24mm;
            border-left-width: 24mm;
        }

        /* ===== LAYER ===== */
        .content-layer {
            position: relative;
            height: 100%;
            z-index: 3;
        }

        /* ===== HEADER ===== */
        .header {
            text-align: center;
        }
        .header__logo {
            width: 16mm;
            height: auto;
            margin: 0 auto 3mm;
            display: block;
        }
        .header__organization {
            font-weight: 800;
            font-size: 16px;
            letter-spacing: 0.7px;
            text-transform: uppercase;
        }
        .header__department {
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            margin-top: 1mm;
        }

        /* ===== MEDAL ===== */
        .medal {
            margin: 6mm auto 0;  /* diperkecil */
            width: 20mm;
            height: 20mm;
            border-radius: 50%;
            border: 3px solid #111827;
            background: #caa24a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 900;
            color: #111827;
        }

        /* ===== TITLE ===== */
        .title {
            text-align: center;
            margin-top: 4mm; /* diperkecil */
        }
        .title__main {
            font-size: 34px;
            font-weight: 900;
            letter-spacing: 3px;
            color: #caa24a;
            text-transform: uppercase;
        }
        .title__number {
            margin-top: 2mm;
            font-size: 12px;
            font-weight: 700;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            text-align: center;
            margin-top: 7mm;    /* diperkecil */
            padding: 0 10mm;    /* diperkecil */
        }
        .main-content__label {
            font-size: 13px;
            margin-bottom: 2.5mm;
        }
        .main-content__name {
            font-size: 34px;
            font-weight: 900;
            margin: 2mm 0;
        }
        .main-content__underline {
            width: 120mm;
            margin: 0 auto 2.5mm;
            border-top: 2px solid #111827;
        }
        .main-content__institution {
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 3.5mm;
        }
        .main-content__description {
            font-size: 12px;
            line-height: 1.65;
        }

        /* ===== SIGNATURE ===== */
        .signature {
            position: absolute;
            right: 14mm;
            bottom: 12mm; /* dinaikkan sedikit biar aman */
            width: 88mm;
            text-align: center;
            z-index: 3;
        }
        .signature__role {
            font-weight: 800;
            font-size: 10px;
            text-transform: uppercase;
            line-height: 1.35;
        }
        .signature__space {
            height: 18mm; /* diperkecil agar tidak overflow */
        }
        .signature__name {
            font-size: 12px;
            font-weight: 900;
            text-decoration: underline;
        }
        .signature__meta {
            margin-top: 1.5mm;
            font-size: 10px;
            color: #374151;
            line-height: 1.35;
        }
    </style>
</head>

<body>
<div class="certificate-outer">
    <div class="certificate-inner">

        <!-- Ornamen -->
        <div class="corner corner--top-left"></div>
        <div class="corner corner--top-right"></div>
        <div class="corner corner--bottom-left"></div>
        <div class="corner corner--bottom-right"></div>

        <div class="ribbon"></div>
        <div class="ribbon ribbon--dark"></div>

        <!-- Watermark -->
        <div class="watermark">
            <img src="{{ public_path('img/logo kab.mgl.png') }}" alt="">
        </div>

        <!-- Content -->
        <div class="content-layer">
            <header class="header">
                <img class="header__logo" src="{{ public_path('img/logo kab.mgl.png') }}" alt="">
                <div class="header__organization">Pemerintah Kabupaten Magelang</div>
                <div class="header__department">Dinas Komunikasi dan Informatika</div>
            </header>

            <div class="medal">★</div>

            <section class="title">
                <div class="title__main">Sertifikat</div>
                <div class="title__number">Nomor: {{ $certificateNo }}</div>
            </section>

            <section class="main-content">
                <div class="main-content__label">Diberikan kepada:</div>
                <div class="main-content__name">{{ $user->name }}</div>
                <div class="main-content__underline"></div>

                <div class="main-content__institution">
                    {{ $user->institution ?? 'Mahasiswa / Peserta Magang' }}
                </div>

                <div class="main-content__description">
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
                </div>
            </section>

            <aside class="signature">
                <div class="signature__role">
                    Kepala Dinas Komunikasi dan Informatika<br>
                    Kabupaten Magelang
                </div>

                <div class="signature__space"></div>

                <div class="signature__name">{{ $signatoryName }}</div>
                <div class="signature__meta">
                    {{ $signatoryRank ?? 'Pembina Tingkat I' }}<br>
                    NIP. {{ $signatoryNip ?? '-' }}
                </div>
            </aside>

        </div>
    </div>
</div>
</body>

</html>
