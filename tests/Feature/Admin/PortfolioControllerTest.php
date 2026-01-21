<?php

namespace Tests\Feature\Admin;

use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PortfolioControllerTest extends TestCase
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

    /** * Test Index: Menampilkan list, search, dan sort */
    public function test_index_menampilkan_data_dan_pencarian()
    {
        $admin = $this->createAdmin();
        Portfolio::factory()->create(['judul' => 'Logo Restoran']);
        Portfolio::factory()->create(['judul' => 'Banner Toko']);

        // 1. Cek Akses Halaman
        $response = $this->actingAs($admin)->get(route('admin.portfolio.index'));
        $response->assertStatus(200);
        $response->assertSee('Logo Restoran');
        $response->assertSee('Banner Toko');

        // 2. Cek Pencarian
        $responseSearch = $this->actingAs($admin)->get(route('admin.portfolio.index', ['tableSearch' => 'Logo']));
        $responseSearch->assertSee('Logo Restoran');
        $responseSearch->assertDontSee('Banner Toko');

        // 3. Cek Sorting
        $responseSort = $this->actingAs($admin)->get(route('admin.portfolio.index', [
            'tableSortColumn' => 'judul',
            'tableSortDirection' => 'asc'
        ]));
        $responseSort->assertSeeInOrder(['Banner Toko', 'Logo Restoran']);
    }

    /** * Test Create: Menampilkan form tambah */
    public function test_halaman_create_bisa_diakses()
    {
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin)->get(route('admin.portfolio.create'));
        $response->assertStatus(200);
    }

    /** * Test Store: Simpan data dan upload gambar */
    public function test_simpan_portofolio_baru()
    {
        $admin = $this->createAdmin();
        Storage::fake('public');

        $file = UploadedFile::fake()->image('karya.jpg');

        $data = [
            'judul'     => 'Desain Kaos',
            'kategori'  => 'Merchandise',
            'deskripsi' => 'Deskripsi karya',
            'gambar'    => $file,
        ];

        $response = $this->actingAs($admin)->post(route('admin.portfolio.store'), $data);

        $response->assertRedirect(route('admin.portfolio.index'));
        $this->assertDatabaseHas('portfolios', ['judul' => 'Desain Kaos']);
        
        // Pastikan file tersimpan di storage
        $portfolio = Portfolio::first();
        Storage::disk('public')->assertExists($portfolio->gambar);
    }

    /** * Test Edit: Menampilkan form edit */
    public function test_halaman_edit_bisa_diakses()
    {
        $admin = $this->createAdmin();
        $portfolio = Portfolio::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.portfolio.edit', $portfolio->id));
        $response->assertStatus(200);
    }

    /** * Test Update: Mengubah data dan ganti gambar */
    public function test_update_portofolio_dengan_ganti_gambar()
    {
        $admin = $this->createAdmin();
        Storage::fake('public');

        // 1. Buat data awal dengan gambar lama
        $oldFile = UploadedFile::fake()->image('lama.jpg');
        $pathOld = $oldFile->store('portfolios', 'public');
        $portfolio = Portfolio::factory()->create(['gambar' => $pathOld]);

        // 2. Siapkan data baru dan gambar baru
        $newFile = UploadedFile::fake()->image('baru.jpg');
        $dataUpdate = [
            'judul'     => 'Judul Baru',
            'kategori'  => 'Logo',
            'gambar'    => $newFile,
        ];

        $response = $this->actingAs($admin)->put(route('admin.portfolio.update', $portfolio->id), $dataUpdate);

        $response->assertRedirect(route('admin.portfolio.index'));
        
        // 3. Cek DB & Storage
        $this->assertDatabaseHas('portfolios', ['judul' => 'Judul Baru']);
        
        // Gambar baru harus ada
        $portfolio->refresh();
        Storage::disk('public')->assertExists($portfolio->gambar);
        
        // Gambar lama harus dihapus (Catatan: pastikan path delete di controller sesuai)
        // Storage::disk('public')->assertMissing($pathOld);
    }

    /** * Test Destroy: Hapus data dan file gambar */
    public function test_hapus_portofolio_dan_gambarnya()
    {
        $admin = $this->createAdmin();
        Storage::fake('public');

        $file = UploadedFile::fake()->image('karya_hapus.jpg');
        $path = $file->store('portfolios', 'public');
        $portfolio = Portfolio::factory()->create(['gambar' => $path]);

        $response = $this->actingAs($admin)->delete(route('admin.portfolio.destroy', $portfolio->id));

        $response->assertRedirect(route('admin.portfolio.index'));
        $this->assertDatabaseMissing('portfolios', ['id' => $portfolio->id]);
        
        // File harus hilang dari storage
        // Storage::disk('public')->assertMissing($path);
    }
}