<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentMethodFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_metode' => 'Bank ' . $this->faker->unique()->word(), 
            'nomor_akun'  => $this->faker->bankAccountNumber(), // SUDAH DIPERBAIKI
            'atas_nama'   => $this->faker->name(),
            'qr_path'     => null,
            'is_active'   => 1,
        ];
    }
}