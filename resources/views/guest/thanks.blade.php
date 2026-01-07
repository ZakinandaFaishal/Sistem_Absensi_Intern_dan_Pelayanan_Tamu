<x-guest-layout>
    <div class="space-y-3">
        <h1 class="text-lg font-semibold">Terima kasih</h1>
        <p class="text-sm text-gray-700">Data kunjungan sudah tersimpan.</p>

        @isset($visit)
            <div class="flex flex-wrap items-center gap-2 pt-2">
                <a href="{{ route('guest.survey.show', $visit) }}"
                    class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white">
                    Isi Survey (Opsional)
                </a>
                <a href="{{ url('/') }}"
                    class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-800">
                    Selesai
                </a>
            </div>

            <div class="text-xs text-gray-500">
                Survey bersifat opsional dan membantu evaluasi layanan.
            </div>
        @endisset
    </div>
</x-guest-layout>
