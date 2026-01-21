<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;

class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Relasi ke Order
            'order_id'          => Order::factory(),
            'jenis_invoice'     => 'DP',
            'status_pembayaran' => 'Belum Dibayar',
            'bukti_path'        => null,
            'tgl_bayar'         => null,
        ];
    }
}