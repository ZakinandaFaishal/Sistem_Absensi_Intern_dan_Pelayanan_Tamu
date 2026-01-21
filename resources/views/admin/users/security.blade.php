@extends('layouts.admin')

@section('title', 'Keamanan Registrasi - Diskominfo Kab. Magelang')
@section('page_title', 'Keamanan Registrasi')

@section('content')

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Keamanan Registrasi</h2>
            <p class="mt-1 text-sm text-slate-600">Batasi registrasi dengan kode yang dibagikan admin.</p>
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
            <div class="px-6 py-5 border-b border-slate-200">
                <p class="text-sm font-semibold text-slate-900">Status Keamanan Registrasi</p>
                <p class="mt-0.5 text-xs text-slate-500">Jika aktif, hanya user yang tahu kode bisa register.</p>
            </div>

            <div class="p-6 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="text-sm text-slate-700">
                        Status:
                        @if (!empty($registrationSecurityEnabled ?? false))
                            <span
                                class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">Aktif</span>
                        @else
                            <span
                                class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-800">Nonaktif</span>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('admin.users.registration-security.disable') }}"
                        onsubmit="return confirm('Nonaktifkan registrasi? User tidak bisa register sebelum kode diaktifkan lagi.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                            Nonaktifkan Registrasi
                        </button>
                    </form>
                </div>

                <form method="POST" action="{{ route('admin.users.registration-security') }}"
                    class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    @csrf
                    <div class="md:col-span-5">
                        <label class="block text-sm font-semibold text-slate-700">Kode Registrasi Baru</label>
                        <input name="registration_code" type="password" autocomplete="new-password"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                            placeholder="Minimal 6 karakter" required>
                        @error('registration_code')
                            <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-5">
                        <label class="block text-sm font-semibold text-slate-700">Konfirmasi Kode</label>
                        <input name="registration_code_confirmation" type="password" autocomplete="new-password"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                            placeholder="Ulangi kode" required>
                    </div>
                    <div class="md:col-span-2 flex items-end">
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </section>

    </div>

@endsection
