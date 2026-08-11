<?php

namespace App\Http\Requests\Urun;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUrunRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
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
            'aciklama' => ['required', 'string', 'max:5000'],
            'fiyat' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:99999999.99'],
            'resimler' => ['required', 'array', 'min:1', 'max:8'],
            'resimler.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'mimetypes:image/jpeg,image/png,image/webp', 'max:4096'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'ad' => 'ürün adı',
            'aciklama' => 'açıklama',
            'fiyat' => 'fiyat',
            'resimler' => 'ürün resimleri',
            'resimler.*' => 'ürün resmi',
        ];
    }
}
