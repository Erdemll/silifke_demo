<?php

namespace App\Http\Controllers\Magaza;

use App\Actions\Magaza\GetUrunDetayAction;
use App\Http\Controllers\Controller;
use App\Models\Urun;
use Inertia\Inertia;
use Inertia\Response;

class UrunDetayController extends Controller
{
    public function __invoke(Urun $urun, GetUrunDetayAction $getUrunDetay): Response
    {
        return Inertia::render('Magaza/UrunDetay', $getUrunDetay->handle($urun));
    }
}
