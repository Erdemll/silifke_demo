<?php

use App\Models\Kullanici;
use App\Models\Urun;
use App\Models\User;
use App\Models\Yorum;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;

test('giriş yapan müşteri ürüne yorum yapabilir', function () {
    $kullanici = Kullanici::factory()->create([
        'ad' => 'Ayşe',
        'soyad' => 'Yılmaz',
    ]);
    $urun = Urun::factory()->create();

    $response = $this
        ->actingAs($kullanici, 'musteri')
        ->from(route('magaza.urunler.show', $urun))
        ->post(route('magaza.yorumlar.store', $urun), [
            'metin' => '  Ürün gerçekten çok lezzetliydi.  ',
        ]);

    $yorum = Yorum::query()->sole();

    $this->assertModelExists($yorum);
    expect($yorum)
        ->urun_id->toBe($urun->id)
        ->kullanici_id->toBe($kullanici->id)
        ->metin->toBe('Ürün gerçekten çok lezzetliydi.');

    $response
        ->assertRedirect(route('magaza.urunler.show', $urun))
        ->assertSessionHas('success', 'Yorumunuz eklendi.');

    $this->get(route('magaza.urunler.show', $urun))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('urun.yorumlar.0.id', $yorum->id)
            ->where('urun.yorumlar.0.metin', 'Ürün gerçekten çok lezzetliydi.')
            ->where('urun.yorumlar.0.kullanici.ad', 'Ayşe')
            ->where('urun.yorumlar.0.kullanici.soyad', 'Yılmaz'),
        );
});

test('misafir yorum yapmaya çalışınca müşteri girişine yönlendirilir', function () {
    $urun = Urun::factory()->create();

    $this->post(route('magaza.yorumlar.store', $urun), [
        'metin' => 'Misafir yorumu',
    ])->assertRedirect(route('kullanici.giris'));

    expect(Yorum::query()->count())->toBe(0);
});

test('admin oturumu müşteri yorumu oluşturamaz', function () {
    $admin = User::factory()->create();
    $urun = Urun::factory()->create();

    $this->actingAs($admin)
        ->post(route('magaza.yorumlar.store', $urun), [
            'metin' => 'Admin yorumu',
        ])
        ->assertRedirect(route('kullanici.giris'));

    expect(Yorum::query()->count())->toBe(0);
});

test('yorum metni doğrulanır', function (string $metin) {
    $kullanici = Kullanici::factory()->create();
    $urun = Urun::factory()->create();

    $this->actingAs($kullanici, 'musteri')
        ->post(route('magaza.yorumlar.store', $urun), ['metin' => $metin])
        ->assertSessionHasErrors('metin');

    expect(Yorum::query()->count())->toBe(0);
})->with([
    'boş yorum' => '   ',
    'çok uzun yorum' => fn (): string => Str::repeat('a', 1001),
]);
