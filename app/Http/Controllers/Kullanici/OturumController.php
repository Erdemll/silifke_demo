<?php

namespace App\Http\Controllers\Kullanici;

use App\Http\Controllers\Controller;
use App\Http\Requests\Kullanici\GirisRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class OturumController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('auth/KullaniciGiris');
    }

    public function store(GirisRequest $request): RedirectResponse
    {
        $credentials = [
            'mail' => $request->validated('mail'),
            'password' => $request->validated('sifre'),
        ];

        if (! Auth::guard('musteri')->attempt($credentials)) {
            throw ValidationException::withMessages([
                'mail' => 'E-posta adresi veya şifre hatalı.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('musteri')->logout();
        $request->session()->regenerateToken();

        return to_route('home');
    }
}
