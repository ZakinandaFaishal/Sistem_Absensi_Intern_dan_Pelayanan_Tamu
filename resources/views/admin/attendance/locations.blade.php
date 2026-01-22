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
            @if (($isSuperAdmin ?? false) === true)
                <a href="{{ route('admin.attendance.index') }}"
                    class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    ← Kembali ke Presensi
                </a>
            @endif
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

                @if (($isSuperAdmin ?? false) === true)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/40 px-4 py-4">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="text-xs font-semibold text-slate-700">Scope Dinas</div>
                                <div class="text-sm text-slate-600">Pilih dinas untuk melihat/mengelola lokasi.</div>
                            </div>
                            <form method="GET" action="{{ route('admin.attendance.locations') }}"
                                class="flex items-center gap-2">
                                <select name="dinas_id"
                                    class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" required>
                                    <option value="" disabled {{ empty($activeDinasId ?? 0) ? 'selected' : '' }}>Pilih
                                        dinas</option>
                                    @foreach ($dinasOptions ?? [] as $d)
                                        <option value="{{ $d->id }}"
                                            {{ (int) ($activeDinasId ?? 0) === (int) $d->id ? 'selected' : '' }}>
                                            {{ $d->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit"
                                    class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">
                                    Terapkan
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/40 px-4 py-4">
                        <div class="text-sm font-semibold text-slate-900">Dinas: {{ $activeDinas->name ?? '-' }}</div>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.attendance.locations.store') }}"
                    class="grid grid-cols-1 md:grid-cols-12 gap-3">
                    @csrf

                    @if (!empty($activeDinasId ?? 0))
                        <input type="hidden" name="dinas_id" value="{{ (int) $activeDinasId }}">
                    @endif

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
                                @if (($isSuperAdmin ?? false) === true)
                                    <th class="py-3 pr-4 font-semibold whitespace-nowrap">Dinas</th>
                                @endif
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
                                    @if (($isSuperAdmin ?? false) === true)
                                        <td class="py-3 pr-4 whitespace-nowrap text-slate-700">
                                            {{ $loc->dinas->name ?? '-' }}
                                        </td>
                                    @endif
                                    <td class="py-3 pr-4">
                                        <input form="locForm{{ $loc->id }}" name="name"
                                            value="{{ $loc->name }}"
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
                                    <td colspan="{{ ($isSuperAdmin ?? false) === true ? 7 : 6 }}"
                                        class="py-8 text-center text-slate-500">Belum ada lokasi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
