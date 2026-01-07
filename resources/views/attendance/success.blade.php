<x-guest-layout>
    <div class="space-y-3">
        <h1 class="text-lg font-semibold">Berhasil</h1>
        <p class="text-sm text-gray-700">
            {{ $action === 'in' ? 'Check-in' : 'Check-out' }} berhasil dicatat.
        </p>

        <div class="flex flex-wrap gap-2 pt-2">
            <a href="{{ route('attendance.qr') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded">
                Scan QR Lagi
            </a>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-800 rounded">
                Dashboard
            </a>
        </div>
    </div>
</x-guest-layout>
