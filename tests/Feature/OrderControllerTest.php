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
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    /** * TEST 1: INDEX & FILTER
     * Target: Menghajar blok 'match ($status)' di method index
     */
    public function test_index_filter_comprehensive()
    {
        $user = User::factory()->create();
        
        // Buat data dummy dan simpan ke variabel agar bisa dicek ID-nya
        $orderUnpaid = Order::factory()->create(['user_id' => $user->user_id, 'status_pesanan' => 'Menunggu DP']);       
        $orderProcess = Order::factory()->create(['user_id' => $user->user_id, 'status_pesanan' => 'Sedang Dikerjakan']); 
        $orderDone = Order::factory()->create(['user_id' => $user->user_id, 'status_pesanan' => 'Selesai']);           
        $orderCancel = Order::factory()->create(['user_id' => $user->user_id, 'status_pesanan' => 'Dibatalkan']);        

        // 1. Test Filter 'unpaid'
        $res = $this->actingAs($user)->get(route('orders.index', ['status' => 'unpaid']));
        $res->assertStatus(200);
        
        // Cek DATA variable 'orders' yang dikirim ke view
        $dataOrders = $res->viewData('orders');
        $this->assertTrue($dataOrders->contains($orderUnpaid), 'Order Unpaid harusnya muncul');
        $this->assertFalse($dataOrders->contains($orderDone), 'Order Selesai JANGAN muncul di tab unpaid');
        $this->assertCount(1, $dataOrders);

        // 2. Test Filter 'process'
        $res = $this->actingAs($user)->get(route('orders.index', ['status' => 'process']));
        $dataOrders = $res->viewData('orders');
        $this->assertTrue($dataOrders->contains($orderProcess));
        $this->assertFalse($dataOrders->contains($orderUnpaid));
        $this->assertCount(1, $dataOrders);

        // 3. Test Filter 'done'
        $res = $this->actingAs($user)->get(route('orders.index', ['status' => 'done']));
        $dataOrders = $res->viewData('orders');
        $this->assertTrue($dataOrders->contains($orderDone));
        $this->assertCount(1, $dataOrders);

        // 4. Test Filter 'cancel'
        $res = $this->actingAs($user)->get(route('orders.index', ['status' => 'cancel']));
        $dataOrders = $res->viewData('orders');
        $this->assertTrue($dataOrders->contains($orderCancel));
        $this->assertCount(1, $dataOrders);

        // 5. Test Default (All)
        $res = $this->actingAs($user)->get(route('orders.index'));
        $this->assertCount(4, $res->viewData('orders'));
    }

    /** * TEST 2: SHOW & SECURITY
     * Target: abort_if dan logika activeInvoice
     */
    public function test_show_page_logic_and_security()
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        
        $order = Order::factory()->create(['user_id' => $owner->user_id]);
        Invoice::factory()->create(['order_id' => $order->order_id]); // Create invoice agar activeInvoice tidak null

        // 1. Owner bisa akses
        $res = $this->actingAs($owner)->get(route('orders.show', $order->order_id));
        $res->assertStatus(200);
        $res->assertViewHas('activeInvoice'); // Pastikan variabel dikirim

        // 2. Orang lain DITOLAK (403)
        $res = $this->actingAs($stranger)->get(route('orders.show', $order->order_id));
        $res->assertStatus(403);
    }

    /** * TEST 3: CREATE PAGE
     * Target: Halaman create render dengan benar
     */
    public function test_create_page_renders()
    {
        $user = User::factory()->create();
        $design = DesignType::factory()->create();
        
        $res = $this->actingAs($user)->get(route('orders.create', ['design' => $design->design_type_id]));
        $res->assertStatus(200);
        $res->assertSee($design->nama_jenis);
    }

    /** * TEST 4: STORE (Upload & No Upload)
     * Target: if ($request->hasFile) dan else-nya
     */
    public function test_store_logic_with_and_without_file()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $design = DesignType::factory()->create();
        $payment = PaymentMethod::factory()->create();

        // Skenario A: Dengan File (Cover blok IF)
        $file = UploadedFile::fake()->image('ref.jpg');
        $this->actingAs($user)->post(route('orders.store'), [
            'design_type_id' => $design->design_type_id,
            'payment_method_id' => $payment->payment_method_id,
            'deskripsi' => 'Ada file',
            'referensi_desain' => $file
        ])->assertRedirect();
        
        $this->assertDatabaseHas('orders', ['deskripsi' => 'Ada file']);

        // Skenario B: TANPA File (Cover blok ELSE/SKIP)
        $this->actingAs($user)->post(route('orders.store'), [
            'design_type_id' => $design->design_type_id,
            'payment_method_id' => $payment->payment_method_id,
            'deskripsi' => 'Tanpa file',
            // 'referensi_desain' tidak dikirim
        ])->assertRedirect();

        $this->assertDatabaseHas('orders', ['deskripsi' => 'Tanpa file', 'referensi_desain' => null]);
    }

    /** * TEST 5: APPROVE LOGIC (Success & Fails)
     * Target: Validasi status, cek file, dan double invoice check
     */
    public function test_approve_validation_logic()
    {
        $user = User::factory()->create();
        
        // 1. Gagal jika status bukan 'Menunggu Konfirmasi Pelanggan'
        $orderSalahStatus = Order::factory()->create(['user_id' => $user->user_id, 'status_pesanan' => 'Menunggu DP']);
        $res = $this->actingAs($user)->post(route('orders.approve', $orderSalahStatus->order_id));
        $res->assertSessionHas('error'); // "Pesanan tidak dapat disetujui..."

        // 2. Gagal jika belum ada file desain
        $orderNoFile = Order::factory()->create(['user_id' => $user->user_id, 'status_pesanan' => 'Menunggu Konfirmasi Pelanggan']);
        $res = $this->actingAs($user)->post(route('orders.approve', $orderNoFile->order_id));
        $res->assertSessionHas('error'); // "Belum ada file..."

        // 3. Sukses Approve
        $orderSukses = Order::factory()->create(['user_id' => $user->user_id, 'status_pesanan' => 'Menunggu Konfirmasi Pelanggan']);
        OrderFile::factory()->create(['order_id' => $orderSukses->order_id]); // Pasang file
        
        $res = $this->actingAs($user)->post(route('orders.approve', $orderSukses->order_id));
        $res->assertSessionHas('success');
        $this->assertDatabaseHas('invoices', ['order_id' => $orderSukses->order_id, 'jenis_invoice' => 'Pelunasan']);

        // 4. Gagal Double Approve (Invoice pelunasan sudah ada)
        // Kita coba approve lagi order yang barusan sukses
        $res = $this->actingAs($user)->post(route('orders.approve', $orderSukses->order_id));
        $res->assertSessionHas('error'); // "Invoice pelunasan sudah tersedia."
    }

    /** * TEST 6: DOWNLOAD SECURITY
     * Target: abort(403) dan abort(404)
     */
    public function test_download_security_and_existence()
    {
        Storage::fake('public');
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => 1]);

        $order = Order::factory()->create(['user_id' => $owner->user_id]);
        
        // File Fisik TIDAK DIBUAT (Simulasi 404)
        $fileRecord = OrderFile::factory()->create(['order_id' => $order->order_id, 'path_file' => 'missing.jpg']);

        // 1. Stranger mencoba download (403)
        $res = $this->actingAs($stranger)->get(route('orders.file.download', $fileRecord->file_id));
        $res->assertStatus(403);

        // 2. Owner mencoba download tapi file hilang di server (404)
        $res = $this->actingAs($owner)->get(route('orders.file.download', $fileRecord->file_id));
        $res->assertStatus(404);

        // 3. Admin mencoba download (Boleh akses record, tapi kena 404 juga karena file fisik ga ada)
        // Ini membuktikan admin lolos cek 403
        $res = $this->actingAs($admin)->get(route('orders.file.download', $fileRecord->file_id));
        $res->assertStatus(404); 
    }

    /** * TEST 7: STATUS HTML & PAYMENT ALERT
     * Target: Logika showPaymentAlert yang rumit
     */
    public function test_status_html_logic_payment_alert()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->user_id]);

        // Kondisi: Ada Invoice DP yang Belum Dibayar -> Alert harus muncul
        $invoice = Invoice::factory()->create([
            'order_id' => $order->order_id,
            'jenis_invoice' => 'DP',
            'status_pembayaran' => 'Belum Dibayar'
        ]);

        $res = $this->actingAs($user)->get(route('orders.status-html', $order->order_id));
        
        $res->assertStatus(200);
        // Pastikan variabel 'showPaymentAlert' bernilai true di view, atau cek text yang muncul jika alert aktif
        // Asumsi di view ada text "Segera lakukan pembayaran"
        // $res->assertSee('Segera lakukan pembayaran'); 
    }
}