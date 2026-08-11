<?php

namespace App\Http\Requests\Magaza;

use App\Models\Kullanici;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreYorumRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user('musteri') instanceof Kullanici;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'metin' => ['required', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'metin.required' => 'Yorum alanı zorunludur.',
            'metin.max' => 'Yorum en fazla 1000 karakter olabilir.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'metin' => Str::of((string) $this->input('metin'))->trim()->toString(),
        ]);
    }
}
