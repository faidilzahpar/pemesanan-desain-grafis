<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Portfolio; // Tambahkan ini di atas
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Menambahkan data contoh portofolio
        Portfolio::create([
    'judul' => 'Desain Gamer Pro',
    'kategori' => 'Gaming',
    'gambar' => 'desain-gamer.jpg', // Harus sama persis dengan yang di folder
    'deskripsi' => 'Poster desain untuk komunitas gamer.'
]);

    }
}