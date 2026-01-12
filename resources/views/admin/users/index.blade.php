@extends('layouts.admin')

@section('title', 'User Management - Diskominfo Kab. Magelang')
@section('page_title', 'User Management')

@section('content')

    {{-- HEADER PAGE --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-extrabold tracking-tight text-slate-900">User Management</h2>
            <p class="mt-1 text-sm text-slate-600">Tambah, edit, dan kelola role serta status akun.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
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
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
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
                            Isi data akun dan atur role serta status aktif.
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

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700">Aktif</label>
                        <div class="mt-2 flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                            <input type="hidden" name="active" value="0">
                            <input id="active" type="checkbox" name="active" value="1"
                                class="rounded border-slate-300" @checked(old('active', $editUser->active ?? true ? '1' : '0') === '1')>
                            <label for="active" class="text-sm text-slate-700">Aktif</label>
                        </div>
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
                            <option value="admin" @selected($roleValue === 'admin')>admin</option>
                        </select>
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
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between px-6 py-5 border-b border-slate-200">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Daftar Users</h3>
                    <p class="text-sm text-slate-500">Kelola role, status aktif, dan aksi akun.</p>
                </div>
                <div class="text-xs text-slate-500">
                    Menampilkan data (paginasi).
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
                                <th class="py-3 pr-4 font-semibold">Aktif</th>
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

                                    <td class="py-3 pr-4 whitespace-nowrap">
                                        <form method="POST" action="{{ route('admin.users.role', $user) }}"
                                            class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <select name="role"
                                                class="rounded-xl border border-slate-200 bg-white px-2 py-1 text-sm">
                                                <option value="intern" @selected(($user->role ?? 'intern') === 'intern')>intern</option>
                                                <option value="admin" @selected(($user->role ?? null) === 'admin')>admin</option>
                                            </select>
                                            <button type="submit"
                                                class="rounded-xl bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-800 transition">
                                                Simpan
                                            </button>
                                        </form>
                                    </td>

                                    <td class="py-3 pr-4 whitespace-nowrap">
                                        <form method="POST" action="{{ route('admin.users.active', $user) }}"
                                            class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="active" value="0">

                                            <label
                                                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                                                <input type="checkbox" name="active" value="1"
                                                    class="rounded border-slate-300" @checked(($user->active ?? true) === true)>
                                                <span class="text-sm text-slate-700">Aktif</span>
                                            </label>

                                            <button type="submit"
                                                class="rounded-xl bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-800 transition">
                                                Simpan
                                            </button>
                                        </form>
                                    </td>

                                    <td class="py-3 pr-0 whitespace-nowrap text-right">
                                        @if (auth()->id() === $user->id)
                                            <span
                                                class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                                Akun sendiri
                                            </span>
                                        @else
                                            <div class="inline-flex items-center gap-2">
                                                <a href="{{ route('admin.users.index', ['edit' => $user->id]) }}"
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
                                    <td colspan="8" class="py-10 text-center text-slate-600">
                                        Belum ada user.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            </div>
        </section>

    </div>

@endsection
