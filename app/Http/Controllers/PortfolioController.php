<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    /**
     * Menampilkan daftar portofolio ke pengunjung
     */
    public function index()
    {
        // Mengambil semua data dari tabel portfolios
        $portofolios = Portfolio::all(); 
        
        // Mengirim data ke file view resources/views/portfolio.blade.php
        return view('portfolio', compact('portofolios'));
    }

    /**
     * Menampilkan form untuk menambah portofolio (di Dashboard Admin)
     */
    public function create()
    {
        return view('admin.portfolio.create');
    }

    /**
     * Menyimpan data portofolio baru ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'kategori' => 'required',
            'gambar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Upload Gambar
        $imagePath = $request->file('gambar')->store('portfolios', 'public');

        // Simpan ke Database
        Portfolio::create([
            'judul' => $request->judul,
            'kategori' => $request->kategori,
            'gambar' => $imagePath,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('portfolio')->with('success', 'Portofolio berhasil ditambah!');
    }
}