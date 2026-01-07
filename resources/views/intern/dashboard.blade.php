<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-xl tracking-tight text-slate-900">
                    Dashboard Magang
                </h2>
                <p class="mt-1 text-sm text-slate-600">
                    Presensi harian peserta magang
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-3">
                    <div class="text-sm text-gray-700">
                        Presensi dilakukan dengan cara scan QR yang tampil di layar kiosk (monitor).
                    </div>

                    <div class="rounded border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                        <div class="font-semibold">Langkah cepat</div>
                        <div>1) Tekan <span class="font-semibold">Scan QR Presensi</span></div>
                        <div>2) Arahkan kamera ke QR di monitor</div>
                        <div>3) Pilih Check-in / Check-out (GPS diminta dari HP)</div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('attendance.qr') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded">
                            Scan QR Presensi
                        </a>
                        <a href="{{ route('intern.attendance.history') }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-800 rounded">
                            Riwayat Presensi
                        </a>
                    </div>

                    <a
                        href="{{ route('intern.attendance.history') }}"
                        class="mt-4 inline-flex items-center justify-center rounded-xl
                               bg-slate-900 px-5 py-3 text-sm font-semibold text-white
                               hover:bg-slate-800 transition"
                    >
                        Lihat Riwayat Presensi →
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
