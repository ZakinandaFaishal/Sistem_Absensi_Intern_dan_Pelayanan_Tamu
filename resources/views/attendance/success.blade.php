<x-guest-layout>
    <div class="space-y-3">
        <h1 class="text-lg font-semibold">Berhasil</h1>
        <p class="text-sm text-gray-700">
            {{ $action === 'in' ? 'Check-in' : 'Check-out' }} berhasil dicatat.
        </p>
    </div>
</x-guest-layout>
