<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Scan QR Absensi</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <div class="text-sm text-gray-600">
                        Arahkan kamera HP ke QR yang tampil di layar kiosk.
                    </div>

                    @if ($errors->any())
                        <div class="rounded border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <div id="attendance-qr-reader" class="w-full"></div>

                    <div class="text-xs text-gray-500" id="attendance-qr-hint">
                        Jika kamera tidak muncul, pastikan izin kamera diaktifkan.
                    </div>

                    <div class="flex flex-wrap gap-2 pt-2">
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-800 rounded">
                            Kembali ke Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
