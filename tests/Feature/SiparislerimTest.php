<?php

use App\Models\Kullanici;
use App\Models\Siparis;
use App\Models\Urun;
use App\Models\UrunResim;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

test('misafir siparişlerim sayfasından müşteri girişine yönlendirilir', function () {
    $this->get(route('magaza.siparislerim.index'))
        ->assertRedirect(route('kullanici.giris'));
});

test('müşteri yalnızca kendi siparişlerini kargo kodlarıyla en yeniden eskiye görür', function () {
    Storage::fake('public');

    $kullanici = Kullanici::factory()->create();
    $digerKullanici = Kullanici::factory()->create();
    $cezerye = Urun::factory()->create(['ad' => 'Silifke Cezeryesi']);
    $yogurt = Urun::factory()->create(['ad' => 'Silifke Yoğurdu']);
    UrunResim::factory()->for($cezerye, 'urun')->create(['yol' => 'urunler/cezerye.webp']);

    $eskiSiparis = Siparis::factory()
        ->for($kullanici, 'kullanici')
        ->for($yogurt, 'urun')
        ->create([
            'fiyat' => '150.00',
            'kargo_kodu' => null,
            'tarih' => now()->subDay(),
        ]);

    $yeniSiparis = Siparis::factory()
        ->for($kullanici, 'kullanici')
        ->for($cezerye, 'urun')
        ->create([
            'fiyat' => '225.50',
            'kargo_kodu' => 'SY-ABC123456789',
            'tarih' => now(),
        ]);

    Siparis::factory()
        ->for($digerKullanici, 'kullanici')
        ->for($cezerye, 'urun')
        ->create(['kargo_kodu' => 'SY-BASKAMUSTERI']);

    $this->actingAs($kullanici, 'musteri')
        ->get(route('magaza.siparislerim.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Magaza/Siparislerim')
            ->has('siparisler.data', 2)
            ->where('siparisler.total', 2)
            ->where('siparisler.data.0.id', $yeniSiparis->id)
            ->where('siparisler.data.0.fiyat', '225.50')
            ->where('siparisler.data.0.kargo_kodu', 'SY-ABC123456789')
            ->where('siparisler.data.0.urun.id', $cezerye->id)
            ->where('siparisler.data.0.urun.ad', 'Silifke Cezeryesi')
            ->where('siparisler.data.0.urun.resim_url', Storage::disk('public')->url('urunler/cezerye.webp'))
            ->where('siparisler.data.1.id', $eskiSiparis->id)
            ->where('siparisler.data.1.kargo_kodu', null),
        );
});

test('siparişler onar kayıt halinde sayfalanır', function () {
    $kullanici = Kullanici::factory()->create();

    Siparis::factory()
        ->count(11)
        ->for($kullanici, 'kullanici')
        ->create();

    $this->actingAs($kullanici, 'musteri')
        ->get(route('magaza.siparislerim.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('siparisler.data', 10)
            ->where('siparisler.current_page', 1)
            ->where('siparisler.last_page', 2)
            ->where('siparisler.total', 11),
        );
});
