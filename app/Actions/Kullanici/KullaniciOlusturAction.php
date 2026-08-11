<?php

namespace App\Actions\Kullanici;

use App\Models\Kullanici;

class KullaniciOlusturAction
{
    /**
     * @param  array{ad: string, soyad: string, mail: string, sifre: string}  $attributes
     */
    public function handle(array $attributes): Kullanici
    {
        return Kullanici::query()->create($attributes);
    }
}
