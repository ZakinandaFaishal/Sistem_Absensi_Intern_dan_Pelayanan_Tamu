<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-xl leading-tight
                           bg-gradient-to-r from-fuchsia-600 via-pink-500 to-rose-500
                           bg-clip-text text-transparent">
                    Profil Pengguna
                </h2>
                <p class="mt-1 text-sm text-slate-600">
                    Informasi akun yang digunakan pada sistem
                </p>
            </div>
        </div>
    </x-slot>

    <div class="relative min-h-screen py-10
                bg-gradient-to-b from-slate-50 via-white to-slate-50">

        {{-- glow halus --}}
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-40 left-1/4 h-[28rem] w-[28rem] rounded-full bg-fuchsia-100/25 blur-3xl"></div>
            <div class="absolute top-32 right-1/4 h-[28rem] w-[28rem] rounded-full bg-sky-100/25 blur-3xl"></div>
        </div>

        {{-- noise --}}
        <div class="pointer-events-none absolute inset-0 opacity-[0.035]
                    [background-image:radial-gradient(rgba(15,23,42,0.9)_1px,transparent_1px)]
                    [background-size:18px_18px]"></div>

        <div class="relative max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="overflow-hidden rounded-3xl border border-slate-200/60
                        bg-white/45 backdrop-blur-xl
                        shadow-lg shadow-slate-900/5">

                <div class="p-6 sm:p-8">
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl
                                    bg-slate-900/5 ring-1 ring-inset ring-slate-200/70">
                            <span class="text-xl font-bold text-slate-700">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        </div>

                        <div>
                            <h3 class="text-lg font-extrabold text-slate-900">
                                {{ Auth::user()->name }}
                            </h3>
                            <p class="text-sm text-slate-600">
                                {{ Auth::user()->email }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="rounded-2xl border border-slate-200/60 bg-white/50 backdrop-blur p-4">
                            <p class="text-xs font-semibold text-slate-500">Nama Lengkap</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900">
                                {{ Auth::user()->name }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-slate-200/60 bg-white/50 backdrop-blur p-4">
                            <p class="text-xs font-semibold text-slate-500">Email</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900">
                                {{ Auth::user()->email }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-slate-200/60 bg-white/50 backdrop-blur p-4">
                            <p class="text-xs font-semibold text-slate-500">Role</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900 capitalize">
                                {{ Auth::user()->role }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-slate-200/60 bg-white/50 backdrop-blur p-4">
                            <p class="text-xs font-semibold text-slate-500">Status</p>
                            <span class="mt-1 inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                                {{ Auth::user()->active
                                    ? 'bg-emerald-600/10 text-emerald-700 ring-1 ring-inset ring-emerald-200/60'
                                    : 'bg-rose-600/10 text-rose-700 ring-1 ring-inset ring-rose-200/60' }}">
                                {{ Auth::user()->active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>

                    {{-- Menu Absensi --}}
                    <div class="mt-8">
                        <div class="flex items-center gap-4 mb-4">
                            <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            <h3 class="text-lg font-extrabold text-slate-900">
                                Absensi
                            </h3>
                        </div>

                        <div class="rounded-2xl border border-slate-200/60 bg-white/50 backdrop-blur p-6">
                            <p class="text-sm text-slate-600 mb-4">
                                Lakukan scan QR code untuk presensi menggunakan kamera perangkat Anda.
                            </p>

                            <div id="qr-reader" class="mb-4" style="display: none;"></div>

                            <div class="flex gap-3">
                                <button id="start-scan" class="inline-flex items-center justify-center rounded-xl
                                       bg-gradient-to-r from-blue-600 to-indigo-600
                                       px-4 py-2 text-sm font-semibold text-white
                                       shadow-md hover:opacity-95 transition">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Mulai Scan
                                </button>

                                <button id="stop-scan" class="inline-flex items-center justify-center rounded-xl
                                       border border-slate-200 bg-white/70 px-4 py-2
                                       text-sm font-semibold text-slate-700
                                       hover:bg-white transition" style="display: none;">
                                    Stop Scan
                                </button>
                            </div>

                            <div id="attendance-actions" class="mt-4 flex gap-3" style="display: none;">
                                <button id="check-in" class="inline-flex items-center justify-center rounded-xl
                                       bg-gradient-to-r from-green-600 to-emerald-600
                                       px-4 py-2 text-sm font-semibold text-white
                                       shadow-md hover:opacity-95 transition">
                                    Check-In
                                </button>

                                <button id="check-out" class="inline-flex items-center justify-center rounded-xl
                                       bg-gradient-to-r from-red-600 to-rose-600
                                       px-4 py-2 text-sm font-semibold text-white
                                       shadow-md hover:opacity-95 transition">
                                    Check-Out
                                </button>
                            </div>

                            <div id="scan-result" class="mt-4 text-sm text-slate-600"></div>
                        </div>
                    </div>

                </div>

                <div class="flex items-center justify-end gap-3
                            border-t border-slate-200/60
                            bg-white/30 backdrop-blur px-6 py-4">
                    <a
                        href="{{ route('dashboard') }}"
                        class="inline-flex items-center justify-center rounded-xl
                               border border-slate-200 bg-white/70 px-5 py-2.5
                               text-sm font-semibold text-slate-700
                               hover:bg-white transition"
                    >
                        Kembali
                    </a>

                    <a
                        href="{{ route('profile.edit') }}"
                        class="inline-flex items-center justify-center rounded-xl
                               bg-gradient-to-r from-fuchsia-600 to-rose-600
                               px-5 py-2.5 text-sm font-semibold text-white
                               shadow-md hover:opacity-95 transition"
                    >
                        Edit Profil
                    </a>
                </div>

            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode/minified/html5-qrcode.min.js"></script>
    <script>
        let html5QrCode = null;
        let scannedToken = null;

        document.getElementById('start-scan').addEventListener('click', function() {
            const qrReader = document.getElementById('qr-reader');
            qrReader.style.display = 'block';
            this.style.display = 'none';
            document.getElementById('stop-scan').style.display = 'inline-flex';
            document.getElementById('attendance-actions').style.display = 'none';
            document.getElementById('scan-result').innerHTML = '';

            html5QrCode = new Html5Qrcode("qr-reader");
            html5QrCode.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                qrCodeMessage => {
                    // QR code scanned
                    scannedToken = qrCodeMessage;
                    document.getElementById('scan-result').innerHTML = 'QR Code terdeteksi. Pilih aksi presensi:';
                    document.getElementById('attendance-actions').style.display = 'flex';
                    stopScan();
                },
                errorMessage => {
                    // console.log(errorMessage);
                }
            ).catch(err => {
                console.log(`Unable to start scanning, error: ${err}`);
            });
        });

        document.getElementById('stop-scan').addEventListener('click', function() {
            stopScan();
        });

        document.getElementById('check-in').addEventListener('click', function() {
            processAttendance('in');
        });

        document.getElementById('check-out').addEventListener('click', function() {
            processAttendance('out');
        });

        function stopScan() {
            if (html5QrCode) {
                html5QrCode.stop().then(ignore => {
                    html5QrCode.clear();
                }).catch(err => {
                    console.log(`Unable to stop scanning, error: ${err}`);
                });
            }
            document.getElementById('qr-reader').style.display = 'none';
            document.getElementById('start-scan').style.display = 'inline-flex';
            document.getElementById('stop-scan').style.display = 'none';
        }

        function processAttendance(action) {
            if (!scannedToken) {
                document.getElementById('scan-result').innerHTML = 'Token tidak ditemukan.';
                return;
            }

            fetch('/presensi/scan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: new URLSearchParams({
                    k: scannedToken,
                    action: action
                })
            })
            .then(response => {
                if (response.ok) {
                    document.getElementById('scan-result').innerHTML = `Presensi ${action === 'in' ? 'check-in' : 'check-out'} berhasil!`;
                    document.getElementById('attendance-actions').style.display = 'none';
                } else {
                    return response.text().then(text => {
                        document.getElementById('scan-result').innerHTML = 'Error: ' + text;
                    });
                }
            })
            .catch(error => {
                document.getElementById('scan-result').innerHTML = 'Error: ' + error.message;
            });
        }
    </script>
</x-app-layout>
