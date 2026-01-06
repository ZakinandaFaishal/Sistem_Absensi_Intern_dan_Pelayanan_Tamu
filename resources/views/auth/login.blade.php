<x-guest-layout>
    {{-- Session Status --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- Background wrapper --}}
    <div class="min-h-[70vh] flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">

            {{-- Card --}}
            <div class="rounded-2xl bg-white/90 backdrop-blur border border-gray-200 shadow-sm p-6 sm:p-8">
                {{-- Header --}}
                <div class="text-center mb-6">
                    <h1 class="text-2xl font-extrabold text-gray-900">Login</h1>
                    <p class="mt-1 text-sm text-gray-600">Silakan masuk untuk melanjutkan.</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input
                            id="email"
                            class="block mt-1 w-full rounded-xl"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Password --}}
                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input
                            id="password"
                            class="block mt-1 w-full rounded-xl"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                        />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    {{-- Remember + Forgot --}}
                    <div class="flex items-center justify-between pt-1">
                        <label for="remember_me" class="inline-flex items-center">
                            <input
                                id="remember_me"
                                type="checkbox"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                name="remember"
                            >
                            <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a
                                class="text-sm text-gray-600 hover:text-gray-900 underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                href="{{ route('password.request') }}"
                            >
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif
                    </div>

                    {{-- Submit --}}
                    <div class="pt-2">
                        <x-primary-button class="w-full justify-center py-3 rounded-xl">
                            {{ __('Log in') }}
                        </x-primary-button>

                        @if (Route::has('register'))
                            <p class="mt-4 text-center text-sm text-gray-600">
                                Belum punya akun?
                                <a
                                    href="{{ route('register') }}"
                                    class="font-semibold text-indigo-600 hover:text-indigo-500 underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                >
                                    Registrasi di sini
                                </a>
                            </p>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Optional small footer text --}}
            <p class="mt-5 text-center text-xs text-gray-500">
                Sistem Buku Tamu & Absensi Peserta Magang
            </p>

        </div>
    </div>
</x-guest-layout>
