<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PortfolioFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'judul'     => $this->faker->sentence(3), // Contoh: "Desain Logo Coffee Shop"
            'kategori'  => $this->faker->randomElement(['Logo', 'Banner', 'Poster', 'Media Sosial']),
            'gambar'    => 'portfolios/' . $this->faker->word() . '.jpg', // Simulasi path file
            'deskripsi' => $this->faker->paragraph(),
        ];
    }
}