<?php

namespace App\Http\Controllers;

use App\Models\Urun;
use Inertia\Inertia;
use Inertia\Response;

class AnaSayfaController extends Controller
{
    public function __invoke(): Response
    {
        $urunler = Urun::query()
            ->select(['id', 'ad', 'fiyat', 'created_at'])
            ->with('ilkResim')
            ->latest()
            ->paginate(12)
            ->onEachSide(1)
            ->through(fn (Urun $urun): array => [
                'id' => $urun->id,
                'ad' => $urun->ad,
                'fiyat' => $urun->fiyat,
                'resim_url' => $urun->ilkResim
                    ? $urun->ilkResim->url
                    : null,
            ]);

        return Inertia::render('Welcome', [
            'urunler' => $urunler,
        ]);
    }
}
