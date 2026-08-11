<?php

namespace App\Actions\Magaza;

use App\Models\Kullanici;
use App\Models\Siparis;
use Illuminate\Support\Facades\Storage;

/**
 * @phpstan-type SiparisSatiri array{
 *     id: int,
 *     fiyat: string,
 *     kargo_kodu: string|null,
 *     tarih: string,
 *     urun: array{id: int, ad: string, resim_url: string|null}
 * }
 * @phpstan-type SayfalamaBaglantisi array{url: string|null, label: string, active: bool}
 */
class GetSiparislerimAction
{
    /**
     * @return array{
     *     data: list<SiparisSatiri>,
     *     current_page: int,
     *     last_page: int,
     *     from: int|null,
     *     to: int|null,
     *     total: int,
     *     links: list<SayfalamaBaglantisi>
     * }
     */
    public function handle(Kullanici $kullanici): array
    {
        $sayfalama = Siparis::query()
            ->with('urun.ilkResim')
            ->whereBelongsTo($kullanici)
            ->latest('tarih')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $siparisler = [];

        foreach ($sayfalama->items() as $siparis) {
            $siparisler[] = $this->siparisiFormatla($siparis);
        }

        $baglantilar = [];

        foreach ($sayfalama->linkCollection() as $baglanti) {
            if (! is_array($baglanti)) {
                continue;
            }

            $url = $baglanti['url'] ?? null;
            $baglantilar[] = [
                'url' => is_string($url) ? $url : null,
                'label' => (string) ($baglanti['label'] ?? ''),
                'active' => (bool) ($baglanti['active'] ?? false),
            ];
        }

        return [
            'data' => $siparisler,
            'current_page' => $sayfalama->currentPage(),
            'last_page' => $sayfalama->lastPage(),
            'from' => $sayfalama->firstItem(),
            'to' => $sayfalama->lastItem(),
            'total' => $sayfalama->total(),
            'links' => $baglantilar,
        ];
    }

    /** @return SiparisSatiri */
    private function siparisiFormatla(Siparis $siparis): array
    {
        return [
            'id' => $siparis->id,
            'fiyat' => $siparis->fiyat,
            'kargo_kodu' => $siparis->kargo_kodu,
            'tarih' => $siparis->tarih->toIso8601String(),
            'urun' => [
                'id' => $siparis->urun->id,
                'ad' => $siparis->urun->ad,
                'resim_url' => $siparis->urun->ilkResim
                    ? Storage::disk('public')->url($siparis->urun->ilkResim->yol)
                    : null,
            ],
        ];
    }
}
