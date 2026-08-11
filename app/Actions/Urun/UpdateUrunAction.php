<?php

namespace App\Actions\Urun;

use App\Models\Urun;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class UpdateUrunAction
{
    /**
     * @param  array{ad: string, aciklama: string, fiyat: mixed}  $attributes
     * @param  array<int, UploadedFile>  $yeniResimler
     * @param  array<int, int>  $silinenResimIds
     */
    public function handle(Urun $urun, array $attributes, array $yeniResimler, array $silinenResimIds): void
    {
        $yeniResimYollari = [];

        try {
            foreach ($yeniResimler as $resim) {
                $yol = $resim->store('urunler', 'public');

                if ($yol === false) {
                    throw new RuntimeException('Ürün resmi saklanamadı.');
                }

                $yeniResimYollari[] = $yol;
            }

            $silinenResimYollari = DB::transaction(function () use ($urun, $attributes, $yeniResimYollari, $silinenResimIds): array {
                $kilitliUrun = Urun::query()
                    ->whereKey($urun->getKey())
                    ->lockForUpdate()
                    ->firstOrFail();

                $silinecekResimler = $kilitliUrun->resimler()
                    ->whereIn('id', $silinenResimIds)
                    ->lockForUpdate()
                    ->get();

                $kilitliUrun->update($attributes);
                $kilitliUrun->resimler()->whereKey($silinecekResimler->modelKeys())->delete();

                foreach ($yeniResimYollari as $yol) {
                    $kilitliUrun->resimler()->create(['yol' => $yol]);
                }

                return $silinecekResimler->pluck('yol')->all();
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($yeniResimYollari);

            throw $exception;
        }

        Storage::disk('public')->delete($silinenResimYollari);
    }
}
