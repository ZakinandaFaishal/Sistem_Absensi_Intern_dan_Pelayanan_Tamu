@extends('layouts.admin')

@section('title', 'Aturan Penilaian - Diskominfo Kab. Magelang')
@section('page_title', 'Aturan Penilaian')

@section('content')

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Aturan Penilaian</h2>
            <p class="mt-1 text-sm text-slate-600">Nilai otomatis berbasis jumlah hari presensi.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.users.index') }}"
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

        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-6 py-5 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100">
                        <x-icon name="star" class="h-5 w-5 text-slate-700" />
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Aturan Penilaian</p>
                        <p class="text-xs text-slate-500">Nilai otomatis berbasis jumlah hari presensi (non fake GPS).</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <form method="POST" action="{{ route('admin.users.scoring.settings') }}"
                    class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    @csrf

                    <div class="md:col-span-6">
                        <label class="block text-sm font-semibold text-slate-700">Poin per Hari Presensi</label>
                        <input name="points_per_attendance"
                            value="{{ old('points_per_attendance', $scoring['points_per_attendance'] ?? 4) }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-200"
                            inputmode="numeric" required>
                    </div>

                    <div class="md:col-span-6">
                        <label class="block text-sm font-semibold text-slate-700">Nilai Maksimal</label>
                        <input name="max_score" value="{{ old('max_score', $scoring['max_score'] ?? 100) }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-200"
                            inputmode="numeric" required>
                    </div>

                    <div class="md:col-span-12 flex items-center justify-end">
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">
                            Simpan Aturan
                        </button>
                    </div>
                </form>
            </div>
        </section>

    </div>

@endsection
