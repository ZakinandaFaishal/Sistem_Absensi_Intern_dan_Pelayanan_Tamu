<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Magang</title>

    <style>
        /* ===== PAGE ===== */
        @page { size: A4 landscape; margin: 14mm; }

        html, body { height: 100%; }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: "DejaVu Sans", Arial, sans-serif;
            color: #0f172a;
            background: #ffffff;
            text-rendering: geometricPrecision;
            -webkit-print-color-adjust: exact;
        }

        /* ===== COLORS ===== */
        :root{
            --gold: #caa24a;
            --gold2:#b88a2a;
            --dark: #0b1324;
            --ink:  #0f172a;
            --muted:#475569;
        }

        /* ===== OUTER FRAME ===== */
        .certificate-outer{
            height: 100%;
            border: 2px solid var(--gold);
            padding: 6mm;
        }

        .certificate-inner{
            position: relative;
            height: 100%;
            border: 1.5px solid var(--gold);
            padding: 12mm 14mm;
            overflow: hidden;
        }

        /* inner inset line like formal certificate */
        .certificate-inner:before{
            content:"";
            position:absolute;
            inset: 6mm;
            border: 1px solid rgba(202,162,74,.55);
            pointer-events:none;
            z-index:1;
        }

        /* ===== DIAGONAL ACCENT (TOP-RIGHT) ===== */
        .diag-top{
            position:absolute;
            top:-1px;
            right:-1px;
            width: 56mm;
            height: 56mm;
            z-index:2;
            pointer-events:none;
            background:
                linear-gradient(135deg, rgba(0,0,0,0) 52%, var(--dark) 52% 100%),
                linear-gradient(135deg, rgba(0,0,0,0) 60%, var(--gold) 60% 78%, rgba(0,0,0,0) 78% 100%);
            opacity: .95;
        }

        /* ===== WATERMARK (LEFT) ===== */
        .watermark{
            position:absolute;
            left: -12mm;
            top: 8mm;
            width: 155mm;
            height: auto;
            opacity: .06;
            z-index:0;
            pointer-events:none;
        }

        /* ===== CONTENT LAYER ===== */
        .content{
            position:relative;
            z-index:3;
            height: 100%;
        }

        /* ===== HEADER ===== */
        .header{
            text-align:center;
            margin-top: 0mm;
        }

        .header__logo{
            width: 18mm;
            height: auto;
            display:block;
            margin: 0 auto 4mm;
        }

        .header__org{
            font-weight: 900;
            letter-spacing: .9px;
            text-transform: uppercase;
            font-size: 15px;
        }

        .header__dept{
            font-weight: 700;
            letter-spacing: .6px;
            text-transform: uppercase;
            font-size: 11px;
            color: var(--ink);
            margin-top: 1mm;
        }

        /* ===== TITLE ===== */
        .title{
            text-align:center;
            margin-top: 6mm; /* lebih rapi tanpa bulatan */
        }

        .title__main{
            font-family: "DejaVu Serif", "Times New Roman", serif;
            font-weight: 900;
            font-size: 36px;
            letter-spacing: 3px;
            color: var(--gold2);
            text-transform: uppercase;
            line-height: 1.05;
        }

        .title__no{
            margin-top: 3mm;
            font-size: 11px;
            font-weight: 700;
            color: var(--ink);
        }

        .title__no span{ font-weight: 800; }

        /* ===== BODY ===== */
        .body{
            text-align:center;
            margin-top: 7mm;
            padding: 0 18mm;
        }

        .body__label{
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 2.5mm;
        }

        .body__name{
            font-family: "DejaVu Serif", "Times New Roman", serif;
            font-weight: 900;
            font-size: 31px;
            color: var(--ink);
            line-height: 1.12;
            word-break: break-word;
        }

        .body__line{
            width: 140mm;
            margin: 3mm auto 3mm;
            border-top: 2px solid var(--ink);
            opacity: .9;
        }

        .body__inst{
            font-size: 11px;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 5mm;
        }

        .body__desc{
            font-size: 11px;
            line-height: 1.65;
            color: var(--ink);
            max-width: 210mm;
            margin: 0 auto;
        }

        .body__desc strong{ font-weight: 800; }

        /* ===== SIGNATURE BLOCK (RIGHT-BOTTOM, NO OVERLAP) ===== */
        .signature{
            position:absolute;
            right: 16mm;
            bottom: 14mm;     /* sedikit dinaikkan biar lebih rapi */
            width: 92mm;
            text-align:center;
        }

        .signature__role{
            font-size: 9.5px;
            font-weight: 800;
            text-transform: uppercase;
            line-height: 1.35;
            color: var(--ink);
        }

        .signature__space{ height: 18mm; }

        .signature__name{
            font-size: 12px;
            font-weight: 900;
            font-family: "DejaVu Serif", "Times New Roman", serif;
            text-transform: uppercase;
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .signature__meta{
            margin-top: 1.5mm;
            font-size: 9px;
            color: var(--muted);
            line-height: 1.35;
        }

        /* ===== SAFE SPACE (prevents overlap with signature) ===== */
        .bottom-safe{
            height: 32mm; /* pas untuk blok tanda tangan */
        }
    </style>
</head>

<body>
@php
    // ===== LOGO BASE64 (AMANKAN UNTUK DOMPDF) =====
    $logoPath = public_path('img/logo_kab_mgl.png');
    $logoBase64 = '';
    if (file_exists($logoPath)) {
        $ext  = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
        $mime = in_array($ext, ['jpg','jpeg']) ? 'image/jpeg' : 'image/png';
        $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($logoPath));
    }

    // ===== TANGGAL MAGANG =====
    $start = $user->internship_start_date ? \Carbon\Carbon::parse($user->internship_start_date) : null;
    $end   = $user->internship_end_date ? \Carbon\Carbon::parse($user->internship_end_date) : null;
    $days  = ($start && $end) ? $start->diffInDays($end) + 1 : null;
@endphp

<div class="certificate-outer">
    <div class="certificate-inner">

        <!-- diagonal accent -->
        <div class="diag-top"></div>

        <!-- watermark kiri -->
        @if($logoBase64)
            <img class="watermark" src="{{ $logoBase64 }}" alt="Watermark">
        @endif

        <div class="content">

            <!-- header -->
            <div class="header">
                @if($logoBase64)
                    <img class="header__logo" src="{{ $logoBase64 }}" alt="Logo">
                @endif
                <div class="header__org">Pemerintah Kabupaten Magelang</div>
                <div class="header__dept">Dinas Komunikasi dan Informatika</div>
            </div>

            <!-- title -->
            <div class="title">
                <div class="title__main">Sertifikat</div>
                <div class="title__no">Nomor: <span>{{ $certificateNo }}</span></div>
            </div>

            <!-- body -->
            <div class="body">
                <div class="body__label">Diberikan kepada:</div>
                <div class="body__name">{{ $user->name }}</div>
                <div class="body__line"></div>

                <div class="body__inst">
                    {{ $user->institution ?? 'Mahasiswa / Peserta Magang' }}
                </div>

                <div class="body__desc">
                    Telah menyelesaikan kegiatan Magang di
                    <strong>Dinas Komunikasi dan Informatika Kabupaten Magelang</strong>
                    @if ($start && $end)
                        selama <strong>{{ $days }} hari</strong>,
                        terhitung sejak <strong>{{ $start->translatedFormat('d F Y') }}</strong>
                        sampai dengan <strong>{{ $end->translatedFormat('d F Y') }}</strong>.
                    @else
                        sesuai dengan periode yang berlaku.
                    @endif
                </div>

                <!-- spacer supaya signature tidak overlap -->
                <div class="bottom-safe"></div>
            </div>

            <!-- signature -->
            <div class="signature">
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
            </div>

        </div>
    </div>
</div>
</body>
</html>
