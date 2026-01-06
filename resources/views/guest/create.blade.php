<x-guest-layout>
    <form method="POST" action="{{ route('guest.store') }}"
          class="max-w-lg mx-auto space-y-5 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        @csrf

        {{-- Header --}}
        <div class="text-center">
            <h1 class="text-xl font-bold text-gray-900">Buku Tamu</h1>
            <p class="mt-1 text-sm text-gray-600">
                Silakan isi data kunjungan Anda
            </p>
        </div>

        {{-- Nama --}}
        <div>
            <x-input-label for="name" value="Nama" />
            <x-text-input
                id="name"
                name="name"
                type="text"
                class="mt-1 block w-full"
                required
                autofocus
            />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- Pekerjaan --}}
        <div>
            <x-input-label for="job" value="Pekerjaan" />
            <x-text-input
                id="job"
                name="job"
                type="text"
                class="mt-1 block w-full"
            />
            <x-input-error class="mt-2" :messages="$errors->get('institution')" />
        </div>

        {{-- Instansi --}}
        <div>
            <x-input-label for="institution" value="Instansi (Opsional)" />
            <x-text-input
                id="institution"
                name="institution"
                type="text"
                class="mt-1 block w-full"
            />
            <x-input-error class="mt-2" :messages="$errors->get('institution')" />
        </div>

        {{-- Jabatan --}}
        <div>
            <x-input-label for="jabatan" value="Jabatan (Opsional)" />
            <x-text-input
                id="jabatan"
                name="jabatan"
                type="text"
                class="mt-1 block w-full"
            />
            <x-input-error class="mt-2" :messages="$errors->get('institution')" />
        </div>

        {{-- No HP --}}
        <div>
            <x-input-label for="phone" value="No. HP (Opsional)" />
            <x-text-input
                id="phone"
                name="phone"
                type="text"
                class="mt-1 block w-full"
            />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        {{-- Jenis Keperluan (Dropdown) --}}
        <div>
            <x-input-label for="service_type" value="Jenis Keperluan" />
            <select
                id="service_type"
                name="service_type"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                       focus:border-indigo-500 focus:ring-indigo-500"
            >
                <option value="" disabled selected>-- Pilih Jenis Keperluan --</option>
                <option value="layanan">Layanan</option>
                <option value="koordinasi">Koordinasi</option>
                <option value="berkas">Pengantaran Berkas</option>
                <option value="lainnya">Lainnya</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('service_type')" />
        </div>

        {{-- Keperluan Detail --}}
        <div>
            <x-input-label for="purpose_detail" value="Keperluan / Alasan" />
            <textarea
                id="purpose_detail"
                name="purpose_detail"
                rows="3"
                required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                       focus:border-indigo-500 focus:ring-indigo-500"
                placeholder="Jelaskan keperluan Anda..."
            ></textarea>
            <x-input-error class="mt-2" :messages="$errors->get('purpose_detail')" />
        </div>

        {{-- Submit --}}
        <x-primary-button class="w-full justify-center py-3 text-base">
            Kirim
        </x-primary-button>
    </form>
</x-guest-layout>
