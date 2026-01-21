<?php

namespace Tests\Feature\Admin;

use App\Models\DesignType;
use App\Models\Order;
use App\Models\OrderFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin()
    {
        return User::factory()->admin()->create();
    }

    /** * TEST 1: INDEX & SEARCH
     * Menguji tampilan awal dan fitur pencarian yang kompleks
     */
    public function test_index_search_functionality()
    {
        $admin = $this->createAdmin();

        // 1. Biarkan Factory membuat User & Design otomatis (Aman dari error ID null)
        $targetOrder = Order::factory()->create([
            'status_pesanan' => 'Revisi'
        ]);
        
        // Ambil data yang sudah ter-generate otomatis
        $targetUser = $targetOrder->user;
        $targetDesign = $targetOrder->designType;
        $realOrderId = $targetOrder->order_id; // Ambil ID asli (misal: ORD26001)

        // Dummy order sebagai pengganggu
        $dummyOrder = Order::factory()->create(['status_pesanan' => 'Sedang Dikerjakan']);

        // A. Search by Order ID
        $res = $this->actingAs($admin)->get(route('admin.orders.index', ['tableSearch' => $realOrderId]));
        $res->assertSee($realOrderId);
        $res->assertDontSee($dummyOrder->order_id);

        // B. Search by Status
        $res = $this->actingAs($admin)->get(route('admin.orders.index', ['tableSearch' => 'Revisi']));
        $res->assertSee($realOrderId);

        // C. Search by User Name (Ambil sebagian nama)
        $namePart = substr($targetUser->name, 0, 3);
        $res = $this->actingAs($admin)->get(route('admin.orders.index', ['tableSearch' => $namePart]));
        $res->assertSee($realOrderId);

        // D. Search by No HP
        $res = $this->actingAs($admin)->get(route('admin.orders.index', ['tableSearch' => $targetUser->no_hp]));
        $res->assertSee($realOrderId);

        // E. Search by Design Type
        $res = $this->actingAs($admin)->get(route('admin.orders.index', ['tableSearch' => $targetDesign->nama_jenis]));
        $res->assertSee($realOrderId);
    }

    /** * TEST 2: INDEX SORTING
     * Menguji pengurutan berdasarkan Nama User dan Jenis Desain
     */
    public function test_index_sorting_functionality()
    {
        $admin = $this->createAdmin();

        // Buat Order A -> Update nama user jadi 'Ahmad'
        $orderA = Order::factory()->create(['status_pesanan' => 'Sedang Dikerjakan']);
        $orderA->user()->update(['name' => 'Ahmad']);

        // Buat Order B -> Update nama user jadi 'Zainal'
        $orderB = Order::factory()->create(['status_pesanan' => 'Sedang Dikerjakan']);
        $orderB->user()->update(['name' => 'Zainal']);

        // 1. Sort User Name ASC (Ahmad dulu)
        $res = $this->actingAs($admin)->get(route('admin.orders.index', [
            'tableSortColumn' => 'user_name',
            'tableSortDirection' => 'asc'
        ]));
        $dataAsc = $res->viewData('orders');
        $this->assertEquals($orderA->order_id, $dataAsc->first()->order_id);

        // 2. Sort User Name DESC (Zainal dulu)
        $res = $this->actingAs($admin)->get(route('admin.orders.index', [
            'tableSortColumn' => 'user_name',
            'tableSortDirection' => 'desc'
        ]));
        $dataDesc = $res->viewData('orders');
        $this->assertEquals($orderB->order_id, $dataDesc->first()->order_id);

        // 3. Sort Design Type
        // Kita update nama jenis desainnya biar pasti
        $orderA->designType()->update(['nama_jenis' => 'Abstrak']);
        $orderB->designType()->update(['nama_jenis' => 'Zebra']);

        $res = $this->actingAs($admin)->get(route('admin.orders.index', [
            'tableSortColumn' => 'design_type',
            'tableSortDirection' => 'asc'
        ]));
        $this->assertEquals($orderA->order_id, $res->viewData('orders')->first()->order_id);
    }

    /** * TEST 3: HISTORY PAGE (Search & Sort)
     * Menguji halaman riwayat yang sebelumnya belum tersentuh tes
     */
    public function test_history_page_logic()
    {
        $admin = $this->createAdmin();

        // Setup Data Status 'Selesai'
        $orderMahal = Order::factory()->create(['status_pesanan' => 'Selesai']);
        $orderMahal->designType()->update(['harga' => 900000]); // Mahal

        $orderMurah = Order::factory()->create(['status_pesanan' => 'Selesai']);
        $orderMurah->designType()->update(['harga' => 50000]); // Murah

        // A. Test Search di History
        $res = $this->actingAs($admin)->get(route('admin.orders.history', ['tableSearch' => $orderMahal->order_id]));
        $res->assertSee($orderMahal->order_id);
        $res->assertDontSee($orderMurah->order_id);

        // B. Test Sort by Total Harga (DESC) -> Mahal dulu
        $res = $this->actingAs($admin)->get(route('admin.orders.history', [
            'tableSortColumn' => 'total',
            'tableSortDirection' => 'desc'
        ]));
        $data = $res->viewData('orders');
        $this->assertEquals($orderMahal->order_id, $data->first()->order_id);

        // C. Test Sort by Total Harga (ASC) -> Murah dulu
        $res = $this->actingAs($admin)->get(route('admin.orders.history', [
            'tableSortColumn' => 'total',
            'tableSortDirection' => 'asc'
        ]));
        $data = $res->viewData('orders');
        $this->assertEquals($orderMurah->order_id, $data->first()->order_id);
    }

    /** * TEST 4: UPLOAD FILE & LOGIC (Preview, Watermark, Revisi, Final)
     */
    public function test_upload_logic_comprehensive()
    {
        $admin = $this->createAdmin();
        Storage::fake('public');

        // A. Upload Preview (Otomatis Watermark)
        $order = Order::factory()->create(['status_pesanan' => 'Sedang Dikerjakan']);
        $file = UploadedFile::fake()->image('preview.jpg');

        $this->actingAs($admin)->post(route('admin.orders.upload', $order->order_id), ['file' => $file]);
        
        $this->assertDatabaseHas('order_files', [
            'order_id' => $order->order_id,
            'tipe_file' => 'Preview'
        ]);

        // B. Upload Revisi (Karena sudah ada preview)
        $fileRevisi = UploadedFile::fake()->image('revisi.jpg');
        $this->actingAs($admin)->post(route('admin.orders.upload', $order->order_id), ['file' => $fileRevisi]);
        
        $this->assertDatabaseHas('order_files', [
            'order_id' => $order->order_id,
            'tipe_file' => 'Revisi'
        ]);

        // C. Upload Final (Pakai file ZIP/PDF biar masuk ke blok else non-image watermark)
        // Set status biar maksa jadi final
        $order->update(['status_pesanan' => 'Menunggu File Final']);
        $fileFinal = UploadedFile::fake()->create('final.zip');

        $this->actingAs($admin)->post(route('admin.orders.upload', $order->order_id), ['file' => $fileFinal]);
        
        $this->assertDatabaseHas('order_files', [
            'order_id' => $order->order_id,
            'tipe_file' => 'Final'
        ]);
        $this->assertDatabaseHas('orders', [
            'order_id' => $order->order_id,
            'status_pesanan' => 'Selesai'
        ]);
    }

    public function test_show_page()
    {
        $admin = $this->createAdmin();
        $order = Order::factory()->create();
        $res = $this->actingAs($admin)->get(route('admin.orders.show', $order->order_id));
        $res->assertStatus(200);
    }
    public function test_non_admin_cannot_access_admin_routes()
    {
        // 1. Buat User Biasa (Bukan Admin)
        $regularUser = User::factory()->create(['is_admin' => 0]);

        // 2. Coba paksa masuk ke halaman Admin
        $response = $this->actingAs($regularUser)->get(route('admin.orders.index'));

        // 3. Pastikan DITOLAK (Bisa 403 Forbidden atau 302 Redirect, tergantung middleware Anda)
        // Biasanya middleware admin melakukan abort(403) atau redirect ke home.
        // Kita cek statusnya bukan 200 OK.
        $this->assertNotEquals(200, $response->status());
        
        $response->assertStatus(404);
    }
}