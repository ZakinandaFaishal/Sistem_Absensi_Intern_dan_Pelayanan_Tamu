<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Log Buku Tamu') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-3">
                    @if (session('status'))
                        <div class="rounded border border-green-200 bg-green-50 px-3 py-2 text-sm text-green-800">
                            {{ session('status') }}
                        </div>
                    @endif

                    @forelse($visits as $visit)
                        <div class="border rounded p-3 flex items-center justify-between">
                            <div>
                                <div class="font-medium">{{ $visit->name }}</div>
                                <div class="text-sm text-gray-600">{{ $visit->purpose }}</div>
                                <div class="text-xs text-gray-500">Datang: {{ $visit->arrived_at }}</div>
                            </div>
                            <div class="flex gap-2">
                                @if ($visit->completed_at)
                                    <span class="px-3 py-2 bg-gray-100 rounded text-sm text-gray-700">Selesai</span>
                                @else
                                    <form method="POST" action="{{ route('admin.guest.complete', $visit) }}">
                                        @csrf
                                        <button class="px-3 py-2 bg-gray-800 text-white rounded"
                                            type="submit">Selesai</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-600">Belum ada data kunjungan.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
