<?php

namespace Database\Factories;

use App\Models\Kullanici;
use App\Models\Urun;
use App\Models\Yorum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Yorum>
 */
class YorumFactory extends Factory
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
            'metin' => fake()->paragraph(),
        ];
    }
}
