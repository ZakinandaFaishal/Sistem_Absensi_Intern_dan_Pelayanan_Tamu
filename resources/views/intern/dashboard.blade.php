<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-xl tracking-tight text-slate-900">
                    Dashboard Magang
                </h2>
                <p class="mt-1 text-sm text-slate-600">
                    Presensi harian peserta magang
                </p>
            </div>
        </div>
    </x-slot>

    <div class="relative min-h-[calc(100vh-64px)] overflow-hidden
                bg-gradient-to-b from-slate-50 via-white to-slate-50 py-10">

        {{-- glow halus --}}
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-40 left-1/4 h-[28rem] w-[28rem] rounded-full bg-emerald-100/25 blur-3xl"></div>
            <div class="absolute top-24 right-1/4 h-[28rem] w-[28rem] rounded-full bg-sky-100/25 blur-3xl"></div>
        </div>

        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Hero --}}
            <div class="rounded-3xl border border-slate-200/60 bg-white/50 backdrop-blur-xl shadow-lg">
                <div class="p-6 sm:p-8 text-center space-y-3">
                    <div class="mx-auto inline-flex h-14 w-14 items-center justify-center
                                rounded-2xl bg-emerald-600/10 ring-1 ring-inset ring-emerald-200">
                        📱
                    </div>

                    <h3 class="text-lg sm:text-xl font-extrabold text-slate-900">
                        Presensi dengan QR Code
                    </h3>

                    <p class="text-sm text-slate-600 max-w-md mx-auto">
                        Gunakan <span class="font-semibold">HP Anda</span> untuk melakukan scan QR
                        yang ditampilkan pada layar kiosk untuk presensi masuk atau pulang.
                    </p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Scan Info --}}
                <div class="rounded-3xl border border-slate-200/60 bg-white/50 backdrop-blur shadow-lg p-6">
                    <p class="text-sm font-semibold text-slate-900">
                        Cara Presensi
                    </p>
                    <ul class="mt-2 text-sm text-slate-600 space-y-1 list-disc list-inside">
                        <li>Datangi layar kiosk presensi</li>
                        <li>Scan QR menggunakan kamera HP</li>
                        <li>Presensi akan tercatat otomatis</li>
                    </ul>
                </div>

                {{-- Riwayat --}}
                <div class="rounded-3xl border border-slate-200/60 bg-white/50 backdrop-blur shadow-lg p-6
                            flex flex-col justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">
                            Riwayat Presensi
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            Lihat catatan check-in dan check-out Anda.
                        </p>
                    </div>

                    <a
                        href="{{ route('intern.attendance.history') }}"
                        class="mt-4 inline-flex items-center justify-center rounded-xl
                               bg-slate-900 px-5 py-3 text-sm font-semibold text-white
                               hover:bg-slate-800 transition"
                    >
                        Lihat Riwayat Presensi →
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
