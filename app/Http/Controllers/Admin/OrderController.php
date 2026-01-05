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
    public function index()
    {
        // STATUS YANG TIDAK DITAMPILKAN
        $excludedStatus = [
            'Menunggu DP',
            'Selesai',
            'Dibatalkan',
        ];

        // AMBIL PESANAN YANG SEDANG DIKERJAKAN
        $orders = Order::with('user', 'designType')
            ->whereNotIn('status_pesanan', $excludedStatus)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('user', 'designType', 'orderFiles');

        return view('admin.orders.show', compact('order'));
    }

    public function uploadFile(Request $request, Order $order)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $uploadedFile = $request->file('file'); // Simpan di variabel

        $totalFiles  = $order->orderFiles()->count();
        $revisiCount = $order->orderFiles()
            ->where('tipe_file', 'Revisi')
            ->count();

        /*
        ALUR:
        1. File pertama  → Preview
        2. Revisi 1–2    → Revisi
        3. Revisi ke-3   → Final
        */

        if ($totalFiles === 0) {
            $tipe = 'Preview';
        } elseif ($revisiCount < 3) {
            $tipe = 'Revisi';
        } else {
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
