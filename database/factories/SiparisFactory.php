<?php

namespace Database\Factories;

use App\Models\Kullanici;
use App\Models\Siparis;
use App\Models\Urun;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Siparis>
 */
class SiparisFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'urun_id' => Urun::factory(),
            'kullanici_id' => Kullanici::factory(),
            'fiyat' => fake()->randomFloat(2, 10, 5000),
            'kargo_kodu' => null,
            'tarih' => now(),
        ];
    }
}
