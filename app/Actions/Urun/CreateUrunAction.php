<?php

namespace App\Actions\Urun;

use App\Models\Urun;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class CreateUrunAction
{
    /**
     * @param  array{ad: string, aciklama: string, fiyat: mixed}  $attributes
     * @param  array<int, UploadedFile>  $resimler
     */
    public function handle(array $attributes, array $resimler): Urun
    {
        $saklananYollar = [];

        try {
            return DB::transaction(function () use ($attributes, $resimler, &$saklananYollar): Urun {
                $urun = Urun::query()->create($attributes);

                foreach ($resimler as $resim) {
                    $yol = $resim->store('urunler', 'public');

                    if ($yol === false) {
                        throw new RuntimeException('Ürün resmi saklanamadı.');
                    }

                    $saklananYollar[] = $yol;
                    $urun->resimler()->create(['yol' => $yol]);
                }

                return $urun;
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($saklananYollar);

            throw $exception;
        }
    }
}
