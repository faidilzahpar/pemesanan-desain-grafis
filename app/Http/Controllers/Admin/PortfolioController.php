<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PortfolioController extends Controller
{
    // Halaman daftar portofolio di Admin Panel
    public function index(Request $request)
    {
        $query = Portfolio::query();

        // 1. LOGIKA SEARCH
        // (Search meliputi: Judul)
        if ($request->filled('tableSearch')) {
            $search = $request->tableSearch;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%");
            });
        }

        // 2. LOGIKA SORTING
        if ($request->filled('tableSortColumn') && $request->filled('tableSortDirection')) {
            $column = $request->tableSortColumn;
            $direction = $request->tableSortDirection === 'desc' ? 'desc' : 'asc';

            // Validasi kolom agar aman
            // Di view tadi kita hanya pasang sort pada 'judul'
            $validColumns = ['judul'];

            if (in_array($column, $validColumns)) {
                $query->orderBy($column, $direction);
            }
        } else {
            // Default sort: Terbaru
            $query->orderBy('created_at', 'desc');
        }

        // Gunakan paginate, bukan get() agar sesuai dengan view yang pakai links()
        $portfolios = $query->paginate(10)->withQueryString();

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

        $namaGambar = $request->file('gambar')->store('portfolios', 'public');

        Portfolio::create([ 
            'judul' => $request->judul,
            'kategori' => $request->kategori,
            'gambar' => $namaGambar,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('admin.portfolio.index')->with('success', 'Portofolio Berhasil Ditambahkan!');
    }

    public function edit(Portfolio $portfolio)
    {
        return view('admin.portfolio.edit', compact('portfolio'));
    }

    public function update(Request $request, Portfolio $portfolio)
    {
        $request->validate([
            'judul'     => 'required|max:255',
            'kategori'  => 'required',
            'deskripsi' => 'nullable',
            'gambar'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. Persiapan data yang akan diupdate
        $data = [
            'judul'     => $request->judul,
            'kategori'  => $request->kategori,
            'deskripsi' => $request->deskripsi,
        ];

        // 3. Cek apakah ada file gambar baru yang diupload
        if ($request->hasFile('gambar')) {
            
            // Hapus gambar lama jika ada
            if ($portfolio->gambar) {
                Storage::delete('public/portfolio/' . $portfolio->gambar);
            }

            // Upload gambar baru
            $namaGambar = $request->file('gambar')->store('portfolios', 'public');

            // Masukkan nama gambar baru ke array data
            $data['gambar'] = $namaGambar;
        }

        // 4. Update Database
        $portfolio->update($data);

        return redirect()->route('admin.portfolio.index')->with('success', 'Portofolio berhasil diperbarui!');
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