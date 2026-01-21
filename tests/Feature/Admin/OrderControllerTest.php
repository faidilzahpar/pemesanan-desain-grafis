<?php

namespace Tests\Feature\Admin;

use App\Models\DesignType;
use App\Models\Order;
use App\Models\OrderFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderControllerTest extends TestCase
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

    /** * Test Index: Menampilkan pesanan aktif */
    public function test_index_menampilkan_pesanan_aktif()
    {
        $admin = $this->createAdmin();
        
        // Buat pesanan status aktif
        Order::factory()->create(['status_pesanan' => 'Sedang Dikerjakan']);
        // Buat pesanan status excluded
        Order::factory()->create(['status_pesanan' => 'Selesai']);

        $response = $this->actingAs($admin)->get(route('admin.orders.index'));

        $response->assertStatus(200);
        // Pastikan hanya 1 yang muncul (karena Selesai di-exclude)
        $this->assertCount(1, $response->viewData('orders'));
    }

    /** * Test History: Menampilkan Selesai & Dibatalkan */
    public function test_history_menampilkan_pesanan_lampau()
    {
        $admin = $this->createAdmin();
        
        Order::factory()->create(['status_pesanan' => 'Selesai']);
        Order::factory()->create(['status_pesanan' => 'Dibatalkan']);
        Order::factory()->create(['status_pesanan' => 'Sedang Dikerjakan']);

        $response = $this->actingAs($admin)->get(route('admin.orders.history'));

        $response->assertStatus(200);
        // Harusnya ada 2 (Selesai & Dibatalkan)
        $this->assertCount(2, $response->viewData('orders'));
    }

    /** * Test Show Detail */
    public function test_show_menampilkan_detail_pesanan()
    {
        $admin = $this->createAdmin();
        $order = Order::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.orders.show', $order->order_id));

        $response->assertStatus(200);
        $response->assertViewHas('order');
    }

    /** * Test Upload dengan Watermark (Penyebab Error 500) */
    public function test_upload_file_preview_dengan_watermark()
    {
        $admin = $this->createAdmin();
        $order = Order::factory()->create(['status_pesanan' => 'Sedang Dikerjakan']);
        
        Storage::fake('public');

        // PERBAIKAN: Buat gambar PNG ASLI untuk watermark agar Intervention tidak error
        $watermarkPath = public_path('watermark.png');
        $fakeWatermark = UploadedFile::fake()->image('watermark.png', 100, 100);
        file_put_contents($watermarkPath, file_get_contents($fakeWatermark));

        $file = UploadedFile::fake()->image('hasil_desain.jpg', 500, 500);

        $response = $this->actingAs($admin)->post(route('admin.orders.upload', $order->order_id), [
            'file' => $file
        ]);

        $response->assertStatus(302); // Redirect back
        $this->assertDatabaseHas('order_files', [
            'order_id'  => $order->order_id,
            'tipe_file' => 'Preview'
        ]);

        // Bersihkan file watermark dummy setelah test
        if (file_exists($watermarkPath)) unlink($watermarkPath);
    }

    /** * Test Logika Tipe File Revisi */
    public function test_upload_file_logika_revisi()
    {
        $admin = $this->createAdmin();
        $order = Order::factory()->create(['status_pesanan' => 'Sedang Dikerjakan']);

        // Tambah 1 file (sebagai Preview)
        OrderFile::factory()->create([
            'order_id'  => $order->order_id,
            'tipe_file' => 'Preview'
        ]);

        Storage::fake('public');
        $file = UploadedFile::fake()->image('revisi_1.jpg');

        $response = $this->actingAs($admin)->post(route('admin.orders.upload', $order->order_id), [
            'file' => $file
        ]);

        $this->assertDatabaseHas('order_files', [
            'order_id'  => $order->order_id,
            'tipe_file' => 'Revisi'
        ]);
    }

    /** * Test Upload Final */
    public function test_upload_file_final()
    {
        $admin = $this->createAdmin();
        // Status khusus yang memaksa tipe jadi 'Final'
        $order = Order::factory()->create(['status_pesanan' => 'Menunggu File Final']);

        Storage::fake('public');
        $file = UploadedFile::fake()->create('final_design.zip', 1000);

        $response = $this->actingAs($admin)->post(route('admin.orders.upload', $order->order_id), [
            'file' => $file
        ]);

        $this->assertDatabaseHas('order_files', [
            'order_id'  => $order->order_id,
            'tipe_file' => 'Final'
        ]);
        
        $this->assertEquals('Selesai', $order->refresh()->status_pesanan);
    }
}