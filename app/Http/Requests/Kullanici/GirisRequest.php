<?php

namespace App\Http\Requests\Kullanici;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class GirisRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mail' => ['required', 'string', 'email:rfc', 'max:255'],
            'sifre' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'mail.required' => 'E-posta alanı zorunludur.',
            'mail.email' => 'Geçerli bir e-posta adresi giriniz.',
            'sifre.required' => 'Şifre alanı zorunludur.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'mail' => Str::of((string) $this->input('mail'))->trim()->lower()->toString(),
        ]);
    }
}
