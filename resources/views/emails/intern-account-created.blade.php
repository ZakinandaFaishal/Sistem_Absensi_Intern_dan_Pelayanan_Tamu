<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akun SIMANTA</title>
</head>

<body
    style="margin:0;padding:0;background:#f8fafc;font-family:ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial;">
    <div style="max-width:640px;margin:0 auto;padding:24px;">
        <div style="background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">
            <div style="padding:18px 20px;background:#0f172a;color:#fff;">
                <div style="font-weight:800;letter-spacing:.3px;">SIMANTA</div>
                <div style="opacity:.8;font-size:12px;margin-top:2px;">Sistem Informasi Manajemen Magang &amp; Tamu</div>
            </div>

            <div style="padding:20px;color:#0f172a;">
                <p style="margin:0 0 12px 0;">Yth. <b>{{ $recipientName }}</b>,</p>
                <p style="margin:0 0 12px 0;">Akun Anda telah dibuat oleh admin (<b>{{ $createdByName }}</b>). Berikut
                    detail akses awal:</p>

                <div style="border:1px solid #e2e8f0;background:#f8fafc;border-radius:12px;padding:14px;">
                    <div style="font-size:12px;color:#475569;">Username</div>
                    <div style="font-size:16px;font-weight:800;">{{ $username }}</div>

                    <div style="height:10px;"></div>

                    <div style="font-size:12px;color:#475569;">Email</div>
                    <div style="font-size:14px;font-weight:700;">{{ $email }}</div>

                    <div style="height:10px;"></div>

                    <div style="font-size:12px;color:#475569;">Password sementara</div>
                    <div style="font-size:16px;font-weight:800;letter-spacing:.3px;">{{ $temporaryPassword }}</div>
                </div>

                <p style="margin:14px 0 0 0;font-size:13px;color:#334155;">
                    Link login: <a href="{{ $loginUrl }}" style="color:#0ea5e9;">{{ $loginUrl }}</a>
                </p>

                <p style="margin:8px 0 0 0;font-size:12px;color:#64748b;">
                    Jika link di atas tidak sesuai (mis. URL tunnel berubah), buka website yang sedang Anda akses lalu
                    ketik <b>/login</b>.
                </p>

                <hr style="border:none;border-top:1px solid #e2e8f0;margin:18px 0;" />

                <p style="margin:0 0 8px 0;font-weight:800;">Langkah setelah login</p>
                <ol style="margin:0 0 0 18px;padding:0;color:#334155;font-size:13px;line-height:1.5;">
                    <li>Login menggunakan username &amp; password sementara.</li>
                    <li>Lengkapi profil dengan <b>Nomor Surat e-Pikir</b> (wajib), contoh: <b>070/028/16/2026</b>.</li>
                    <li>Setelah itu, lakukan <b>ganti password</b>.</li>
                </ol>

                <p style="margin:16px 0 0 0;font-size:12px;color:#64748b;">
                    Catatan: Demi keamanan, segera ganti password setelah login.
                </p>
            </div>
        </div>

        <div style="margin-top:14px;font-size:12px;color:#64748b;text-align:center;">
            Email otomatis dari SIMANTA.
        </div>
    </div>
</body>

</html>
