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
                <form id="dinas-switch" method="GET" action="{{ route('admin.attendance.manage') }}"
                    class="flex items-center gap-2">
                    <select name="dinas_id"
                        class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-200">
                        @foreach ($dinasOptions as $d)
                            <option value="{{ $d->id }}" @selected((int) $activeDinasId === (int) $d->id)>
                                {{ $d->name }}
                            </option>
                        @endforeach
                    </select>
                    <noscript>
                        <button type="submit"
                            class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">
                            Terapkan
                        </button>
                    </noscript>
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
                    @if (($locationsForDinas ?? collect())->count() > 0)
                        <div class="rounded-xl border border-slate-200 bg-slate-50/40 px-4 py-4">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <div class="text-xs font-semibold text-slate-700">Scope Lokasi</div>
                                    <div class="text-sm text-slate-600">Aturan presensi disimpan per lokasi.</div>
                                </div>
                                <form id="location-switch" method="GET" action="{{ route('admin.attendance.manage') }}"
                                    class="flex items-center gap-2">
                                    <select name="location_id"
                                        class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" required>
                                        <option value="" disabled
                                            {{ empty($activeLocationId ?? 0) ? 'selected' : '' }}>Pilih lokasi</option>
                                        @foreach ($locationsForDinas ?? [] as $loc)
                                            <option value="{{ $loc->id }}"
                                                {{ (int) ($activeLocationId ?? 0) === (int) $loc->id ? 'selected' : '' }}>
                                                {{ $loc->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <noscript>
                                        <button type="submit"
                                            class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">
                                            Terapkan
                                        </button>
                                    </noscript>
                                </form>
                            </div>
                        </div>
                    @endif

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
                                        @if ($isSuperAdmin)
                                            <td class="px-3 py-3 whitespace-nowrap text-slate-700">
                                                {{ $loc->dinas?->name ?? '—' }}
                                            </td>
                                        @endif
                                        <td class="px-3 py-3 whitespace-nowrap text-slate-900 font-semibold">
                                            {{ $loc->name }}
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
                                        <td class="px-3 py-3 text-slate-700 truncate" title="{{ $loc->address ?? '' }}">
                                            {{ $loc->address ?? '—' }}
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
                                                <a href="{{ route('admin.attendance.locations.edit', ['location' => $loc, 'dinas_id' => (int) $activeDinasId, 'back' => 'manage']) }}"
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

    <script>
        (function() {
            var dinasSwitch = document.getElementById('dinas-switch');
            if (dinasSwitch) {
                var select = dinasSwitch.querySelector('select[name="dinas_id"]');
                if (select) {
                    select.addEventListener('change', function() {
                        dinasSwitch.submit();
                    });
                }
            }

            var officeSelect = document.getElementById('officeFromLocation');
            if (officeSelect) {
                officeSelect.addEventListener('change', function() {
                    var opt = officeSelect.options[officeSelect.selectedIndex];
                    var lat = opt && opt.getAttribute('data-lat');
                    var lng = opt && opt.getAttribute('data-lng');
                    if (!lat || !lng) return;

                    var latInput = document.querySelector('input[name="office_lat"]');
                    var lngInput = document.querySelector('input[name="office_lng"]');
                    if (latInput) latInput.value = lat;
                    if (lngInput) lngInput.value = lng;
                });
            }

            var locationSwitch = document.getElementById('location-switch');
            if (locationSwitch) {
                var locationSelect = locationSwitch.querySelector('select[name="location_id"]');
                if (locationSelect) {
                    locationSelect.addEventListener('change', function() {
                        locationSwitch.submit();
                    });
                }
            }
        })();
    </script>

@endsection
