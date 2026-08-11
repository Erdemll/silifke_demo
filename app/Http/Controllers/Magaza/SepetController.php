<?php

namespace App\Http\Controllers\Magaza;

use App\Actions\Magaza\SepeteEkleAction;
use App\Actions\Magaza\SepetGetirAction;
use App\Actions\Magaza\SepetiSatinAlAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Magaza\SepeteEkleRequest;
use App\Models\Kullanici;
use App\Models\Urun;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SepetController extends Controller
{
    public function index(Request $request, SepetGetirAction $sepetGetir): Response
    {
        return Inertia::render('Magaza/Sepet', $sepetGetir->handle($request->session()));
    }

    public function store(
        SepeteEkleRequest $request,
        Urun $urun,
        SepeteEkleAction $sepeteEkle,
    ): RedirectResponse {
        $adet = (int) $request->validated('adet');
        $sepeteEkle->handle($request->session(), $urun, $adet);

        return back()->with('success', "{$urun->ad} sepetinize eklendi.");
    }

    public function satinAl(Request $request, SepetiSatinAlAction $sepetiSatinAl): RedirectResponse
    {
        /** @var Kullanici $kullanici */
        $kullanici = $request->user('musteri');
        $siparisAdedi = $sepetiSatinAl->handle($request->session(), $kullanici);

        return to_route('magaza.sepet.index')
            ->with('success', "Satın alma tamamlandı. {$siparisAdedi} sipariş oluşturuldu.");
    }
}
