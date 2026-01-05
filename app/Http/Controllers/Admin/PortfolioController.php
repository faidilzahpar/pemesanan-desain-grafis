<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PortfolioController extends Controller
{
    // Halaman daftar portofolio di Admin Panel
    public function index()
    {
        $portfolios = Portfolio::latest()->get();
        return view('admin.portfolio.index', compact('portfolios'));
    }

    // Form untuk menambah desain baru
    public function create()
    {
        return view('admin.portfolio.create');
    }

    // Logika simpan data ke database
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|max:255',
            'kategori' => 'required',
            'gambar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'deskripsi' => 'nullable',
        ]);

        // Proses upload file ke storage/app/public/portfolios
        $namaGambar = $request->file('gambar')->hashName();
        $request->file('gambar')->storeAs('public/portfolio', $namaGambar);

        Portfolio::create([
            'judul' => $request->judul,
            'kategori' => $request->kategori,
            'gambar' => $namaGambar,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('admin.portfolio.index')->with('success', 'Portofolio Berhasil Ditambahkan!');
    }

    public function destroy(Portfolio $portfolio)
    {
        // Hapus file gambar dari folder storage agar tidak menumpuk
        Storage::delete('public/portfolio/' . $portfolio->gambar);

        // Hapus data dari database
        $portfolio->delete();

        return redirect()->route('admin.portfolio.index')->with('success', 'Karya berhasil dihapus!');
    }
}