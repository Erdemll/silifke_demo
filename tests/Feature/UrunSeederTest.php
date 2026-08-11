<?php

use App\Models\Urun;
use App\Models\UrunResim;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\UrunSeeder;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\seed;

test('ana veritabanı seederı örnek ürünleri ve resimlerini oluşturur', function () {
    seed(DatabaseSeeder::class);

    $beklenenUrunler = [
        'Karadut Özü' => [
            'aciklama' => 'Doğal olarak üretilen ve ekstra şeker eklenilmeden ağzınızı tatlandıracak bu öz ile yazlarınızı şenlendirin. Su ile hızlı ve pratik bir içecek elde edin.',
            'fiyat' => '300.00',
            'resim_yolu' => 'seeder_resimler/karadut.jpeg',
        ],
        'Karadut Özü ve Keçi Boynuzu Paketi' => [
            'aciklama' => 'Doğal olarak üretilen ve ekstra şeker eklenilmeden ağzınızı tatlandıracak bu öz ile yazlarınızı şenlendirin. Su ile hızlı ve pratik bir içecek elde edin. Keçi boynuzu pekmezi ise boğaz ağrıları ve astuım gibi birçok hastalığa şifadır, şimdi ikisi avantajlı pakette.',
            'fiyat' => '600.00',
            'resim_yolu' => 'seeder_resimler/oz.jpeg',
        ],
        'Pekmez' => [
            'aciklama' => 'Keçi boynuzu, Andız pekmezi ise boğaz ağrıları ve astuım gibi birçok hastalığa şifadır, şimdi ikisi avantajlı pakette.',
            'fiyat' => '300.00',
            'resim_yolu' => 'seeder_resimler/pekmez.jpeg',
        ],
        'Salça' => [
            'aciklama' => 'Zamanında dalından koparılan taptaze domates ve biberlerle yapılan salçalarımızı gönül rahatlığıyla kullanın. Her an yazın o kıpkırmızı domateslerini yemeklerinizde kullanın.',
            'fiyat' => '300.00',
            'resim_yolu' => 'seeder_resimler/salca.jpeg',
        ],
        'Vişne Özü' => [
            'aciklama' => 'Doğal olarak üretilen ve ekstra şeker eklenilmeden ağzınızı tatlandıracak bu öz ile yazlarınızı şenlendirin. Su ile hızlı ve pratik bir içecek elde edin.',
            'fiyat' => '300.00',
            'resim_yolu' => 'seeder_resimler/visneozu.jpeg',
        ],
    ];

    expect(Urun::query()->count())->toBe(5)
        ->and(UrunResim::query()->count())->toBe(5);

    foreach ($beklenenUrunler as $urunAdi => $beklenen) {
        $urun = Urun::query()->where('ad', $urunAdi)->sole();
        $resim = $urun->resimler()->sole();

        assertModelExists($urun);
        assertModelExists($resim);

        expect($urun->aciklama)->toBe($beklenen['aciklama'])
            ->and($urun->fiyat)->toBe($beklenen['fiyat'])
            ->and($resim->urun_id)->toBe($urun->id)
            ->and($resim->yol)->toBe($beklenen['resim_yolu']);

        Storage::disk('public')->assertExists($beklenen['resim_yolu']);
    }
});

test('ürün seederı tekrar çalıştırıldığında kayıtları çoğaltmaz', function () {
    seed(UrunSeeder::class);

    $urunKimlikleri = Urun::query()->orderBy('id')->pluck('id')->all();

    seed(UrunSeeder::class);

    expect(Urun::query()->count())->toBe(5)
        ->and(UrunResim::query()->count())->toBe(5)
        ->and(Urun::query()->orderBy('id')->pluck('id')->all())->toBe($urunKimlikleri);
});
