@extends('layouts.admin')

@section('title', 'Aturan Presensi - Diskominfo Kab. Magelang')
@section('page_title', 'Aturan Presensi')

@section('content')

    {{-- HEADER PAGE --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Aturan Presensi</h2>
            <p class="mt-1 text-sm text-slate-600">Geofence radius + pembatasan jam check-in/check-out.</p>
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
                <p class="text-sm font-semibold text-slate-900">Aturan Presensi</p>
                <p class="mt-0.5 text-xs text-slate-500">Geofence radius + pembatasan jam check-in/check-out.</p>
            </div>

            <form method="POST" action="{{ route('admin.attendance.settings') }}" class="p-6 space-y-5">
                @csrf

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
                    <button type="submit"
                        class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">
                        Simpan Aturan
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
