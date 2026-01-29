<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileAndPasswordUpdateRequest;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Jangan tampilkan/overwrite token jika input kosong.
        if (array_key_exists('epikir_letter_token', $validated)) {
            $token = trim((string) ($validated['epikir_letter_token'] ?? ''));
            if ($token === '') {
                unset($validated['epikir_letter_token']);
            } else {
                $validated['epikir_letter_token'] = $token;
            }
        }

        $request->user()->fill($validated);

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Combined update: profile (name/email) + e-Pikir token + (optional) password change.
     * For interns with must_change_password=true, password change is required.
     */
    public function updateCombined(ProfileAndPasswordUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->fill([
            'name' => $validated['name'],
            // Email dikunci di validation (tidak bisa berubah dari halaman profil)
            'email' => $validated['email'],
        ]);

        // Jangan overwrite token dengan string kosong; hanya update kalau diisi.
        if (array_key_exists('epikir_letter_token', $validated)) {
            $token = trim((string) ($validated['epikir_letter_token'] ?? ''));
            if ($token !== '') {
                $user->epikir_letter_token = $token;
            }
        }

        $newPassword = trim((string) ($validated['password'] ?? ''));
        if ($newPassword !== '') {
            $user->forceFill([
                'password' => Hash::make($newPassword),
                'must_change_password' => false,
            ]);
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-password-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
