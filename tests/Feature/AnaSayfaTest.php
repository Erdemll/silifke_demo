<?php

use App\Models\Kullanici;
use App\Models\Urun;
use App\Models\UrunResim;
use App\Models\Yorum;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

test('ana sayfa ürün bulunmadığında boş ürün listesiyle gösterilir', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->has('urunler.data', 0)
            ->where('urunler.total', 0)
            ->where('urunler.current_page', 1),
        );
});

test('ana sayfa ürünleri en yeniden eskiye ilk resimleriyle sayfalar', function () {
    $urunler = collect(range(1, 13))->map(fn (int $sira): Urun => Urun::factory()->create([
        'ad' => "Silifke Ürünü {$sira}",
        'fiyat' => '149.90',
        'created_at' => now()->subMinutes(13 - $sira),
    ]));

    $enYeniUrun = $urunler->last();
    $ilkResim = UrunResim::factory()->create([
        'urun_id' => $enYeniUrun->id,
        'yol' => 'urunler/ilk-resim.webp',
    ]);
    UrunResim::factory()->create([
        'urun_id' => $enYeniUrun->id,
        'yol' => 'urunler/ikinci-resim.webp',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->has('urunler.data', 12)
            ->where('urunler.data.0.id', $enYeniUrun->id)
            ->where('urunler.data.0.ad', $enYeniUrun->ad)
            ->where('urunler.data.0.fiyat', '149.90')
            ->where('urunler.data.0.resim_url', Storage::disk('public')->url($ilkResim->yol))
            ->where('urunler.current_page', 1)
            ->where('urunler.last_page', 2)
            ->where('urunler.from', 1)
            ->where('urunler.to', 12)
            ->where('urunler.total', 13),
        );
});

test('ana sayfanın ikinci ürün sayfasına gidilebilir', function () {
    $urunler = collect(range(1, 13))->map(fn (int $sira): Urun => Urun::factory()->create([
        'created_at' => now()->subMinutes(13 - $sira),
    ]));

    $this->get(route('home', ['page' => 2]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->has('urunler.data', 1)
            ->where('urunler.data.0.id', $urunler->first()->id)
            ->where('urunler.current_page', 2)
            ->where('urunler.from', 13)
            ->where('urunler.to', 13),
        );
});

test('ürün detay sayfası görselleri yorumları ve dört farklı ürünü gösterir', function () {
    $urun = Urun::factory()->create([
        'ad' => 'Silifke Yoğurdu',
        'aciklama' => 'Geleneksel yöntemlerle hazırlanan doğal yoğurt.',
        'fiyat' => '189.90',
    ]);

    $ilkResim = UrunResim::factory()->create([
        'urun_id' => $urun->id,
        'yol' => 'urunler/yogurt-1.webp',
    ]);
    $ikinciResim = UrunResim::factory()->create([
        'urun_id' => $urun->id,
        'yol' => 'urunler/yogurt-2.webp',
    ]);

    $ilkKullanici = Kullanici::factory()->create([
        'ad' => 'Ayşe',
        'soyad' => 'Yılmaz',
    ]);
    $ikinciKullanici = Kullanici::factory()->create([
        'ad' => 'Mehmet',
        'soyad' => 'Kaya',
    ]);

    $eskiYorum = Yorum::factory()->create([
        'urun_id' => $urun->id,
        'kullanici_id' => $ilkKullanici->id,
        'metin' => 'Tadı çok doğal.',
        'created_at' => now()->subDay(),
    ]);
    $yeniYorum = Yorum::factory()->create([
        'urun_id' => $urun->id,
        'kullanici_id' => $ikinciKullanici->id,
        'metin' => 'Paketleme ve lezzet harikaydı.',
        'created_at' => now(),
    ]);

    $digerUrunler = Urun::factory()->count(6)->create();
    $digerUrunler->each(fn (Urun $digerUrun) => UrunResim::factory()->create([
        'urun_id' => $digerUrun->id,
        'yol' => "urunler/diger-{$digerUrun->id}.webp",
    ]));

    Yorum::factory()->create([
        'urun_id' => $digerUrunler->first()->id,
        'metin' => 'Bu yorum başka ürüne aittir.',
    ]);

    $response = $this->get(route('magaza.urunler.show', $urun));

    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Magaza/UrunDetay')
            ->where('urun.id', $urun->id)
            ->where('urun.ad', 'Silifke Yoğurdu')
            ->where('urun.aciklama', 'Geleneksel yöntemlerle hazırlanan doğal yoğurt.')
            ->where('urun.fiyat', '189.90')
            ->has('urun.resimler', 2)
            ->where('urun.resimler.0.id', $ilkResim->id)
            ->where('urun.resimler.0.url', Storage::disk('public')->url($ilkResim->yol))
            ->where('urun.resimler.1.id', $ikinciResim->id)
            ->has('urun.yorumlar', 2)
            ->where('urun.yorumlar.0.id', $yeniYorum->id)
            ->where('urun.yorumlar.0.metin', 'Paketleme ve lezzet harikaydı.')
            ->where('urun.yorumlar.0.kullanici.ad', 'Mehmet')
            ->where('urun.yorumlar.0.kullanici.soyad', 'Kaya')
            ->where('urun.yorumlar.1.id', $eskiYorum->id)
            ->has('diger_urunler', 4),
        );

    $oneriler = collect($response->inertiaProps('diger_urunler'));

    expect($oneriler)->toHaveCount(4)
        ->and($oneriler->pluck('id'))->not->toContain($urun->id);

    $oneriler->each(function (array $onerilenUrun) use ($digerUrunler): void {
        expect($digerUrunler->modelKeys())->toContain($onerilenUrun['id'])
            ->and($onerilenUrun['resim_url'])->toBeString();
    });
});

test('bulunmayan ürün detay sayfası 404 döndürür', function () {
    $this->get(route('magaza.urunler.show', 999999))->assertNotFound();
});
