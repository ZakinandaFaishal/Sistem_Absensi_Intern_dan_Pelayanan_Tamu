<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Accept either an email address or a username in a single field.
            'email' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $identifier = Str::of((string) $this->string('email'))->trim()->toString();
        $nikCandidate = preg_replace('/\D+/', '', $identifier) ?? '';
        $password = (string) $this->string('password');

        $candidates = [];

        // 1) NIK (accept input with spaces/dashes; normalize to digits)
        if (preg_match('/^\d{16}$/', $nikCandidate) === 1) {
            $candidates[] = ['nik' => $nikCandidate];
        }

        // 2) Email (support both real emails and internal ones like user@localhost)
        if (str_contains($identifier, '@') || filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $candidates[] = ['email' => Str::lower($identifier)];
        }

        // 3) Username
        $candidates[] = ['username' => Str::lower($identifier)];

        $authenticated = false;
        foreach ($candidates as $base) {
            $credentials = $base + ['password' => $password];
            if (Auth::attempt($credentials, $this->boolean('remember'))) {
                $authenticated = true;
                break;
            }
        }

        if (! $authenticated) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        $identifier = Str::of((string) $this->string('email'))->trim()->toString();

        return Str::transliterate(Str::lower($identifier) . '|' . $this->ip());
    }
}
