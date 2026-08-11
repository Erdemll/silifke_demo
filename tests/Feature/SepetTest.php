<?php

use App\Models\Kullanici;
use App\Models\Siparis;
use App\Models\Urun;
use App\Models\UrunResim;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

test('sepet sayfası misafirlere açık ve başlangıçta boştur', function () {
    $this->get(route('magaza.sepet.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Magaza/Sepet')
            ->where('sepet.urunler', [])
            ->where('sepet.toplam', '0.00')
            ->where('sepet.toplam_adet', 0)
            ->where('sepet_ozeti.adet', 0),
        );
});

test('ürün belirtilen adetle sepete eklenir ve aynı ürünün adedi birikir', function () {
    $urun = Urun::factory()->create();

    $this->post(route('magaza.sepet.store', $urun), ['adet' => 2])
        ->assertRedirect()
        ->assertSessionHas('sepet', [$urun->id => 2]);

    $this->post(route('magaza.sepet.store', $urun), ['adet' => 3])
        ->assertRedirect()
        ->assertSessionHas('sepet', [$urun->id => 5]);

    $this->get(route('home'))
        ->assertInertia(fn (Assert $page) => $page->where('sepet_ozeti.adet', 5));
});

test('sepete eklenen ürün adedi doğrulanır', function (mixed $adet) {
    $urun = Urun::factory()->create();

    $this->post(route('magaza.sepet.store', $urun), ['adet' => $adet])
        ->assertSessionHasErrors('adet')
        ->assertSessionMissing('sepet');
})->with([
    'sıfır' => 0,
    'negatif' => -1,
    'ondalıklı' => 1.5,
    'üst sınırdan fazla' => 100,
]);

test('bir üründen sepette en fazla 99 adet tutulur', function () {
    $urun = Urun::factory()->create();

    $this->withSession(['sepet' => [$urun->id => 98]])
        ->post(route('magaza.sepet.store', $urun), ['adet' => 2])
        ->assertSessionHasErrors('adet')
        ->assertSessionHas('sepet', [$urun->id => 98]);
});

test('sepet ürünleri güncel fiyat ve toplamlarla gösterilir', function () {
    Storage::fake('public');

    $ilkUrun = Urun::factory()->create(['ad' => 'Silifke Yoğurdu', 'fiyat' => '125.50']);
    $ikinciUrun = Urun::factory()->create(['ad' => 'Kekik', 'fiyat' => '40.00']);
    UrunResim::factory()->for($ilkUrun, 'urun')->create(['yol' => 'urunler/yogurt.webp']);

    $this->withSession(['sepet' => [$ilkUrun->id => 2, $ikinciUrun->id => 1]])
        ->get(route('magaza.sepet.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Magaza/Sepet')
            ->has('sepet.urunler', 2)
            ->where('sepet.urunler.0.id', $ilkUrun->id)
            ->where('sepet.urunler.0.ad', 'Silifke Yoğurdu')
            ->where('sepet.urunler.0.fiyat', '125.50')
            ->where('sepet.urunler.0.adet', 2)
            ->where('sepet.urunler.0.ara_toplam', '251.00')
            ->where('sepet.urunler.0.resim_url', Storage::disk('public')->url('urunler/yogurt.webp'))
            ->where('sepet.toplam', '291.00')
            ->where('sepet.toplam_adet', 3),
        );
});

test('misafir satın alma işleminden müşteri girişine yönlendirilir', function () {
    $urun = Urun::factory()->create();

    $this->withSession(['sepet' => [$urun->id => 1]])
        ->post(route('magaza.sepet.satin_al'))
        ->assertRedirect(route('kullanici.giris'));

    expect(Siparis::query()->count())->toBe(0);
});

test('satın alma her ürün adedi için güncel fiyatla ayrı sipariş oluşturur ve sepeti temizler', function () {
    $kullanici = Kullanici::factory()->create();
    $ilkUrun = Urun::factory()->create(['fiyat' => '125.50']);
    $ikinciUrun = Urun::factory()->create(['fiyat' => '40.00']);

    $response = $this
        ->actingAs($kullanici, 'musteri')
        ->withSession(['sepet' => [$ilkUrun->id => 2, $ikinciUrun->id => 1]])
        ->post(route('magaza.sepet.satin_al'));

    $response
        ->assertRedirect(route('magaza.sepet.index'))
        ->assertSessionMissing('sepet')
        ->assertSessionHas('success', 'Satın alma tamamlandı. 3 sipariş oluşturuldu.');

    $siparisler = Siparis::query()->oldest('id')->get();

    expect($siparisler)->toHaveCount(3)
        ->and($siparisler->pluck('kullanici_id')->unique()->all())->toBe([$kullanici->id])
        ->and($siparisler->where('urun_id', $ilkUrun->id)->pluck('fiyat')->all())->toBe(['125.50', '125.50'])
        ->and($siparisler->where('urun_id', $ikinciUrun->id)->pluck('fiyat')->all())->toBe(['40.00'])
        ->and($siparisler->pluck('kargo_kodu')->unique())->toHaveCount(3);

    foreach ($siparisler as $siparis) {
        expect($siparis->kargo_kodu)->toMatch('/^SY-[A-Z0-9]{12}$/');
    }
});

test('boş sepet satın alınamaz', function () {
    $kullanici = Kullanici::factory()->create();

    $this->actingAs($kullanici, 'musteri')
        ->post(route('magaza.sepet.satin_al'))
        ->assertSessionHasErrors('sepet');

    expect(Siparis::query()->count())->toBe(0);
});
