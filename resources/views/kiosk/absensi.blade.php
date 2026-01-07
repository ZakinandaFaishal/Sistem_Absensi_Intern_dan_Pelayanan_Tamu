<x-kiosk-layout title="Kiosk Absensi">
    <div class="space-y-4">
        <div class="flex flex-col items-center gap-4">
            <canvas id="kiosk-qr" data-token-endpoint="{{ route('kiosk.token') }}"></canvas>
            <div class="text-sm text-gray-600 break-all text-center">
                <div>Scan QR ini dari HP mahasiswa</div>
                <div>URL: <span id="kiosk-scan-url"></span></div>
                <div>Berlaku (detik): <span id="kiosk-expires"></span></div>
            </div>
        </div>
    </div>
</x-kiosk-layout>
