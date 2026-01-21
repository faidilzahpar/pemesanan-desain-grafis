<?php

namespace Tests\Feature\Admin;

use App\Models\DesignType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DesignTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper untuk membuat Admin
     */
    private function createAdmin()
    {
        return User::factory()->create([
            'user_id'   => (string) Str::uuid(),
            'no_hp'     => '0812' . rand(1000, 9999),
            'is_admin'  => 1,
        ]);
    }

    /** * Test halaman index dan pencarian */
    public function test_index_menampilkan_data_dan_pencarian()
    {
        $admin = $this->createAdmin();
        DesignType::factory()->create(['nama_jenis' => 'Logo Special']);
        DesignType::factory()->create(['nama_jenis' => 'Kartu Nama']);

        // Test akses biasa
        $response = $this->actingAs($admin)->get(route('design-types.index'));
        $response->assertStatus(200);
        $response->assertSee('Logo Special');
        $response->assertSee('Kartu Nama');

        // Test Search (mencari Logo)
        $responseSearch = $this->actingAs($admin)->get(route('design-types.index', ['tableSearch' => 'Logo']));
        $responseSearch->assertSee('Logo Special');
        $responseSearch->assertDontSee('Kartu Nama');
    }

    /** * Test sorting di halaman index */
    public function test_index_sorting_berjalan_lancar()
    {
        $admin = $this->createAdmin();
        DesignType::factory()->create(['nama_jenis' => 'A Berawal A', 'harga' => 1000]);
        DesignType::factory()->create(['nama_jenis' => 'Z Berawal Z', 'harga' => 5000]);

        // Sort harga desc (paling mahal di atas)
        $response = $this->actingAs($admin)->get(route('design-types.index', [
            'tableSortColumn' => 'harga',
            'tableSortDirection' => 'desc'
        ]));
        
        $response->assertSeeInOrder(['Z Berawal Z', 'A Berawal A']);
    }

    /** * Test halaman create */
    public function test_halaman_create_bisa_diakses()
    {
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin)->get(route('design-types.create'));
        $response->assertStatus(200);
    }

    /** * Test simpan data baru (Store) */
    public function test_simpan_jenis_desain_baru()
    {
        $admin = $this->createAdmin();
        $data = [
            'nama_jenis' => 'Banner Toko',
            'deskripsi' => 'Deskripsi banner',
            'durasi' => 3,
            'harga' => 150000,
        ];

        $response = $this->actingAs($admin)->post(route('design-types.store'), $data);

        $response->assertRedirect(route('design-types.index'));
        $this->assertDatabaseHas('design_types', ['nama_jenis' => 'Banner Toko']);
    }

    /** * Test halaman edit */
    public function test_halaman_edit_bisa_diakses()
    {
        $admin = $this->createAdmin();
        $type = DesignType::factory()->create();

        $response = $this->actingAs($admin)->get(route('design-types.edit', $type->design_type_id));
        $response->assertStatus(200);
        $response->assertSee($type->nama_jenis);
    }

    /** * Test update data (termasuk logika checkbox is_active) */
    public function test_update_jenis_desain()
    {
        $admin = $this->createAdmin();
        $type = DesignType::factory()->create(['is_active' => 1]);

        // Data baru yang akan dikirim
        $dataUpdate = [
            'nama_jenis' => 'Logo Updated',
            'deskripsi'  => 'Deskripsi baru',
            'durasi'     => 5,
            'harga'      => 200000,
            'is_active'  => 'on', // Simulasi checkbox HTML yang dicentang
        ];

        // Eksekusi PUT
        $response = $this->actingAs($admin)
                         ->put(route('design-types.update', $type->design_type_id), $dataUpdate);

        // Pastikan redirect ke index
        $response->assertRedirect(route('design-types.index'));
        
        // Cek apakah data di database sudah berubah
        $this->assertDatabaseHas('design_types', [
            'design_type_id' => $type->design_type_id,
            'nama_jenis'     => 'Logo Updated',
            'is_active'      => 1
        ]);
    }

    /** * Test hapus data (Destroy) */
    public function test_hapus_jenis_desain()
    {
        $admin = $this->createAdmin();
        $type = DesignType::factory()->create();

        $response = $this->actingAs($admin)->delete(route('design-types.destroy', $type->design_type_id));

        $response->assertRedirect(route('design-types.index'));
        $this->assertDatabaseMissing('design_types', ['design_type_id' => $type->design_type_id]);
    }

    /** * Test fitur toggle status (AJAX/JSON) */
    public function test_toggle_status_jenis_desain()
    {
        $admin = $this->createAdmin();
        $type = DesignType::factory()->create(['is_active' => 1]);

        // Panggil route toggle (pastikan namanya sesuai di web.php: design-types.toggle)
        $response = $this->actingAs($admin)->patch(route('design-types.toggle', $type->design_type_id));

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'is_active' => false]);
        
        $this->assertEquals(0, $type->refresh()->is_active);
    }
}