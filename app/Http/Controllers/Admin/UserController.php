<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $editUserId = $request->query('edit');
        $editUser = null;
        if ($editUserId !== null && $editUserId !== '') {
            $editUser = User::query()->find($editUserId);
        }

        $users = User::query()
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'editUser' => $editUser,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'digits:16', Rule::unique('users', 'nik')],
            'phone' => ['required', 'string', 'max:30'],
            'username' => ['required', 'string', 'lowercase', 'alpha_dash', 'max:50', Rule::unique('users', 'username')],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')],
            'role' => ['required', 'string', Rule::in(['admin', 'intern'])],
            'active' => ['required', 'boolean'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::query()->create([
            'name' => $validated['name'],
            'nik' => $validated['nik'],
            'phone' => $validated['phone'],
            'username' => Str::lower($validated['username']),
            'email' => $validated['email'],
            'role' => $validated['role'],
            'active' => (bool) $validated['active'],
            'password' => $validated['password'],
        ]);

        return back()->with('status', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'digits:16', Rule::unique('users', 'nik')->ignore($user->id)],
            'phone' => ['required', 'string', 'max:30'],
            'username' => ['required', 'string', 'lowercase', 'alpha_dash', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in(['admin', 'intern'])],
            'active' => ['required', 'boolean'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (($request->user()?->id === $user->id) && ($validated['role'] ?? null) !== ($user->role ?? 'intern')) {
            return back()->withErrors([
                'role' => 'Tidak bisa mengubah role akun sendiri.',
            ]);
        }

        if (($request->user()?->id === $user->id) && ((bool) ($validated['active'] ?? true) === false)) {
            return back()->withErrors([
                'active' => 'Tidak bisa menonaktifkan akun sendiri.',
            ]);
        }

        $payload = [
            'name' => $validated['name'],
            'nik' => $validated['nik'],
            'phone' => $validated['phone'],
            'username' => Str::lower($validated['username']),
            'email' => $validated['email'],
            'role' => $validated['role'],
            'active' => (bool) $validated['active'],
        ];

        if (!empty($validated['password'] ?? '')) {
            $payload['password'] = $validated['password'];
        }

        $user->forceFill($payload)->save();

        return redirect()->route('admin.users.index')->with('status', 'User berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($request->user()?->id === $user->id) {
            return back()->withErrors([
                'delete' => 'Tidak bisa menghapus akun sendiri.',
            ]);
        }

        $user->delete();

        return back()->with('status', 'User berhasil dihapus.');
    }

    public function updateRole(Request $request, User $user)
    {
        if ($request->user()?->id === $user->id) {
            return back()->withErrors([
                'role' => 'Tidak bisa mengubah role akun sendiri.',
            ]);
        }

        $validated = $request->validate([
            'role' => ['required', 'string', Rule::in(['admin', 'intern'])],
        ]);

        $user->forceFill([
            'role' => $validated['role'],
        ])->save();

        return back()->with('status', 'Role user berhasil diperbarui.');
    }

    public function updateActive(Request $request, User $user)
    {
        if ($request->user()?->id === $user->id) {
            return back()->withErrors([
                'active' => 'Tidak bisa menonaktifkan akun sendiri.',
            ]);
        }

        $validated = $request->validate([
            'active' => ['required', 'boolean'],
        ]);

        $user->forceFill([
            'active' => (bool) $validated['active'],
        ])->save();

        return back()->with('status', 'Status user berhasil diperbarui.');
    }
}
