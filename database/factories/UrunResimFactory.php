<?php

namespace Database\Factories;

use App\Models\Urun;
use App\Models\UrunResim;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UrunResim>
 */
class UrunResimFactory extends Factory
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
            'yol' => 'urunler/'.fake()->uuid().'.webp',
        ];
    }
}
