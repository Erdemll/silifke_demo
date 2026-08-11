<?php

namespace App\Actions\Siparis;

use App\Models\Siparis;

/**
 * @phpstan-type SiparisSatiri array{
 *     id: int,
 *     fiyat: string,
 *     kargo_kodu: string|null,
 *     tarih: string,
 *     kullanici: array{ad: string, soyad: string, mail: string},
 *     urun: array{id: int, ad: string}
 * }
 * @phpstan-type SayfalamaBaglantisi array{url: string|null, label: string, active: bool}
 */
class GetSiparislerAction
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
    public function handle(): array
    {
        $sayfalama = Siparis::query()
            ->select(['id', 'urun_id', 'kullanici_id', 'fiyat', 'kargo_kodu', 'tarih'])
            ->with([
                'kullanici:id,ad,soyad,mail',
                'urun:id,ad',
            ])
            ->latest('tarih')
            ->latest('id')
            ->paginate(15)
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
            'kullanici' => [
                'ad' => $siparis->kullanici->ad,
                'soyad' => $siparis->kullanici->soyad,
                'mail' => $siparis->kullanici->mail,
            ],
            'urun' => [
                'id' => $siparis->urun->id,
                'ad' => $siparis->urun->ad,
            ],
        ];
    }
}
