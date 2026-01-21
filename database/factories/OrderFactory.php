<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\DesignType;
use App\Models\PaymentMethod;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'           => User::factory(), 
            'design_type_id'    => DesignType::factory(),
            'payment_method_id' => PaymentMethod::factory(),
            'deskripsi'         => $this->faker->paragraph(),
            'referensi_desain'  => null,
            'status_pesanan'    => 'Menunggu DP',
            'deadline'          => now()->addDays(7), 
        ];
    }
}