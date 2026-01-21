@extends('layouts.admin')

@section('title', 'Lokasi / Dinas - Diskominfo Kab. Magelang')
@section('page_title', 'Lokasi / Dinas')

@section('content')

    {{-- HEADER PAGE --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Lokasi / Dinas</h2>
            <p class="mt-1 text-sm text-slate-600">Kelola titik koordinat untuk penugasan peserta magang.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.attendance.index') }}"
                class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                ← Kembali ke Presensi
            </a>
        </div>
    </div>

    <div class="pt-5 space-y-6">

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200">
                <p class="text-sm font-semibold text-slate-900">Lokasi / Dinas</p>
                <p class="mt-0.5 text-xs text-slate-500">Kelola titik koordinat untuk penugasan peserta magang.</p>
            </div>

            <div class="p-6 space-y-5">

                <form method="POST" action="{{ route('admin.attendance.locations.store') }}"
                    class="grid grid-cols-1 md:grid-cols-12 gap-3">
                    @csrf
                    <div class="md:col-span-3">
                        <label class="text-xs font-semibold text-slate-700">Nama</label>
                        <input name="name" value="{{ old('name') }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                            placeholder="Contoh: Diskominfo" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-slate-700">Kode (opsional)</label>
                        <input name="code" value="{{ old('code') }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                            placeholder="KOMINFO">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-slate-700">Latitude</label>
                        <input name="lat" value="{{ old('lat') }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                            placeholder="-7.59..." required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-slate-700">Longitude</label>
                        <input name="lng" value="{{ old('lng') }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                            placeholder="110.21..." required>
                    </div>
                    <div class="md:col-span-3">
                        <label class="text-xs font-semibold text-slate-700">Alamat (opsional)</label>
                        <input name="address" value="{{ old('address') }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                            placeholder="Alamat singkat">
                    </div>
                    <div class="md:col-span-12 flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">
                            Tambah Lokasi
                        </button>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-slate-600 border-b border-slate-200">
                                <th class="py-3 pr-4 font-semibold whitespace-nowrap">Nama</th>
                                <th class="py-3 pr-4 font-semibold whitespace-nowrap">Kode</th>
                                <th class="py-3 pr-4 font-semibold whitespace-nowrap">Lat</th>
                                <th class="py-3 pr-4 font-semibold whitespace-nowrap">Lng</th>
                                <th class="py-3 pr-4 font-semibold whitespace-nowrap">Alamat</th>
                                <th class="py-3 pr-0 font-semibold whitespace-nowrap text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse(($locations ?? []) as $loc)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="py-3 pr-4">
                                        <input form="locForm{{ $loc->id }}" name="name" value="{{ $loc->name }}"
                                            class="w-56 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                                    </td>
                                    <td class="py-3 pr-4">
                                        <input form="locForm{{ $loc->id }}" name="code"
                                            value="{{ $loc->code }}"
                                            class="w-32 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                                    </td>
                                    <td class="py-3 pr-4">
                                        <input form="locForm{{ $loc->id }}" name="lat"
                                            value="{{ $loc->lat }}"
                                            class="w-40 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                                    </td>
                                    <td class="py-3 pr-4">
                                        <input form="locForm{{ $loc->id }}" name="lng"
                                            value="{{ $loc->lng }}"
                                            class="w-40 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                                    </td>
                                    <td class="py-3 pr-4">
                                        <input form="locForm{{ $loc->id }}" name="address"
                                            value="{{ $loc->address }}"
                                            class="w-72 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                                    </td>
                                    <td class="py-3 pr-0 text-right whitespace-nowrap">
                                        <form id="locForm{{ $loc->id }}" method="POST"
                                            action="{{ route('admin.attendance.locations.update', $loc) }}"
                                            class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition">
                                                Simpan
                                            </button>
                                        </form>

                                        <form method="POST"
                                            action="{{ route('admin.attendance.locations.destroy', $loc) }}"
                                            class="inline-block" onsubmit="return confirm('Hapus lokasi ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-xl bg-rose-600 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-700 transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-500">Belum ada lokasi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
