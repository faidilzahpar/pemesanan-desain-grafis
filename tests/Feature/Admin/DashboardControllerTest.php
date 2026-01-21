<?php

namespace Tests\Feature\Admin;

use App\Models\DesignType;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str; // Tambahan penting untuk UUID
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper untuk membuat Admin dengan USER_ID string (Sesuai migrasi anda)
     */
    private function createAdmin()
    {
        // Kita paksa isi user_id dan no_hp karena di migrasi anda wajib dan unique
        return User::factory()->create([
            'user_id'   => (string) Str::uuid(), // Generate UUID manual
            'name'      => 'Admin Test',
            'email'     => 'admintest@example.com',
            'no_hp'     => '081234567890',       // Harus unik
            'password'  => bcrypt('password'),
            'is_admin'  => 1,
        ]);
    }

    /** @test */
    public function test_halaman_dashboard_bisa_diakses_oleh_admin()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)
                         ->get('/admin'); 

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
    }

    /** @test */
    public function test_halaman_dashboard_menampilkan_jumlah_data_yang_benar()
    {
        $admin = $this->createAdmin();

        // --- SETUP DATA DUMMY ---
        // Kita perlu user biasa untuk pemilik order
        $userBiasa = User::factory()->create([
            'user_id' => (string) Str::uuid(),
            'email'   => 'user@example.com',
            'no_hp'   => '08987654321',
            'is_admin'=> 0
        ]);

        // 1. Skenario: Orders In Progress
        Order::factory()->create(['user_id' => $userBiasa->user_id, 'status_pesanan' => 'Sedang Dikerjakan']);
        Order::factory()->create(['user_id' => $userBiasa->user_id, 'status_pesanan' => 'Menunggu Konfirmasi Pelanggan']);
        Order::factory()->create(['user_id' => $userBiasa->user_id, 'status_pesanan' => 'Selesai']); 

        // 2. Skenario: Orders Revisi
        Order::factory()->count(2)->create([
            'user_id' => $userBiasa->user_id, 
            'status_pesanan' => 'Revisi'
        ]);
        
        // 3. Skenario: Pending Payments
        // Pastikan order terkait dibuat dulu
        $orderInvoice = Order::factory()->create(['user_id' => $userBiasa->user_id]);
        Invoice::factory()->create(['order_id' => $orderInvoice->order_id, 'status_pembayaran' => 'Menunggu Verifikasi']);
        
        $orderLunas = Order::factory()->create(['user_id' => $userBiasa->user_id]);
        Invoice::factory()->create(['order_id' => $orderLunas->order_id, 'status_pembayaran' => 'Lunas']);

        // 4. Skenario: Near Deadline
        Order::factory()->create([
            'user_id' => $userBiasa->user_id,
            'deadline' => Carbon::now()->addHours(2),
            'status_pesanan' => 'Sedang Dikerjakan'
        ]);
        
        // 5. Skenario: Total Design Types
        DesignType::factory()->count(3)->create();

        // --- EKSEKUSI ---
        $response = $this->actingAs($admin)
                         ->get('/admin');

        // --- ASSERT ---
        $response->assertStatus(200);
        
        // Cek Variable View
        $response->assertViewHas('ordersInProgress', 3); 
        $response->assertViewHas('ordersRevisi', 2);
        $response->assertViewHas('pendingPayments', 1);
        $response->assertViewHas('nearDeadline', 1);
        // PERBAIKAN: Menjadi 11 karena factory Order otomatis membuat DesignType
        $response->assertViewHas('totalDesignTypes', 11);
    }

    /** @test */
    public function test_endpoint_get_stats_mengembalikan_json_yang_benar()
    {
        $admin = $this->createAdmin();
        
        // Buat User Biasa
        $userBiasa = User::factory()->create([
            'user_id' => (string) Str::uuid(),
            'email'   => 'userjson@example.com',
            'no_hp'   => '08111112222',
            'is_admin'=> 0
        ]);

        // Buat 1 data dummy
        Order::factory()->create([
            'user_id' => $userBiasa->user_id,
            'status_pesanan' => 'Revisi'
        ]);
        
        // URL DISESUAIKAN ( /admin/stats )
        $response = $this->actingAs($admin)
                         ->getJson('/admin/stats'); 

        $response->assertStatus(200)
                 ->assertJson([
                     'ordersRevisi' => '1',
                     'ordersInProgress' => '0',
                 ]);
    }
}