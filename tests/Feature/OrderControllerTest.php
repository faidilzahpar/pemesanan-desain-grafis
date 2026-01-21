<?php

namespace Tests\Feature;

use App\Models\DesignType;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderFile;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    /** * Test Index: User hanya melihat pesanan miliknya sendiri */
    public function test_user_hanya_melihat_pesanan_milik_sendiri()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        // Order milik A
        Order::factory()->create(['user_id' => $userA->user_id]);
        
        // Order milik B (Seharusnya tidak terlihat oleh A)
        Order::factory()->create(['user_id' => $userB->user_id]);

        $response = $this->actingAs($userA)->get(route('orders.index'));

        $response->assertStatus(200);
        $this->assertCount(1, $response->viewData('orders'));
    }

    /** * Test Filter Status: Memastikan filter url ?status=... bekerja */
    public function test_filter_status_pesanan_bekerja()
    {
        $user = User::factory()->create();
        
        // Buat 1 Order Selesai & 1 Order Menunggu DP
        Order::factory()->create(['user_id' => $user->user_id, 'status_pesanan' => 'Selesai']);
        Order::factory()->create(['user_id' => $user->user_id, 'status_pesanan' => 'Menunggu DP']);

        // Filter 'done' (Selesai)
        $response = $this->actingAs($user)->get(route('orders.index', ['status' => 'done']));
        
        $this->assertCount(1, $response->viewData('orders'));
        $response->assertSee('Selesai');
        $response->assertDontSee('Menunggu DP');
    }

    /** * Test Show: Mencegah User melihat detail pesanan orang lain (IDOR Protection) */
    public function test_mencegah_akses_pesanan_orang_lain()
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        
        $order = Order::factory()->create(['user_id' => $owner->user_id]);

        // Stranger mencoba akses
        $response = $this->actingAs($stranger)->get(route('orders.show', $order->order_id));
        
        $response->assertStatus(403);
    }

    /** * Test Store: Alur lengkap pembuatan pesanan baru + Auto Invoice DP */
    public function test_customer_membuat_pesanan_baru()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        
        $designType = DesignType::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create();
        $file = UploadedFile::fake()->image('referensi.jpg');

        $data = [
            'design_type_id'    => $designType->design_type_id,
            'payment_method_id' => $paymentMethod->payment_method_id,
            'deskripsi'         => 'Buatkan logo burung api',
            'referensi_desain'  => $file,
        ];

        $response = $this->actingAs($user)->post(route('orders.store'), $data);

        // 1. Cek Redirect ke halaman Invoice
        $response->assertRedirect(); // Biasanya ke invoice show
        
        // 2. Cek Database Order
        $this->assertDatabaseHas('orders', [
            'user_id'        => $user->user_id,
            'status_pesanan' => 'Menunggu DP',
            'deskripsi'      => 'Buatkan logo burung api'
        ]);

        // 3. Cek Otomatis Terbuat Invoice DP
        $order = Order::where('user_id', $user->user_id)->first();
        $this->assertDatabaseHas('invoices', [
            'order_id'      => $order->order_id,
            'jenis_invoice' => 'DP',
            'status_pembayaran' => 'Belum Dibayar'
        ]);

        // 4. Cek File Referensi
        Storage::disk('public')->assertExists($order->referensi_desain);
    }

    /** * Test Approve: Customer menyetujui desain (Memicu Invoice Pelunasan) */
    public function test_customer_approve_desain_dan_invoice_pelunasan_terbuat()
    {
        $user = User::factory()->create();
        
        // Setup Order: Harus status 'Menunggu Konfirmasi Pelanggan'
        $order = Order::factory()->create([
            'user_id' => $user->user_id,
            'status_pesanan' => 'Menunggu Konfirmasi Pelanggan'
        ]);

        // Setup: Harus ada file desain yang diupload admin sebelumnya
        OrderFile::factory()->create(['order_id' => $order->order_id, 'tipe_file' => 'Preview']);

        // Eksekusi Approve
        $response = $this->actingAs($user)->post(route('orders.approve', $order->order_id));

        // 1. Cek Redirect
        $response->assertRedirect();
        
        // 2. Cek Status Order Berubah
        $this->assertEquals('Menunggu Pelunasan', $order->refresh()->status_pesanan);

        // 3. Cek Invoice Pelunasan Terbuat
        $this->assertDatabaseHas('invoices', [
            'order_id'      => $order->order_id,
            'jenis_invoice' => 'Pelunasan',
            'status_pembayaran' => 'Belum Dibayar'
        ]);
    }

    /** * Test Fail Approve: Gagal approve jika admin belum upload file */
    public function test_gagal_approve_jika_belum_ada_file_desain()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->user_id,
            'status_pesanan' => 'Menunggu Konfirmasi Pelanggan'
        ]);
        
        // Tidak ada OrderFile::create() di sini

        $response = $this->actingAs($user)->post(route('orders.approve', $order->order_id));
        
        $response->assertSessionHas('error'); // Harus error
    }

    /** * Test Download: Customer download file hasil */
    public function test_customer_bisa_download_file_miliknya()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->user_id]);
        
        // Buat file dummy fisik
        $dummyContent = 'isi file gambar';
        $path = 'order-files/hasil.jpg';
        Storage::disk('public')->put($path, $dummyContent);

        // Record database
        $fileRecord = OrderFile::factory()->create([
            'order_id'  => $order->order_id,
            'path_file' => $path
        ]);

        $response = $this->actingAs($user)->get(route('orders.file.download', $fileRecord->file_id));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=Desain-Preview-'.$order->order_id.'.jpg');
    }

    /** * Test AJAX Status HTML */
    public function test_get_status_html_return_partial_view()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->user_id]);

        $response = $this->actingAs($user)->get(route('orders.status-html', $order->order_id));

        $response->assertStatus(200);
        // Pastikan view yang dirender mengandung string tertentu yang ada di badge status
        // (Misal nama statusnya)
        $response->assertSee($order->status_pesanan); 
    }
}