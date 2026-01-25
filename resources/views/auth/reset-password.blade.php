<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>


<!-- <x-guest-layout>
    <div class="space-y-8">

        {{-- Header --}}
        <div class="text-center">
            <h1 class="mt-4 text-2xl sm:text-3xl font-semibold tracking-wide text-white drop-shadow">
                Reset Password
            </h1>
            <p class="mt-1 text-sm text-white/75">
                Silakan buat password baru untuk akun Anda.
            </p>
            <div class="mx-auto mt-5 h-[2px] w-20 rounded-full bg-white/35"></div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
            @csrf

            {{-- Token --}}
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            {{-- Section --}}
            <section class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur shadow-xl">
                <div class="border-b border-white/10 px-5 py-4">
                    <p class="text-sm font-semibold text-white">Keamanan Akun</p>
                    <p class="mt-0.5 text-xs text-white/65">
                        Pastikan password baru cukup kuat
                    </p>
                </div>

                <div class="px-5 py-5 space-y-5">

                    {{-- Email --}}
                    <div>
                        <x-input-label for="email" value="Alamat Email" class="text-white/85" />
                        <div class="mt-1 relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">
                                <x-icon name="envelope" class="h-5 w-5" />
                            </span>
                            <x-text-input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email', $request->email) }}"
                                required
                                autofocus
                                autocomplete="username"
                                class="block w-full pl-10 rounded-xl
                                       border-white/20 bg-white/10 text-white
                                       placeholder:text-white/45
                                       focus:border-white/35 focus:ring-white/25" />
                        </div>
                        <x-input-error class="mt-2 text-red-200" :messages="$errors->get('email')" />
                    </div>

                    {{-- Password --}}
                    <div>
                        <x-input-label for="password" value="Password Baru" class="text-white/85" />
                        <div class="mt-1 relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">
                                <x-icon name="lock-closed" class="h-5 w-5" />
                            </span>
                            <x-text-input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="new-password"
                                placeholder="Minimal 8 karakter"
                                class="block w-full pl-10 rounded-xl
                                       border-white/20 bg-white/10 text-white
                                       placeholder:text-white/45
                                       focus:border-white/35 focus:ring-white/25" />
                        </div>
                        <x-input-error class="mt-2 text-red-200" :messages="$errors->get('password')" />
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <x-input-label for="password_confirmation" value="Konfirmasi Password" class="text-white/85" />
                        <div class="mt-1 relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">
                                <x-icon name="check-circle" class="h-5 w-5" />
                            </span>
                            <x-text-input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                required
                                autocomplete="new-password"
                                placeholder="Ulangi password"
                                class="block w-full pl-10 rounded-xl
                                       border-white/20 bg-white/10 text-white
                                       placeholder:text-white/45
                                       focus:border-white/35 focus:ring-white/25" />
                        </div>
                        <x-input-error class="mt-2 text-red-200" :messages="$errors->get('password_confirmation')" />
                    </div>

                </div>
            </section>

            {{-- Action --}}
            <button type="submit"
                class="w-full inline-flex items-center justify-center rounded-xl
                       bg-white/20 px-5 py-3 text-base font-semibold text-white
                       border border-white/25 shadow-xl
                       hover:bg-white/30 hover:-translate-y-0.5 transition
                       focus:outline-none focus:ring-2 focus:ring-white/50">
                Reset Password
            </button>

            {{-- Footer --}}
            <p class="text-center text-xs text-white/60">
                Pastikan password tidak dibagikan kepada siapa pun.
            </p>

        </form>
    </div>
</x-guest-layout> -->
