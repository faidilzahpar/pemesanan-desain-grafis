<?php

namespace Tests\Feature;

use App\Models\DesignType;
use App\Models\Portfolio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPageTest extends TestCase
{
    use RefreshDatabase;

    /** * Test Halaman Home: Menampilkan Design Type yang Aktif saja */
    public function test_halaman_home_menampilkan_daftar_harga_aktif()
    {
        // 1. Setup: Buat 2 yang aktif, 1 yang non-aktif
        DesignType::factory()->create([
            'nama_jenis' => 'Paket Hemat',
            'is_active' => 1,
            'created_at' => now()->subDays(2)
        ]);
        DesignType::factory()->create([
            'nama_jenis' => 'Paket Sultan',
            'is_active' => 1,
            'created_at' => now()->subDay()
        ]);
        DesignType::factory()->create([
            'nama_jenis' => 'Paket Rahasia',
            'is_active' => 0 // Ini tidak boleh muncul
        ]);

        // 2. Eksekusi
        $response = $this->get(route('home'));

        // 3. Assert
        $response->assertStatus(200);
        $response->assertViewIs('home');
        $response->assertSee('Paket Hemat');
        $response->assertSee('Paket Sultan');
        $response->assertDontSee('Paket Rahasia');
        
        // Memastikan urutan Ascending (Hemat dulu baru Sultan)
        $response->assertSeeInOrder(['Paket Hemat', 'Paket Sultan']);
    }

    /** * Test Halaman Portfolio: Menampilkan galeri karya terbaru */
    public function test_halaman_portfolio_menampilkan_semua_karya()
    {
        // 1. Setup: Buat beberapa portfolio
        Portfolio::factory()->create(['judul' => 'Desain Logo A']);
        Portfolio::factory()->create(['judul' => 'Desain Logo B']);

        // 2. Eksekusi
        $response = $this->get(route('portfolio'));

        // 3. Assert
        $response->assertStatus(200);
        $response->assertViewIs('portfolio');
        $response->assertViewHas('portfolios');
        $response->assertSee('Desain Logo A');
        $response->assertSee('Desain Logo B');
    }
}