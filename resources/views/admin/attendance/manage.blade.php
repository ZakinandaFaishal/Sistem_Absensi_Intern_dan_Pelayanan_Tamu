@extends('layouts.admin')

@section('title', 'Presensi')
@section('page_title', 'Presensi')

@section('content')

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Presensi</h2>
            <p class="mt-1 text-sm text-slate-600">Kelola aturan presensi dan lokasi/dinas dalam satu halaman.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if ($isSuperAdmin)
                <div class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                    Scope: <span class="font-semibold">Semua Dinas</span>
                </div>
            @else
                <div class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                    Dinas: <span class="font-semibold">{{ $activeDinas?->name ?? '—' }}</span>
                </div>
            @endif

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                ← Kembali
            </a>
        </div>
    </div>

    <div class="pt-5 space-y-6">
        @if (($noDinas ?? false) === true)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                <div class="font-semibold">Belum ada data dinas</div>
                <div class="mt-1 text-amber-800">
                    Tambahkan minimal 1 dinas dulu supaya pengaturan presensi bisa digunakan.
                    Cara cepat: jalankan <span class="font-semibold">php artisan db:seed</span>.
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                <div class="font-semibold">Terjadi kesalahan</div>
                <ul class="mt-1 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex flex-wrap items-center gap-2">
            @if (!$isSuperAdmin)
                <a href="#aturan-presensi"
                    class="inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Aturan Presensi
                </a>
            @endif
            @if ($isSuperAdmin)
                <a href="#lokasi-dinas"
                    class="inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Lokasi / Dinas
                </a>
            @endif
        </div>

        {{-- Aturan Presensi (admin_dinas only) --}}
        @if (!$isSuperAdmin)
            <section id="aturan-presensi" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200">
                    <p class="text-sm font-semibold text-slate-900">Aturan Presensi</p>
                    <p class="mt-0.5 text-xs text-slate-500">Geofence radius + pembatasan jam check-in/check-out.</p>
                </div>

                @if (session('status'))
                    <div class="px-6 pt-5">
                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                            {{ session('status') }}
                        </div>
                    </div>
                @endif

                <div class="p-6 space-y-5">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/40 px-4 py-4">
                        <div class="text-xs font-semibold text-slate-700">Ringkasan Aturan</div>
                        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-slate-700">
                            <div>
                                <div class="text-xs text-slate-500">Office Lat/Lng</div>
                                <div class="font-semibold">
                                    {{ ($settings['office_lat'] ?? '') !== '' ? $settings['office_lat'] : '—' }} ,
                                    {{ ($settings['office_lng'] ?? '') !== '' ? $settings['office_lng'] : '—' }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-slate-500">Radius / Akurasi</div>
                                <div class="font-semibold">
                                    {{ (int) ($settings['radius_m'] ?? 50) }} m / maks
                                    {{ (int) ($settings['max_accuracy_m'] ?? 100) }} m
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-slate-500">Jam Check-in</div>
                                <div class="font-semibold">
                                    {{ (string) ($settings['checkin_start'] ?? '08:00') }} –
                                    {{ (string) ($settings['checkin_end'] ?? '12:00') }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-slate-500">Jam Check-out</div>
                                <div class="font-semibold">
                                    {{ (string) ($settings['checkout_start'] ?? '13:00') }} –
                                    {{ (string) ($settings['checkout_end'] ?? '16:30') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <a href="{{ route('admin.attendance.rules', ['location_id' => (int) ($activeLocationId ?? 0)]) }}"
                            class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">
                            Edit Aturan Presensi
                        </a>
                    </div>
                </div>
            </section>
        @endif

        {{-- Lokasi / Dinas (super_admin only) --}}
        @if ($isSuperAdmin)
            <section id="lokasi-dinas" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200">
                    <p class="text-sm font-semibold text-slate-900">Lokasi / Dinas</p>
                    <p class="mt-0.5 text-xs text-slate-500">Kelola titik koordinat untuk penugasan peserta magang.</p>
                </div>

                @if (session('status'))
                    <div class="px-6 pt-5">
                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                            {{ session('status') }}
                        </div>
                    </div>
                @endif

                <div class="p-6 space-y-5">
                    <form method="POST" action="{{ route('admin.attendance.locations.store') }}"
                        class="grid grid-cols-1 md:grid-cols-12 gap-3">
                        @csrf

                        <input type="hidden" name="_redirect" value="manage">

                        <div class="md:col-span-4">
                            <label class="text-xs font-semibold text-slate-700">Dinas</label>
                            <select name="dinas_id"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" required>
                                <option value="" disabled {{ old('dinas_id') ? '' : 'selected' }}>Pilih dinas
                                </option>
                                @foreach ($dinasOptions as $d)
                                    <option value="{{ $d->id }}" @selected((string) old('dinas_id') === (string) $d->id)>
                                        {{ $d->name }}{{ $d->code ? ' (' . $d->code . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-4">
                            <label class="text-xs font-semibold text-slate-700">Nama Lokasi</label>
                            <input name="name" value="{{ old('name') }}"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                placeholder="Contoh: Diskominfo" required>
                        </div>

                        <div class="md:col-span-4">
                            <label class="text-xs font-semibold text-slate-700">Kode (opsional)</label>
                            <input name="code" value="{{ old('code') }}"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                placeholder="KOMINFO">
                        </div>

                        <div class="md:col-span-3">
                            <label class="text-xs font-semibold text-slate-700">Latitude</label>
                            <input name="lat" value="{{ old('lat') }}"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                placeholder="-7.59..." required>
                        </div>

                        <div class="md:col-span-3">
                            <label class="text-xs font-semibold text-slate-700">Longitude</label>
                            <input name="lng" value="{{ old('lng') }}"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                placeholder="110.21..." required>
                        </div>

                        <div class="md:col-span-6">
                            <label class="text-xs font-semibold text-slate-700">Alamat (opsional)</label>
                            <input name="address" value="{{ old('address') }}"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                placeholder="Alamat singkat">
                        </div>
                        <div class="md:col-span-12">
                            <div class="mt-2 rounded-2xl border border-slate-200 bg-slate-50/40 px-4 py-4">
                                <div class="text-xs font-semibold text-slate-700">Aturan Presensi (wajib)</div>
                                <div class="mt-3 grid grid-cols-1 md:grid-cols-12 gap-3">
                                    <div class="md:col-span-3">
                                        <label class="text-xs font-semibold text-slate-700">Radius (meter)</label>
                                        <input type="number" name="radius_m" min="1" max="5000"
                                            value="{{ old('radius_m', 50) }}"
                                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                            required>
                                    </div>
                                    <div class="md:col-span-3">
                                        <label class="text-xs font-semibold text-slate-700">Maks Akurasi (meter)</label>
                                        <input type="number" name="max_accuracy_m" min="1" max="5000"
                                            value="{{ old('max_accuracy_m', 50) }}"
                                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                            required>
                                    </div>

                                    <div class="md:col-span-3">
                                        <label class="text-xs font-semibold text-slate-700">Check-in Mulai</label>
                                        <input type="time" name="checkin_start"
                                            value="{{ old('checkin_start', '08:00') }}"
                                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                            required>
                                    </div>
                                    <div class="md:col-span-3">
                                        <label class="text-xs font-semibold text-slate-700">Check-in Selesai</label>
                                        <input type="time" name="checkin_end"
                                            value="{{ old('checkin_end', '12:00') }}"
                                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                            required>
                                    </div>

                                    <div class="md:col-span-3">
                                        <label class="text-xs font-semibold text-slate-700">Check-out Mulai</label>
                                        <input type="time" name="checkout_start"
                                            value="{{ old('checkout_start', '13:00') }}"
                                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                            required>
                                    </div>
                                    <div class="md:col-span-3">
                                        <label class="text-xs font-semibold text-slate-700">Check-out Selesai</label>
                                        <input type="time" name="checkout_end"
                                            value="{{ old('checkout_end', '16:30') }}"
                                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                            required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-12 flex justify-end">
                            <button type="submit" @disabled(($noDinas ?? false) === true)
                                class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">
                                Tambah Lokasi
                            </button>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-[1100px] w-full text-sm table-fixed">
                            <thead>
                                <tr class="text-left text-slate-600 border-b border-slate-200">
                                    <th class="px-3 py-3 font-semibold w-72">Dinas</th>
                                    <th class="px-3 py-3 font-semibold w-28">Kode</th>
                                    <th class="px-3 py-3 font-semibold w-28">Lat</th>
                                    <th class="px-3 py-3 font-semibold w-28">Lng</th>
                                    <th class="px-3 py-3 font-semibold w-72">Alamat</th>
                                    <th class="px-3 py-3 font-semibold w-32">Radius</th>
                                    <th class="px-3 py-3 font-semibold w-36">Check-in</th>
                                    <th class="px-3 py-3 font-semibold w-36">Check-out</th>
                                    <th class="px-3 py-3 font-semibold w-44 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($locations as $loc)
                                    @php
                                        $rule = $loc->attendanceRule;
                                        $radiusM = (int) ($rule?->radius_m ?? 50);
                                        $maxAccuracyM = (int) ($rule?->max_accuracy_m ?? 100);
                                        $checkinStart = (string) ($rule?->checkin_start ?? '08:00');
                                        $checkinEnd = (string) ($rule?->checkin_end ?? '12:00');
                                        $checkoutStart = (string) ($rule?->checkout_start ?? '13:00');
                                        $checkoutEnd = (string) ($rule?->checkout_end ?? '16:30');
                                    @endphp
                                    <tr class="hover:bg-slate-50/70">
                                        <td class="px-3 py-3 text-slate-900 font-semibold">
                                            <div class="truncate" title="{{ $loc->dinas?->name ?? '' }}">
                                                {{ $loc->dinas?->name ?? '—' }}
                                            </div>
                                            <div class="truncate text-[11px] font-normal text-slate-500"
                                                title="{{ $loc->name }}">
                                                {{ $loc->name }}
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-slate-700">
                                            {{ $loc->code ?? '—' }}
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-slate-700">
                                            {{ $loc->lat ?? '—' }}
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-slate-700">
                                            {{ $loc->lng ?? '—' }}
                                        </td>
                                        <td class="px-3 py-3 text-slate-700">
                                            <div class="truncate" title="{{ $loc->address ?? '' }}">
                                                {{ $loc->address ?? '—' }}
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-slate-700">
                                            {{ $radiusM }} m
                                            <div class="text-[11px] text-slate-500">maks akurasi {{ $maxAccuracyM }} m
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-slate-700">
                                            {{ $checkinStart }} – {{ $checkinEnd }}
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-slate-700">
                                            {{ $checkoutStart }} – {{ $checkoutEnd }}
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-right">
                                            <div class="inline-flex items-center justify-end gap-2">
                                                <a href="{{ route('admin.attendance.locations.edit', ['location' => $loc, 'back' => 'manage']) }}"
                                                    class="h-9 inline-flex items-center rounded-lg bg-slate-900 px-3 text-xs font-semibold text-white hover:bg-slate-800 transition">
                                                    Pengaturan
                                                </a>

                                                <form method="POST"
                                                    action="{{ route('admin.attendance.locations.destroy', $loc) }}"
                                                    onsubmit="return confirm('Hapus lokasi ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="_redirect" value="manage">
                                                    <button type="submit"
                                                        class="h-9 rounded-lg bg-rose-600 px-3 text-xs font-semibold text-white hover:bg-rose-700 transition">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-3 py-10 text-center text-slate-600">Belum ada lokasi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        @endif

    </div>

@endsection
