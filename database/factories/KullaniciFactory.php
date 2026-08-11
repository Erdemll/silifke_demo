<?php

namespace Database\Factories;

use App\Models\Kullanici;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kullanici>
 */
class KullaniciFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ad' => fake()->firstName(),
            'soyad' => fake()->lastName(),
            'mail' => fake()->unique()->safeEmail(),
            'sifre' => 'password',
        ];
    }
}
