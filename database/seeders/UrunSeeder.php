<?php

namespace Database\Seeders;

use App\Models\Urun;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UrunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var array<int, array{ad: string, aciklama: string, fiyat: string, resim_yolu: string}> $urunler */
        $urunler = [
            [
                'ad' => 'Karadut Özü',
                'aciklama' => 'Doğal olarak üretilen ve ekstra şeker eklenilmeden ağzınızı tatlandıracak bu öz ile yazlarınızı şenlendirin. Su ile hızlı ve pratik bir içecek elde edin.',
                'fiyat' => '300.00',
                'resim_yolu' => 'seeder_resimler/karadut.jpeg',
            ],
            [
                'ad' => 'Karadut Özü ve Keçi Boynuzu Paketi',
                'aciklama' => 'Doğal olarak üretilen ve ekstra şeker eklenilmeden ağzınızı tatlandıracak bu öz ile yazlarınızı şenlendirin. Su ile hızlı ve pratik bir içecek elde edin. Keçi boynuzu pekmezi ise boğaz ağrıları ve astuım gibi birçok hastalığa şifadır, şimdi ikisi avantajlı pakette.',
                'fiyat' => '600.00',
                'resim_yolu' => 'seeder_resimler/oz.jpeg',
            ],
            [
                'ad' => 'Pekmez',
                'aciklama' => 'Keçi boynuzu, Andız pekmezi ise boğaz ağrıları ve astuım gibi birçok hastalığa şifadır, şimdi ikisi avantajlı pakette.',
                'fiyat' => '300.00',
                'resim_yolu' => 'seeder_resimler/pekmez.jpeg',
            ],
            [
                'ad' => 'Salça',
                'aciklama' => 'Zamanında dalından koparılan taptaze domates ve biberlerle yapılan salçalarımızı gönül rahatlığıyla kullanın. Her an yazın o kıpkırmızı domateslerini yemeklerinizde kullanın.',
                'fiyat' => '300.00',
                'resim_yolu' => 'seeder_resimler/salca.jpeg',
            ],
            [
                'ad' => 'Vişne Özü',
                'aciklama' => 'Doğal olarak üretilen ve ekstra şeker eklenilmeden ağzınızı tatlandıracak bu öz ile yazlarınızı şenlendirin. Su ile hızlı ve pratik bir içecek elde edin.',
                'fiyat' => '300.00',
                'resim_yolu' => 'seeder_resimler/visneozu.jpeg',
            ],
        ];

        DB::transaction(function () use ($urunler): void {
            foreach ($urunler as $urunVerisi) {
                $urun = Urun::query()->updateOrCreate(
                    ['ad' => $urunVerisi['ad']],
                    [
                        'aciklama' => $urunVerisi['aciklama'],
                        'fiyat' => $urunVerisi['fiyat'],
                    ],
                );

                $urun->resimler()->updateOrCreate([
                    'yol' => $urunVerisi['resim_yolu'],
                ]);
            }
        });
    }
}
