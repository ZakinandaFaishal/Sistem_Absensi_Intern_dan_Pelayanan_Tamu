<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">User Management</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    @if (session('status'))
                        <div class="rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="rounded border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                            <div class="font-semibold">Terjadi kesalahan</div>
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="rounded border bg-gray-50 p-4">
                        <div class="font-semibold text-gray-900">Tambah User</div>
                        <form method="POST" action="{{ route('admin.users.store') }}"
                            class="mt-3 grid grid-cols-1 md:grid-cols-6 gap-3">
                            @csrf

                            <div class="md:col-span-2">
                                <label class="block text-sm text-gray-700">Nama</label>
                                <input name="name" value="{{ old('name') }}"
                                    class="mt-1 w-full border rounded px-3 py-2" required>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm text-gray-700">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                    class="mt-1 w-full border rounded px-3 py-2" required>
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-sm text-gray-700">Role</label>
                                <select name="role" class="mt-1 w-full border rounded px-3 py-2" required>
                                    <option value="intern" @selected(old('role', 'intern') === 'intern')>intern</option>
                                    <option value="admin" @selected(old('role') === 'admin')>admin</option>
                                </select>
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-sm text-gray-700">Aktif</label>
                                <div class="mt-2">
                                    <input type="hidden" name="active" value="0">
                                    <label class="inline-flex items-center gap-2">
                                        <input type="checkbox" name="active" value="1"
                                            @checked(old('active', '1') === '1')>
                                        <span class="text-sm">Aktif</span>
                                    </label>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm text-gray-700">Password</label>
                                <input type="password" name="password" class="mt-1 w-full border rounded px-3 py-2"
                                    required>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm text-gray-700">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation"
                                    class="mt-1 w-full border rounded px-3 py-2" required>
                            </div>

                            <div class="md:col-span-2 flex items-end">
                                <button type="submit"
                                    class="w-full md:w-auto px-4 py-2 bg-gray-800 text-white rounded">
                                    Tambah
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-600">
                                    <th class="py-2 pr-4">Nama</th>
                                    <th class="py-2 pr-4">Email</th>
                                    <th class="py-2 pr-4">Role</th>
                                    <th class="py-2 pr-4">Aktif</th>
                                    <th class="py-2 pr-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr class="border-t">
                                        <td class="py-2 pr-4 whitespace-nowrap">{{ $user->name }}</td>
                                        <td class="py-2 pr-4 whitespace-nowrap">{{ $user->email }}</td>
                                        <td class="py-2 pr-4 whitespace-nowrap">
                                            <form method="POST" action="{{ route('admin.users.role', $user) }}"
                                                class="flex items-center gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <select name="role" class="border rounded px-2 py-1">
                                                    <option value="intern" @selected(($user->role ?? 'intern') === 'intern')>intern</option>
                                                    <option value="admin" @selected(($user->role ?? null) === 'admin')>admin</option>
                                                </select>
                                                <button type="submit"
                                                    class="px-3 py-1.5 bg-gray-800 text-white rounded">Simpan</button>
                                            </form>
                                        </td>
                                        <td class="py-2 pr-4 whitespace-nowrap">
                                            <form method="POST" action="{{ route('admin.users.active', $user) }}"
                                                class="flex items-center gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="active" value="0">
                                                <label class="inline-flex items-center gap-2">
                                                    <input type="checkbox" name="active" value="1"
                                                        @checked(($user->active ?? true) === true)>
                                                    <span class="text-sm">Aktif</span>
                                                </label>
                                                <button type="submit"
                                                    class="px-3 py-1.5 bg-gray-800 text-white rounded">Simpan</button>
                                            </form>
                                        </td>
                                        <td class="py-2 pr-4 whitespace-nowrap text-gray-600">
                                            @if (auth()->id() === $user->id)
                                                Akun sendiri
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-3 text-gray-600">Belum ada user.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div>
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
