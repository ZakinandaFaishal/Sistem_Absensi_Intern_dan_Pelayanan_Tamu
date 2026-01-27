<x-guest-layout>
    <div class="space-y-8">

        {{-- Session Status --}}
        <x-auth-session-status class="text-white/80 text-center" :status="session('status')" />

        {{-- Header --}}
        <div class="text-center">
            <h1 class="mt-4 text-2xl sm:text-3xl font-semibold tracking-wide text-white drop-shadow">
                Login
            </h1>
            <p class="mt-1 text-sm text-white/75">
                Silakan masuk untuk melanjutkan.
            </p>
            <div class="mx-auto mt-5 h-[2px] w-20 rounded-full bg-white/25"></div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            {{-- Section: Kredensial --}}
            <section class="rounded-2xl border border-white/12 bg-slate-950/30 backdrop-blur-xl shadow-2xl">
                <div class="border-b border-white/10 px-5 py-4">
                    <p class="text-sm font-semibold text-white">Akun</p>
                    <p class="mt-0.5 text-xs text-white/60">
                        Masukkan email, username, atau NIK dan password
                    </p>
                </div>

                <div class="px-5 py-5 space-y-5">

                    {{-- Email / Username / NIK --}}
                    <div>
                        <x-input-label for="email" value="Email / Username / NIK" class="text-white/85" />
                        <div class="mt-1 relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">
                                <x-icon name="user" class="h-5 w-5" />
                            </span>

                            <x-text-input id="email" name="email" type="text" value="{{ old('email') }}"
                                required autofocus autocomplete="username"
                                placeholder="email@domain.com / username / 16 digit NIK"
                                class="block w-full pl-10 rounded-xl
                                       border-white/14 bg-slate-950/25 text-white placeholder:text-white/40
                                       focus:border-white/25 focus:ring-white/20" />
                        </div>
                        <x-input-error class="mt-2 text-red-200" :messages="$errors->get('email')" />
                    </div>

                    {{-- Password --}}
                    <div>
                        <x-input-label for="password" value="Password" class="text-white/85" />
                        <div class="mt-1 relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">
                                <x-icon name="lock-closed" class="h-5 w-5" />
                            </span>

                            <x-text-input id="password" name="password" type="password" required
                                autocomplete="current-password" placeholder="••••••••"
                                class="block w-full pl-10 rounded-xl
                                       border-white/14 bg-slate-950/25 text-white placeholder:text-white/40
                                       focus:border-white/25 focus:ring-white/20" />
                        </div>
                        <x-input-error class="mt-2 text-red-200" :messages="$errors->get('password')" />
                    </div>

                    {{-- Remember + Forgot --}}
                    <div class="flex items-center justify-between text-sm">
                        <label for="remember_me" class="inline-flex items-center text-white/80">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="rounded border-white/30 bg-slate-950/20 text-white focus:ring-white/40">
                            <span class="ms-2">Remember me</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-white/70 hover:text-white underline">
                                Lupa password?
                            </a>
                        @endif
                    </div>

                </div>
            </section>

            {{-- Action --}}
            <button type="submit"
                class="w-full inline-flex items-center justify-center rounded-xl
                       bg-slate-950/35 px-5 py-3 text-base font-semibold text-white
                       border border-white/16 shadow-xl backdrop-blur-md
                       hover:bg-slate-950/45 hover:-translate-y-0.5 transition duration-200
                       focus:outline-none focus:ring-2 focus:ring-white/35">
                Login
            </button>

            {{-- Register --}}
            <p class="text-center text-sm text-white/75">
                Belum punya akun? Hubungi admin untuk dibuatkan akun.
            </p>
        </form>

        {{-- Footer --}}
        <p class="text-center text-xs text-white/60">
            Sistem Buku Tamu & Absensi Peserta Magang
        </p>

    </div>
</x-guest-layout>
