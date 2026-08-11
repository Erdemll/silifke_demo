<?php

namespace App\Actions\Magaza;

use App\Models\Urun;
use App\Models\UrunResim;
use App\Models\Yorum;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class GetUrunDetayAction
{
    /**
     * @return array{
     *     urun: array<string, mixed>,
     *     diger_urunler: list<array<string, mixed>>
     * }
     */
    public function handle(Urun $urun): array
    {
        $urun->load([
            'resimler' => fn (HasMany $query) => $query
                ->select(['id', 'urun_id', 'yol'])
                ->oldest(),
            'yorumlar' => fn (HasMany $query) => $query
                ->select(['id', 'urun_id', 'kullanici_id', 'metin', 'created_at'])
                ->latest(),
            'yorumlar.kullanici:id,ad,soyad',
        ]);

        $digerUrunler = Urun::query()
            ->select(['id', 'ad', 'fiyat'])
            ->with('ilkResim')
            ->whereKeyNot($urun->getKey())
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return [
            'urun' => [
                'id' => $urun->id,
                'ad' => $urun->ad,
                'aciklama' => $urun->aciklama,
                'fiyat' => $urun->fiyat,
                'resimler' => $urun->resimler->map(fn (UrunResim $resim): array => [
                    'id' => $resim->id,
                    'url' => Storage::disk('public')->url($resim->yol),
                ])->values()->all(),
                'yorumlar' => $urun->yorumlar->map(fn (Yorum $yorum): array => [
                    'id' => $yorum->id,
                    'metin' => $yorum->metin,
                    'created_at' => $yorum->created_at?->toIso8601String(),
                    'kullanici' => [
                        'ad' => $yorum->kullanici?->ad ?? 'Müşteri',
                        'soyad' => $yorum->kullanici?->soyad ?? '',
                    ],
                ])->values()->all(),
            ],
            'diger_urunler' => $digerUrunler->map(fn (Urun $digerUrun): array => [
                'id' => $digerUrun->id,
                'ad' => $digerUrun->ad,
                'fiyat' => $digerUrun->fiyat,
                'resim_url' => $digerUrun->ilkResim
                    ? Storage::disk('public')->url($digerUrun->ilkResim->yol)
                    : null,
            ])->values()->all(),
        ];
    }
}
