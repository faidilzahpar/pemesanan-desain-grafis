<?php

namespace Tests\Feature\Admin;

use App\Models\DesignType;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper untuk membuat Admin
     */
    private function createAdmin()
    {
        return User::factory()->create([
            'is_admin' => 1,
            'no_hp'    => '0812' . rand(1000, 9999),
        ]);
    }

    /** * Test Index: Menampilkan pembayaran yang menunggu verifikasi */
    public function test_index_menampilkan_pembayaran_menunggu_verifikasi()
    {
        $admin = $this->createAdmin();

        // Buat invoice yang harus muncul
        $invoiceWait = Invoice::factory()->create(['status_pembayaran' => 'Menunggu Verifikasi']);
        
        // Buat invoice yang harus sembunyi (Lunas)
        Invoice::factory()->create(['status_pembayaran' => 'Lunas']);

        $response = $this->actingAs($admin)->get(route('admin.payments.index'));

        $response->assertStatus(200);
        $this->assertCount(1, $response->viewData('invoices'));
        $response->assertSee($invoiceWait->order->order_id);
    }

    /** * Test Search: Berdasarkan Nama User */
    public function test_index_pencarian_berdasarkan_nama_customer()
    {
        $admin = $this->createAdmin();
        
        $userTarget = User::factory()->create(['name' => 'Budi Sudarsono']);
        $order = Order::factory()->create(['user_id' => $userTarget->user_id]);
        Invoice::factory()->create([
            'order_id' => $order->order_id,
            'status_pembayaran' => 'Menunggu Verifikasi'
        ]);

        // Invoice lain yang tidak dicari
        Invoice::factory()->create(['status_pembayaran' => 'Menunggu Verifikasi']);

        $response = $this->actingAs($admin)->get(route('admin.payments.index', [
            'tableSearch' => 'Budi'
        ]));

        $response->assertStatus(200);
        $response->assertSee('Budi Sudarsono');
        $this->assertCount(1, $response->viewData('invoices'));
    }

    /** * Test Sorting: Berdasarkan Nama User (Multi-Join) */
    public function test_index_sorting_nama_user()
    {
        $admin = $this->createAdmin();

        $userA = User::factory()->create(['name' => 'Agus']);
        $orderA = Order::factory()->create(['user_id' => $userA->user_id]);
        Invoice::factory()->create(['order_id' => $orderA->order_id, 'status_pembayaran' => 'Menunggu Verifikasi']);

        $userZ = User::factory()->create(['name' => 'Zola']);
        $orderZ = Order::factory()->create(['user_id' => $userZ->user_id]);
        Invoice::factory()->create(['order_id' => $orderZ->order_id, 'status_pembayaran' => 'Menunggu Verifikasi']);

        // Test Sort DESC (Zola dulu baru Agus)
        $response = $this->actingAs($admin)->get(route('admin.payments.index', [
            'tableSortColumn' => 'user_name',
            'tableSortDirection' => 'desc'
        ]));

        $response->assertStatus(200);
        $response->assertSeeInOrder(['Zola', 'Agus']);
    }

    /** * Test Sorting: Berdasarkan Jumlah Bayar */
    public function test_index_sorting_jumlah_bayar()
    {
        $admin = $this->createAdmin();

        // Design Type Murah (Harga 100.000 -> DP 50.000)
        $cheap = DesignType::factory()->create(['harga' => 100000]);
        $orderCheap = Order::factory()->create(['design_type_id' => $cheap->design_type_id]);
        Invoice::factory()->create([
            'order_id' => $orderCheap->order_id, 
            'status_pembayaran' => 'Menunggu Verifikasi',
            'jenis_invoice' => 'DP'
        ]);

        // Design Type Mahal (Harga 900.000 -> DP 450.000)
        $expensive = DesignType::factory()->create(['harga' => 900000]);
        $orderExpensive = Order::factory()->create(['design_type_id' => $expensive->design_type_id]);
        Invoice::factory()->create([
            'order_id' => $orderExpensive->order_id, 
            'status_pembayaran' => 'Menunggu Verifikasi',
            'jenis_invoice' => 'DP'
        ]);

        // Test Sort ASC (Murah ke Mahal)
        $response = $this->actingAs($admin)->get(route('admin.payments.index', [
            'tableSortColumn' => 'jumlah_bayar',
            'tableSortDirection' => 'asc'
        ]));

        $response->assertStatus(200);
        
        // Memastikan Order ID yang murah muncul lebih dulu daripada yang mahal
        $response->assertSeeInOrder([$orderCheap->order_id, $orderExpensive->order_id]); 
    }
}