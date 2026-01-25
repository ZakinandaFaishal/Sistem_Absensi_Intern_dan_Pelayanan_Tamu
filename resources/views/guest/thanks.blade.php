<x-guest-layout>
    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="text-center">
            <div
                class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-2xl
                       bg-slate-950/30 border border-white/18
                       backdrop-blur-md shadow-xl"
            >
                <x-icon name="check-circle" class="h-8 w-8 text-white" />
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
            {{-- MAIN GLASS CARD --}}
            <section
                class="rounded-2xl
                       bg-slate-950/30 border border-white/18
                       backdrop-blur-md shadow-xl"
            >
                <div class="border-b border-white/12 px-5 py-4">
                    <p class="text-sm font-semibold text-white">
                        Langkah Selanjutnya
                    </p>

                    <p class="mt-0.5 text-xs text-white/65">
                        @if (($visit->service_type ?? null) === 'layanan')
                            Survey opsional untuk evaluasi layanan
                        @else
                            Kunjungan berhasil disimpan
                        @endif
                    </p>
                </div>

                <div class="px-5 py-5 space-y-4">

                    {{-- INFO BOX --}}
                    <div
                        class="rounded-xl
                               bg-slate-950/30 border border-white/18
                               backdrop-blur-md px-4 py-3"
                    >
                        <p class="text-sm font-semibold text-white/90">
                            @if (($visit->service_type ?? null) === 'layanan')
                                Survey (Opsional)
                            @else
                                Selesai
                            @endif
                        </p>

                        <p class="mt-1 text-xs text-white/65">
                            @if (($visit->service_type ?? null) === 'layanan')
                                Masukan Anda sangat membantu peningkatan kualitas layanan.
                            @else
                                Terima kasih, silakan tekan tombol selesai untuk kembali.
                            @endif
                        </p>
                    </div>

                    {{-- ACTIONS --}}
                    <div class="flex flex-col sm:flex-row gap-3">
                        @if (($visit->service_type ?? null) === 'layanan')
                            <a href="{{ route('guest.survey.show', $visit) }}"
                               class="w-full inline-flex items-center justify-center gap-2 rounded-xl
                                      bg-slate-950/30 px-5 py-3 text-base font-semibold text-white
                                      border border-white/18 shadow-xl backdrop-blur-md
                                      hover:bg-slate-950/40 hover:-translate-y-0.5 transition duration-200
                                      focus:outline-none focus:ring-2 focus:ring-white/35">
                                <x-icon name="star" class="h-5 w-5" />
                                Isi Survey
                            </a>
                        @endif

                        <a href="{{ url('/') }}"
                           class="w-full inline-flex items-center justify-center rounded-xl
                                  bg-slate-950/30 px-5 py-3 text-base font-semibold text-white
                                  border border-white/18 shadow-xl backdrop-blur-md
                                  hover:bg-slate-950/40 hover:-translate-y-0.5 transition duration-200
                                  focus:outline-none focus:ring-2 focus:ring-white/35">
                            Selesai
                        </a>
                    </div>

                    <p class="text-center text-xs text-white/65">
                        @if (($visit->service_type ?? null) === 'layanan')
                            Survey bersifat opsional dan membantu evaluasi layanan.
                        @else
                            Anda dapat kembali ke halaman utama.
                        @endif
                    </p>
                </div>
            </section>
        @endisset

        @empty($visit)
            {{-- FALLBACK --}}
            <section
                class="rounded-2xl
                       bg-slate-950/30 border border-white/18
                       backdrop-blur-md shadow-xl"
            >
                <div class="px-5 py-5 text-center space-y-3">
                    <p class="text-sm text-white/80">
                        Silakan kembali ke halaman utama.
                    </p>

                    <a href="{{ url('/') }}"
                       class="inline-flex items-center justify-center rounded-xl
                              bg-slate-950/30 px-5 py-3 text-base font-semibold text-white
                              border border-white/18 shadow-xl backdrop-blur-md
                              hover:bg-slate-950/40 hover:-translate-y-0.5 transition duration-200">
                        Kembali
                    </a>
                </div>
            </section>
        @endempty

    </div>
</x-guest-layout>
