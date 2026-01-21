<?php

namespace Tests\Feature;

use App\Models\DesignType;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Tests\TestCase;

class InvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    /** * Test: User tidak bisa melihat invoice orang lain (403) */
    public function test_user_tidak_bisa_melihat_invoice_orang_lain()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        
        $orderA = Order::factory()->create(['user_id' => $userA->user_id]);
        $invoiceA = Invoice::factory()->create(['order_id' => $orderA->order_id]);

        // Login sebagai User B, mencoba akses Invoice milik User A
        $response = $this->actingAs($userB)->get(route('invoices.show', $invoiceA->invoice_id));

        $response->assertStatus(403);
    }

    /** * Test: Admin bisa melihat semua invoice */
    public function test_admin_bisa_melihat_invoice_siapapun()
    {
        $admin = User::factory()->create(['is_admin' => 1]);
        $user = User::factory()->create();
        
        $order = Order::factory()->create(['user_id' => $user->user_id]);
        $invoice = Invoice::factory()->create(['order_id' => $order->order_id]);

        $response = $this->actingAs($admin)->get(route('invoices.show', $invoice->invoice_id));

        $response->assertStatus(200);
        $response->assertViewHas('invoice');
    }

    /** * Test: Auto Expire (DP lebih dari 24 jam) */
    public function test_handle_auto_expire_setelah_24_jam()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->user_id, 'status_pesanan' => 'Menunggu DP']);
        
        // Buat invoice DP yang dibuat 25 jam yang lalu
        $invoice = Invoice::factory()->create([
            'order_id' => $order->order_id,
            'jenis_invoice' => 'DP',
            'status_pembayaran' => 'Belum Dibayar',
            'created_at' => Carbon::now()->subHours(25)
        ]);

        // Akses halaman show (ini akan memicu handleAutoExpire)
        $this->actingAs($user)->get(route('invoices.show', $invoice->invoice_id));

        // Cek status invoice & order
        $this->assertEquals('Pembayaran Expired', $invoice->refresh()->status_pembayaran);
        $this->assertEquals('Dibatalkan', $order->refresh()->status_pesanan);
    }

    /** * Test: Customer upload bukti pembayaran */
    public function test_customer_berhasil_upload_bukti_pembayaran()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->user_id, 'status_pesanan' => 'Menunggu DP']);
        $invoice = Invoice::factory()->create(['order_id' => $order->order_id, 'jenis_invoice' => 'DP']);

        $file = UploadedFile::fake()->image('transfer.jpg');

        $response = $this->actingAs($user)->post(route('invoices.upload', $invoice->invoice_id), [
            'bukti_pembayaran' => $file
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('invoices', [
            'invoice_id' => $invoice->invoice_id,
            'status_pembayaran' => 'Menunggu Verifikasi'
        ]);
        
        // Pastikan file ada di storage
        $invoice->refresh();
        Storage::disk('public')->assertExists($invoice->bukti_path);
    }

    /** * Test: Admin verifikasi DP (Status Order berubah & Deadline muncul) */
    public function test_admin_verifikasi_pembayaran_dp()
    {
        $admin = User::factory()->create(['is_admin' => 1]);
        $type = DesignType::factory()->create(['durasi' => 3]);
        $order = Order::factory()->create(['design_type_id' => $type->design_type_id, 'status_pesanan' => 'Menunggu DP']);
        
        $invoice = Invoice::factory()->create([
            'order_id' => $order->order_id,
            'jenis_invoice' => 'DP',
            'status_pembayaran' => 'Menunggu Verifikasi'
        ]);

        $response = $this->actingAs($admin)->post(route('invoices.verify', $invoice->invoice_id));

        $response->assertRedirect(route('invoices.show', $invoice->invoice_id));
        
        // Cek Invoice
        $this->assertEquals('Pembayaran Diterima', $invoice->refresh()->status_pembayaran);
        
        // Cek Order: Harus "Sedang Dikerjakan" & Deadline bertambah 3 hari
        $order->refresh();
        $this->assertEquals('Sedang Dikerjakan', $order->status_pesanan);
        $this->assertNotNull($order->deadline);
    }

    /** * Test: Admin Reject Pembayaran */
    public function test_admin_reject_pembayaran()
    {
        $admin = User::factory()->create(['is_admin' => 1]);
        $order = Order::factory()->create(['status_pesanan' => 'Menunggu DP']);
        $invoice = Invoice::factory()->create([
            'order_id' => $order->order_id,
            'jenis_invoice' => 'DP',
            'status_pembayaran' => 'Menunggu Verifikasi'
        ]);

        $response = $this->actingAs($admin)->post(route('invoices.reject', $invoice->invoice_id));

        $this->assertEquals('Pembayaran Ditolak', $invoice->refresh()->status_pembayaran);
        // Status order harus balik lagi ke awal
        $this->assertEquals('Menunggu DP', $order->refresh()->status_pesanan);
    }

    /** * Test: Melihat file bukti pembayaran (Admin) */
    public function test_admin_bisa_melihat_file_bukti_bayar()
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => 1]);
        
        $path = UploadedFile::fake()->image('bukti.jpg')->store('bukti-pembayaran', 'public');
        $invoice = Invoice::factory()->create(['bukti_path' => $path]);

        $response = $this->actingAs($admin)->get(route('invoices.file', $invoice->invoice_id));

        $response->assertStatus(200);
    }
}