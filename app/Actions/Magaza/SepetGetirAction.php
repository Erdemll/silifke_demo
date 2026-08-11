<?php

namespace App\Actions\Magaza;

use App\Models\Urun;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class SepetGetirAction
{
    /**
     * @return array{
     *     sepet: array{
     *         urunler: list<array{id: int, ad: string, fiyat: string, adet: int, ara_toplam: string, resim_url: string|null}>,
     *         toplam: string,
     *         toplam_adet: int
     *     }
     * }
     */
    public function handle(Session $session): array
    {
        $sepet = $this->gecerliSepet($session);

        if ($sepet->isEmpty()) {
            return $this->bosSepet();
        }

        $urunler = Urun::query()
            ->select(['id', 'ad', 'fiyat'])
            ->with('ilkResim')
            ->whereKey($sepet->keys()->all())
            ->oldest('id')
            ->get();

        $mevcutUrunIdleri = $urunler->modelKeys();
        $temizSepet = $sepet->only($mevcutUrunIdleri);

        if ($temizSepet->count() !== $sepet->count()) {
            $session->put('sepet', $temizSepet->all());
        }

        $toplamKurus = 0;
        $sepetUrunleri = [];

        foreach ($urunler as $urun) {
            $adet = (int) $temizSepet->get($urun->id);
            $birimFiyatKurus = (int) round((float) $urun->fiyat * 100);
            $araToplamKurus = $birimFiyatKurus * $adet;
            $toplamKurus += $araToplamKurus;

            $sepetUrunleri[] = [
                'id' => $urun->id,
                'ad' => $urun->ad,
                'fiyat' => $this->kurusuFormatla($birimFiyatKurus),
                'adet' => $adet,
                'ara_toplam' => $this->kurusuFormatla($araToplamKurus),
                'resim_url' => $urun->ilkResim
                    ? Storage::disk('public')->url($urun->ilkResim->yol)
                    : null,
            ];
        }

        return [
            'sepet' => [
                'urunler' => $sepetUrunleri,
                'toplam' => $this->kurusuFormatla($toplamKurus),
                'toplam_adet' => (int) $temizSepet->sum(),
            ],
        ];
    }

    /**
     * @return Collection<int, int<1, 99>>
     */
    private function gecerliSepet(Session $session): Collection
    {
        $hamSepet = $session->get('sepet', []);

        if (! is_array($hamSepet)) {
            return collect();
        }

        return collect($hamSepet)
            ->mapWithKeys(fn (mixed $adet, mixed $urunId): array => [(int) $urunId => (int) $adet])
            ->filter(fn (int $adet, int $urunId): bool => $urunId > 0 && $adet > 0 && $adet <= 99);
    }

    /**
     * @return array{sepet: array{urunler: list<never>, toplam: string, toplam_adet: int}}
     */
    private function bosSepet(): array
    {
        return [
            'sepet' => [
                'urunler' => [],
                'toplam' => '0.00',
                'toplam_adet' => 0,
            ],
        ];
    }

    private function kurusuFormatla(int $kurus): string
    {
        return number_format($kurus / 100, 2, '.', '');
    }
}
