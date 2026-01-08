@extends('layouts.navigation')

@section('title', 'Dashboard Admin - Diskominfo Kab. Magelang')
@section('page_title', 'Dashboard Admin')

@section('content')

    {{-- ROW: CHART + KPI --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

        {{-- "Chart" card (dummy visual biar mirip) --}}
        <div class="xl:col-span-2 rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs text-slate-500">Ringkasan</p>
                    <p class="mt-1 text-lg font-bold text-slate-900">Aktivitas Mingguan</p>
                    <p class="mt-1 text-sm text-slate-600">Presensi, kunjungan, dan survey.</p>
                </div>

                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                        Total Presensi
                    </span>
                    <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                        Total Kunjungan
                    </span>
                </div>
            </div>

            {{-- fake chart --}}
            <div class="mt-5 h-52 rounded-xl bg-slate-50 border border-slate-200 relative overflow-hidden">
                <div class="absolute inset-0 opacity-60
                            [background-image:linear-gradient(to_right,rgba(15,23,42,0.07)_1px,transparent_1px),linear-gradient(to_bottom,rgba(15,23,42,0.07)_1px,transparent_1px)]
                            [background-size:40px_40px]"></div>
                <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-slate-200/50 to-transparent"></div>
                <div class="absolute left-6 bottom-10 text-xs text-slate-500">Grafik (placeholder)</div>
            </div>
        </div>

        {{-- KPI cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-1 gap-4">
            <div class="rounded-2xl bg-orange-500 text-white shadow-sm p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-white/80">Presensi Hari Ini</p>
                        <p class="mt-2 text-2xl font-extrabold">—</p>
                        <p class="mt-1 text-xs text-white/80">Update realtime</p>
                    </div>
                    <div class="h-10 w-10 rounded-xl bg-white/15 flex items-center justify-center">📌</div>
                </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-slate-500">Buku Tamu Hari Ini</p>
                        <p class="mt-2 text-2xl font-extrabold text-slate-900">—</p>
                        <p class="mt-1 text-xs text-emerald-700 font-semibold">+0% dari biasanya</p>
                    </div>
                    <div class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">📝</div>
                </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-slate-500">Survey Masuk</p>
                        <p class="mt-2 text-2xl font-extrabold text-slate-900">—</p>
                        <p class="mt-1 text-xs text-emerald-700 font-semibold">+0% dari biasanya</p>
                    </div>
                    <div class="h-10 w-10 rounded-xl bg-sky-50 text-sky-700 flex items-center justify-center">⭐</div>
                </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-slate-500">Total Users</p>
                        <p class="mt-2 text-2xl font-extrabold text-slate-900">—</p>
                        <p class="mt-1 text-xs text-slate-500">Akun aktif terdaftar</p>
                    </div>
                    <div class="h-10 w-10 rounded-xl bg-fuchsia-50 text-fuchsia-700 flex items-center justify-center">👥</div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLE SECTION --}}
    <section class="rounded-2xl bg-white border border-slate-200 shadow-sm">
        <div class="p-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Ringkasan Data</h3>
                <p class="text-sm text-slate-500">Preview cepat data terbaru.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.attendance.index') }}"
                   class="rounded-xl bg-slate-900 text-white px-4 py-2 text-sm font-semibold hover:bg-slate-800 transition">
                    Presensi
                </a>
                <a href="{{ route('admin.guest.index') }}"
                   class="rounded-xl bg-white border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Buku Tamu
                </a>
                <a href="{{ route('admin.survey.index') }}"
                   class="rounded-xl bg-white border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Survey
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="rounded-xl bg-white border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Users
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr class="text-left">
                        <th class="px-5 py-3 font-semibold">No</th>
                        <th class="px-5 py-3 font-semibold">Modul</th>
                        <th class="px-5 py-3 font-semibold">Keterangan</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200">
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4">1</td>
                        <td class="px-5 py-4 font-semibold text-slate-900">Presensi</td>
                        <td class="px-5 py-4 text-slate-600">Lihat riwayat check-in/out</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Aktif</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.attendance.index') }}"
                               class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition">
                                Buka →
                            </a>
                        </td>
                    </tr>

                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4">2</td>
                        <td class="px-5 py-4 font-semibold text-slate-900">Buku Tamu</td>
                        <td class="px-5 py-4 text-slate-600">Data kunjungan & keperluan</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Aktif</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.guest.index') }}"
                               class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition">
                                Buka →
                            </a>
                        </td>
                    </tr>

                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4">3</td>
                        <td class="px-5 py-4 font-semibold text-slate-900">Survey</td>
                        <td class="px-5 py-4 text-slate-600">Rekap penilaian layanan</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Aktif</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.survey.index') }}"
                               class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition">
                                Buka →
                            </a>
                        </td>
                    </tr>

                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-4">4</td>
                        <td class="px-5 py-4 font-semibold text-slate-900">Users</td>
                        <td class="px-5 py-4 text-slate-600">Kelola akun & role</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Aktif</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.users.index') }}"
                               class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition">
                                Buka →
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

@endsection
