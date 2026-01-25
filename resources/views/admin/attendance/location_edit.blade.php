@extends('layouts.admin')

@section('title', 'Edit Lokasi - Diskominfo Kab. Magelang')
@section('page_title', 'Edit Lokasi')

@section('content')

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Edit Lokasi</h2>
            <p class="mt-1 text-sm text-slate-600">Perbarui data lokasi/dinas penugasan peserta magang.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ $backUrl }}"
                class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                ← Kembali
            </a>
        </div>
    </div>

    <div class="pt-5 space-y-6">

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

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200">
                <p class="text-sm font-semibold text-slate-900">Form Edit Lokasi</p>
                <p class="mt-0.5 text-xs text-slate-500">Dinas: {{ $location->dinas?->name ?? '—' }}</p>
            </div>

            <form method="POST" action="{{ route('admin.attendance.locations.update', $location) }}" class="p-6 space-y-5">
                @csrf
                @method('PATCH')

                <input type="hidden" name="_redirect" value="{{ $backKey ?? 'manage' }}" />
                @if (!empty($activeDinasId ?? 0))
                    <input type="hidden" name="dinas_id" value="{{ (int) $activeDinasId }}" />
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-700">Nama</label>
                        <input name="name" value="{{ old('name', $location->name) }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" required>
                        @error('name')
                            <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-700">Kode (opsional)</label>
                        <input name="code" value="{{ old('code', $location->code) }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                            placeholder="KOMINFO">
                        @error('code')
                            <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-700">Latitude</label>
                        <input name="lat" value="{{ old('lat', $location->lat) }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" required>
                        @error('lat')
                            <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-700">Longitude</label>
                        <input name="lng" value="{{ old('lng', $location->lng) }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" required>
                        @error('lng')
                            <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-700">Alamat (opsional)</label>
                    <input name="address" value="{{ old('address', $location->address) }}"
                        class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                        placeholder="Alamat singkat">
                    @error('address')
                        <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-2">
                    <a href="{{ $backUrl }}"
                        class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200">
                <p class="text-sm font-semibold text-slate-900">Aturan Presensi</p>
                <p class="mt-0.5 text-xs text-slate-500">Aturan untuk dinas lokasi ini (radius + jam + akurasi).</p>
            </div>

            <form method="POST" action="{{ route('admin.attendance.settings') }}" class="p-6 space-y-5">
                @csrf

                <input type="hidden" name="dinas_id" value="{{ (int) ($location->dinas_id ?? 0) }}" />
                <input type="hidden" name="location_id" value="{{ (int) $location->id }}" />

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

                @if (($locationsForDinas ?? collect())->count() > 0)
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <div class="text-xs font-semibold text-slate-700">Gunakan koordinat dari Lokasi</div>
                        <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <select id="officeFromLocation"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                                    <option value="">— Pilih lokasi —</option>
                                    @foreach ($locationsForDinas as $loc)
                                        <option value="{{ $loc->id }}" data-lat="{{ $loc->lat }}" data-lng="{{ $loc->lng }}">
                                            {{ $loc->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-slate-500">Memasukkan lat/lng otomatis (tidak langsung menyimpan).</p>
                            </div>
                        </div>
                    </div>
                @endif

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
                    <button type="submit"
                        class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">
                        Simpan Aturan
                    </button>
                </div>
            </form>
        </div>

        <form method="POST" action="{{ route('admin.attendance.locations.destroy', $location) }}"
            onsubmit="return confirm('Hapus lokasi ini?');"
            class="flex justify-end">
            @csrf
            @method('DELETE')
            <input type="hidden" name="_redirect" value="{{ $backKey ?? 'manage' }}" />
            @if (!empty($activeDinasId ?? 0))
                <input type="hidden" name="dinas_id" value="{{ (int) $activeDinasId }}" />
            @endif
            <button type="submit"
                class="inline-flex items-center rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700 transition">
                Hapus Lokasi
            </button>
        </form>

    </div>

    <script>
        (function() {
            var officeSelect = document.getElementById('officeFromLocation');
            if (!officeSelect) return;

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
        })();
    </script>

@endsection
