@extends('layouts.admin')

@section('title', 'Keamanan Registrasi')
@section('page_title', 'Keamanan Registrasi')

@section('content')

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">Keamanan Registrasi</h2>
            <p class="mt-1 text-sm text-slate-600">Batasi registrasi dengan kode yang dibagikan admin.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.users.index') }}"
                class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700
                       shadow-sm shadow-slate-900/5 hover:bg-slate-50 hover:shadow-md hover:shadow-slate-900/5
                       transition active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-slate-200">
                ← Kembali
            </a>
        </div>
    </div>

    <div class="pt-5 space-y-6">

        @if (session('status'))
            <div
                class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm shadow-emerald-900/5">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div
                class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 shadow-sm shadow-rose-900/5">
                <div class="font-semibold">Terjadi kesalahan</div>
                <ul class="mt-1 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-900/5 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 bg-gradient-to-b from-white to-slate-50/30">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 ring-1 ring-slate-200/70">
                        <x-icon name="lock-closed" class="h-5 w-5 text-slate-700" />
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Status Keamanan Registrasi</p>
                        <p class="mt-0.5 text-xs text-slate-500">Jika aktif, hanya user yang tahu kode bisa register.</p>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="text-sm text-slate-700">
                        Status:
                        @if (!empty($registrationSecurityEnabled ?? false))
                            <span
                                class="ml-2 inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-100">
                                Aktif
                            </span>
                        @else
                            <span
                                class="ml-2 inline-flex items-center rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 ring-1 ring-rose-100">
                                Nonaktif
                            </span>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('admin.users.registration-security.disable') }}"
                        onsubmit="return confirm('Nonaktifkan registrasi? User tidak bisa register sebelum kode diaktifkan lagi.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700
                                   shadow-sm shadow-slate-900/5 hover:bg-slate-50 hover:shadow-md hover:shadow-slate-900/5
                                   transition active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-slate-200">
                            Nonaktifkan Registrasi
                        </button>
                    </form>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                    <div class="text-xs font-semibold text-slate-700">Atur Kode Registrasi</div>
                    <div class="mt-1 text-xs text-slate-500">Gunakan minimal 6 karakter. Simpan kode ini dengan aman.</div>

                    <form method="POST" action="{{ route('admin.users.registration-security') }}"
                        class="mt-4 grid grid-cols-1 md:grid-cols-12 gap-4">
                        @csrf

                        <div class="md:col-span-5">
                            <label class="block text-sm font-semibold text-slate-700">Kode Registrasi Baru</label>
                            <input name="registration_code" type="password" autocomplete="new-password"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                       shadow-sm shadow-slate-900/5
                                       focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition"
                                placeholder="Minimal 6 karakter" required>
                            @error('registration_code')
                                <p class="mt-1 text-xs text-rose-700">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-5">
                            <label class="block text-sm font-semibold text-slate-700">Konfirmasi Kode</label>
                            <input name="registration_code_confirmation" type="password" autocomplete="new-password"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                       shadow-sm shadow-slate-900/5
                                       focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition"
                                placeholder="Ulangi kode" required>
                        </div>

                        <div class="md:col-span-2 flex items-end">
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white
                                       shadow-sm shadow-slate-900/10 hover:bg-slate-800 hover:shadow-md hover:shadow-slate-900/10
                                       transition active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-slate-200">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

    </div>

@endsection
