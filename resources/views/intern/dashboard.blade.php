<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard Magang</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-3">
                    <div class="text-sm text-gray-700">
                        Gunakan HP untuk scan QR yang tampil di kiosk untuk melakukan presensi.
                    </div>

                    <div>
                        <a href="{{ route('intern.attendance.history') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded">
                            Lihat Riwayat Presensi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
