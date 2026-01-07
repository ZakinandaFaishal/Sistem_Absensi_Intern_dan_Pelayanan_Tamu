<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Survey Pelayanan</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-3">
                    @forelse($surveys as $survey)
                        <div class="border rounded p-3">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div>
                                    <div class="font-medium">
                                        {{ $survey->visit?->name ?? 'Tamu' }}
                                        <span class="text-gray-500 font-normal">•</span>
                                        <span class="text-gray-700">Rating: {{ $survey->rating }}/5</span>
                                    </div>
                                    <div class="text-sm text-gray-600">{{ $survey->visit?->purpose ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">Dikirim:
                                        {{ $survey->submitted_at?->format('d M Y H:i') }}</div>
                                </div>
                                @if ($survey->visit)
                                    <a class="px-3 py-2 bg-gray-100 rounded text-sm"
                                        href="{{ route('guest.survey.show', $survey->visit) }}">Lihat</a>
                                @endif
                            </div>

                            @if ($survey->comment)
                                <div class="mt-2 text-sm text-gray-800">{{ $survey->comment }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="text-sm text-gray-600">Belum ada survey masuk.</div>
                    @endforelse

                    <div>
                        {{ $surveys->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
