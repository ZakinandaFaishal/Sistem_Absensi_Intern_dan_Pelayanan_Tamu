<x-guest-layout>
    <form
        method="POST"
        action="{{ route('guest.store') }}"
        class="space-y-6"
        x-data="{
            visitType: '{{ old('visit_type', 'single') }}',
            groupCount: Number('{{ old('group_count', 2) }}') || 2,
        }"
    >
        @csrf

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200/40 bg-rose-500/10 px-5 py-4 text-left text-sm text-rose-100">
                <div class="font-semibold">Data belum tersimpan</div>
                <div class="mt-1">{{ $errors->first() }}</div>
            </div>
        @endif

        {{-- HEADER --}}
        <div class="text-center">
            <h1 class="mt-4 text-2xl sm:text-3xl font-semibold tracking-wide text-white drop-shadow">
                Buku Tamu Kunjungan
            </h1>
            <p class="mt-1 text-sm text-white/75">
                Silakan isi data kunjungan Anda dengan benar.
            </p>

            <div class="mx-auto mt-5 h-[2px] w-20 rounded-full bg-white/35"></div>
        </div>

        {{-- IDENTITAS --}}
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

                {{-- TIPE KUNJUNGAN --}}
                <div>
                    <x-input-label value="Datang *" class="text-white/85" />
                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label
                            class="flex items-center justify-between gap-3 rounded-xl border border-white/20 bg-white/10 px-4 py-3
                                   hover:bg-white/15 transition">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="visit_type" value="single"
                                    class="h-4 w-4 border-white/30 bg-white/10 text-white focus:ring-white/40"
                                    x-model="visitType"
                                    {{ old('visit_type', 'single') === 'single' ? 'checked' : '' }}>
                                <span class="text-sm font-semibold text-white/85">Sendiri</span>
                            </div>
                            <span class="text-xs text-white/55">1 orang</span>
                        </label>

                        <label
                            class="flex items-center justify-between gap-3 rounded-xl border border-white/20 bg-white/10 px-4 py-3
                                   hover:bg-white/15 transition">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="visit_type" value="group"
                                    class="h-4 w-4 border-white/30 bg-white/10 text-white focus:ring-white/40"
                                    x-model="visitType"
                                    {{ old('visit_type') === 'group' ? 'checked' : '' }}>
                                <span class="text-sm font-semibold text-white/85">Berkelompok</span>
                            </div>
                            <span class="text-xs text-white/55">> 1 orang</span>
                        </label>
                    </div>

                    <x-input-error class="mt-2 text-red-200" :messages="$errors->get('visit_type')" />
                </div>

                {{-- NAMA (ketua/yang mengisi form) --}}
                <div>
                    <x-input-label for="name" value="Nama *" class="text-white/85" />
                    <div class="mt-1 relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">
                            <x-icon name="user" class="h-5 w-5" />
                        </span>
                        <x-text-input
                            id="name"
                            name="name"
                            type="text"
                            required
                            autofocus
                            value="{{ old('name') }}"
                            placeholder="Silahkan masukkan nama"
                            class="block w-full pl-10 rounded-xl
                                   border-white/20 bg-white/10 text-white placeholder:text-white/45
                                   focus:border-white/35 focus:ring-white/25"
                        />
                    </div>
                    <x-input-error class="mt-2 text-red-200" :messages="$errors->get('name')" />
                    <p x-show="visitType === 'group'" x-transition.opacity style="display:none;"
                    class="mt-1 text-xs text-white/60">
                        Nama ini sebagai perwakilan/ketua rombongan.
                    </p>

                </div>

                {{-- JUMLAH + NAMA ANGGOTA (muncul kalau group) --}}
                <div x-show="visitType === 'group'" x-transition.opacity class="space-y-4" style="display:none;">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-1">
                            <x-input-label for="group_count" value="Jumlah orang *" class="text-white/85" />
                            <div class="mt-1 relative">
                                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">👥</span>
                                <input
                                    id="group_count"
                                    name="group_count"
                                    type="number"
                                    min="2"
                                    max="50"
                                    x-model.number="groupCount"
                                    :required="visitType === 'group'"
                                    :disabled="visitType !== 'group'"
                                    class="block w-full pl-10 rounded-xl border-white/20 bg-white/10 text-white placeholder:text-white/45
                                            focus:border-white/35 focus:ring-white/25"
                                    placeholder="2"
                                />
                            </div>
                            <p class="mt-1 text-xs text-white/60">Minimal 2 orang.</p>
                            <x-input-error class="mt-2 text-red-200" :messages="$errors->get('group_count')" />
                        </div>

                        <div class="sm:col-span-2 rounded-2xl border border-white/15 bg-white/5 p-4">
                            <p class="text-sm font-semibold text-white">Nama Anggota</p>
                            <p class="mt-0.5 text-xs text-white/65">Isi nama semua anggota sesuai jumlah orang.</p>
                            <div class="mt-3 space-y-3">
                                <template x-for="i in groupCount" :key="i">
                                    <div>
                                        <label class="block text-xs font-semibold text-white/70" x-text="'Anggota ' + i + ' *'"></label>
                                        <input
                                            type="text"
                                            :name="'group_names[' + (i-1) + ']'"
                                            class="mt-1 block w-full rounded-xl border-white/20 bg-white/10 text-white placeholder:text-white/45
                                                    focus:border-white/35 focus:ring-white/25"
                                            :placeholder="'Nama anggota ' + i"
                                            :required="visitType === 'group'"
                                            :disabled="visitType !== 'group'"
                                            x-bind:value="(@json(old('group_names', []))[i-1] ?? '')"
                                        />

                                    </div>
                                </template>
                            </div>

                            <x-input-error class="mt-3 text-red-200" :messages="$errors->get('group_names')" />
                            {{-- kalau error per index: group_names.0 dst --}}
                            @if($errors->has('group_names.*'))
                                <div class="mt-2 text-xs text-red-200 space-y-1">
                                    @foreach ($errors->get('group_names.*') as $msgs)
                                        <div>• {{ $msgs[0] }}</div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>


                {{-- GENDER --}}
                <div>
                    <x-input-label value="Jenis Kelamin *" class="text-white/85" />

                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label
                            class="flex items-center justify-between gap-3 rounded-xl border border-white/20 bg-white/10 px-4 py-3
                                   hover:bg-white/15 transition">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="gender" value="L"
                                    class="h-4 w-4 border-white/30 bg-white/10 text-white focus:ring-white/40"
                                    {{ old('gender') === 'L' ? 'checked' : '' }}>
                                <span class="text-sm font-semibold text-white/85">Laki-laki</span>
                            </div>
                            <span class="text-xs text-white/55">L</span>
                        </label>

                        <label
                            class="flex items-center justify-between gap-3 rounded-xl border border-white/20 bg-white/10 px-4 py-3
                                   hover:bg-white/15 transition">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="gender" value="P"
                                    class="h-4 w-4 border-white/30 bg-white/10 text-white focus:ring-white/40"
                                    {{ old('gender') === 'P' ? 'checked' : '' }}>
                                <span class="text-sm font-semibold text-white/85">Perempuan</span>
                            </div>
                            <span class="text-xs text-white/55">P</span>
                        </label>
                    </div>

                    <x-input-error class="mt-2 text-red-200" :messages="$errors->get('gender')" />
                </div>

                {{-- EMAIL + PENDIDIKAN --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- EMAIL (OPSIONAL) --}}
                    <div>
                        <x-input-label for="email" value="Email (Opsional)" class="text-white/85" />
                        <div class="mt-1 relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">
                                ✉️
                            </span>
                            <x-text-input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                placeholder="contoh@email.com"
                                class="block w-full pl-10 rounded-xl
                                       border-white/20 bg-white/10 text-white placeholder:text-white/45
                                       focus:border-white/35 focus:ring-white/25"
                            />
                        </div>
                        <x-input-error class="mt-2 text-red-200" :messages="$errors->get('email')" />
                    </div>

                    {{-- PENDIDIKAN --}}
                    <div>
                        <x-input-label for="education" value="Pendidikan Terakhir" class="text-white/85" />
                        <div class="mt-1 relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">
                                🎓
                            </span>
                            <select
                                id="education"
                                name="education"
                                class="block w-full pl-10 rounded-xl
                                       border-white/20 bg-white/10 text-white
                                       shadow-sm focus:border-white/35 focus:ring-white/25">
                                <option value="" class="text-gray-900" @selected(old('education') === null || old('education') === '')>
                                    -- Pilih Pendidikan --
                                </option>
                                <!-- <option value="TIDAK_TAMAT" class="text-gray-900" @selected(old('education') === 'TIDAK_TAMAT')>
                                    Tidak tamat sekolah
                                </option> -->
                                <option value="SD" class="text-gray-900" @selected(old('education') === 'SD')>
                                    SD / Sederajat
                                </option>
                                <option value="SMP" class="text-gray-900" @selected(old('education') === 'SMP')>
                                    SMP / Sederajat
                                </option>
                                <option value="SMA" class="text-gray-900" @selected(old('education') === 'SMA')>
                                    SMA / SMK / Sederajat
                                </option>

                                <option value="D1" class="text-gray-900" @selected(old('education') === 'D1')>
                                    Diploma 1 (D1)
                                </option>
                                <option value="D2" class="text-gray-900" @selected(old('education') === 'D2')>
                                    Diploma 2 (D2)
                                </option>
                                <option value="D3" class="text-gray-900" @selected(old('education') === 'D3')>
                                    Diploma 3 (D3)
                                </option>
                                <option value="D4" class="text-gray-900" @selected(old('education') === 'D4')>
                                    Diploma 4 (D4)
                                </option>

                                <option value="S1" class="text-gray-900" @selected(old('education') === 'S1')>
                                    Sarjana (S1)
                                </option>
                                <option value="S2" class="text-gray-900" @selected(old('education') === 'S2')>
                                    Magister (S2)
                                </option>
                                <option value="S3" class="text-gray-900" @selected(old('education') === 'S3')>
                                    Doktor (S3)
                                </option>
                            </select>
                        </div>
                        <x-input-error class="mt-2 text-red-200" :messages="$errors->get('education')" />
                    </div>
                </div>

                {{-- PEKERJAAN + NO HP --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- PEKERJAAN --}}
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

                    {{-- NO HP --}}
                    <div>
                        <x-input-label for="phone" value="No. HP (Opsional)" class="text-white/85" />
                        <div class="mt-1 relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">
                                <x-icon name="phone" class="h-5 w-5" />
                            </span>
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

                {{-- INSTANSI --}}
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

                {{-- JABATAN --}}
                <div>
                    <x-input-label for="jabatan" value="Jabatan (Opsional)" class="text-white/85" />
                    <div class="mt-1 relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">
                            <x-icon name="identification" class="h-5 w-5" />
                        </span>
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

        {{-- KEPERLUAN --}}
        <section class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur shadow-xl">
            <div class="border-b border-white/10 px-5 py-4">
                <p class="text-sm font-semibold text-white">Keperluan</p>
                <p class="mt-0.5 text-xs text-white/65">Pilih jenis keperluan dan jelaskan detailnya</p>
            </div>

            <div class="px-5 py-5 space-y-5">
                {{-- JENIS KEPERLUAN --}}
                <div>
                    <x-input-label for="service_type" value="Jenis Keperluan *" class="text-white/85" />
                    <div class="mt-1 relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">
                            <x-icon name="map-pin" class="h-5 w-5" />
                        </span>

                        <select
                            id="service_type"
                            name="service_type"
                            required
                            class="block w-full pl-10 rounded-xl
                                   border-white/20 bg-white/10 text-white
                                   shadow-sm focus:border-white/35 focus:ring-white/25">
                            <option value="" disabled {{ old('service_type') ? '' : 'selected' }} class="text-gray-900">
                                -- Pilih Jenis Keperluan --
                            </option>
                            <option value="layanan"    {{ old('service_type') == 'layanan' ? 'selected' : '' }} class="text-gray-900">Layanan</option>
                            <option value="koordinasi" {{ old('service_type') == 'koordinasi' ? 'selected' : '' }} class="text-gray-900">Koordinasi</option>
                            <option value="berkas"     {{ old('service_type') == 'berkas' ? 'selected' : '' }} class="text-gray-900">Pengantaran Berkas</option>
                            <option value="lainnya"    {{ old('service_type') == 'lainnya' ? 'selected' : '' }} class="text-gray-900">Lainnya</option>
                        </select>
                    </div>

                    <x-input-error class="mt-2 text-red-200" :messages="$errors->get('service_type')" />
                </div>

                {{-- DETAIL --}}
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
                               shadow-sm focus:border-white/35 focus:ring-white/25">{{ old('purpose_detail') }}</textarea>
                    <x-input-error class="mt-2 text-red-200" :messages="$errors->get('purpose_detail')" />
                </div>
            </div>
        </section>

        {{-- ACTIONS --}}
        <div class="flex flex-col-reverse sm:flex-row gap-3">
            <button
                type="submit"
                class="w-full inline-flex items-center justify-center rounded-xl
                       bg-white/20 px-5 py-3 text-base font-semibold text-white
                       border border-white/25 shadow-xl
                       hover:bg-white/30 hover:-translate-y-0.5 transition duration-200
                       focus:outline-none focus:ring-2 focus:ring-white/50">
                Kirim
            </button>
        </div>

        {{-- FOOTNOTE --}}
        <p class="text-center text-xs text-white/65">
            Data digunakan untuk administrasi kunjungan dan pencatatan layanan.
        </p>
    </form>
</x-guest-layout>
