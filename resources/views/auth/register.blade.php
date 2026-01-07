<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Nama -->
        <div>
            <x-input-label for="name" :value="__('Nama')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- NIK -->
        <div class="mt-4">
            <x-input-label for="nik" :value="__('NIK')" />
            <x-text-input id="nik" class="block mt-1 w-full" type="text" name="nik" :value="old('nik')"
                required autocomplete="off" inputmode="numeric" />
            <x-input-error :messages="$errors->get('nik')" class="mt-2" />
        </div>

        <!-- No Telepon -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('No Telepon')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')"
                required autocomplete="tel" inputmode="tel" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- User Name -->
        <div class="mt-4">
            <x-input-label for="username" :value="__('User Name')" />
            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')"
                required autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
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

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />

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

            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />

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

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}">
                {{ __('Already registered?') }}
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
