@extends('layouts.admin')

@section('title', $editUser ? 'Edit User' : 'Tambah User')
@section('page_title', $editUser ? 'Edit User' : 'Tambah User')

@section('content')
@php
    $actor = Auth::user();
    $actor?->loadMissing('dinas');

    $isAdminDinasActor = $actor && ($actor->role ?? null) === 'admin_dinas';
    $roleValue = old('role', $editUser->role ?? 'intern');

    $internStatusValue = old('intern_status', $editUser->intern_status ?? 'aktif');

    $defaultLocationId = (int) old(
        'internship_location_id',
        $editUser->internship_location_id ?? ($defaultInternshipLocationId ?? 0)
    );

    $defaultLocationName = $actor?->dinas?->name ?? ($defaultInternshipLocationName ?? '—');
@endphp

<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-xl font-extrabold tracking-tight text-slate-900">
            {{ $editUser ? 'Edit User' : 'Tambah User' }}
        </h2>
        <p class="mt-1 text-sm text-slate-600">Isi data akun dan atur role.</p>
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
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm shadow-emerald-900/5">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 shadow-sm shadow-rose-900/5">
            <div class="font-semibold">Terjadi kesalahan</div>
            <ul class="mt-1 list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-900/5 overflow-hidden">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-6 py-5 border-b border-slate-200 bg-gradient-to-b from-white to-slate-50/30">
            <div class="flex items-center gap-2">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 ring-1 ring-slate-200/70">
                    <x-icon name="users" class="h-5 w-5 text-slate-700" />
                </span>
                <div>
                    <p class="text-sm font-semibold text-slate-900">{{ $editUser ? 'Edit User' : 'Tambah User' }}</p>
                    <p class="text-xs text-slate-500">Data akun + konfigurasi.</p>
                </div>
            </div>

            @if ($editUser)
                <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-800 ring-1 ring-amber-200">
                    Mode Edit
                </span>
            @endif
        </div>

        <div class="p-6">
            <form method="POST"
                  action="{{ $editUser ? route('admin.users.update', $editUser) : route('admin.users.store') }}"
                  class="grid grid-cols-1 md:grid-cols-12 gap-4">
                @csrf
                @if ($editUser)
                    @method('PATCH')
                @endif

                {{-- =========================
                     IDENTITAS
                ========================== --}}
                <div class="md:col-span-12">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                        <div class="flex items-center justify-between gap-2">
                            <div>
                                <div class="text-xs font-semibold text-slate-700">Identitas</div>
                                <div class="mt-0.5 text-xs text-slate-500">Informasi dasar akun.</div>
                            </div>
                            <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-[11px] font-semibold text-slate-600 ring-1 ring-slate-200">
                                Wajib diisi
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-12 gap-4">
                            <div class="md:col-span-4">
                                <label class="block text-sm font-semibold text-slate-700">Nama</label>
                                <input name="name" value="{{ old('name', $editUser->name ?? '') }}"
                                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                              shadow-sm shadow-slate-900/5 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition"
                                       required>
                            </div>

                            <div class="md:col-span-4">
                                <label class="block text-sm font-semibold text-slate-700">NIK</label>
                                <input name="nik" value="{{ old('nik', $editUser->nik ?? '') }}"
                                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                              shadow-sm shadow-slate-900/5 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition"
                                       required inputmode="numeric">
                            </div>

                            <div class="md:col-span-4">
                                <label class="block text-sm font-semibold text-slate-700">No Telepon</label>
                                <input name="phone" value="{{ old('phone', $editUser->phone ?? '') }}"
                                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                              shadow-sm shadow-slate-900/5 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition"
                                       required inputmode="tel">
                            </div>

                            <div class="md:col-span-4">
                                <label class="block text-sm font-semibold text-slate-700">Username</label>
                                <input name="username" value="{{ old('username', $editUser->username ?? '') }}"
                                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                              shadow-sm shadow-slate-900/5 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition"
                                       required autocomplete="off">
                            </div>

                            <div class="md:col-span-4">
                                <label class="block text-sm font-semibold text-slate-700">Email</label>
                                <input type="email" name="email" value="{{ old('email', $editUser->email ?? '') }}"
                                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                              shadow-sm shadow-slate-900/5 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition"
                                       required>
                            </div>

                            <div class="md:col-span-4">
                                <label class="block text-sm font-semibold text-slate-700">Role</label>

                                @if ($editUser && ($editUser->role ?? 'intern') === 'super_admin')
                                    <input type="hidden" name="role" value="super_admin">
                                    <div class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
                                        <span class="font-semibold">super_admin</span>
                                        <span class="text-slate-500">(dikunci)</span>
                                    </div>
                                @else
                                    @if ($isAdminDinasActor)
                                        <input type="hidden" name="role" value="intern">
                                        <div class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
                                            <span class="font-semibold">intern</span>
                                            <span class="text-slate-500">(dibuat oleh admin dinas)</span>
                                        </div>
                                    @else
                                        <select id="userRoleSelect" name="role"
                                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                                       shadow-sm shadow-slate-900/5 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition"
                                                required>
                                            <option value="intern" @selected($roleValue === 'intern')>intern</option>
                                            <option value="admin_dinas" @selected($roleValue === 'admin_dinas')>admin_dinas</option>
                                        </select>
                                    @endif
                                @endif
                            </div>

                            {{-- Dinas untuk admin_dinas --}}
                            <div id="userDinasFields"
                                 class="md:col-span-4 {{ $isAdminDinasActor ? 'hidden' : ($roleValue === 'admin_dinas' ? '' : 'hidden') }}">
                                <label class="block text-sm font-semibold text-slate-700">Dinas (wajib untuk admin_dinas)</label>
                                <select id="userDinasSelect" name="dinas_id"
                                        class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                               shadow-sm shadow-slate-900/5 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition">
                                    <option value="">—</option>
                                    @foreach ($dinasOptions ?? [] as $d)
                                        <option value="{{ $d->id }}"
                                                @selected((string) old('dinas_id', $editUser->dinas_id ?? '') === (string) $d->id)>
                                            {{ $d->name }}{{ $d->code ? ' (' . $d->code . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-slate-500">
                                    Wajib dipilih jika role <span class="font-semibold">admin_dinas</span>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- =========================
                     INTERN FIELDS
                ========================== --}}
                <div id="userInternFields" class="md:col-span-12 {{ $roleValue === 'intern' ? '' : 'hidden' }}">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                        <div class="text-xs font-semibold text-slate-700">Konfigurasi Intern</div>
                        <div class="mt-0.5 text-xs text-slate-500">Atur status, periode magang, lokasi, dan penilaian.</div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-12 gap-4">
                            <div class="md:col-span-4">
                                <label class="block text-sm font-semibold text-slate-700">Status Intern</label>
                                <select name="intern_status"
                                        class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                               shadow-sm shadow-slate-900/5 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition">
                                    <option value="aktif" @selected($internStatusValue === 'aktif')>aktif</option>
                                    <option value="tamat" @selected($internStatusValue === 'tamat')>tamat</option>
                                </select>
                                <p class="mt-1 text-xs text-slate-500">
                                    Jika <span class="font-semibold">tamat</span>, user tidak bisa presensi lagi.
                                </p>
                            </div>

                            <div class="md:col-span-4">
                                <label class="block text-sm font-semibold text-slate-700">Mulai Magang</label>
                                <input name="internship_start_date" type="date"
                                       value="{{ old('internship_start_date', $editUser->internship_start_date ?? '') }}"
                                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                              shadow-sm shadow-slate-900/5 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition">
                            </div>

                            <div class="md:col-span-4">
                                <label class="block text-sm font-semibold text-slate-700">Selesai Magang</label>
                                <input name="internship_end_date" type="date"
                                       value="{{ old('internship_end_date', $editUser->internship_end_date ?? '') }}"
                                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                              shadow-sm shadow-slate-900/5 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition">
                            </div>

                            <div class="md:col-span-4">
                                <label class="block text-sm font-semibold text-slate-700">Lokasi / Dinas Magang</label>

                                @if ($isAdminDinasActor)
                                    <input type="hidden" name="internship_location_id" value="{{ $defaultLocationId }}">
                                    <div class="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
                                        <span class="font-semibold">{{ $defaultLocationName }}</span>
                                        <span class="text-slate-500">(otomatis)</span>
                                    </div>
                                    <p class="mt-1 text-xs text-slate-500">Lokasi magang otomatis mengikuti dinas Anda.</p>
                                @else
                                    <select name="internship_location_id"
                                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                                   shadow-sm shadow-slate-900/5 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition">
                                        <option value="">—</option>
                                        @foreach ($locations ?? [] as $loc)
                                            <option value="{{ $loc->id }}"
                                                    @selected((string) old('internship_location_id', $editUser->internship_location_id ?? '') === (string) $loc->id)>
                                                {{ $loc->name }}{{ $loc->code ? ' (' . $loc->code . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <div class="md:col-span-4">
                                <label class="block text-sm font-semibold text-slate-700">Override Nilai (0–100)</label>
                                <input name="score_override"
                                       value="{{ old('score_override', $editUser->score_override ?? '') }}"
                                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                              shadow-sm shadow-slate-900/5 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition"
                                       inputmode="numeric" placeholder="Kosongkan untuk auto">
                            </div>

                            <div class="md:col-span-4">
                                <label class="block text-sm font-semibold text-slate-700">Catatan Override (opsional)</label>
                                <input name="score_override_note"
                                       value="{{ old('score_override_note', $editUser->score_override_note ?? '') }}"
                                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                              shadow-sm shadow-slate-900/5 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition"
                                       placeholder="Contoh: penugasan khusus / dispensasi">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- =========================
                     PASSWORD
                ========================== --}}
                <div class="md:col-span-12">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/40 p-4">
                        <div class="text-xs font-semibold text-slate-700">Keamanan</div>
                        <div class="mt-0.5 text-xs text-slate-500">
                            {{ $editUser ? 'Kosongkan jika tidak ingin mengubah password.' : 'Wajib diisi untuk akun baru.' }}
                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-12 gap-4">
                            <div class="md:col-span-6">
                                <label class="block text-sm font-semibold text-slate-700">
                                    Password {{ $editUser ? '(Opsional)' : '' }}
                                </label>
                                <input type="password" name="password"
                                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                              shadow-sm shadow-slate-900/5 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition"
                                       @if (!$editUser) required @endif>
                            </div>

                            <div class="md:col-span-6">
                                <label class="block text-sm font-semibold text-slate-700">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation"
                                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                              shadow-sm shadow-slate-900/5 focus:outline-none focus:ring-4 focus:ring-slate-200 focus:border-slate-300 transition"
                                       @if (!$editUser) required @endif>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- =========================
                     ACTIONS
                ========================== --}}
                <div class="md:col-span-12 flex flex-col sm:flex-row sm:items-center sm:justify-end gap-2 pt-2">
                    @if ($editUser)
                        <a href="{{ route('admin.users.index') }}"
                           class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700
                                  shadow-sm shadow-slate-900/5 hover:bg-slate-50 hover:shadow-md hover:shadow-slate-900/5
                                  transition active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-slate-200">
                            Batal
                        </a>
                    @endif

                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white
                                   shadow-sm shadow-slate-900/10 hover:bg-slate-800 hover:shadow-md hover:shadow-slate-900/10
                                   transition active:scale-[0.98] focus:outline-none focus:ring-4 focus:ring-slate-200">
                        {{ $editUser ? 'Simpan' : 'Tambah' }}
                    </button>
                </div>
            </form>
        </div>
    </section>

</div>

<script>
    (function () {
        const roleSelect  = document.getElementById('userRoleSelect');
        const roleHidden  = document.querySelector('input[name="role"]');

        const dinasFields = document.getElementById('userDinasFields');
        const dinasSelect = document.getElementById('userDinasSelect');

        const internFields = document.getElementById('userInternFields');

        const getRole = () => (roleSelect?.value || roleHidden?.value || 'intern');

        const setVisible = (el, visible) => {
            if (!el) return;
            el.classList.toggle('hidden', !visible);

            // disable input/select inside hidden area (prevent submit stray values)
            el.querySelectorAll('input, select, textarea').forEach((x) => {
                // jangan disable role hidden input
                if (x.name === 'role') return;
                x.disabled = !visible;
            });
        };

        const sync = () => {
            const role = getRole();
            const isAdminDinas = role === 'admin_dinas';
            const isIntern = role === 'intern';

            setVisible(dinasFields, isAdminDinas);
            setVisible(internFields, isIntern);

            if (dinasSelect) dinasSelect.required = isAdminDinas;
        };

        roleSelect?.addEventListener('change', sync);
        sync();
    })();
</script>
@endsection
