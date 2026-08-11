<?php

use App\Models\Kullanici;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;

test('ana sayfa misafirlere açıktır', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->where('auth.kullanici', null),
        );
});

test('müşteri giriş ve kayıt sayfaları gösterilir', function (string $routeName, string $component) {
    $this->get(route($routeName))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component($component));
})->with([
    'giriş' => ['kullanici.giris', 'auth/KullaniciGiris'],
    'kayıt' => ['kullanici.kayit', 'auth/KullaniciKayit'],
]);

test('müşteri ad soyad e-posta ve şifre tekrarıyla kayıt olabilir', function () {
    $response = $this->post(route('kullanici.kayit.store'), [
        'ad' => '  Ayşe  Nur ',
        'soyad' => ' Yılmaz ',
        'mail' => 'AYSE@EXAMPLE.COM ',
        'sifre' => 'password',
        'sifre_confirmation' => 'password',
    ]);

    $kullanici = Kullanici::query()->sole();

    expect($kullanici)
        ->ad->toBe('Ayşe Nur')
        ->soyad->toBe('Yılmaz')
        ->mail->toBe('ayse@example.com')
        ->and(Hash::check('password', $kullanici->sifre))->toBeTrue();

    $this->assertAuthenticatedAs($kullanici, 'musteri');
    $this->assertGuest('web');
    $response->assertRedirect(route('home'));
});

test('kayıt bilgileri ve şifre tekrarı doğrulanır', function (array $payload, string $errorField) {
    $this->post(route('kullanici.kayit.store'), $payload)
        ->assertSessionHasErrors($errorField);

    expect(Kullanici::query()->count())->toBe(0);
})->with([
    'zorunlu alanlar' => [[], 'ad'],
    'geçersiz e-posta' => [[
        'ad' => 'Ayşe',
        'soyad' => 'Yılmaz',
        'mail' => 'gecersiz',
        'sifre' => 'password',
        'sifre_confirmation' => 'password',
    ], 'mail'],
    'eşleşmeyen şifre' => [[
        'ad' => 'Ayşe',
        'soyad' => 'Yılmaz',
        'mail' => 'ayse@example.com',
        'sifre' => 'password',
        'sifre_confirmation' => 'different-password',
    ], 'sifre'],
]);

test('aynı e-posta adresiyle ikinci müşteri kaydedilemez', function () {
    Kullanici::factory()->create(['mail' => 'ayse@example.com']);

    $this->post(route('kullanici.kayit.store'), [
        'ad' => 'Ayşe',
        'soyad' => 'Yılmaz',
        'mail' => 'AYSE@EXAMPLE.COM',
        'sifre' => 'password',
        'sifre_confirmation' => 'password',
    ])->assertSessionHasErrors('mail');

    expect(Kullanici::query()->count())->toBe(1);
});

test('müşteri e-posta ve şifresiyle giriş yapıp ana sayfaya yönlendirilir', function () {
    $kullanici = Kullanici::factory()->create([
        'mail' => 'ayse@example.com',
        'sifre' => 'password',
    ]);

    $response = $this->post(route('kullanici.giris.store'), [
        'mail' => ' AYSE@EXAMPLE.COM ',
        'sifre' => 'password',
    ]);

    $this->assertAuthenticatedAs($kullanici, 'musteri');
    $this->assertGuest('web');
    $response->assertRedirect(route('home', absolute: false));
});

test('hatalı şifreyle müşteri girişi yapılamaz', function () {
    Kullanici::factory()->create([
        'mail' => 'ayse@example.com',
        'sifre' => 'password',
    ]);

    $this->from(route('kullanici.giris'))
        ->post(route('kullanici.giris.store'), [
            'mail' => 'ayse@example.com',
            'sifre' => 'yanlis-password',
        ])
        ->assertRedirect(route('kullanici.giris'))
        ->assertSessionHasErrors('mail');

    $this->assertGuest('musteri');
});

test('müşteri giriş denemeleri sınırlandırılır', function () {
    Kullanici::factory()->create([
        'mail' => 'limit@example.com',
        'sifre' => 'password',
    ]);

    foreach (range(1, 5) as $attempt) {
        $this->post(route('kullanici.giris.store'), [
            'mail' => 'limit@example.com',
            'sifre' => 'yanlis-password',
        ])->assertSessionHasErrors('mail');
    }

    $this->post(route('kullanici.giris.store'), [
        'mail' => 'limit@example.com',
        'sifre' => 'yanlis-password',
    ])->assertTooManyRequests();

    $this->assertGuest('musteri');
});

test('giriş yapan müşteri ana sayfada paylaşılır', function () {
    $kullanici = Kullanici::factory()->create();

    $this->actingAs($kullanici, 'musteri')
        ->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->where('auth.kullanici.id', $kullanici->id)
            ->where('auth.kullanici.ad', $kullanici->ad)
            ->where('auth.kullanici.soyad', $kullanici->soyad)
            ->where('auth.kullanici.mail', $kullanici->mail),
        );
});

test('giriş yapan müşteri giriş ve kayıt sayfalarından ana sayfaya yönlendirilir', function (string $routeName) {
    $kullanici = Kullanici::factory()->create();

    $this->actingAs($kullanici, 'musteri')
        ->get(route($routeName))
        ->assertRedirect(route('home'));
})->with([
    'giriş' => 'kullanici.giris',
    'kayıt' => 'kullanici.kayit',
]);

test('müşteri çıkış yaparken admin oturumu korunur', function () {
    $admin = User::factory()->create();
    $kullanici = Kullanici::factory()->create();

    $response = $this
        ->actingAs($admin, 'web')
        ->actingAs($kullanici, 'musteri')
        ->post(route('kullanici.cikis'));

    $this->assertGuest('musteri');
    $this->assertAuthenticatedAs($admin, 'web');
    $response->assertRedirect(route('home'));
});

test('misafir müşteri çıkış rotasından müşteri girişine yönlendirilir', function () {
    $this->post(route('kullanici.cikis'))
        ->assertRedirect(route('kullanici.giris'));
});
