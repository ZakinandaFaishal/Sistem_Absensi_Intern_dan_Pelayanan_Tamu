<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileAndPasswordUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $user = $this->user();
        $isIntern = (($user?->role ?? null) === 'intern');
        $mustChangePassword = (bool) ($user?->must_change_password ?? false);
        $currentToken = trim((string) ($user?->epikir_letter_token ?? ''));

        // Example format from e-Pikir: 070/028/16/2026
        $epikirFormatRule = 'regex:/^\d{1,4}\/\d{1,4}\/\d{1,4}\/\d{4}$/';

        $wantsToChangePassword = $mustChangePassword
            || trim((string) $this->input('password', '')) !== ''
            || trim((string) $this->input('password_confirmation', '')) !== ''
            || trim((string) $this->input('current_password', '')) !== '';

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                // Email tidak boleh diubah dari halaman profil.
                Rule::in([(string) ($user?->email ?? '')]),
                Rule::unique(User::class)->ignore($user?->id),
            ],
            'epikir_letter_token' => [
                ($isIntern && $currentToken === '') ? 'required' : 'nullable',
                'string',
                'max:120',
                $epikirFormatRule,
            ],

            // Password section (optional unless must_change_password)
            'current_password' => array_merge(
                $wantsToChangePassword ? ['required', 'current_password'] : ['nullable'],
            ),
            'password' => array_merge(
                $wantsToChangePassword ? ['required', Password::defaults(), 'confirmed'] : ['nullable'],
            ),
        ];
    }

    public function messages(): array
    {
        return [
            'epikir_letter_token.required' => 'Nomor surat dari e-Pikir wajib diisi untuk akun intern.',
            'epikir_letter_token.regex' => 'Format nomor surat e-Pikir tidak sesuai. Contoh: 070/028/16/2026',
        ];
    }
}
