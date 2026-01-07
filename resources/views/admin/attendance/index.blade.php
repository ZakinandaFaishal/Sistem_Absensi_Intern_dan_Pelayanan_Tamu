<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Log Presensi</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-600">
                                    <th class="py-2 pr-4">Tanggal</th>
                                    <th class="py-2 pr-4">Nama</th>
                                    <th class="py-2 pr-4">Lokasi</th>
                                    <th class="py-2 pr-4">Check-in</th>
                                    <th class="py-2 pr-4">Check-out</th>
                                    <th class="py-2 pr-4">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $attendance)
                                    <tr class="border-t">
                                        <td class="py-2 pr-4 whitespace-nowrap">
                                            {{ optional($attendance->date)->format('d M Y') }}</td>
                                        <td class="py-2 pr-4 whitespace-nowrap">{{ $attendance->user?->name ?? '-' }}
                                        </td>
                                        <td class="py-2 pr-4 whitespace-nowrap">
                                            {{ $attendance->location?->name ?? '-' }}</td>
                                        <td class="py-2 pr-4 whitespace-nowrap">
                                            {{ $attendance->check_in_at?->format('H:i') ?? '-' }}</td>
                                        <td class="py-2 pr-4 whitespace-nowrap">
                                            {{ $attendance->check_out_at?->format('H:i') ?? '-' }}</td>
                                        <td class="py-2 pr-4">{{ $attendance->notes ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-3 text-gray-600">Belum ada data presensi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div>
                        {{ $attendances->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
