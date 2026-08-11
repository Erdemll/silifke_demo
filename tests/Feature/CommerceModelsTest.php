<?php

use App\Models\Kullanici;
use App\Models\Siparis;
use App\Models\Urun;
use App\Models\UrunResim;
use App\Models\Yorum;
use Carbon\CarbonInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

test('commerce tables contain the expected columns', function () {
    expect(Schema::hasColumns('kullanicilar', [
        'id', 'ad', 'soyad', 'mail', 'sifre', 'created_at', 'updated_at',
    ]))->toBeTrue()
        ->and(Schema::hasColumns('urunler', [
            'id', 'ad', 'aciklama', 'fiyat', 'created_at', 'updated_at',
        ]))->toBeTrue()
        ->and(Schema::hasColumns('urun_resimler', [
            'id', 'urun_id', 'yol', 'created_at', 'updated_at',
        ]))->toBeTrue()
        ->and(Schema::hasColumns('yorumlar', [
            'id', 'urun_id', 'kullanici_id', 'metin', 'created_at', 'updated_at',
        ]))->toBeTrue()
        ->and(Schema::hasColumns('siparisler', [
            'id', 'urun_id', 'kullanici_id', 'fiyat', 'kargo_kodu', 'tarih', 'created_at', 'updated_at',
        ]))->toBeTrue();
});

test('commerce models store data and expose their relationships', function () {
    $kullanici = Kullanici::factory()->create([
        'sifre' => 'gizli-parola',
    ]);
    $urun = Urun::factory()->create([
        'fiyat' => '349.90',
    ]);
    $urunResim = UrunResim::factory()->for($urun, 'urun')->create();
    $yorum = Yorum::factory()
        ->for($urun, 'urun')
        ->for($kullanici, 'kullanici')
        ->create();
    $siparis = Siparis::factory()
        ->for($urun, 'urun')
        ->for($kullanici, 'kullanici')
        ->create([
            'fiyat' => '349.90',
            'kargo_kodu' => 'SY-2026-0001',
        ]);

    $this->assertModelExists($urunResim);
    $this->assertModelExists($yorum);
    $this->assertModelExists($siparis);

    expect($urun->resimler->first()->is($urunResim))->toBeTrue()
        ->and($urun->yorumlar->first()->is($yorum))->toBeTrue()
        ->and($urun->siparisler->first()->is($siparis))->toBeTrue()
        ->and($yorum->kullanici->is($kullanici))->toBeTrue()
        ->and($siparis->urun->is($urun))->toBeTrue()
        ->and($siparis->kullanici->is($kullanici))->toBeTrue()
        ->and($siparis->fiyat)->toBe('349.90')
        ->and($siparis->tarih)->toBeInstanceOf(CarbonInterface::class)
        ->and(Hash::check('gizli-parola', $kullanici->sifre))->toBeTrue()
        ->and($kullanici->toArray())->not->toHaveKey('sifre');
});

test('ürün resmi kaynağına göre doğru public URL üretir', function () {
    $seederResmi = UrunResim::factory()->make([
        'yol' => 'seeder_resimler/pekmez.jpeg',
    ]);
    $yuklenenResim = UrunResim::factory()->make([
        'yol' => 'urunler/yuklenen-resim.jpg',
    ]);

    expect($seederResmi->url)->toBe(asset('seeder_resimler/pekmez.jpeg'))
        ->and(parse_url($seederResmi->url, PHP_URL_PATH))->toBe('/seeder_resimler/pekmez.jpeg')
        ->and($seederResmi->url)->not->toContain('/public/', '/storage/seeder_resimler/')
        ->and($yuklenenResim->url)->toBe(Storage::disk('public')->url('urunler/yuklenen-resim.jpg'));
});

test('dependent records cascade while orders protect their history', function () {
    $kullanici = Kullanici::factory()->create();
    $urun = Urun::factory()->create();
    $urunResim = UrunResim::factory()->for($urun, 'urun')->create();
    $yorum = Yorum::factory()
        ->for($urun, 'urun')
        ->for($kullanici, 'kullanici')
        ->create();

    $urun->delete();

    $this->assertModelMissing($urunResim);
    $this->assertModelMissing($yorum);

    $siparisUrunu = Urun::factory()->create();
    $siparis = Siparis::factory()
        ->for($siparisUrunu, 'urun')
        ->for($kullanici, 'kullanici')
        ->create();

    expect(fn () => $siparisUrunu->delete())->toThrow(QueryException::class);

    $this->assertModelExists($siparis);
});
