<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();
        $intended = (string) $request->session()->get('url.intended', '');
        $intendedPath = $intended !== '' ? (parse_url($intended, PHP_URL_PATH) ?? '') : '';

        $role = $user->role ?? null;
        if ($role === 'admin') {
            $role = 'super_admin';
        }

        $isAdminAreaUser = in_array($role, ['super_admin', 'admin_dinas'], true);

        if (!$isAdminAreaUser && ($user->role ?? null) === 'intern') {
            $needsPasswordChange = (bool) ($user->must_change_password ?? false);
            $missingToken = trim((string) ($user->epikir_letter_token ?? '')) === '';

            if ($needsPasswordChange || $missingToken) {
                $request->session()->forget('url.intended');

                $parts = [];
                if ($needsPasswordChange) {
                    $parts[] = 'ganti password';
                }
                if ($missingToken) {
                    $parts[] = 'isi token nomor surat';
                }

                return redirect()
                    ->route('intern.userProfile')
                    ->with('status', 'Silakan lengkapi profil: ' . implode(' dan ', $parts) . '.');
            }
        }

        // Never send non-admin users into the admin area, even if they previously visited an /admin URL.
        if (!$isAdminAreaUser && $intendedPath !== '' && str_starts_with($intendedPath, '/admin')) {
            $request->session()->forget('url.intended');
        }

        if ($isAdminAreaUser) {
            if ($role === 'admin_dinas') {
                return redirect()->intended(route('admin.dashboard', absolute: false));
            }

            return redirect()->intended(route('admin.dashboard', absolute: false));
        }

        return redirect()->intended(route('intern.userProfile', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
