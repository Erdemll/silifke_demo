<?php

namespace App\Http\Controllers;

use App\Actions\Siparis\GetSiparislerAction;
use Inertia\Inertia;
use Inertia\Response;

class SiparisController extends Controller
{
    public function index(GetSiparislerAction $getSiparisler): Response
    {
        return Inertia::render('Siparisler/Index', [
            'siparisler' => $getSiparisler->handle(),
        ]);
    }
}
