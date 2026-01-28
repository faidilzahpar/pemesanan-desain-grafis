<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Invoice;

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
                $query->join('users', 'orders.user_id', '=', 'users.user_id')
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

        // Hitung jumlah revisi yang SUDAH ADA di database
        $revisiCount = $order->orderFiles()
            ->where('tipe_file', 'Revisi')
            ->count();

        $totalFiles = $order->orderFiles()->count();
        
        // --- 1. PENENTUAN TIPE FILE & STATUS ---
        $isMaxRevisionReached = false;
        $isTrueFinal          = false; 

        // KASUS A: Upload File Final yang Asli (Setelah lunas)
        if ($order->status_pesanan === 'Menunggu File Final') {
            $tipe = 'Final';
            $isTrueFinal = true;
        } 
        // KASUS B: Upload Pertama Kali (Preview Awal)
        elseif ($totalFiles === 0) {
            $tipe = 'Preview';
        } 
        // KASUS C: CEK APAKAH INI REVISI TERAKHIR? (LOGIKA YANG DIPERBAIKI)
        // Jika di database sudah ada 2 file revisi, maka upload ini adalah revisi ke-3 (BATAS MAX).
        elseif ($revisiCount >= 2) { 
            $tipe = 'Revisi'; // Tetap namakan revisi biar kena watermark
            $isMaxRevisionReached = true; // Tandai bahwa ini batas akhir
        } 
        // KASUS D: Masih revisi biasa (Revisi ke-1 atau ke-2)
        else {
            $tipe = 'Revisi';
        }

        // --- 2. LOGIKA PENYIMPANAN FILE (WATERMARK) ---
        $isImage = str_starts_with($uploadedFile->getMimeType(), 'image/');
        $storagePath = "order-files/{$order->order_id}";
        $filename = $uploadedFile->hashName();
        $fullPath = $storagePath . '/' . $filename;
        $pathForDb = null;

        // Jika bukan Final asli & berupa gambar => KASIH WATERMARK
        if ($tipe !== 'Final' && $isImage) {
            
            // Setup Image Manager
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($uploadedFile);

            // Tempel Watermark
            if (file_exists(public_path('watermark.png'))) {
                $image->place(public_path('watermark.png'), 'center', 0, 0, 50);
            }

            $encoded = $image->toPng();
            
            // SIMPAN FISIK FILE
            \Illuminate\Support\Facades\Storage::disk('public')->put($fullPath, $encoded);
            $pathForDb = $fullPath;

        } else {
            // Simpan Biasa (PDF/Zip atau Final Image)
            $pathForDb = $uploadedFile->store($storagePath, 'public');
        }

        // --- 3. SIMPAN DATA KE DATABASE ---
        $order->orderFiles()->create([
            'tipe_file' => $tipe,
            'path_file' => $pathForDb,
        ]);

        // --- 4. UPDATE STATUS & GENERATE INVOICE ---
        $msg = '';

        if ($isTrueFinal) {
            // Selesai total
            $order->update(['status_pesanan' => 'Selesai']);
            $msg = "File Final terkirim. Pesanan Selesai.";

        } elseif ($isMaxRevisionReached) {
            // --- INI LOGIKA PENTINGNYA ---
            // Karena ini revisi ke-3, JANGAN tanya ke user lagi.
            // Langsung paksa ke status 'Menunggu Pelunasan'.
            
            // Cek invoice pelunasan (biar gak dobel)
            $existingInvoice = $order->invoices()->where('jenis_invoice', 'Pelunasan')->first();
            
            if (!$existingInvoice) {
                \App\Models\Invoice::create([
                    'order_id'          => $order->order_id,
                    'jenis_invoice'     => 'Pelunasan',
                    'status_pembayaran' => 'Belum Dibayar',
                    'bukti_path'        => null,
                ]);
            }

            $order->update(['status_pesanan' => 'Menunggu Pelunasan']);
            $msg = "Batas revisi (3x) tercapai. Invoice pelunasan diterbitkan.";

        } else {
            // Revisi biasa (ke-1 atau ke-2), lempar bola ke user
            $order->update(['status_pesanan' => 'Menunggu Konfirmasi Pelanggan']);
            $msg = "File {$tipe} diunggah. Menunggu tanggapan pelanggan.";
        }

        return back()->with('success', $msg);
    }
}
