<x-guest-layout>
    <form method="POST" action="{{ route('attendance.scan.store') }}" class="space-y-4" id="attendance-scan-form">
        @csrf
        <input type="hidden" name="k" value="{{ $token }}" />
        <input type="hidden" name="lat" id="geo-lat" value="" />
        <input type="hidden" name="lng" id="geo-lng" value="" />
        <input type="hidden" name="accuracy_m" id="geo-accuracy" value="" />

        <div>
            <h1 class="text-lg font-semibold">Presensi Magang</h1>
            <p class="text-sm text-gray-600">
                Lokasi akan diambil dari GPS HP saat tombol presensi ditekan.
            </p>
            <p class="text-xs text-gray-500" id="geo-status">Mengecek lokasi…</p>
        </div>

        @if ($errors->any())
            <div class="rounded border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="flex gap-3">
            <button name="action" value="in"
                class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-800 text-white rounded">
                Check-in
            </button>
            <button name="action" value="out"
                class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-800 text-white rounded">
                Check-out
            </button>
        </div>

        <div class="flex flex-wrap gap-2 pt-1">
            <a href="{{ route('attendance.qr') }}" class="text-sm text-gray-700 underline">
                Scan ulang QR
            </a>
            <span class="text-sm text-gray-400">•</span>
            <a href="{{ route('dashboard') }}" class="text-sm text-gray-700 underline">
                Kembali ke Dashboard
            </a>
        </div>
    </form>

    <script>
        (function() {
            const statusEl = document.getElementById('geo-status');
            const latEl = document.getElementById('geo-lat');
            const lngEl = document.getElementById('geo-lng');
            const accEl = document.getElementById('geo-accuracy');
            const form = document.getElementById('attendance-scan-form');

            function setStatus(text) {
                if (statusEl) statusEl.textContent = text;
            }

            function getLocation() {
                if (!('geolocation' in navigator)) {
                    setStatus('Browser tidak mendukung GPS.');
                    return;
                }

                setStatus('Meminta izin lokasi…');

                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        latEl.value = String(pos.coords.latitude);
                        lngEl.value = String(pos.coords.longitude);
                        accEl.value = pos.coords.accuracy != null ? String(Math.round(pos.coords.accuracy)) : '';
                        setStatus('Lokasi terdeteksi.');
                    },
                    (err) => {
                        setStatus('Gagal mengambil lokasi. Aktifkan GPS dan izinkan akses lokasi.');
                        console.warn(err);
                    }, {
                        enableHighAccuracy: true,
                        maximumAge: 0,
                        timeout: 10000,
                    }
                );
            }

            getLocation();

            form?.addEventListener('submit', function(e) {
                if (!latEl.value || !lngEl.value) {
                    e.preventDefault();
                    setStatus('Lokasi belum tersedia. Coba lagi & pastikan izin lokasi aktif.');
                    getLocation();
                }
            });
        })();
    </script>
</x-guest-layout>
