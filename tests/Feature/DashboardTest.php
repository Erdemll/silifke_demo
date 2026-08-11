<?php

use App\Models\Kullanici;
use App\Models\Siparis;
use App\Models\Urun;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('public registration is disabled for the single admin demo', function () {
    $this->get('/register')->assertNotFound();
    $this->post('/register')->assertNotFound();
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $kullanicilar = Kullanici::factory()->count(3)->create();
    $urun = Urun::factory()->create();

    Siparis::factory()->create([
        'urun_id' => $urun->id,
        'kullanici_id' => $kullanicilar[0]->id,
        'fiyat' => '125.50',
        'kargo_kodu' => null,
        'tarih' => now(),
    ]);
    Siparis::factory()->create([
        'urun_id' => $urun->id,
        'kullanici_id' => $kullanicilar[1]->id,
        'fiyat' => '74.50',
        'kargo_kodu' => 'SY-2026-0001',
        'tarih' => now(),
    ]);
    Siparis::factory()->create([
        'urun_id' => $urun->id,
        'kullanici_id' => $kullanicilar[2]->id,
        'fiyat' => '500.00',
        'kargo_kodu' => null,
        'tarih' => now()->subDay(),
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('metrics.customerCount', 3)
            ->where('metrics.todaySalesTotal', 200)
            ->where('metrics.pendingShipmentCount', 2),
        );
});

test('management pages require authentication', function (string $routeName) {
    $this->get(route($routeName))->assertRedirect(route('login'));
})->with([
    'products' => 'urunler.index',
    'orders' => 'siparisler.index',
]);

test('authenticated users can visit management pages', function (string $routeName, string $component) {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route($routeName))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component($component));
})->with([
    'products' => ['urunler.index', 'Urunler/Index'],
    'orders' => ['siparisler.index', 'Siparisler/Index'],
]);
