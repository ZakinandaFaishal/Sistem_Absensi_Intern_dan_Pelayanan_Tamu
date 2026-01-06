<x-guest-layout>
    <form method="POST" action="{{ route('guest.store') }}" class="space-y-4">
        @csrf

        <div>
            <h1 class="text-lg font-semibold">Buku Tamu</h1>
            <p class="text-sm text-gray-600">Silakan isi data kunjungan.</p>
        </div>

        <div>
            <x-input-label for="name" value="Nama" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required autofocus />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="institution" value="Instansi (opsional)" />
            <x-text-input id="institution" name="institution" type="text" class="mt-1 block w-full" />
            <x-input-error class="mt-2" :messages="$errors->get('institution')" />
        </div>

        <div>
            <x-input-label for="phone" value="No. HP (opsional)" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div>
            <x-input-label for="purpose" value="Keperluan" />
            <x-text-input id="purpose" name="purpose" type="text" class="mt-1 block w-full" required />
            <x-input-error class="mt-2" :messages="$errors->get('purpose')" />
        </div>

        <x-primary-button class="w-full justify-center">Kirim</x-primary-button>
    </form>
</x-guest-layout>
