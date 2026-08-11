<?php

use App\Models\Kullanici;
use App\Models\Siparis;
use App\Models\Urun;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('admin can see orders with customer product and shipment information', function () {
    $admin = User::factory()->create();
    $ilkKullanici = Kullanici::factory()->create([
        'ad' => 'Ayşe',
        'soyad' => 'Yılmaz',
        'mail' => 'ayse@example.com',
    ]);
    $ikinciKullanici = Kullanici::factory()->create([
        'ad' => 'Mehmet',
        'soyad' => 'Demir',
        'mail' => 'mehmet@example.com',
    ]);
    $ilkUrun = Urun::factory()->create(['ad' => 'Silifke Yoğurdu']);
    $ikinciUrun = Urun::factory()->create(['ad' => 'Keçiboynuzu Pekmezi']);

    $eskiSiparis = Siparis::factory()->create([
        'kullanici_id' => $ilkKullanici->id,
        'urun_id' => $ilkUrun->id,
        'fiyat' => '175.50',
        'kargo_kodu' => 'SY-ESKI-001',
        'tarih' => now()->subHour(),
    ]);
    $yeniSiparis = Siparis::factory()->create([
        'kullanici_id' => $ikinciKullanici->id,
        'urun_id' => $ikinciUrun->id,
        'fiyat' => '240.00',
        'kargo_kodu' => 'SY-YENI-002',
        'tarih' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('siparisler.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Siparisler/Index')
            ->where('siparisler.total', 2)
            ->has('siparisler.data', 2)
            ->where('siparisler.data.0.id', $yeniSiparis->id)
            ->where('siparisler.data.0.kullanici.ad', 'Mehmet')
            ->where('siparisler.data.0.kullanici.soyad', 'Demir')
            ->where('siparisler.data.0.kullanici.mail', 'mehmet@example.com')
            ->where('siparisler.data.0.urun.ad', 'Keçiboynuzu Pekmezi')
            ->where('siparisler.data.0.fiyat', '240.00')
            ->where('siparisler.data.0.kargo_kodu', 'SY-YENI-002')
            ->where('siparisler.data.1.id', $eskiSiparis->id)
            ->where('siparisler.data.1.kullanici.ad', 'Ayşe')
            ->where('siparisler.data.1.kullanici.soyad', 'Yılmaz')
            ->where('siparisler.data.1.kullanici.mail', 'ayse@example.com')
            ->where('siparisler.data.1.urun.ad', 'Silifke Yoğurdu')
            ->where('siparisler.data.1.fiyat', '175.50')
            ->where('siparisler.data.1.kargo_kodu', 'SY-ESKI-001'),
        );
});

test('admin order list is paginated', function () {
    $admin = User::factory()->create();

    Siparis::factory()->count(16)->create();

    $this->actingAs($admin)
        ->get(route('siparisler.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('siparisler.total', 16)
            ->where('siparisler.current_page', 1)
            ->where('siparisler.last_page', 2)
            ->has('siparisler.data', 15),
        );
});
