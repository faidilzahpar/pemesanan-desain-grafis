<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;

class OrderFileFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Relasi ke Order
            'order_id'  => Order::factory(),
            'tipe_file' => 'Preview',
            'path_file' => 'order-files/test-file.jpg',
        ];
    }
}