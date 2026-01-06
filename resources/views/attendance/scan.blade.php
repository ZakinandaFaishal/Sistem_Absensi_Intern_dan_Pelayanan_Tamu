<x-guest-layout>
    <form method="POST" action="{{ route('attendance.scan.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="k" value="{{ $token }}" />

        <div>
            <h1 class="text-lg font-semibold">Presensi Magang</h1>
            <p class="text-sm text-gray-600">Lokasi: {{ $location->name }}</p>
        </div>

        @if ($errors->any())
            <div class="text-sm text-red-600">
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
    </form>
</x-guest-layout>
