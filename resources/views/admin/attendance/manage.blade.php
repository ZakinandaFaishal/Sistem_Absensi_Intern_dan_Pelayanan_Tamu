@extends('layouts.admin')

@section('title', 'Presensi - Diskominfo Kab. Magelang')
@section('page_title', 'Presensi')

@section('content')

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Presensi</h2>
            <p class="mt-1 text-sm text-slate-600">Kelola aturan presensi dan lokasi/dinas dalam satu halaman.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if ($isSuperAdmin)
                <form method="GET" action="{{ route('admin.attendance.manage') }}" class="flex items-center gap-2">
                    <select name="dinas_id"
                        class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-200">
                        @foreach ($dinasOptions as $d)
                            <option value="{{ $d->id }}" @selected((int) $activeDinasId === (int) $d->id)>
                                {{ $d->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit"
                        class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">
                        Terapkan
                    </button>
                </form>
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
                    Tambahkan minimal 1 dinas dulu supaya pengaturan presensi bisa dipilih per dinas.
                    Cara cepat: jalankan <span class="font-semibold">php artisan db:seed</span>.
                </div>
            </div>
        @endif

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('status') }}
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
            <a href="#aturan-presensi"
                class="inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                Aturan Presensi
            </a>
            @if ($isSuperAdmin)
                <a href="#lokasi-dinas"
                    class="inline-flex items-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                    Lokasi / Dinas
                </a>
            @endif
        </div>

        {{-- Aturan Presensi --}}
        <section id="aturan-presensi" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200">
                <p class="text-sm font-semibold text-slate-900">Aturan Presensi</p>
                <p class="mt-0.5 text-xs text-slate-500">Geofence radius + pembatasan jam check-in/check-out.</p>
            </div>

            <form method="POST" action="{{ route('admin.attendance.settings') }}" class="p-6 space-y-5">
                @csrf

                @if ($isSuperAdmin)
                    <input type="hidden" name="dinas_id" value="{{ (int) $activeDinasId }}">
                @endif

                @if (($noDinas ?? false) === true)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
                        Pilih/buat dinas terlebih dahulu. Form dinonaktifkan sementara.
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-700">Office Latitude</label>
                        <input name="office_lat" value="{{ old('office_lat', $settings['office_lat'] ?? '') }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                            placeholder="contoh: -7.479xxx" />
                        @error('office_lat')
                            <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-700">Office Longitude</label>
                        <input name="office_lng" value="{{ old('office_lng', $settings['office_lng'] ?? '') }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                            placeholder="contoh: 110.217xxx" />
                        @error('office_lng')
                            <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-700">Radius (meter)</label>
                        <input name="radius_m" type="number" min="1" max="50"
                            value="{{ old('radius_m', $settings['radius_m'] ?? 50) }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                        @error('radius_m')
                            <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-700">Maks Akurasi GPS (meter)</label>
                        <input name="max_accuracy_m" type="number" min="1" max="5000"
                            value="{{ old('max_accuracy_m', $settings['max_accuracy_m'] ?? 100) }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                        @error('max_accuracy_m')
                            <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-slate-700">Check-in Mulai</label>
                            <input name="checkin_start" type="time"
                                value="{{ old('checkin_start', $settings['checkin_start'] ?? '08:00') }}"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                            @error('checkin_start')
                                <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-700">Check-in Sampai</label>
                            <input name="checkin_end" type="time"
                                value="{{ old('checkin_end', $settings['checkin_end'] ?? '12:00') }}"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                            @error('checkin_end')
                                <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-slate-700">Check-out Mulai</label>
                            <input name="checkout_start" type="time"
                                value="{{ old('checkout_start', $settings['checkout_start'] ?? '13:00') }}"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                            @error('checkout_start')
                                <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-700">Check-out Sampai</label>
                            <input name="checkout_end" type="time"
                                value="{{ old('checkout_end', $settings['checkout_end'] ?? '16:30') }}"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                            @error('checkout_end')
                                <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" @disabled(($noDinas ?? false) === true)
                        class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">
                        Simpan Aturan
                    </button>
                </div>
            </form>
        </section>

        {{-- Lokasi / Dinas (super_admin only) --}}
        @if ($isSuperAdmin)
            <section id="lokasi-dinas" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200">
                    <p class="text-sm font-semibold text-slate-900">Lokasi / Dinas</p>
                    <p class="mt-0.5 text-xs text-slate-500">Kelola titik koordinat untuk penugasan peserta magang.</p>
                </div>

                <div class="p-6 space-y-5">
                    <form method="POST" action="{{ route('admin.attendance.locations.store') }}"
                        class="grid grid-cols-1 md:grid-cols-12 gap-3">
                        @csrf

                        @if ($isSuperAdmin)
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
                                    @if ($isSuperAdmin)
                                        <th class="px-3 py-3 font-semibold w-52">Dinas</th>
                                    @endif
                                    <th class="px-3 py-3 font-semibold w-48">Nama</th>
                                    <th class="px-3 py-3 font-semibold w-28">Kode</th>
                                    <th class="px-3 py-3 font-semibold w-28">Lat</th>
                                    <th class="px-3 py-3 font-semibold w-28">Lng</th>
                                    <th class="px-3 py-3 font-semibold w-72">Alamat</th>
                                    <th class="px-3 py-3 font-semibold w-40 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($locations as $loc)
                                    <tr class="hover:bg-slate-50/70">
                                        @if ($isSuperAdmin)
                                            <td class="px-3 py-3 whitespace-nowrap text-slate-700">
                                                {{ $loc->dinas?->name ?? '—' }}
                                            </td>
                                        @endif
                                        <td class="px-3 py-3">
                                            <input type="text" name="name" value="{{ $loc->name }}"
                                                form="loc-update-{{ $loc->id }}"
                                                class="h-9 w-full min-w-0 rounded-lg border border-slate-200 bg-white px-2 text-xs text-slate-900 font-semibold">
                                        </td>
                                        <td class="px-3 py-3">
                                            <input type="text" name="code" value="{{ $loc->code ?? '' }}"
                                                form="loc-update-{{ $loc->id }}"
                                                class="h-9 w-full min-w-0 rounded-lg border border-slate-200 bg-white px-2 text-xs text-slate-700">
                                        </td>
                                        <td class="px-3 py-3">
                                            <input type="text" name="lat" value="{{ $loc->lat ?? '' }}"
                                                form="loc-update-{{ $loc->id }}"
                                                class="h-9 w-full min-w-0 rounded-lg border border-slate-200 bg-white px-2 text-xs text-slate-700">
                                        </td>
                                        <td class="px-3 py-3">
                                            <input type="text" name="lng" value="{{ $loc->lng ?? '' }}"
                                                form="loc-update-{{ $loc->id }}"
                                                class="h-9 w-full min-w-0 rounded-lg border border-slate-200 bg-white px-2 text-xs text-slate-700">
                                        </td>
                                        <td class="px-3 py-3">
                                            <input type="text" name="address" value="{{ $loc->address ?? '' }}"
                                                form="loc-update-{{ $loc->id }}"
                                                class="h-9 w-full min-w-0 rounded-lg border border-slate-200 bg-white px-2 text-xs text-slate-700"
                                                placeholder="—">
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-right">
                                            <div class="inline-flex items-center justify-end gap-2">
                                                <form id="loc-update-{{ $loc->id }}" method="POST"
                                                    action="{{ route('admin.attendance.locations.update', $loc) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                </form>

                                                <button type="submit" form="loc-update-{{ $loc->id }}"
                                                    class="h-9 rounded-lg bg-slate-900 px-3 text-xs font-semibold text-white hover:bg-slate-800 transition">
                                                    Edit
                                                </button>

                                                <form method="POST"
                                                    action="{{ route('admin.attendance.locations.destroy', $loc) }}"
                                                    onsubmit="return confirm('Hapus lokasi ini?');">
                                                    @csrf
                                                    @method('DELETE')
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
                                        <td colspan="{{ $isSuperAdmin ? 7 : 6 }}"
                                            class="px-3 py-10 text-center text-slate-600">Belum ada lokasi.</td>
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
