<x-guest-layout>
    <form method="POST" action="{{ route('guest.survey.store', $visit) }}" class="space-y-4">
        @csrf

        <div>
            <h1 class="text-lg font-semibold">Survey Kepuasan</h1>
            <p class="text-sm text-gray-600">Opsional. Mohon isi penilaian Anda.</p>
        </div>

        <div>
            <x-input-label value="Rating (1-5)" />
            <select name="rating" class="mt-1 block w-full border-gray-300 rounded" required>
                <option value="">Pilih</option>
                @for ($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('rating')" />
        </div>

        <div>
            <x-input-label for="comment" value="Komentar (opsional)" />
            <textarea id="comment" name="comment" class="mt-1 block w-full border-gray-300 rounded" rows="4"></textarea>
            <x-input-error class="mt-2" :messages="$errors->get('comment')" />
        </div>

        <x-primary-button class="w-full justify-center">Kirim</x-primary-button>

        <div class="pt-1 text-center">
            <a href="{{ url('/') }}" class="text-sm text-gray-700 underline">Lewati survey</a>
        </div>
    </form>
</x-guest-layout>
