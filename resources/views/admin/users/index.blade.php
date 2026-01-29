@extends('layouts.admin')

@section('title', 'User Management')
@section('page_title', 'User Management')

@section('content')

    @php
        // ====== FILTER & SORT STATE (dari query string) ======
        $q = request('q', '');
        $role = request('role', '');
        $active = request('active', ''); // '', '1', '0'
        $sort = request('sort', 'created_at');
        $dir = request('dir', 'desc');

        // helper bikin URL dengan query merge
        $mergeQuery = function (array $extra = []) {
            return url()->current() . '?' . http_build_query(array_merge(request()->query(), $extra));
        };

        // helper toggle sorting untuk column tertentu
        $sortUrl = function (string $col) use ($sort, $dir, $mergeQuery) {
            $nextDir = $sort === $col && $dir === 'asc' ? 'desc' : 'asc';
            return $mergeQuery(['sort' => $col, 'dir' => $nextDir, 'page' => 1]);
        };

        // helper icon sort (simple)
        $sortIcon = function (string $col) use ($sort, $dir) {
            if ($sort !== $col) {
                return '↕';
            }
            return $dir === 'asc' ? '↑' : '↓';
        };
    @endphp

    {{-- HEADER PAGE --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">User Management</h2>
            <p class="mt-1 text-sm text-slate-600">Tambah, edit, dan kelola role serta status akun.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">

            {{-- EXPORT LAPORAN (READY + WORKING UI) --}}
            <div class="relative">
                <button type="button" id="btnExportUsers"
                    class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white
                           hover:bg-slate-800 transition active:scale-[0.98]">
                    <span>Export Laporan</span>
                    <span class="inline-block transition" id="exportUsersChevron">▾</span>
                </button>

                <div id="menuExportUsers"
                    class="hidden absolute right-0 mt-2 w-56 rounded-xl border border-slate-200 bg-white shadow-lg overflow-hidden z-50">
                    <button type="button"
                        class="export-users-action w-full text-left px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center justify-between"
                        data-url="{{ route('admin.users.export.excel', request()->query()) }}" data-label="Excel">
                        <span>Export Excel (Users)</span>
                        <span class="text-xs text-slate-400">.xlsx</span>
                    </button>

                    <button type="button"
                        class="export-users-action w-full text-left px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 flex items-center justify-between"
                        data-url="{{ route('admin.users.export.pdf', request()->query()) }}" data-label="PDF">
                        <span>Export PDF (Users)</span>
                        <span class="text-xs text-slate-400">.pdf</span>
                    </button>
                </div>
            </div>

            {{-- Hidden iframe untuk download tanpa redirect --}}
            <iframe id="dlUsersFrame" class="hidden"></iframe>

            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700
                      hover:bg-slate-50 transition">
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

        {{-- FORM CARD --}}
        <section id="form-user" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-6 py-5 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100">
                        <x-icon name="users" class="h-5 w-5 text-slate-700" />
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">
                            {{ isset($editUser) && $editUser ? 'Edit User' : 'Tambah User' }}
                        </p>
                        <p class="text-xs text-slate-500">
                            Isi data akun dan atur role.
                        </p>
                    </div>
                </div>

                @if (isset($editUser) && $editUser)
                    <span
                        class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">
                        Mode Edit
                    </span>
                @endif
            </div>

            <div class="p-6">
                <form method="POST"
                    action="{{ isset($editUser) && $editUser ? route('admin.users.update', $editUser) : route('admin.users.store') }}"
                    class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    @csrf
                    @if (isset($editUser) && $editUser)
                        @method('PATCH')
                    @endif

                    <div class="md:col-span-4">
                        <label class="block text-sm font-semibold text-slate-700">Nama</label>
                        <input name="name" value="{{ old('name', $editUser->name ?? '') }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-slate-200"
                            required>
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-sm font-semibold text-slate-700">NIK</label>
                        <input name="nik" value="{{ old('nik', $editUser->nik ?? '') }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-slate-200"
                            required inputmode="numeric">
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-sm font-semibold text-slate-700">No Telepon</label>
                        <input name="phone" value="{{ old('phone', $editUser->phone ?? '') }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-slate-200"
                            required inputmode="tel">
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-sm font-semibold text-slate-700">Username</label>
                        <input name="username" value="{{ old('username', $editUser->username ?? '') }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-slate-200"
                            required autocomplete="off">
                    </div>

                    <div class="md:col-span-5">
                        <label class="block text-sm font-semibold text-slate-700">Email</label>
                        <input type="email" name="email" value="{{ old('email', $editUser->email ?? '') }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-slate-200"
                            required>
                    </div>

                    <div class="md:col-span-4">
                        <label class="block text-sm font-semibold text-slate-700">Role</label>
                        <select name="role"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-slate-200"
                            required>
                            @php($roleValue = old('role', $editUser->role ?? 'intern'))
                            <option value="intern" @selected($roleValue === 'intern')>intern</option>
                            <option value="admin_dinas" @selected($roleValue === 'admin_dinas')>admin_dinas</option>
                        </select>
                    </div>

                    <div class="md:col-span-4">
                        <label class="block text-sm font-semibold text-slate-700">Status Intern</label>
                        <select name="intern_status"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-slate-200">
                            @php($internStatusValue = old('intern_status', $editUser->intern_status ?? 'aktif'))
                            <option value="aktif" @selected($internStatusValue === 'aktif')>aktif</option>
                            <option value="tamat" @selected($internStatusValue === 'tamat')>tamat</option>
                        </select>
                        <p class="mt-1 text-xs text-slate-500">Jika <span class="font-semibold">tamat</span>, user tidak
                            bisa presensi lagi.</p>
                    </div>

                    <div class="md:col-span-4">
                        <label class="block text-sm font-semibold text-slate-700">Mulai Magang</label>
                        <input name="internship_start_date" type="date"
                            value="{{ old('internship_start_date', $editUser->internship_start_date ?? '') }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-slate-200">
                    </div>

                    <div class="md:col-span-4">
                        <label class="block text-sm font-semibold text-slate-700">Selesai Magang</label>
                        <input name="internship_end_date" type="date"
                            value="{{ old('internship_end_date', $editUser->internship_end_date ?? '') }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-slate-200">
                    </div>

                    <div class="md:col-span-4">
                        <label class="block text-sm font-semibold text-slate-700">Lokasi / Dinas Magang</label>
                        <select name="internship_location_id"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-slate-200">
                            <option value="">—</option>
                            @foreach ($locations ?? [] as $loc)
                                <option value="{{ $loc->id }}" @selected((string) old('internship_location_id', $editUser->internship_location_id ?? '') === (string) $loc->id)>
                                    {{ $loc->name }}{{ $loc->code ? ' (' . $loc->code . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-slate-500">Diabaikan jika role bukan <span
                                class="font-semibold">intern</span>.</p>
                    </div>

                    <div class="md:col-span-4">
                        <label class="block text-sm font-semibold text-slate-700">Override Nilai (0–100)</label>
                        <input name="score_override" value="{{ old('score_override', $editUser->score_override ?? '') }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-slate-200"
                            inputmode="numeric" placeholder="Kosongkan untuk auto">
                    </div>

                    <div class="md:col-span-12">
                        <label class="block text-sm font-semibold text-slate-700">Catatan Override (opsional)</label>
                        <input name="score_override_note"
                            value="{{ old('score_override_note', $editUser->score_override_note ?? '') }}"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-slate-200"
                            placeholder="Contoh: penugasan khusus / dispensasi">
                    </div>

                    <div class="md:col-span-4">
                        <label class="block text-sm font-semibold text-slate-700">
                            Password {{ isset($editUser) && $editUser ? '(Kosongkan jika tidak diubah)' : '' }}
                        </label>
                        <input type="password" name="password"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-slate-200"
                            @if (!isset($editUser) || !$editUser) required @endif>
                    </div>

                    <div class="md:col-span-4">
                        <label class="block text-sm font-semibold text-slate-700">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation"
                            class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-slate-200"
                            @if (!isset($editUser) || !$editUser) required @endif>
                    </div>

                    <div class="md:col-span-12 flex flex-col sm:flex-row sm:items-center sm:justify-end gap-2 pt-2">
                        @if (isset($editUser) && $editUser)
                            <a href="{{ route('admin.users.index') }}"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700
                                      hover:bg-slate-50 transition">
                                Batal
                            </a>
                        @endif

                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white
                                   hover:bg-slate-800 transition">
                            {{ isset($editUser) && $editUser ? 'Simpan' : 'Tambah' }}
                        </button>
                    </div>
                </form>
            </div>
        </section>

        {{-- TABLE CARD --}}
        <section id="daftar-users" class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">

            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-6 py-5 border-b border-slate-200">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Daftar Users</h3>
                    <p class="text-sm text-slate-500">Kelola role, status, dan aksi akun.</p>
                </div>
                <div class="text-xs text-slate-500">
                    Menampilkan data.
                </div>
            </div>

            {{-- FILTER BAR --}}
            <div class="px-6 pt-5">
                <form method="GET" action="{{ route('admin.users.index') }}"
                    class="grid grid-cols-1 sm:grid-cols-12 gap-3">

                    class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-slate-200">
                    <option value="" @selected($role === '')>Semua</option>
                    <option value="intern" @selected($role === 'intern')>intern</option>
                    <option value="admin_dinas" @selected($role === 'admin_dinas')>admin_dinas</option>
                    </select>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-600">Status</label>
                <select name="active"
                    class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-slate-200">
                    <option value="" @selected($active === '')>Semua</option>
                    <option value="1" @selected($active === '1')>Aktif</option>
                    <option value="0" @selected($active === '0')>Nonaktif</option>
                </select>
            </div>

            <div class="sm:col-span-2 flex items-end gap-2">
                {{-- keep sort state --}}
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="dir" value="{{ $dir }}">

                <button type="submit"
                    class="w-full inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white
                                   hover:bg-slate-800 transition">
                    Terapkan
                </button>

                <a href="{{ route('admin.users.index') }}"
                    class="w-full inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700
                                   hover:bg-slate-50 transition">
                    Reset
                </a>
            </div>
            </form>

            {{-- INFO FILTER --}}
            <div class="mt-3 text-xs text-slate-500">
                Sort: <span class="font-semibold text-slate-700">{{ $sort }}</span> ({{ $dir }})
            </div>
    </div>

    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-600 border-b border-slate-200">
                        <th class="py-3 pr-4 font-semibold">Nama</th>
                        <th class="py-3 pr-4 font-semibold">Username</th>
                        <th class="py-3 pr-4 font-semibold">NIK</th>
                        <th class="py-3 pr-4 font-semibold">No. Telepon</th>
                        <th class="py-3 pr-4 font-semibold">Email</th>
                        <th class="py-3 pr-4 font-semibold">Role</th>
                        <th class="py-3 pr-4 font-semibold">Status</th>
                        <th class="py-3 pr-4 font-semibold">Nilai</th>
                        <th class="py-3 pr-0 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-3 pr-4 whitespace-nowrap">
                                <div class="font-semibold text-slate-900">{{ $user->name }}</div>
                                <div class="text-xs text-slate-500">{{ $user->email }}</div>
                            </td>

                            <td class="py-3 pr-4 whitespace-nowrap text-slate-700">{{ $user->username ?? '—' }}
                            </td>
                            <td class="py-3 pr-4 whitespace-nowrap text-slate-700">{{ $user->nik ?? '—' }}</td>
                            <td class="py-3 pr-4 whitespace-nowrap text-slate-700">{{ $user->phone ?? '—' }}</td>
                            <td class="py-3 pr-4 whitespace-nowrap text-slate-700">{{ $user->email }}</td>

                            <td class="py-3 pr-4 whitespace-nowrap align-middle">
                                <form method="POST" action="{{ route('admin.users.role', $user) }}"
                                    class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')

                                    <div class="relative">
                                        <select name="role"
                                            class="appearance-none h-8 rounded-lg border border-slate-200 bg-white
                                                           pl-3 pr-7 text-xs focus:outline-none focus:ring-2 focus:ring-slate-200">
                                            <option value="intern" @selected(($user->role ?? 'intern') === 'intern')>intern</option>
                                            <option value="admin_dinas" @selected(($user->role ?? null) === 'admin_dinas')>admin_dinas</option>
                                        </select>
                                    </div>

                                    <div class="relative">
                                        <select name="dinas_id"
                                            class="appearance-none h-8 rounded-lg border border-slate-200 bg-white
                                                           pl-3 pr-7 text-xs focus:outline-none focus:ring-2 focus:ring-slate-200">
                                            <option value="">— dinas —</option>
                                            @foreach ($dinasOptions ?? [] as $d)
                                                <option value="{{ $d->id }}" @selected((string) ($user->dinas_id ?? '') === (string) $d->id)>
                                                    {{ $d->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <button type="submit"
                                        class="h-8 rounded-lg bg-slate-900 px-3 text-xs font-semibold text-white hover:bg-slate-800 transition">
                                        Simpan
                                    </button>
                                </form>
                            </td>

                            <td class="py-3 pr-4 whitespace-nowrap">
                                @if (($user->role ?? 'intern') === 'intern')
                                    @php($st = $user->intern_status ?? 'aktif')
                                    <span
                                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $st === 'tamat' ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' }}">
                                        {{ $st }}
                                    </span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>

                            <td class="py-3 pr-4 whitespace-nowrap">
                                @if (($user->role ?? 'intern') !== 'intern')
                                    <span class="text-slate-400">—</span>
                                @else
                                    <div class="font-semibold text-slate-900">
                                        {{ (int) ($user->computed_score ?? 0) }}</div>
                                    @if (!empty($user->computed_score_subtitle))
                                        <div class="text-xs text-slate-500">{{ $user->computed_score_subtitle }}
                                        </div>
                                    @endif
                                @endif
                            </td>

                            <td class="py-3 pr-0 whitespace-nowrap text-right">
                                @if (auth()->id() === $user->id)
                                    <span
                                        class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                        Akun sendiri
                                    </span>
                                @else
                                    <div class="inline-flex items-center gap-2">
                                        @if (($user->role ?? 'intern') === 'intern' && ($user->intern_status ?? 'aktif') === 'aktif')
                                            <button type="button"
                                                class="btnCompleteInternship rounded-xl bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700 transition"
                                                data-url="{{ route('admin.users.complete-internship', $user) }}"
                                                data-name="{{ $user->name }}">
                                                Selesai Magang
                                            </button>
                                        @endif

                                        @if (($user->role ?? 'intern') === 'intern' && ($user->intern_status ?? 'aktif') === 'tamat')
                                            <a href="{{ route('admin.users.certificate.pdf', $user) }}"
                                                class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                                Sertifikat
                                            </a>
                                        @endif

                                        <a href="{{ $mergeQuery(['edit' => $user->id]) }}"
                                            class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                            onsubmit="return confirm('Hapus user ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="rounded-xl bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-700 transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-10 text-center text-slate-600">
                                Belum ada user.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{-- penting: pagination harus appends() di controller --}}
            {{ $users->links() }}
        </div>
    </div>
    </section>

    </div>

    {{-- MODAL: COMPLETE INTERNSHIP --}}
    <div id="completeInternshipModal" class="hidden fixed inset-0 z-50">
        <div class="absolute inset-0 bg-slate-900/60" data-close="1"></div>

        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="w-full max-w-xl rounded-2xl bg-white shadow-xl overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Selesai Magang</p>
                        <p id="completeInternshipSubtitle" class="mt-0.5 text-xs text-slate-500">Input penilaian akhir.
                        </p>
                    </div>
                    <button type="button"
                        class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                        data-close="1">Tutup</button>
                </div>

                <form id="completeInternshipForm" method="POST" action="#" class="p-6 space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Aspek 1</label>
                            <input name="aspect_1" type="number" min="0" max="100" required
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                placeholder="0-100">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Aspek 2</label>
                            <input name="aspect_2" type="number" min="0" max="100" required
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                placeholder="0-100">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Aspek 3</label>
                            <input name="aspect_3" type="number" min="0" max="100" required
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                placeholder="0-100">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Aspek 4</label>
                            <input name="aspect_4" type="number" min="0" max="100" required
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                placeholder="0-100">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700">Aspek 5</label>
                            <input name="aspect_5" type="number" min="0" max="100" required
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                placeholder="0-100">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Nama Penandatangan (opsional)</label>
                            <input name="signatory_name" type="text"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                placeholder="Kepala Dinas">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700">Jabatan Penandatangan
                                (opsional)</label>
                            <input name="signatory_title" type="text"
                                class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm"
                                placeholder="Kepala Dinas Kominfo">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <button type="button"
                            class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                            data-close="1">Batal</button>
                        <button type="submit"
                            class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition">Simpan
                            & Tamatkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- EXPORT USERS SCRIPT --}}
    <script>
        (function() {
            const modal = document.getElementById('completeInternshipModal');
            const form = document.getElementById('completeInternshipForm');
            const subtitle = document.getElementById('completeInternshipSubtitle');

            function openModal(url, name) {
                if (!modal || !form) return;
                form.setAttribute('action', url);
                if (subtitle) subtitle.textContent = 'Input penilaian akhir untuk: ' + (name || '');
                modal.classList.remove('hidden');
            }

            function closeModal() {
                if (!modal) return;
                modal.classList.add('hidden');
            }

            document.querySelectorAll('.btnCompleteInternship').forEach((btn) => {
                btn.addEventListener('click', () => {
                    openModal(btn.getAttribute('data-url'), btn.getAttribute('data-name'));
                });
            });
            modal?.querySelectorAll('[data-close="1"]').forEach((el) => {
                el.addEventListener('click', closeModal);
            });

            const btn = document.getElementById('btnExportUsers');
            const menu = document.getElementById('menuExportUsers');
            const chevron = document.getElementById('exportUsersChevron');
            const dlFrame = document.getElementById('dlUsersFrame');

            function openMenu() {
                if (!menu) return;
                menu.classList.remove('hidden');
                if (chevron) chevron.style.transform = 'rotate(180deg)';
            }

            function closeMenu() {
                if (!menu) return;
                menu.classList.add('hidden');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }

            btn?.addEventListener('click', (e) => {
                e.stopPropagation();
                if (!menu) return;
                menu.classList.contains('hidden') ? openMenu() : closeMenu();
            });

            document.addEventListener('click', closeMenu);
            menu?.addEventListener('click', (e) => e.stopPropagation());

            document.querySelectorAll('.export-users-action').forEach(el => {
                el.addEventListener('click', () => {
                    const url = el.getAttribute('data-url');
                    const label = el.getAttribute('data-label') || 'Export';

                    const original = el.innerHTML;
                    el.disabled = true;
                    el.innerHTML = `
                        <span class="inline-flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full border-2 border-slate-300 border-t-slate-700 animate-spin"></span>
                            <span>Mengekspor ${label}...</span>
                        </span>
                        <span class="text-xs text-slate-400">harap tunggu</span>
                    `;

                    closeMenu();

                    if (url && dlFrame) dlFrame.src = url;

                    setTimeout(() => {
                        el.disabled = false;
                        el.innerHTML = original;
                    }, 1800);
                });
            });
        })();
    </script>

@endsection
