<x-guest-layout>
    <div class="relative max-w-md mx-auto space-y-6">

        <div
            class="relative rounded-3xl border border-white/20
                   bg-white/10 backdrop-blur-xl shadow-xl
                   px-6 py-7 text-center space-y-4">

            {{-- Icon --}}
            <div
                class="mx-auto inline-flex h-14 w-14 items-center justify-center
                       rounded-2xl border border-emerald-400/30
                       bg-emerald-400/15 backdrop-blur shadow-lg">
                <x-icon name="check-circle" class="h-7 w-7 text-emerald-200" />
            </div>

            {{-- Title --}}
            <div class="space-y-1">
                <h1 class="text-2xl font-extrabold tracking-tight text-white drop-shadow">
                    Terima Kasih
                </h1>
                <p class="text-sm text-white/70">
                    Survey kepuasan Anda berhasil tersimpan.
                </p>
            </div>

            {{-- Divider --}}
            <div
                class="mx-auto h-[2px] w-24 rounded-full
                       bg-gradient-to-r from-transparent via-emerald-300/50 to-transparent">
            </div>

            {{-- Action --}}
            <div class="pt-2">
                <a href="{{ url('/') }}"
                   class="inline-flex items-center justify-center gap-2
                          rounded-xl bg-emerald-500/25 px-5 py-3
                          text-sm font-semibold text-emerald-50
                          border border-emerald-400/30 shadow-lg
                          hover:bg-emerald-500/35 hover:-translate-y-0.5
                          transition duration-200
                          focus:outline-none focus:ring-2 focus:ring-emerald-400/50">
                    <x-icon name="arrow-right" class="h-4 w-4" />
                    Kembali ke Beranda
                </a>
            </div>

        </div>
    </div>
</x-guest-layout>
