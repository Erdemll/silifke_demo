<?php

namespace App\Actions\Magaza;

use App\Models\Kullanici;
use App\Models\Urun;
use App\Models\Yorum;

class CreateYorumAction
{
    public function handle(Urun $urun, Kullanici $kullanici, string $metin): Yorum
    {
        return $urun->yorumlar()->create([
            'kullanici_id' => $kullanici->id,
            'metin' => $metin,
        ]);
    }
}
