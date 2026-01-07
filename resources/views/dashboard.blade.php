<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2
                    class="font-extrabold text-xl leading-tight
                           bg-gradient-to-r from-fuchsia-600 via-pink-500 to-rose-500
                           bg-clip-text text-transparent">
                    Dashboard Admin
                </h2>
                <p class="mt-1 text-sm text-slate-600">
                    Log Presensi, Buku Tamu, Survey Pelayanan, dan User Management.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                           bg-gradient-to-r from-emerald-400 to-teal-500
                           text-white shadow-sm ring-1 ring-white/40">
                    ● Aktif
                </span>

                <span
                    class="hidden sm:inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                           bg-white/60 backdrop-blur-md
                           text-slate-700 shadow-sm ring-1 ring-white/40">
                    {{ now()->format('d M Y • H:i') }} WIB
                </span>
            </div>
        </div>
    </x-slot>

    {{-- BACKGROUND: dibuat lebih “abu susu” + glow super halus (tidak kontras) --}}
    <div
        class="relative min-h-screen overflow-hidden py-10
                bg-gradient-to-b from-slate-50 via-white to-slate-50">

        {{-- glow super subtle (opacity kecil + warna lebih netral) --}}
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -top-48 left-1/4 h-[34rem] w-[34rem] rounded-full bg-fuchsia-100/25 blur-3xl"></div>
            <div class="absolute top-24 right-1/4 h-[34rem] w-[34rem] rounded-full bg-sky-100/25 blur-3xl"></div>
            <div class="absolute -bottom-56 left-1/3 h-[36rem] w-[36rem] rounded-full bg-rose-100/20 blur-3xl"></div>
        </div>

        {{-- “noise” tipis biar menyatu (tidak flat) --}}
        <div
            class="pointer-events-none absolute inset-0 opacity-[0.035]
                    [background-image:radial-gradient(rgba(15,23,42,0.9)_1px,transparent_1px)]
                    [background-size:18px_18px]">
        </div>

        <div class="relative max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- HERO (glass diturunin biar nggak putih banget) --}}
            <div
                class="relative overflow-hidden rounded-3xl border border-slate-200/60 bg-white/45 backdrop-blur-xl shadow-lg shadow-slate-900/5">
                <div class="absolute inset-0 bg-gradient-to-r from-white/35 via-white/10 to-white/35"></div>

                <div class="relative p-6 sm:p-8">
                    <div class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
                        <div>
                            <div
                                class="inline-flex items-center gap-2 rounded-full border border-slate-200/60 bg-white/55 px-3 py-1 text-xs font-semibold text-slate-700">
                                🏛️ Diskominfo Kabupaten Magelang
                            </div>

                            <h3 class="mt-3 text-lg sm:text-xl font-extrabold tracking-tight text-slate-900">
                                Selamat datang, {{ Auth::user()->name ?? 'Admin' }} 👋
                            </h3>
                            <p class="mt-1 text-sm text-slate-600">
                                Monitor data harian, kelola hasil survey, dan atur pengguna.
                            </p>

                            <div class="mt-4 flex flex-wrap gap-2">
                                <span
                                    class="inline-flex items-center rounded-full bg-slate-900/5 px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200/60">
                                    Panel Admin
                                </span>
                                <span
                                    class="inline-flex items-center rounded-full bg-emerald-600/10 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200/60">
                                    Monitoring
                                </span>
                                <span
                                    class="inline-flex items-center rounded-full bg-sky-700/10 px-3 py-1 text-xs font-semibold text-sky-700 ring-1 ring-inset ring-sky-200/60">
                                    Survey
                                </span>
                                <span
                                    class="inline-flex items-center rounded-full bg-fuchsia-700/10 px-3 py-1 text-xs font-semibold text-fuchsia-700 ring-1 ring-inset ring-fuchsia-200/60">
                                    Users
                                </span>
                            </div>
                        </div>

                        <div class="md:text-right">
                            <div
                                class="inline-flex flex-col rounded-2xl border border-slate-200/60 bg-white/55 backdrop-blur px-4 py-3 shadow-sm">
                                <span class="text-xs font-semibold text-slate-500">Hari ini</span>
                                <span class="text-lg font-extrabold text-slate-900">{{ now()->format('d M Y') }}</span>
                                <span class="text-xs text-slate-500">{{ now()->format('H:i') }} WIB</span>
                            </div>
                        </div>
                    </div>

                    {{-- mini stats --}}
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="rounded-2xl border border-slate-200/60 bg-white/50 backdrop-blur p-4 shadow-sm">
                            <p class="text-xs font-semibold text-slate-500">Presensi Hari Ini</p>
                            <p class="mt-1 text-2xl font-extrabold text-slate-900">—</p>
                            <p class="mt-1 text-xs text-slate-500">Total check-in/out</p>
                        </div>

                        <div class="rounded-2xl border border-slate-200/60 bg-white/50 backdrop-blur p-4 shadow-sm">
                            <p class="text-xs font-semibold text-slate-500">Buku Tamu Hari Ini</p>
                            <p class="mt-1 text-2xl font-extrabold text-slate-900">—</p>
                            <p class="mt-1 text-xs text-slate-500">Total kunjungan</p>
                        </div>

                        <div class="rounded-2xl border border-slate-200/60 bg-white/50 backdrop-blur p-4 shadow-sm">
                            <p class="text-xs font-semibold text-slate-500">Survey Masuk</p>
                            <p class="mt-1 text-2xl font-extrabold text-slate-900">—</p>
                            <p class="mt-1 text-xs text-slate-500">Umpan balik layanan</p>
                        </div>

                        <!-- <div class="rounded-2xl border border-slate-200/60 bg-white/50 backdrop-blur p-4 shadow-sm">
                            <p class="text-xs font-semibold text-slate-500">Status Sistem</p>
                            <p class="mt-1 text-2xl font-extrabold text-emerald-700">OK</p>
                            <p class="mt-1 text-xs text-slate-500">Normal</p>
                        </div> -->
                    </div>
                </div>
            </div>

            {{-- MAIN MENU (4 cards) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

                {{-- ✅ PRESENSI: buka log admin --}}
                <a href="{{ route('admin.attendance.index') }}"
                    class="group relative overflow-hidden rounded-3xl border border-slate-200/60 bg-white/45 backdrop-blur-xl shadow-lg shadow-slate-900/5
                           hover:-translate-y-0.5 hover:shadow-xl transition">
                    <div class="absolute inset-0 bg-gradient-to-br from-slate-900/5 via-transparent to-transparent">
                    </div>
                    <div class="relative p-5">
                        <div class="flex items-start justify-between">
                            <div
                                class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-900/5 ring-1 ring-inset ring-slate-200/70">
                                📌
                            </div>
                            <span class="text-xs font-semibold text-slate-500">Buka</span>
                        </div>
                        <p class="mt-4 text-sm font-extrabold text-slate-900">Log Presensi</p>
                        <p class="mt-1 text-xs text-slate-600">Riwayat check-in/check-out dan rekap.</p>

                        <div
                            class="mt-5 inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white">
                            Lihat Log →
                        </div>
                    </div>
                </a>

                {{-- ✅ BUKU TAMU: route yang PASTI ADA --}}
                <a href="{{ route('admin.guest.index') }}"
                    class="group relative overflow-hidden rounded-3xl border border-slate-200/60 bg-white/45 backdrop-blur-xl shadow-lg shadow-slate-900/5
                           hover:-translate-y-0.5 hover:shadow-xl transition">
                    <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/10 via-transparent to-transparent">
                    </div>
                    <div class="relative p-5">
                        <div class="flex items-start justify-between">
                            <div
                                class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-600/10 ring-1 ring-inset ring-emerald-200/70">
                                📝
                            </div>
                            <span class="text-xs font-semibold text-slate-500">Buka</span>
                        </div>
                        <p class="mt-4 text-sm font-extrabold text-slate-900">Log Buku Tamu</p>
                        <p class="mt-1 text-xs text-slate-600">Daftar kunjungan, status, dan detail.</p>

                        <div
                            class="mt-5 inline-flex items-center rounded-xl bg-emerald-700 px-4 py-2 text-xs font-semibold text-white">
                            Lihat Data →
                        </div>
                    </div>
                </a>

                {{-- ✅ SURVEY: buka log survey admin --}}
                <a href="{{ route('admin.survey.index') }}"
                    class="group relative overflow-hidden rounded-3xl border border-slate-200/60 bg-white/45 backdrop-blur-xl shadow-lg shadow-slate-900/5
                           hover:-translate-y-0.5 hover:shadow-xl transition">
                    <div class="absolute inset-0 bg-gradient-to-br from-sky-700/10 via-transparent to-transparent">
                    </div>
                    <div class="relative p-5">
                        <div class="flex items-start justify-between">
                            <div
                                class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-sky-700/10 ring-1 ring-inset ring-sky-200/70">
                                ⭐
                            </div>
                            <span class="text-xs font-semibold text-slate-500">Buka</span>
                        </div>
                        <p class="mt-4 text-sm font-extrabold text-slate-900">Survey Pelayanan</p>
                        <p class="mt-1 text-xs text-slate-600">Kepuasan & masukan pengunjung.</p>

                        <div
                            class="mt-5 inline-flex items-center rounded-xl bg-sky-700 px-4 py-2 text-xs font-semibold text-white">
                            Lihat Survey →
                        </div>
                    </div>
                </a>

                {{-- ✅ USER: buka user management admin --}}
                <a href="{{ route('admin.users.index') }}"
                    class="group relative overflow-hidden rounded-3xl border border-slate-200/60 bg-white/45 backdrop-blur-xl shadow-lg shadow-slate-900/5
                           hover:-translate-y-0.5 hover:shadow-xl transition">
                    <div class="absolute inset-0 bg-gradient-to-br from-fuchsia-700/10 via-transparent to-transparent">
                    </div>
                    <div class="relative p-5">
                        <div class="flex items-start justify-between">
                            <div
                                class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-fuchsia-700/10 ring-1 ring-inset ring-fuchsia-200/70">
                                👥
                            </div>
                            <span class="text-xs font-semibold text-slate-500">Buka</span>
                        </div>
                        <p class="mt-4 text-sm font-extrabold text-slate-900">User Management</p>
                        <p class="mt-1 text-xs text-slate-600">Kelola akun, role, dan akses.</p>

                        <div
                            class="mt-5 inline-flex items-center rounded-xl bg-fuchsia-700 px-4 py-2 text-xs font-semibold text-white">
                            Kelola User →
                        </div>
                    </div>
                </a>
            </div>

            {{-- Note --}}
            <div
                class="rounded-3xl border border-slate-200/60 bg-white/45 backdrop-blur p-5 shadow-lg shadow-slate-900/5">
                <p class="text-sm font-semibold text-slate-900">Catatan</p>
                <p class="mt-1 text-sm text-slate-600">
                    Gunakan menu log untuk rekap dan pelaporan. Survey membantu evaluasi kualitas layanan.
                </p>
            </div>

        </div>
    </div>
</x-app-layout>
