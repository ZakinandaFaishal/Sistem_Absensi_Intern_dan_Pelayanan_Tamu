<x-guest-layout>
    <div class="min-h-[70vh] flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-lg">

            <div class="relative overflow-hidden rounded-3xl border border-white/10 bg-[#111827]/55 backdrop-blur-xl shadow-[0_22px_70px_rgba(0,0,0,.55)]">
                {{-- glow (same as profil) --}}
                <div class="pointer-events-none absolute inset-0">
                    <div class="absolute -top-28 -left-28 h-72 w-72 rounded-full bg-emerald-400/10 blur-3xl"></div>
                    <div class="absolute -bottom-28 -right-28 h-72 w-72 rounded-full bg-white/6 blur-3xl"></div>
                    <div class="absolute inset-0 bg-gradient-to-br from-white/8 via-transparent to-white/4"></div>
                </div>

                <div class="relative p-6 sm:p-7 space-y-5">

                    {{-- accent bar --}}
                    <div class="h-1 w-full rounded-full bg-gradient-to-r from-emerald-300/55 via-white/10 to-transparent"></div>

                    {{-- Icon + Title --}}
                    <div class="flex items-start gap-4">
                        <div class="shrink-0 h-12 w-12 rounded-2xl bg-emerald-500/15 border border-emerald-300/20 flex items-center justify-center">
                            <span class="text-xl">✅</span>
                        </div>

                        <div class="min-w-0">
                            <h1 class="text-xl font-extrabold tracking-tight text-white">
                                Berhasil
                            </h1>
                            <p class="mt-1 text-sm text-white/70">
                                {{ $action === 'in' ? 'Check-in' : 'Check-out' }} berhasil dicatat.
                            </p>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="h-px w-full bg-gradient-to-r from-transparent via-emerald-300/25 to-transparent"></div>

                    {{-- Info --}}
                    <div class="rounded-2xl border border-white/10 bg-white/6 px-4 py-3 text-xs sm:text-sm text-white/70 leading-relaxed">
                        Anda bisa melakukan scan ulang jika QR berubah, atau kembali ke dashboard untuk melihat ringkasan.
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row gap-2">
                        <a href="{{ route('attendance.qr') }}"
                           class="inline-flex items-center justify-center gap-2 rounded-2xl px-4 py-3
                                  bg-white text-slate-900 text-sm font-extrabold
                                  hover:bg-white/90 transition
                                  focus:outline-none focus:ring-2 focus:ring-emerald-200/25">
                            <span>↻</span>
                            <span>Scan QR Lagi</span>
                        </a>

                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center justify-center gap-2 rounded-2xl px-4 py-3
                                  bg-white/7 border border-white/10 text-white/90 text-sm font-semibold
                                  hover:bg-white/10 hover:text-white transition
                                  focus:outline-none focus:ring-2 focus:ring-white/15">
                            <span>🏠</span>
                            <span>Dashboard</span>
                        </a>
                    </div>

                    {{-- Footer hint --}}
                    <p class="text-[11px] text-white/50">
                        Jika terjadi kesalahan, pastikan koneksi internet stabil dan coba ulang.
                    </p>

                </div>
            </div>

        </div>
    </div>
</x-guest-layout>
