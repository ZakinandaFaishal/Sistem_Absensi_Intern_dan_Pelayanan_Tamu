<x-kiosk-layout title="Kiosk">
    <div class="space-y-4">
        <a class="inline-flex w-full justify-center items-center px-4 py-3 bg-gray-800 text-white rounded"
            href="{{ route('kiosk.absensi') }}">
            Absensi Magang
        </a>
        <a class="inline-flex w-full justify-center items-center px-4 py-3 bg-gray-800 text-white rounded"
            href="{{ route('guest.create') }}">
            Buku Tamu
        </a>
    </div>
</x-kiosk-layout>
