<?php

namespace App\Http\Controllers;

use App\Actions\Urun\CreateUrunAction;
use App\Actions\Urun\DeleteUrunAction;
use App\Actions\Urun\UpdateUrunAction;
use App\Http\Requests\Urun\DeleteUrunRequest;
use App\Http\Requests\Urun\IndexUrunRequest;
use App\Http\Requests\Urun\StoreUrunRequest;
use App\Http\Requests\Urun\UpdateUrunRequest;
use App\Models\Urun;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class UrunController extends Controller
{
    public function index(IndexUrunRequest $request): Response
    {
        $arama = Str::squish($request->string('ara')->toString());

        $urunler = Urun::query()
            ->select(['id', 'ad', 'aciklama', 'fiyat', 'created_at'])
            ->with('resimler:id,urun_id,yol')
            ->when($arama !== '', fn ($query) => $query->where('ad', 'like', "%{$arama}%"))
            ->latest()
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Urun $urun): array => [
                'id' => $urun->id,
                'ad' => $urun->ad,
                'aciklama' => $urun->aciklama,
                'fiyat' => $urun->fiyat,
                'created_at' => $urun->created_at?->toIso8601String(),
                'resimler' => $urun->resimler->map(fn ($resim): array => [
                    'id' => $resim->id,
                    'url' => $resim->url,
                ])->values()->all(),
            ]);

        return Inertia::render('Urunler/Index', [
            'urunler' => $urunler,
            'filters' => ['ara' => $arama],
        ]);
    }

    public function store(StoreUrunRequest $request, CreateUrunAction $createUrun): RedirectResponse
    {
        $createUrun->handle(
            $request->safe()->only(['ad', 'aciklama', 'fiyat']),
            $request->file('resimler', []),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Ürün başarıyla eklendi.']);

        return to_route('urunler.index');
    }

    public function update(UpdateUrunRequest $request, Urun $urun, UpdateUrunAction $updateUrun): RedirectResponse
    {
        $updateUrun->handle(
            $urun,
            $request->safe()->only(['ad', 'aciklama', 'fiyat']),
            $request->file('resimler', []),
            $request->validated('silinen_resim_ids', []),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Ürün başarıyla güncellendi.']);

        return back();
    }

    public function destroy(DeleteUrunRequest $request, Urun $urun, DeleteUrunAction $deleteUrun): RedirectResponse
    {
        if ($urun->siparisler()->exists()) {
            return back()->withErrors([
                'urun_silme' => 'Sipariş geçmişi bulunan ürünler silinemez.',
            ]);
        }

        $deleteUrun->handle($urun);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Ürün başarıyla silindi.']);

        return to_route('urunler.index');
    }
}
