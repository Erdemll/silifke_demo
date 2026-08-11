<?php

namespace App\Actions\Magaza;

use App\Models\Kullanici;
use App\Models\Siparis;
use App\Models\Urun;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SepetiSatinAlAction
{
    public function handle(Session $session, Kullanici $kullanici): int
    {
        $sepet = $this->gecerliSepet($session);

        if ($sepet->isEmpty()) {
            throw ValidationException::withMessages([
                'sepet' => 'Satın alma işlemi için sepetinize ürün ekleyin.',
            ]);
        }

        $siparisAdedi = DB::transaction(function () use ($sepet, $kullanici): int {
            $urunler = Urun::query()
                ->whereKey($sepet->keys()->all())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($urunler->count() !== $sepet->count()) {
                throw ValidationException::withMessages([
                    'sepet' => 'Sepetinizde artık satışta olmayan bir ürün bulunuyor.',
                ]);
            }

            $siparisTarihi = now();
            $siparisAdedi = 0;

            foreach ($sepet as $urunId => $adet) {
                $urun = $urunler->get($urunId);

                if (! $urun instanceof Urun) {
                    throw ValidationException::withMessages([
                        'sepet' => 'Sepetinizde artık satışta olmayan bir ürün bulunuyor.',
                    ]);
                }

                for ($sira = 0; $sira < $adet; $sira++) {
                    Siparis::query()->create([
                        'urun_id' => $urun->id,
                        'kullanici_id' => $kullanici->id,
                        'fiyat' => $urun->fiyat,
                        'kargo_kodu' => $this->benzersizKargoKoduOlustur(),
                        'tarih' => $siparisTarihi,
                    ]);

                    $siparisAdedi++;
                }
            }

            return $siparisAdedi;
        }, 3);

        $session->forget('sepet');

        return $siparisAdedi;
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

    private function benzersizKargoKoduOlustur(): string
    {
        do {
            $kargoKodu = 'SY-'.Str::upper(Str::random(12));
        } while (Siparis::query()->where('kargo_kodu', $kargoKodu)->exists());

        return $kargoKodu;
    }
}
