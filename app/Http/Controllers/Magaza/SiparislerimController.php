<?php

namespace App\Http\Controllers\Magaza;

use App\Actions\Magaza\GetSiparislerimAction;
use App\Http\Controllers\Controller;
use App\Models\Kullanici;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SiparislerimController extends Controller
{
    public function index(Request $request, GetSiparislerimAction $getSiparislerim): Response
    {
        $kullanici = $request->user('musteri');

        if (! $kullanici instanceof Kullanici) {
            abort(403);
        }

        return Inertia::render('Magaza/Siparislerim', [
            'siparisler' => $getSiparislerim->handle($kullanici),
        ]);
    }
}
