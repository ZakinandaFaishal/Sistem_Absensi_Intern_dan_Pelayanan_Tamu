<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (($user?->role ?? null) === 'intern') {
            $token = trim((string) ($user->epikir_letter_token ?? ''));
            $valid = (bool) preg_match('/^\d{1,4}\/\d{1,4}\/\d{1,4}\/\d{4}$/', $token);

            if ($token === '') {
                return back()->withErrors([
                    'epikir_letter_token' => 'Nomor surat e-Pikir wajib diisi terlebih dahulu sebelum mengganti password.',
                ], 'updatePassword');
            }

            if (!$valid) {
                return back()->withErrors([
                    'epikir_letter_token' => 'Format nomor surat e-Pikir tidak sesuai. Contoh: 070/028/16/2026',
                ], 'updatePassword');
            }
        }

        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
        ]);

        return back()->with('status', 'password-updated');
    }
}
