<x-guest-layout>
    <div class="space-y-6">

        {{-- Header --}}
        <div class="text-center">
            <div class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-2xl
                        border border-white/15 bg-white/10 backdrop-blur shadow-xl">
                ✅
            </div>

            <h1 class="mt-4 text-2xl sm:text-3xl font-semibold tracking-wide text-white drop-shadow">
                Terima kasih
            </h1>
            <p class="mt-1 text-sm text-white/75">
                Data kunjungan sudah tersimpan.
            </p>

            <div class="mx-auto mt-5 h-[2px] w-20 rounded-full bg-white/35"></div>
        </div>

        @isset($visit)
            {{-- Card info --}}
            <section class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur shadow-xl">
                <div class="border-b border-white/10 px-5 py-4">
                    <p class="text-sm font-semibold text-white">Langkah Selanjutnya</p>
                    <p class="mt-0.5 text-xs text-white/65">Survey opsional untuk evaluasi layanan</p>
                </div>

                <div class="px-5 py-5 space-y-4">
                    <div class="rounded-xl border border-white/15 bg-white/10 px-4 py-3">
                        <p class="text-sm font-semibold text-white/90">Survey (Opsional)</p>
                        <p class="mt-1 text-xs text-white/65">
                            Masukan Anda sangat membantu peningkatan kualitas layanan.
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('guest.survey.show', $visit) }}"
                           class="w-full inline-flex items-center justify-center rounded-xl
                                  bg-white/20 px-5 py-3 text-base font-semibold text-white
                                  border border-white/25 shadow-xl
                                  hover:bg-white/30 hover:-translate-y-0.5 transition duration-200
                                  focus:outline-none focus:ring-2 focus:ring-white/50">
                            ⭐ Isi Survey
                        </a>

                        <a href="{{ url('/') }}"
                           class="w-full inline-flex items-center justify-center rounded-xl
                                  bg-white/10 px-5 py-3 text-base font-semibold text-white
                                  border border-white/20 shadow-xl
                                  hover:bg-white/20 hover:-translate-y-0.5 transition duration-200
                                  focus:outline-none focus:ring-2 focus:ring-white/50">
                            Selesai
                        </a>
                    </div>

                    <p class="text-center text-xs text-white/65">
                        Survey bersifat opsional dan membantu evaluasi layanan.
                    </p>
                </div>
            </section>
        @endisset

        @empty($visit)
            {{-- Fallback --}}
            <section class="rounded-2xl border border-white/15 bg-white/10 backdrop-blur shadow-xl">
                <div class="px-5 py-5 text-center">
                    <p class="text-sm text-white/80">Silakan kembali ke halaman utama.</p>
                    <a href="{{ url('/') }}"
                       class="mt-4 inline-flex items-center justify-center rounded-xl
                              bg-white/20 px-5 py-3 text-base font-semibold text-white
                              border border-white/25 shadow-xl
                              hover:bg-white/30 hover:-translate-y-0.5 transition duration-200">
                        Kembali
                    </a>
                </div>
            </section>
        @endempty

    </div>
</x-guest-layout>
