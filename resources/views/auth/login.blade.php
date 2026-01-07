<x-guest-layout>
    <div class="space-y-6">
        {{-- Session Status --}}
        <x-auth-session-status class="text-white/80" :status="session('status')" />

        {{-- Header --}}
        <div class="text-center">
            <h1 class="mt-4 text-2xl sm:text-3xl font-semibold tracking-wide text-white drop-shadow">
                Login
            </h1>
            <p class="mt-1 text-sm text-white/75">
                Silakan masuk untuk melanjutkan.
            </p>
            <div class="mx-auto mt-5 h-[2px] w-20 rounded-full bg-white/35"></div>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            {{-- Section: Kredensial --}}
            <section class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur shadow-xl">
                <div class="border-b border-white/10 px-5 py-4">
                    <p class="text-sm font-semibold text-white">Akun</p>
                    <p class="mt-0.5 text-xs text-white/65">Masukkan email dan password</p>
                </div>

                    {{-- Email / Username / NIK --}}
                    <div>
                        <x-input-label for="email" :value="__('Email / Username / NIK')" />
                        <x-text-input id="email" class="block mt-1 w-full rounded-xl" type="text" name="email"
                            :value="old('email')" required autofocus autocomplete="username"
                            placeholder="contoh@domain.com / username / 16 digit NIK" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Password --}}
                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" class="block mt-1 w-full rounded-xl" type="password"
                            name="password" required autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    {{-- Remember + Forgot --}}
                    <div class="flex items-center justify-between pt-1">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                name="remember">
                            <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-gray-600 hover:text-gray-900 underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif
                    </div>
                </div>
            </section>

            {{-- Actions --}}
            <div class="flex flex-col gap-4">
                <button
                    type="submit"
                    class="w-full inline-flex items-center justify-center rounded-xl
                           bg-white/20 px-5 py-3 text-base font-semibold text-white
                           border border-white/25 shadow-xl
                           hover:bg-white/30 hover:-translate-y-0.5 transition duration-200
                           focus:outline-none focus:ring-2 focus:ring-white/50"
                >
                    {{ __('Log in') }}
                </button>

                        @if (Route::has('register'))
                            <p class="mt-4 text-center text-sm text-gray-600">
                                Belum punya akun?
                                <a href="{{ route('register') }}"
                                    class="font-semibold text-indigo-600 hover:text-indigo-500 underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Registrasi di sini
                                </a>
                            </p>
                        @endif
                    </div>
                </form>
            </div>

            <p class="text-center text-xs text-white/65">
                Sistem Buku Tamu & Absensi Peserta Magang
            </p>
        </form>
    </div>
</x-guest-layout>
