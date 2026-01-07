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

                <div class="px-5 py-5 space-y-5">
                    {{-- Email --}}
                    <div>
                        <x-input-label for="email" :value="__('Email')" class="text-white/85" />
                        <div class="mt-1 relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">✉️</span>
                            <x-text-input
                                id="email"
                                name="email"
                                type="email"
                                :value="old('email')"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="nama@email.com"
                                class="block w-full pl-10 rounded-xl
                                       border-white/20 bg-white/10 text-white placeholder:text-white/45
                                       focus:border-white/35 focus:ring-white/25"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-200" />
                    </div>

                    {{-- Password --}}
                    <div>
                        <x-input-label for="password" :value="__('Password')" class="text-white/85" />
                        <div class="mt-1 relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">🔒</span>
                            <x-text-input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="current-password"
                                placeholder="Masukkan password"
                                class="block w-full pl-10 rounded-xl
                                       border-white/20 bg-white/10 text-white placeholder:text-white/45
                                       focus:border-white/35 focus:ring-white/25"
                            />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-200" />
                    </div>

                    {{-- Remember + Forgot --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
                        <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-white/80">
                            <input
                                id="remember_me"
                                type="checkbox"
                                name="remember"
                                class="rounded border-white/25 bg-white/10 text-white/90
                                       focus:ring-2 focus:ring-white/40"
                            >
                            <span>{{ __('Remember me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a
                                class="text-sm font-semibold text-white/80 hover:text-white underline
                                       focus:outline-none focus:ring-2 focus:ring-white/40 rounded-md"
                                href="{{ route('password.request') }}"
                            >
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
                    <p class="text-center text-sm text-white/75">
                        Belum punya akun?
                        <a
                            href="{{ route('register') }}"
                            class="font-semibold text-white hover:text-white/90 underline
                                   focus:outline-none focus:ring-2 focus:ring-white/40 rounded-md"
                        >
                            Registrasi di sini
                        </a>
                    </p>
                @endif
            </div>

            <p class="text-center text-xs text-white/65">
                Sistem Buku Tamu & Absensi Peserta Magang
            </p>
        </form>
    </div>
</x-guest-layout>
