<x-guest-layout>
    <form method="POST" action="{{ route('guest.store') }}" class="space-y-6">
        @csrf

        {{-- Header (menyatu dengan layout glass) --}}
        <div class="text-center">
            <h1 class="mt-4 text-2xl sm:text-3xl font-semibold tracking-wide text-white drop-shadow">
                Buku Tamu Kunjungan
            </h1>
            <p class="mt-1 text-sm text-white/75">
                Silakan isi data kunjungan Anda dengan benar.
            </p>

            <div class="mx-auto mt-5 h-[2px] w-20 rounded-full bg-white/35"></div>
        </div>

        {{-- Section: Identitas --}}
        <section class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur shadow-xl">
            <div class="flex items-start justify-between gap-3 border-b border-white/10 px-5 py-4">
                <div>
                    <p class="text-sm font-semibold text-white">Identitas</p>
                    <p class="mt-0.5 text-xs text-white/65">Data dasar pengunjung</p>
                </div>
                <div class="rounded-xl bg-white/10 border border-white/15 px-3 py-2 text-xs font-semibold text-white/85">
                    Wajib: <span class="text-red-200">*</span>
                </div>
            </div>

            <div class="px-5 py-5 space-y-5">
                {{-- Nama --}}
                <div>
                    <x-input-label for="name" value="Nama *" class="text-white/85" />
                    <div class="mt-1 relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">👤</span>
                        <x-text-input
                            id="name"
                            name="name"
                            type="text"
                            required
                            autofocus
                            value="{{ old('name') }}"
                            placeholder="Contoh: Budi Santoso"
                            class="block w-full pl-10 rounded-xl
                                   border-white/20 bg-white/10 text-white placeholder:text-white/45
                                   focus:border-white/35 focus:ring-white/25"
                        />
                    </div>
                    <x-input-error class="mt-2 text-red-200" :messages="$errors->get('name')" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Pekerjaan --}}
                    <div>
                        <x-input-label for="job" value="Pekerjaan" class="text-white/85" />
                        <div class="mt-1 relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">💼</span>
                            <x-text-input
                                id="job"
                                name="job"
                                type="text"
                                value="{{ old('job') }}"
                                placeholder="Mahasiswa / ASN / Wiraswasta"
                                class="block w-full pl-10 rounded-xl
                                       border-white/20 bg-white/10 text-white placeholder:text-white/45
                                       focus:border-white/35 focus:ring-white/25"
                            />
                        </div>
                        <x-input-error class="mt-2 text-red-200" :messages="$errors->get('job')" />
                    </div>

                    {{-- No HP --}}
                    <div>
                        <x-input-label for="phone" value="No. HP (Opsional)" class="text-white/85" />
                        <div class="mt-1 relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">📞</span>
                            <x-text-input
                                id="phone"
                                name="phone"
                                type="text"
                                inputmode="numeric"
                                value="{{ old('phone') }}"
                                placeholder="08xxxxxxxxxx"
                                class="block w-full pl-10 rounded-xl
                                       border-white/20 bg-white/10 text-white placeholder:text-white/45
                                       focus:border-white/35 focus:ring-white/25"
                            />
                        </div>
                        <x-input-error class="mt-2 text-red-200" :messages="$errors->get('phone')" />
                    </div>
                </div>

                {{-- Instansi --}}
                <div>
                    <x-input-label for="institution" value="Instansi (Opsional)" class="text-white/85" />
                    <div class="mt-1 relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">🏢</span>
                        <x-text-input
                            id="institution"
                            name="institution"
                            type="text"
                            value="{{ old('institution') }}"
                            placeholder="Universitas / Perusahaan / Komunitas"
                            class="block w-full pl-10 rounded-xl
                                   border-white/20 bg-white/10 text-white placeholder:text-white/45
                                   focus:border-white/35 focus:ring-white/25"
                        />
                    </div>
                    <x-input-error class="mt-2 text-red-200" :messages="$errors->get('institution')" />
                </div>

                {{-- Jabatan --}}
                <div>
                    <x-input-label for="jabatan" value="Jabatan (Opsional)" class="text-white/85" />
                    <div class="mt-1 relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">🪪</span>
                        <x-text-input
                            id="jabatan"
                            name="jabatan"
                            type="text"
                            value="{{ old('jabatan') }}"
                            placeholder="Staf / Kepala Seksi / dll."
                            class="block w-full pl-10 rounded-xl
                                   border-white/20 bg-white/10 text-white placeholder:text-white/45
                                   focus:border-white/35 focus:ring-white/25"
                        />
                    </div>
                    <x-input-error class="mt-2 text-red-200" :messages="$errors->get('jabatan')" />
                </div>
            </div>
        </section>

        {{-- Section: Keperluan --}}
        <section class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur shadow-xl">
            <div class="border-b border-white/10 px-5 py-4">
                <p class="text-sm font-semibold text-white">Keperluan</p>
                <p class="mt-0.5 text-xs text-white/65">Pilih jenis keperluan dan jelaskan detailnya</p>
            </div>

            <div class="px-5 py-5 space-y-5">
                {{-- Jenis Keperluan --}}
                <div>
                    <x-input-label for="service_type" value="Jenis Keperluan *" class="text-white/85" />
                    <div class="mt-1 relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">📌</span>

                        <select
                            id="service_type"
                            name="service_type"
                            required
                            class="block w-full pl-10 rounded-xl
                                   border-white/20 bg-white/10 text-white
                                   shadow-sm focus:border-white/35 focus:ring-white/25"
                        >
                            <option value="" disabled {{ old('service_type') ? '' : 'selected' }} class="text-gray-900">
                                -- Pilih Jenis Keperluan --
                            </option>
                            <option value="layanan" {{ old('service_type')=='layanan' ? 'selected' : '' }} class="text-gray-900">Layanan</option>
                            <option value="koordinasi" {{ old('service_type')=='koordinasi' ? 'selected' : '' }} class="text-gray-900">Koordinasi</option>
                            <option value="berkas" {{ old('service_type')=='berkas' ? 'selected' : '' }} class="text-gray-900">Pengantaran Berkas</option>
                            <option value="lainnya" {{ old('service_type')=='lainnya' ? 'selected' : '' }} class="text-gray-900">Lainnya</option>
                        </select>
                    </div>

                    <x-input-error class="mt-2 text-red-200" :messages="$errors->get('service_type')" />
                </div>

                {{-- Keperluan Detail --}}
                <div>
                    <x-input-label for="purpose_detail" value="Keperluan / Alasan *" class="text-white/85" />
                    <textarea
                        id="purpose_detail"
                        name="purpose_detail"
                        rows="4"
                        required
                        placeholder="Jelaskan keperluan Anda..."
                        class="mt-1 block w-full rounded-xl
                               border-white/20 bg-white/10 text-white placeholder:text-white/45
                               shadow-sm focus:border-white/35 focus:ring-white/25"
                    >{{ old('purpose_detail') }}</textarea>
                    <x-input-error class="mt-2 text-red-200" :messages="$errors->get('purpose_detail')" />
                </div>
            </div>
        </section>

        {{-- Actions --}}
        <div class="flex flex-col-reverse sm:flex-row gap-3">

            <button
                type="submit"
                class="w-full inline-flex items-center justify-center rounded-xl
                       bg-white/20 px-5 py-3 text-base font-semibold text-white
                       border border-white/25 shadow-xl
                       hover:bg-white/30 hover:-translate-y-0.5 transition duration-200
                       focus:outline-none focus:ring-2 focus:ring-white/50"
            >
                Kirim
            </button>
        </div>

        <p class="text-center text-xs text-white/65">
            Data digunakan untuk administrasi kunjungan dan pencatatan layanan.
        </p>
    </form>
</x-guest-layout>
