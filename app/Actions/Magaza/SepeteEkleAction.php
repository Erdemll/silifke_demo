<?php

namespace App\Actions\Magaza;

use App\Models\Urun;
use Illuminate\Contracts\Session\Session;
use Illuminate\Validation\ValidationException;

class SepeteEkleAction
{
    public function handle(Session $session, Urun $urun, int $adet): void
    {
        /** @var array<int|string, int> $sepet */
        $sepet = $session->get('sepet', []);
        $mevcutAdet = (int) ($sepet[$urun->getKey()] ?? 0);

        if ($mevcutAdet + $adet > 99) {
            throw ValidationException::withMessages([
                'adet' => 'Bir üründen sepette en fazla 99 adet bulunabilir.',
            ]);
        }

        $sepet[$urun->getKey()] = $mevcutAdet + $adet;
        $session->put('sepet', $sepet);
    }
}
