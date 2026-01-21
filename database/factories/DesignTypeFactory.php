<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DesignTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_jenis' => $this->faker->words(2, true),
            'deskripsi'  => $this->faker->sentence(),
            'durasi'     => $this->faker->numberBetween(1, 7),
            'harga'      => $this->faker->numberBetween(50000, 1000000),
            'is_active'  => 1,
        ];
    }
}