<?php

namespace Database\Factories;

use App\Models\Urun;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Urun>
 */
class UrunFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ad' => fake()->words(3, true),
            'aciklama' => fake()->paragraph(),
            'fiyat' => fake()->randomFloat(2, 10, 5000),
        ];
    }
}
