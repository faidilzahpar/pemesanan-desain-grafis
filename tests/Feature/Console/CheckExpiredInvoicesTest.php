<?php

namespace Tests\Feature\Console;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckExpiredInvoicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_bisa_mengubah_status_invoice_yang_basi()
    {
        // 1. Setup User
        $user = User::factory()->create();

        // ---------------------------------------------------------
        // SKENARIO A: Invoice yang SUDAH LEWAT 24 JAM (Harus Expired)
        // ---------------------------------------------------------
        $orderOld = Order::factory()->create([
            'user_id' => $user->user_id, 
            'status_pesanan' => 'Menunggu DP'
        ]);
        
        $invoiceOld = Invoice::factory()->create([
            'order_id' => $orderOld->order_id,
            'jenis_invoice' => 'DP',
            'status_pembayaran' => 'Belum Dibayar',
            'created_at' => Carbon::now()->subHours(25) // 25 Jam yang lalu
        ]);

        // ---------------------------------------------------------
        // SKENARIO B: Invoice BARU 1 JAM (Jangan Berubah)
        // ---------------------------------------------------------
        $orderNew = Order::factory()->create([
            'user_id' => $user->user_id, 
            'status_pesanan' => 'Menunggu DP'
        ]);
        
        $invoiceNew = Invoice::factory()->create([
            'order_id' => $orderNew->order_id,
            'jenis_invoice' => 'DP',
            'status_pembayaran' => 'Belum Dibayar',
            'created_at' => Carbon::now()->subHour() // 1 Jam yang lalu
        ]);

        // ---------------------------------------------------------
        // 3. JALANKAN COMMAND
        // ---------------------------------------------------------
        // Penting: Signature harus sama persis dengan protected $signature di Command file
        $this->artisan('invoices:check-expired')
             ->assertExitCode(0); 

        // ---------------------------------------------------------
        // 4. ASSERTION (PEMBUKTIAN)
        // ---------------------------------------------------------
        
        // Cek Invoice Lama (Harus berubah jadi Expired & Dibatalkan)
        $this->assertEquals('Pembayaran Expired', $invoiceOld->refresh()->status_pembayaran);
        $this->assertEquals('Dibatalkan', $orderOld->refresh()->status_pesanan);

        // Cek Invoice Baru (Harus tetap Belum Dibayar)
        $this->assertEquals('Belum Dibayar', $invoiceNew->refresh()->status_pembayaran);
        $this->assertEquals('Menunggu DP', $orderNew->refresh()->status_pesanan);
    }
}