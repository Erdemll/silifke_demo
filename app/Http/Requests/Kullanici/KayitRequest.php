<?php

namespace App\Http\Requests\Kullanici;

use App\Models\Kullanici;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class KayitRequest extends FormRequest
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
            'ad' => ['required', 'string', 'max:255'],
            'soyad' => ['required', 'string', 'max:255'],
            'mail' => ['required', 'string', 'lowercase', 'email:rfc', 'max:255', Rule::unique(Kullanici::class, 'mail')],
            'sifre' => ['required', 'string', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ad.required' => 'Ad alanı zorunludur.',
            'ad.max' => 'Ad en fazla 255 karakter olabilir.',
            'soyad.required' => 'Soyad alanı zorunludur.',
            'soyad.max' => 'Soyad en fazla 255 karakter olabilir.',
            'mail.required' => 'E-posta alanı zorunludur.',
            'mail.email' => 'Geçerli bir e-posta adresi giriniz.',
            'mail.unique' => 'Bu e-posta adresiyle daha önce kayıt olunmuş.',
            'sifre.required' => 'Şifre alanı zorunludur.',
            'sifre.confirmed' => 'Şifre tekrarı eşleşmiyor.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'ad' => Str::of((string) $this->input('ad'))->squish()->toString(),
            'soyad' => Str::of((string) $this->input('soyad'))->squish()->toString(),
            'mail' => Str::of((string) $this->input('mail'))->trim()->lower()->toString(),
        ]);
    }
}
