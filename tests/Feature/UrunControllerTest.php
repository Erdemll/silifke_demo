<?php

use App\Models\Kullanici;
use App\Models\Siparis;
use App\Models\Urun;
use App\Models\UrunResim;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

test('product list can be searched by name', function () {
    $user = User::factory()->create();
    $arananUrun = Urun::factory()->create(['ad' => 'Silifke Yoğurdu']);
    Urun::factory()->create(['ad' => 'Zeytinyağı']);
    UrunResim::factory()->for($arananUrun, 'urun')->create(['yol' => 'urunler/yogurt.jpg']);

    $this->actingAs($user)
        ->get(route('urunler.index', ['ara' => '  Silifke   Yoğurdu  ']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Urunler/Index')
            ->where('filters.ara', 'Silifke Yoğurdu')
            ->has('urunler.data', 1)
            ->where('urunler.data.0.id', $arananUrun->id)
            ->where('urunler.data.0.ad', 'Silifke Yoğurdu')
            ->has('urunler.data.0.resimler', 1),
        );
});

test('admin can create a product with multiple images', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('urunler.store'), [
        'ad' => 'Silifke Çileği',
        'aciklama' => 'Silifke ovasından günlük toplanan çilek.',
        'fiyat' => '249.90',
        'resimler' => [
            UploadedFile::fake()->image('cilek-1.jpg'),
            UploadedFile::fake()->image('cilek-2.png'),
        ],
    ]);

    $response->assertRedirect(route('urunler.index'));

    $urun = Urun::query()->where('ad', 'Silifke Çileği')->firstOrFail();
    $resimYollari = $urun->resimler()->pluck('yol')->all();

    $this->assertModelExists($urun);
    expect($urun->fiyat)->toBe('249.90')
        ->and($resimYollari)->toHaveCount(2);
    Storage::disk('public')->assertExists($resimYollari);
});

test('product creation validates fields and image uploads', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('urunler.store'), [
            'ad' => '',
            'aciklama' => '',
            'fiyat' => '0',
            'resimler' => [
                UploadedFile::fake()->create('urun.pdf', 100, 'application/pdf'),
            ],
        ])
        ->assertInvalid(['ad', 'aciklama', 'fiyat', 'resimler.0']);

    expect(Urun::query()->count())->toBe(0);
    Storage::disk('public')->assertDirectoryEmpty('urunler');
});

test('admin can update product details and replace selected images', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $urun = Urun::factory()->create();
    $silinecekResim = UrunResim::factory()->for($urun, 'urun')->create([
        'yol' => 'urunler/silinecek.jpg',
    ]);
    $kalacakResim = UrunResim::factory()->for($urun, 'urun')->create([
        'yol' => 'urunler/kalacak.jpg',
    ]);
    Storage::disk('public')->put($silinecekResim->yol, 'image');
    Storage::disk('public')->put($kalacakResim->yol, 'image');

    $response = $this->actingAs($user)
        ->from(route('urunler.index'))
        ->post(route('urunler.update', $urun), [
            '_method' => 'put',
            'ad' => 'Güncellenmiş Ürün',
            'aciklama' => 'Güncellenmiş ürün açıklaması.',
            'fiyat' => '199.50',
            'silinen_resim_ids' => [$silinecekResim->id],
            'resimler' => [UploadedFile::fake()->image('yeni-resim.webp')],
        ]);

    $response->assertRedirect(route('urunler.index'));

    $urun->refresh();
    $this->assertModelMissing($silinecekResim);
    $this->assertModelExists($kalacakResim);
    expect($urun->ad)->toBe('Güncellenmiş Ürün')
        ->and($urun->fiyat)->toBe('199.50')
        ->and($urun->resimler()->count())->toBe(2);
    Storage::disk('public')->assertMissing('urunler/silinecek.jpg');
    Storage::disk('public')->assertExists('urunler/kalacak.jpg');
    Storage::disk('public')->assertCount('urunler', 2);
});

test('product update keeps at least one image', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $urun = Urun::factory()->create();
    $resim = UrunResim::factory()->for($urun, 'urun')->create();

    $this->actingAs($user)
        ->from(route('urunler.index'))
        ->put(route('urunler.update', $urun), [
            'ad' => $urun->ad,
            'aciklama' => $urun->aciklama,
            'fiyat' => $urun->fiyat,
            'silinen_resim_ids' => [$resim->id],
        ])
        ->assertInvalid(['resimler']);

    $this->assertModelExists($resim);
});

test('product update cannot remove an image owned by another product', function () {
    $user = User::factory()->create();
    $urun = Urun::factory()->create();
    $urunResmi = UrunResim::factory()->for($urun, 'urun')->create();
    $digerUrun = Urun::factory()->create();
    $digerUrunResmi = UrunResim::factory()->for($digerUrun, 'urun')->create();

    $this->actingAs($user)
        ->put(route('urunler.update', $urun), [
            'ad' => $urun->ad,
            'aciklama' => $urun->aciklama,
            'fiyat' => $urun->fiyat,
            'silinen_resim_ids' => [$digerUrunResmi->id],
        ])
        ->assertInvalid(['silinen_resim_ids.0']);

    $this->assertModelExists($urunResmi);
    $this->assertModelExists($digerUrunResmi);
});

test('admin can delete a product and its image files', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $urun = Urun::factory()->create();
    $resimler = UrunResim::factory()->count(2)->for($urun, 'urun')->create();

    foreach ($resimler as $resim) {
        Storage::disk('public')->put($resim->yol, 'image');
    }

    $this->actingAs($user)
        ->delete(route('urunler.destroy', $urun))
        ->assertRedirect(route('urunler.index'));

    $this->assertModelMissing($urun);
    foreach ($resimler as $resim) {
        $this->assertModelMissing($resim);
        Storage::disk('public')->assertMissing($resim->yol);
    }
});

test('products with order history cannot be deleted', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $urun = Urun::factory()->create();
    $resim = UrunResim::factory()->for($urun, 'urun')->create();
    $musteri = Kullanici::factory()->create();
    Siparis::factory()->for($urun, 'urun')->for($musteri, 'kullanici')->create();
    Storage::disk('public')->put($resim->yol, 'image');

    $this->actingAs($user)
        ->delete(route('urunler.destroy', $urun))
        ->assertSessionHasErrors(['urun_silme']);

    $this->assertModelExists($urun);
    $this->assertModelExists($resim);
    Storage::disk('public')->assertExists($resim->yol);
});

test('product mutations require authentication', function (string $method, string $routeName) {
    $urun = Urun::factory()->create();
    $route = $routeName === 'urunler.store' ? route($routeName) : route($routeName, $urun);

    $this->{$method}($route)->assertRedirect(route('login'));
})->with([
    'create' => ['post', 'urunler.store'],
    'update' => ['put', 'urunler.update'],
    'delete' => ['delete', 'urunler.destroy'],
]);
