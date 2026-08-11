<?php

namespace App\Http\Requests\Magaza;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SepeteEkleRequest extends FormRequest
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
            'adet' => ['required', 'integer', 'min:1', 'max:99'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'adet.required' => 'Ürün adedi zorunludur.',
            'adet.integer' => 'Ürün adedi tam sayı olmalıdır.',
            'adet.min' => 'En az bir ürün ekleyebilirsiniz.',
            'adet.max' => 'Tek seferde en fazla 99 ürün ekleyebilirsiniz.',
        ];
    }
}
