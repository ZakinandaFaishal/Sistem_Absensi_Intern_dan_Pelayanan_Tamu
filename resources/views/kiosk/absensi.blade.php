<x-kiosk-layout title="Kiosk Absensi">
    <div class="space-y-4">
        @if ($locations->isEmpty())
            <div class="text-sm text-gray-600">
                Belum ada lokasi. Tambahkan 1 data di tabel <b>locations</b> dulu.
            </div>
        @else
            <div class="flex items-center gap-3">
                <label class="text-sm font-medium" for="kiosk-location">Lokasi</label>
                <select id="kiosk-location" class="border-gray-300 rounded" name="location_id">
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col items-center gap-4">
                <canvas id="kiosk-qr" data-token-endpoint="{{ route('kiosk.token') }}"></canvas>
                <div class="text-sm text-gray-600 break-all text-center">
                    <div>Scan QR ini dari HP mahasiswa</div>
                    <div>URL: <span id="kiosk-scan-url"></span></div>
                    <div>Berlaku (detik): <span id="kiosk-expires"></span></div>
                </div>
            </div>
        @endif
    </div>
</x-kiosk-layout>
