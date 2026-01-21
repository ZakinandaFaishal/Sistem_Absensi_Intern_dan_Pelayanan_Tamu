<x-guest-layout>
    <div class="space-y-8">

        {{-- Header --}}
        <div class="text-center">
            <h1 class="mt-4 text-2xl sm:text-3xl font-semibold tracking-wide text-white drop-shadow">
                Registrasi Akun
            </h1>
            <p class="mt-1 text-sm text-white/75">
                Lengkapi data untuk membuat akun sistem.
            </p>
            <div class="mx-auto mt-5 h-[2px] w-20 rounded-full bg-white/35"></div>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            {{-- Section: Identitas --}}
            <section class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur shadow-xl">
                <div class="border-b border-white/10 px-5 py-4">
                    <p class="text-sm font-semibold text-white">Identitas</p>
                    <p class="mt-0.5 text-xs text-white/65">Data diri pengguna</p>
                </div>

                <div class="px-5 py-5 space-y-5">

                    {{-- Nama --}}
                    <div>
                        <x-input-label for="name" value="Nama Lengkap *" class="text-white/85" />
                        <div class="mt-1 relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">
                                <x-icon name="user" class="h-5 w-5" />
                            </span>
                            <x-text-input id="name" name="name" type="text" required autofocus
                                value="{{ old('name') }}" placeholder="Contoh: Andi Pratama"
                                class="block w-full pl-10 rounded-xl
                                       border-white/20 bg-white/10 text-white
                                       placeholder:text-white/45
                                       focus:border-white/35 focus:ring-white/25" />
                        </div>
                        <x-input-error class="mt-2 text-red-200" :messages="$errors->get('name')" />
                    </div>

                    {{-- NIK --}}
                    <div>
                        <x-input-label for="nik" value="NIK *" class="text-white/85" />
                        <div class="mt-1 relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">
                                <x-icon name="identification" class="h-5 w-5" />
                            </span>
                            <x-text-input id="nik" name="nik" type="text" inputmode="numeric" required
                                value="{{ old('nik') }}" placeholder="16 digit NIK"
                                class="block w-full pl-10 rounded-xl
                                       border-white/20 bg-white/10 text-white
                                       placeholder:text-white/45
                                       focus:border-white/35 focus:ring-white/25" />
                        </div>
                        <x-input-error class="mt-2 text-red-200" :messages="$errors->get('nik')" />
                    </div>

                    {{-- No Telepon --}}
                    <div>
                        <x-input-label for="phone" value="Nomor Telepon *" class="text-white/85" />
                        <div class="mt-1 relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">
                                <x-icon name="phone" class="h-5 w-5" />
                            </span>
                            <x-text-input id="phone" name="phone" type="text" inputmode="tel" required
                                value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                                class="block w-full pl-10 rounded-xl
                                       border-white/20 bg-white/10 text-white
                                       placeholder:text-white/45
                                       focus:border-white/35 focus:ring-white/25" />
                        </div>
                        <x-input-error class="mt-2 text-red-200" :messages="$errors->get('phone')" />
                    </div>
                </div>
            </section>

            {{-- Section: Akun --}}
            <section class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur shadow-xl">
                <div class="border-b border-white/10 px-5 py-4">
                    <p class="text-sm font-semibold text-white">Akun</p>
                    <p class="mt-0.5 text-xs text-white/65">Data login pengguna</p>
                </div>

                <div class="px-5 py-5 space-y-5">

                    {{-- Username --}}
                    <div>
                        <x-input-label for="username" value="Username *" class="text-white/85" />
                        <div class="mt-1 relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">
                                <x-icon name="user" class="h-5 w-5" />
                            </span>
                            <x-text-input id="username" name="username" type="text" required
                                value="{{ old('username') }}" placeholder="contoh: andi.pratama"
                                class="block w-full pl-10 rounded-xl
                                       border-white/20 bg-white/10 text-white
                                       placeholder:text-white/45
                                       focus:border-white/35 focus:ring-white/25" />
                        </div>
                        <x-input-error class="mt-2 text-red-200" :messages="$errors->get('username')" />
                    </div>

                    {{-- Email --}}
                    <div>
                        <x-input-label for="email" value="Email *" class="text-white/85" />
                        <div class="mt-1 relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">
                                <x-icon name="envelope" class="h-5 w-5" />
                            </span>
                            <x-text-input id="email" name="email" type="email" required
                                value="{{ old('email') }}" placeholder="contoh@email.com"
                                class="block w-full pl-10 rounded-xl
                                       border-white/20 bg-white/10 text-white
                                       placeholder:text-white/45
                                       focus:border-white/35 focus:ring-white/25" />
                        </div>
                        <x-input-error class="mt-2 text-red-200" :messages="$errors->get('email')" />
                    </div>

                    {{-- Password --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <x-input-label for="password" value="Password *" class="text-white/85" />
                            <div class="mt-1 relative">
                                <span
                                    class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">
                                    <x-icon name="lock-closed" class="h-5 w-5" />
                                </span>
                                <x-text-input id="password" name="password" type="password" required
                                    placeholder="Minimal 8 karakter"
                                    class="block w-full pl-10 rounded-xl
                                           border-white/20 bg-white/10 text-white
                                           placeholder:text-white/45
                                           focus:border-white/35 focus:ring-white/25" />
                            </div>
                            <x-input-error class="mt-2 text-red-200" :messages="$errors->get('password')" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" value="Konfirmasi Password *"
                                class="text-white/85" />
                            <div class="mt-1 relative">
                                <span
                                    class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">
                                    <x-icon name="lock-closed" class="h-5 w-5" />
                                </span>
                                <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                                    required placeholder="Ulangi password"
                                    class="block w-full pl-10 rounded-xl
                                           border-white/20 bg-white/10 text-white
                                           placeholder:text-white/45
                                           focus:border-white/35 focus:ring-white/25" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Section: Masa Magang --}}
            <section class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur shadow-xl">
                <div class="border-b border-white/10 px-5 py-4">
                    <p class="text-sm font-semibold text-white">Masa Magang</p>
                    <p class="mt-0.5 text-xs text-white/65">Digunakan untuk menghitung nilai berdasarkan kehadiran.</p>
                </div>

                <div class="px-5 py-5 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <x-input-label for="internship_start_date" value="Mulai *" class="text-white/85" />
                            <div class="mt-1 relative">
                                <span
                                    class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">
                                    <x-icon name="calendar-days" class="h-5 w-5" />
                                </span>
                                <x-text-input id="internship_start_date" name="internship_start_date" type="date"
                                    required value="{{ old('internship_start_date') }}"
                                    class="block w-full pl-10 rounded-xl
                                           border-white/20 bg-white/10 text-white
                                           focus:border-white/35 focus:ring-white/25" />
                            </div>
                            <x-input-error class="mt-2 text-red-200" :messages="$errors->get('internship_start_date')" />
                        </div>

                        <div>
                            <x-input-label for="internship_end_date" value="Selesai *" class="text-white/85" />
                            <div class="mt-1 relative">
                                <span
                                    class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">
                                    <x-icon name="calendar-days" class="h-5 w-5" />
                                </span>
                                <x-text-input id="internship_end_date" name="internship_end_date" type="date"
                                    required value="{{ old('internship_end_date') }}"
                                    class="block w-full pl-10 rounded-xl
                                           border-white/20 bg-white/10 text-white
                                           focus:border-white/35 focus:ring-white/25" />
                            </div>
                            <x-input-error class="mt-2 text-red-200" :messages="$errors->get('internship_end_date')" />
                        </div>
                    </div>
                </div>
            </section>

            {{-- Section: Lokasi Magang --}}
            <section class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur shadow-xl">
                <div class="border-b border-white/10 px-5 py-4">
                    <p class="text-sm font-semibold text-white">Lokasi Magang</p>
                    <p class="mt-0.5 text-xs text-white/65">Pilih dinas/lokasi untuk penentuan area presensi.</p>
                </div>

                <div class="px-5 py-5 space-y-5">
                    <div>
                        <x-input-label for="internship_location_id" value="Dinas / Lokasi *" class="text-white/85" />
                        <div class="mt-1 relative">
                            <span
                                class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">
                                <x-icon name="map-pin" class="h-5 w-5" />
                            </span>

                            <select id="internship_location_id" name="internship_location_id" required
                                class="block w-full pl-10 rounded-xl border-white/20 bg-white/10 text-white
                                       focus:border-white/35 focus:ring-white/25">
                                <option value="" class="text-slate-900" @selected(old('internship_location_id') === null || old('internship_location_id') === '')>
                                    -- Pilih Lokasi --
                                </option>
                                @foreach ($locations ?? [] as $loc)
                                    <option value="{{ $loc->id }}" class="text-slate-900"
                                        @selected((string) old('internship_location_id') === (string) $loc->id)>
                                        {{ $loc->name }}{{ $loc->code ? ' (' . $loc->code . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <x-input-error class="mt-2 text-red-200" :messages="$errors->get('internship_location_id')" />
                    </div>
                </div>
            </section>

            {{-- Section: Kode Registrasi --}}
            <section class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur shadow-xl">
                <div class="border-b border-white/10 px-5 py-4">
                    <p class="text-sm font-semibold text-white">Kode Registrasi</p>
                    <p class="mt-0.5 text-xs text-white/65">Diberikan oleh admin untuk membatasi registrasi.</p>
                </div>

                <div class="px-5 py-5 space-y-5">
                    <div>
                        <x-input-label for="registration_code" value="Kode *" class="text-white/85" />
                        <div class="mt-1 relative">
                            <span
                                class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">
                                <x-icon name="lock-closed" class="h-5 w-5" />
                            </span>
                            <x-text-input id="registration_code" name="registration_code" type="password" required
                                value="{{ old('registration_code') }}" placeholder="Masukkan kode dari admin"
                                class="block w-full pl-10 rounded-xl border-white/20 bg-white/10 text-white
                                       placeholder:text-white/45 focus:border-white/35 focus:ring-white/25" />
                        </div>
                        <x-input-error class="mt-2 text-red-200" :messages="$errors->get('registration_code')" />
                    </div>
                </div>
            </section>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('login') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl
                           bg-white/10 px-5 py-3 text-sm font-semibold text-white
                           border border-white/15 shadow-xl
                           hover:bg-white/20 transition">
                    Sudah punya akun?
                </a>

                <button type="submit"
                    class="w-full inline-flex items-center justify-center rounded-xl
                           bg-white/20 px-6 py-3 text-base font-semibold text-white
                           border border-white/25 shadow-xl
                           hover:bg-white/30 hover:-translate-y-0.5 transition
                           focus:outline-none focus:ring-2 focus:ring-white/50">
                    Daftar
                </button>
            </div>

            <p class="text-center text-xs text-white/65">
                Data digunakan untuk administrasi dan pembuatan akun sistem.
            </p>
        </form>
    </div>

</x-guest-layout>
