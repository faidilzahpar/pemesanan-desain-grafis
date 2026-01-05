<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $excludedStatus = ['Menunggu DP', 'Selesai', 'Dibatalkan'];

        $query = Order::with('user', 'designType')
            ->whereNotIn('status_pesanan', $excludedStatus);

        // 1. LOGIKA SEARCH UPDATE
        if ($request->filled('tableSearch')) {
            $search = $request->tableSearch;
            $query->where(function($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhere('status_pesanan', 'like', "%{$search}%") // Tambah Status
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('no_hp', 'like', "%{$search}%");
                  })
                  ->orWhereHas('designType', function($d) use ($search) { // Tambah Jenis Desain
                      $d->where('nama_jenis', 'like', "%{$search}%");
                  });
            });
        }

        // 2. LOGIKA SORTING
        if ($request->filled('tableSortColumn') && $request->filled('tableSortDirection')) {
            $column = $request->tableSortColumn;
            $direction = $request->tableSortDirection === 'desc' ? 'desc' : 'asc';

            if ($column === 'user_name') {
                // Sort Relasi User
                $query->join('users', 'orders.user_id', '=', 'users.id')
                      ->orderBy('users.name', $direction)
                      ->select('orders.*');
            } 
            elseif ($column === 'design_type') { 
                $query->join('design_types', 'orders.design_type_id', '=', 'design_types.design_type_id')
                      ->orderBy('design_types.nama_jenis', $direction)
                      ->select('orders.*');
            } 
            else {
                // Sort Kolom Biasa
                $query->orderBy($column, $direction);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function history(Request $request)
    {
        // Filter hanya status Selesai & Dibatalkan
        $query = Order::with('user', 'designType')
            ->whereIn('status_pesanan', ['Selesai', 'Dibatalkan']);

        // 1. LOGIKA SEARCH
        if ($request->filled('tableSearch')) {
            $search = $request->tableSearch;
            $query->where(function($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                ->orWhere('status_pesanan', 'like', "%{$search}%")
                ->orWhereHas('user', function($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%")
                        ->orWhere('no_hp', 'like', "%{$search}%");
                })
                ->orWhereHas('designType', function($d) use ($search) {
                    $d->where('nama_jenis', 'like', "%{$search}%");
                });
            });
        }

        // 2. LOGIKA SORTING
        if ($request->filled('tableSortColumn') && $request->filled('tableSortDirection')) {
            $column = $request->tableSortColumn;
            $direction = $request->tableSortDirection === 'desc' ? 'desc' : 'asc';

            if ($column === 'user_name') {
                // Sort Relasi User
                $query->join('users', 'orders.user_id', '=', 'users.user_id')
                    ->orderBy('users.name', $direction)
                    ->select('orders.*');
            } 
            elseif ($column === 'design_type') { 
                // Sort Relasi Jenis Desain
                $query->join('design_types', 'orders.design_type_id', '=', 'design_types.design_type_id')
                    ->orderBy('design_types.nama_jenis', $direction)
                    ->select('orders.*');
            }
            elseif ($column === 'total') {
                // Sort Relasi Harga (Total)
                $query->join('design_types', 'orders.design_type_id', '=', 'design_types.design_type_id')
                    ->orderBy('design_types.harga', $direction)
                    ->select('orders.*');
            }
            else {
                // Sort Kolom Biasa (order_id, updated_at/tgl_selesai, status_pesanan)
                $query->orderBy($column, $direction);
            }
        } else {
            // Default sort: Tgl Selesai terbaru
            $query->orderBy('updated_at', 'desc');
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('admin.orders.history', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('user', 'designType', 'orderFiles');

        return view('admin.orders.show', compact('order'));
    }

    public function uploadFile(Request $request, Order $order)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $uploadedFile = $request->file('file');

        $totalFiles  = $order->orderFiles()->count();
        $revisiCount = $order->orderFiles()
            ->where('tipe_file', 'Revisi')
            ->count();
        
        // 1. Cek STATUS terlebih dahulu. Jika statusnya 'Menunggu File Final', 
        // maka file yang diupload PASTI adalah Final, tidak peduli jumlah revisinya.
        if ($order->status_pesanan === 'Menunggu File Final') {
            $tipe = 'Final';
        } 
        // 2. Jika belum ada file sama sekali -> Preview
        elseif ($totalFiles === 0) {
            $tipe = 'Preview';
        } 
        // 3. Jika revisi masih kurang dari 3 -> Revisi
        elseif ($revisiCount < 3) {
            $tipe = 'Revisi';
        } 
        // 4. Fallback jika slot revisi habis
        else {
            $tipe = 'Final';
        }

        // --- LOGIKA WATERMARK ---
        
        // Cek apakah file adalah gambar
        $isImage = str_starts_with($uploadedFile->getMimeType(), 'image/');
        
        // Path penyimpanan (tanpa nama file dulu)
        $storagePath = "order-files/{$order->order_id}";
        
        // Nama file random hash
        $filename = $uploadedFile->hashName();
        $fullPath = $storagePath . '/' . $filename;

        // JIKA BUKAN FINAL & ADALAH GAMBAR => KASIH WATERMARK
        if ($tipe !== 'Final' && $isImage) {
            
            // 1. Setup Image Manager
            $manager = new ImageManager(new Driver());

            // 2. Baca file yang diupload
            $image = $manager->read($uploadedFile);

            // 3. Baca watermark (Pastikan file public/watermark.png ADA)
            if (file_exists(public_path('watermark.png'))) {


                // Atau baca langsung
                $watermark = public_path('watermark.png');

                // 4. Tempel Watermark (Posisi: Center, Opacity: 50%)
                // Param: (source, position, x, y, opacity)
                $image->place($watermark, 'center', 0, 0, 50);
            }

            // 5. Simpan (Encode) kembali ke format aslinya
            $encoded = $image->toPng(); // atau toJpeg() sesuai kebutuhan, toPng() aman buat transparansi

            // 6. Simpan manual ke Storage Public
            Storage::disk('public')->put($fullPath, $encoded);
            
            // Set path untuk database
            $path = $fullPath;

        } else {
            // JIKA FINAL ATAU BUKAN GAMBAR (PDF/ZIP) => SIMPAN BIASA
            $path = $uploadedFile->store($storagePath, 'public');
        }

        // --- END LOGIKA WATERMARK ---

        $order->orderFiles()->create([
            'tipe_file' => $tipe,
            'path_file' => $path,
        ]);

        // Status pesanan
        $order->update([
            'status_pesanan' => $tipe === 'Final'
                ? 'Selesai'
                : 'Menunggu Konfirmasi Pelanggan',
        ]);

        return back()->with(
            'success',
            "File {$tipe} berhasil diunggah."
        );
    }
}
