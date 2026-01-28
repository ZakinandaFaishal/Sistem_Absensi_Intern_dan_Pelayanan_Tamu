<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isIntern = (($this->user()?->role ?? null) === 'intern');

        // Example format from e-Pikir: 070/028/16/2026
        $epikirFormatRule = 'regex:/^\d{1,4}\/\d{1,4}\/\d{1,4}\/\d{4}$/';

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'epikir_letter_token' => [
                $isIntern ? 'required' : 'nullable',
                'string',
                'max:120',
                $epikirFormatRule,
            ],
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
