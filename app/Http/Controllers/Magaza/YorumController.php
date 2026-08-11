<?php

namespace App\Http\Controllers\Magaza;

use App\Actions\Magaza\CreateYorumAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Magaza\StoreYorumRequest;
use App\Models\Kullanici;
use App\Models\Urun;
use Illuminate\Http\RedirectResponse;

class YorumController extends Controller
{
    public function store(
        StoreYorumRequest $request,
        Urun $urun,
        CreateYorumAction $createYorum,
    ): RedirectResponse {
        $kullanici = $request->user('musteri');

        if (! $kullanici instanceof Kullanici) {
            abort(403);
        }

        $createYorum->handle(
            $urun,
            $kullanici,
            $request->string('metin')->toString(),
        );

        return back()->with('success', 'Yorumunuz eklendi.');
    }
}
