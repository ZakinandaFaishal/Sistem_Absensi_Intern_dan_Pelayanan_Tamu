<x-guest-layout>
    <div class="relative min-h-[65vh] flex items-center justify-center px-4">
        <div class="relative w-full max-w-sm space-y-5">

            {{-- Header --}}
            <div class="text-center space-y-1.5">
                <div
                    class="mx-auto inline-flex h-10 w-10 items-center justify-center rounded-xl
                           bg-slate-950/30 border border-white/18 backdrop-blur-md shadow-xl"
                >
                    <x-icon name="camera" class="h-5 w-5 text-white" />
                </div>

                <h1 class="text-xl font-bold tracking-tight text-white drop-shadow">
                    Scan QR Absensi
                </h1>
                <p class="text-xs text-white/70">
                    Gunakan HP mahasiswa
                </p>

                <div
                    class="mx-auto mt-2 h-[1.5px] w-16 rounded-full bg-gradient-to-r
                           from-transparent via-white/40 to-transparent"
                ></div>
            </div>

            {{-- Glass Card (match button style) --}}
            <section
                class="relative rounded-2xl
                       bg-slate-950/30 border border-white/18
                       backdrop-blur-md shadow-xl p-4"
            >
                {{-- subtle highlight (lebih cocok buat dark glass) --}}
                <div
                    class="pointer-events-none absolute inset-0 rounded-2xl
                           bg-gradient-to-br from-white/10 via-transparent to-transparent"
                ></div>

                <div class="relative flex flex-col items-center gap-3">

                    {{-- QR --}}
                    <div class="rounded-xl bg-white p-2.5 shadow-xl">
                        <canvas
                            id="kiosk-qr"
                            data-token-endpoint="{{ route('kiosk.token') }}"
                            class="block w-44 h-44 sm:w-48 sm:h-48"
                        ></canvas>
                    </div>

                    {{-- Info --}}
                    <div
                        class="w-full rounded-xl
                               bg-slate-950/30 border border-white/18
                               backdrop-blur-md px-3 py-2 text-center space-y-0.5"
                    >
                        <p class="text-xs font-semibold text-white">
                            Info QR
                        </p>

                        <div class="text-[11px] text-white/70 break-all leading-snug">
                            <div>
                                URL:
                                <span id="kiosk-scan-url" class="font-mono text-white/90"></span>
                            </div>
                            <div>
                                Berlaku:
                                <span id="kiosk-expires" class="font-semibold text-white"></span>
                                detik
                            </div>
                        </div>
                    </div>

                    {{-- Hint --}}
                    <p class="text-[11px] text-white/55 text-center">
                        QR diperbarui otomatis
                    </p>

                </div>
            </section>

        </div>
    </div>
</x-guest-layout>
