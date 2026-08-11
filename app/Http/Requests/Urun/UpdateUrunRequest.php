<?php

namespace App\Http\Requests\Urun;

use App\Models\Urun;
use App\Models\UrunResim;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateUrunRequest extends FormRequest
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
        $urun = $this->route('urun');

        return [
            'ad' => ['required', 'string', 'max:255'],
            'aciklama' => ['required', 'string', 'max:5000'],
            'fiyat' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:99999999.99'],
            'resimler' => ['sometimes', 'array', 'max:8'],
            'resimler.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'mimetypes:image/jpeg,image/png,image/webp', 'max:4096'],
            'silinen_resim_ids' => ['sometimes', 'array'],
            'silinen_resim_ids.*' => [
                'integer',
                'distinct',
                Rule::exists((new UrunResim)->getTable(), 'id')->where(
                    fn (Builder $query): Builder => $query->where('urun_id', $urun instanceof Urun ? $urun->getKey() : 0),
                ),
            ],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $urun = $this->route('urun');

                if (! $urun instanceof Urun) {
                    return;
                }

                $silinenResimIds = collect($this->input('silinen_resim_ids', []))
                    ->filter(fn (mixed $id): bool => is_numeric($id))
                    ->map(fn (mixed $id): int => (int) $id)
                    ->unique()
                    ->all();
                $silinenResimSayisi = $urun->resimler()
                    ->whereIn('id', $silinenResimIds)
                    ->count();
                $yeniResimSayisi = count($this->file('resimler', []));
                $toplamResimSayisi = $urun->resimler()->count() - $silinenResimSayisi + $yeniResimSayisi;

                if ($toplamResimSayisi < 1) {
                    $validator->errors()->add('resimler', 'Üründe en az bir resim bulunmalıdır.');
                }

                if ($toplamResimSayisi > 8) {
                    $validator->errors()->add('resimler', 'Bir ürüne en fazla 8 resim ekleyebilirsiniz.');
                }
            },
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
            'silinen_resim_ids' => 'silinecek resimler',
            'silinen_resim_ids.*' => 'silinecek resim',
        ];
    }
}
