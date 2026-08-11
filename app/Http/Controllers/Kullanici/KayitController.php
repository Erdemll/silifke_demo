<?php

namespace App\Http\Controllers\Kullanici;

use App\Actions\Kullanici\KullaniciOlusturAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Kullanici\KayitRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class KayitController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('auth/KullaniciKayit');
    }

    public function store(KayitRequest $request, KullaniciOlusturAction $kullaniciOlustur): RedirectResponse
    {
        $kullanici = $kullaniciOlustur->handle($request->safe()->only(['ad', 'soyad', 'mail', 'sifre']));

        Auth::guard('musteri')->login($kullanici);
        $request->session()->regenerate();

        return to_route('home');
    }
}
