<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    {{ __('Dashboard') }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Ringkasan aktivitas dan akses cepat fitur utama.
                </p>
            </div>

            <div class="hidden sm:flex items-center gap-2">
                <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                    Status: Aktif
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-gradient-to-b from-slate-50 to-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- HERO / WELCOME CARD --}}
            <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-50 via-white to-sky-50"></div>
                <div class="relative p-6 sm:p-8">
                    <div class="flex items-start justify-between gap-6">
                        <div>
                            <h3 class="text-lg sm:text-xl font-bold text-slate-900">
                                Selamat datang 👋
                            </h3>
                            <p class="mt-1 text-sm text-slate-600">
                                {{ __("You're logged in!") }}
                            </p>

                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="inline-flex items-center rounded-lg bg-slate-900/5 px-3 py-1 text-xs font-semibold text-slate-700">
                                    Sistem Buku Tamu & Absensi Magang
                                </span>
                                <span class="inline-flex items-center rounded-lg bg-emerald-600/10 px-3 py-1 text-xs font-semibold text-emerald-700">
                                    QR Absensi Harian
                                </span>
                            </div>
                        </div>

                        <div class="hidden sm:block">
                            <div class="rounded-2xl bg-white/60 backdrop-blur px-4 py-3 border border-white/50 shadow-sm">
                                <p class="text-xs font-semibold text-slate-500">Hari ini</p>
                                <p class="text-lg font-bold text-slate-900">
                                    {{ now()->format('d M Y') }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ now()->format('H:i') }} WIB
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- QUICK STATS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold text-slate-500">Presensi Hari Ini</p>
                    <p class="mt-2 text-2xl font-extrabold text-slate-900">—</p>
                    <p class="mt-1 text-xs text-slate-500">Check-in / Check-out</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold text-slate-500">Buku Tamu</p>
                    <p class="mt-2 text-2xl font-extrabold text-slate-900">—</p>
                    <p class="mt-1 text-xs text-slate-500">Total hari ini</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold text-slate-500">Ekspor Data</p>
                    <p class="mt-2 text-2xl font-extrabold text-slate-900">—</p>
                    <p class="mt-1 text-xs text-slate-500">Presensi / Buku tamu</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold text-slate-500">Status Sistem</p>
                    <p class="mt-2 text-2xl font-extrabold text-emerald-700">OK</p>
                    <p class="mt-1 text-xs text-slate-500">Layanan berjalan normal</p>
                </div>
            </div>

            {{-- QUICK ACTIONS --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="p-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h4 class="text-base font-bold text-slate-900">Aksi Cepat</h4>
                            <p class="mt-1 text-sm text-slate-600">
                                Akses fitur utama tanpa banyak klik.
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Dummy button: nanti tinggal ganti href --}}
                        <a href="javascript:void(0)"
                           class="group rounded-2xl border border-slate-200 bg-slate-50 p-5 hover:bg-white hover:shadow-sm transition">
                            <p class="text-sm font-bold text-slate-900 group-hover:text-slate-950">
                                Tampilkan QR
                            </p>
                            <p class="mt-1 text-xs text-slate-600">
                                Mode fullscreen untuk monitor.
                            </p>
                            <div class="mt-4 inline-flex items-center rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white">
                                Buka
                            </div>
                        </a>

                        <a href="javascript:void(0)"
                           class="group rounded-2xl border border-slate-200 bg-slate-50 p-5 hover:bg-white hover:shadow-sm transition">
                            <p class="text-sm font-bold text-slate-900 group-hover:text-slate-950">
                                Log Presensi
                            </p>
                            <p class="mt-1 text-xs text-slate-600">
                                Lihat & export data presensi.
                            </p>
                            <div class="mt-4 inline-flex items-center rounded-lg bg-emerald-700 px-3 py-2 text-xs font-semibold text-white">
                                Buka
                            </div>
                        </a>

                        <a href="javascript:void(0)"
                           class="group rounded-2xl border border-slate-200 bg-slate-50 p-5 hover:bg-white hover:shadow-sm transition">
                            <p class="text-sm font-bold text-slate-900 group-hover:text-slate-950">
                                Buku Tamu
                            </p>
                            <p class="mt-1 text-xs text-slate-600">
                                Statistik dan daftar kunjungan.
                            </p>
                            <div class="mt-4 inline-flex items-center rounded-lg bg-sky-700 px-3 py-2 text-xs font-semibold text-white">
                                Buka
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            {{-- INFO / NOTE --}}
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
                <p class="text-sm font-semibold text-amber-900">Catatan</p>
                <p class="mt-1 text-sm text-amber-800">
                    QR absensi otomatis berganti setiap hari. Pastikan monitor QR aktif saat jam kerja.
                </p>
            </div>

        </div>
    </div>
</x-app-layout>
