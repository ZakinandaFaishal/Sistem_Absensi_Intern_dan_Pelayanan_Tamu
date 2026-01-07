<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'role' => ['required', 'string', Rule::in(['admin', 'intern'])],
            'active' => ['required', 'boolean'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'active' => (bool) $validated['active'],
            'password' => $validated['password'],
        ]);

        return back()->with('status', 'User berhasil ditambahkan.');
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
