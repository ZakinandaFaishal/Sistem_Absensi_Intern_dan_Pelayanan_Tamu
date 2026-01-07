<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        {{-- Header --}}
        <div class="text-center">
            <h1 class="mt-4 text-2xl sm:text-3xl font-semibold tracking-wide text-white drop-shadow">
                Buat Akun Baru
            </h1>
            <p class="mt-1 text-sm text-white/75">
                Silakan lengkapi data berikut untuk mendaftar.
            </p>
            <div class="mx-auto mt-5 h-[2px] w-20 rounded-full bg-white/35"></div>
        </div>

        {{-- Section: Data Akun --}}
        <section class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur shadow-xl">
            <div class="flex items-start justify-between gap-3 border-b border-white/10 px-5 py-4">
                <div>
                    <p class="text-sm font-semibold text-white">Data Akun</p>
                    <p class="mt-0.5 text-xs text-white/65">Informasi identitas & akun</p>
                </div>
                <div class="rounded-xl bg-white/10 border border-white/15 px-3 py-2 text-xs font-semibold text-white/85">
                    Wajib: <span class="text-red-200">*</span>
                </div>
            </div>

            <div class="px-5 py-5 space-y-5">
                {{-- Nama --}}
                <div>
                    <x-input-label for="name" value="Nama Lengkap *" class="text-white/85" />
                    <div class="mt-1 relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">👤</span>
                        <x-text-input
                            id="name"
                            name="name"
                            type="text"
                            required
                            autofocus
                            autocomplete="name"
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
                    {{-- NIK --}}
                    <div>
                        <x-input-label for="nik" value="NIK *" class="text-white/85" />
                        <div class="mt-1 relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">🆔</span>
                            <x-text-input
                                id="nik"
                                name="nik"
                                type="text"
                                inputmode="numeric"
                                required
                                autocomplete="off"
                                value="{{ old('nik') }}"
                                placeholder="16 digit"
                                class="block w-full pl-10 rounded-xl
                                       border-white/20 bg-white/10 text-white placeholder:text-white/45
                                       focus:border-white/35 focus:ring-white/25"
                            />
                        </div>
                        <x-input-error class="mt-2 text-red-200" :messages="$errors->get('nik')" />
                    </div>

                    {{-- Nomor Telepon --}}
                    <div>
                        <x-input-label for="noTelpon" value="Nomor Telepon *" class="text-white/85" />
                        <div class="mt-1 relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">📞</span>
                            <x-text-input
                                id="noTelpon"
                                name="noTelpon"
                                type="text"
                                inputmode="numeric"
                                required
                                autocomplete="tel"
                                value="{{ old('noTelpon') }}"
                                placeholder="08xxxxxxxxxx"
                                class="block w-full pl-10 rounded-xl
                                       border-white/20 bg-white/10 text-white placeholder:text-white/45
                                       focus:border-white/35 focus:ring-white/25"
                            />
                        </div>
                        <x-input-error class="mt-2 text-red-200" :messages="$errors->get('noTelpon')" />
                    </div>
                </div>

                {{-- Username --}}
                <div>
                    <x-input-label for="userName" value="Username *" class="text-white/85" />
                    <div class="mt-1 relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">👤</span>
                        <x-text-input
                            id="userName"
                            name="userName"
                            type="text"
                            required
                            autocomplete="username"
                            value="{{ old('userName') }}"
                            placeholder="Contoh: budi.santoso"
                            class="block w-full pl-10 rounded-xl
                                   border-white/20 bg-white/10 text-white placeholder:text-white/45
                                   focus:border-white/35 focus:ring-white/25"
                        />
                    </div>
                    <x-input-error class="mt-2 text-red-200" :messages="$errors->get('userName')" />
                </div>

                {{-- Email --}}
                <div>
                    <x-input-label for="email" value="Email *" class="text-white/85" />
                    <div class="mt-1 relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">✉️</span>
                        <x-text-input
                            id="email"
                            name="email"
                            type="email"
                            required
                            autocomplete="email"
                            value="{{ old('email') }}"
                            placeholder="nama@email.com"
                            class="block w-full pl-10 rounded-xl
                                   border-white/20 bg-white/10 text-white placeholder:text-white/45
                                   focus:border-white/35 focus:ring-white/25"
                        />
                    </div>
                    <x-input-error class="mt-2 text-red-200" :messages="$errors->get('email')" />
                </div>
            </div>
            <div class="border-b border-white/10 px-5 py-4">
                <p class="text-sm font-semibold text-white">Keamanan</p>
                <p class="mt-0.5 text-xs text-white/65">Buat password untuk akun</p>
            </div>

            <div class="px-5 py-5 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Password --}}
                    <div>
                        <x-input-label for="password" value="Password *" class="text-white/85" />
                        <div class="mt-1 relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">🔒</span>
                            <x-text-input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="new-password"
                                placeholder="Minimal 8 karakter"
                                class="block w-full pl-10 rounded-xl
                                       border-white/20 bg-white/10 text-white placeholder:text-white/45
                                       focus:border-white/35 focus:ring-white/25"
                            />
                        </div>
                        <x-input-error class="mt-2 text-red-200" :messages="$errors->get('password')" />
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <x-input-label for="password_confirmation" value="Konfirmasi Password *" class="text-white/85" />
                        <div class="mt-1 relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">✅</span>
                            <x-text-input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                required
                                autocomplete="new-password"
                                placeholder="Ulangi password"
                                class="block w-full pl-10 rounded-xl
                                       border-white/20 bg-white/10 text-white placeholder:text-white/45
                                       focus:border-white/35 focus:ring-white/25"
                            />
                        </div>
                        <x-input-error class="mt-2 text-red-200" :messages="$errors->get('password_confirmation')" />
                    </div>
                </div>
            </div>
        </section>

        {{-- Actions --}}
        <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-3">
            <a
                href="{{ route('login') }}"
                class="text-sm font-semibold text-white/80 hover:text-white transition"
            >
                Sudah punya akun? <span class="underline">Login</span>
            </a>

            <button
                type="submit"
                class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl
                       bg-white/20 px-6 py-3 text-base font-semibold text-white
                       border border-white/25 shadow-xl
                       hover:bg-white/30 hover:-translate-y-0.5 transition duration-200
                       focus:outline-none focus:ring-2 focus:ring-white/50"
            >
                Daftar
            </button>
        </div>

        <p class="text-center text-xs text-white/65">
            Data digunakan untuk administrasi dan pembuatan akun sistem.
        </p>
    </form>
</x-guest-layout>
