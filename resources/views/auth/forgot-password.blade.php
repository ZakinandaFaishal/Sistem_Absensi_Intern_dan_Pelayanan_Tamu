<x-guest-layout>
    <div class="space-y-8">

        {{-- Header --}}
        <div class="text-center">
            <h1 class="mt-4 text-2xl sm:text-3xl font-semibold tracking-wide text-white drop-shadow">
                Reset Password
            </h1>
            <p class="mt-1 text-sm text-white/75">
                Masukkan email untuk menerima tautan reset password.
            </p>
            <div class="mx-auto mt-5 h-[2px] w-20 rounded-full bg-white/35"></div>
        </div>

        {{-- Info --}}
        <div class="text-center text-sm text-white/70 leading-relaxed">
            Tidak masalah jika Anda lupa password. Kami akan mengirimkan link reset ke email Anda.
        </div>

        {{-- Session Status --}}
        <x-auth-session-status class="text-center text-white/80" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            {{-- Section --}}
            <section class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur shadow-xl">
                <div class="border-b border-white/10 px-5 py-4">
                    <p class="text-sm font-semibold text-white">Email</p>
                    <p class="mt-0.5 text-xs text-white/65">Pastikan email aktif dan dapat menerima pesan</p>
                </div>

                <div class="px-5 py-5 space-y-5">
                    <div>
                        <x-input-label for="email" value="Alamat Email" class="text-white/85" />
                        <div class="mt-1 relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-white/55">
                                <x-icon name="envelope" class="h-5 w-5" />
                            </span>

                            <x-text-input id="email" name="email" type="email" value="{{ old('email') }}"
                                required autofocus autocomplete="email" placeholder="contoh@domain.com"
                                class="block w-full pl-10 rounded-xl
                                       border-white/20 bg-white/10 text-white
                                       placeholder:text-white/45
                                       focus:border-white/35 focus:ring-white/25" />
                        </div>
                        <x-input-error class="mt-2 text-red-200" :messages="$errors->get('email')" />
                    </div>
                </div>
            </section>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('login') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl
                           bg-white/10 px-5 py-3 text-sm font-semibold text-white
                           border border-white/15 shadow-xl
                           hover:bg-white/20 transition
                           focus:outline-none focus:ring-2 focus:ring-white/40">
                    ← Kembali ke Login
                </a>

                <button type="submit"
                    class="w-full inline-flex items-center justify-center rounded-xl
                           bg-white/20 px-5 py-3 text-sm font-semibold text-white
                           border border-white/25 shadow-xl
                           hover:bg-white/30 hover:-translate-y-0.5 transition
                           focus:outline-none focus:ring-2 focus:ring-white/50">
                    Kirim Link Reset
                </button>
            </div>

            <p class="text-center text-xs text-white/60">
                Jika email tidak masuk, cek folder spam atau coba beberapa menit lagi.
            </p>
        </form>

    </div>
</x-guest-layout>
