<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\User;
use App\Support\AppSettings;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $locations = Location::query()
            ->orderBy('name')
            ->get();

        return view('auth.register', [
            'locations' => $locations,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $codeHash = AppSettings::getString(AppSettings::REGISTRATION_ADMIN_CODE_HASH, '');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nik' => ['required', 'digits:16', 'unique:users,nik'],
            'phone' => ['required', 'string', 'max:30'],
            'username' => ['required', 'string', 'lowercase', 'alpha_dash', 'max:50', 'unique:users,username'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'internship_start_date' => ['required', 'date'],
            'internship_end_date' => ['required', 'date', 'after_or_equal:internship_start_date'],
            'internship_location_id' => ['required', 'integer', 'exists:locations,id'],
            'registration_code' => [
                'required',
                'string',
                'max:100',
                function (string $attribute, mixed $value, \Closure $fail) use ($codeHash) {
                    if ($codeHash === '') {
                        $fail('Registrasi belum dibuka. Hubungi admin untuk mendapatkan kode registrasi.');
                        return;
                    }
                    if (!Hash::check((string) $value, $codeHash)) {
                        $fail('Kode registrasi salah. Hubungi admin untuk kode yang benar.');
                    }
                },
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $location = Location::query()
            ->select(['id', 'dinas_id'])
            ->findOrFail((int) $request->internship_location_id);

        if (($location->dinas_id ?? null) === null) {
            return back()
                ->withInput()
                ->withErrors([
                    'internship_location_id' => 'Lokasi magang belum terhubung ke dinas. Silakan pilih lokasi lain atau hubungi admin.',
                ]);
        }

        $user = User::create([
            'name' => $request->name,
            'nik' => $request->nik,
            'phone' => $request->phone,
            'username' => Str::lower($request->username),
            'email' => $request->email,
            'dinas_id' => (int) $location->dinas_id,
            'internship_start_date' => $request->internship_start_date,
            'internship_end_date' => $request->internship_end_date,
            'internship_location_id' => (int) $request->internship_location_id,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
