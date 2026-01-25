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
            <div class="mx-auto mt-5 h-[2px] w-20 rounded-full bg-white/25"></div>
        </div>

        {{-- Info --}}
        <div class="rounded-2xl border border-white/12 bg-slate-950/20 backdrop-blur-xl px-5 py-4 text-sm text-white/75 shadow-xl">
            Tidak masalah jika Anda lupa password. Kami akan mengirimkan link reset ke email Anda.
            <div class="mt-1 text-xs text-white/60">
                Pastikan email aktif dan dapat menerima pesan.
            </div>
        </div>

        {{-- Session Status --}}
        <x-auth-session-status class="text-center text-white/80" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            {{-- Section --}}
            <section class="rounded-2xl border border-white/12 bg-slate-950/30 backdrop-blur-xl shadow-2xl">
                <div class="border-b border-white/10 px-5 py-4">
                    <p class="text-sm font-semibold text-white">Email</p>
                    <p class="mt-0.5 text-xs text-white/60">Kami akan mengirim tautan reset ke alamat email ini</p>
                </div>

                <div class="px-5 py-5 space-y-5">
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
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="email"
                                placeholder="contoh@domain.com"
                                class="block w-full pl-10 rounded-xl
                                       border-white/14 bg-slate-950/25 text-white placeholder:text-white/40
                                       focus:border-white/25 focus:ring-white/20"
                            />
                        </div>
                        <x-input-error class="mt-2 text-red-200" :messages="$errors->get('email')" />
                    </div>
                </div>
            </section>

            {{-- Actions --}}
            <div class="flex flex-col-reverse sm:flex-row gap-3">
                <a href="{{ route('login') }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl
                           bg-slate-950/25 px-5 py-3 text-sm font-semibold text-white
                           border border-white/14 shadow-xl backdrop-blur-md
                           hover:bg-slate-950/35 transition
                           focus:outline-none focus:ring-2 focus:ring-white/35">
                    ← Kembali ke Login
                </a>

                <button type="submit"
                    class="w-full inline-flex items-center justify-center rounded-xl
                           bg-slate-950/35 px-5 py-3 text-sm font-semibold text-white
                           border border-white/16 shadow-xl backdrop-blur-md
                           hover:bg-slate-950/45 hover:-translate-y-0.5 transition duration-200
                           focus:outline-none focus:ring-2 focus:ring-white/35">
                    Kirim Link Reset
                </button>
            </div>

            <p class="text-center text-xs text-white/60">
                Jika email tidak masuk, cek folder spam atau coba beberapa menit lagi.
            </p>
        </form>

    </div>
</x-guest-layout>
