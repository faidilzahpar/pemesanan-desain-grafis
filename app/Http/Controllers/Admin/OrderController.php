<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index()
    {
        // STATUS YANG TIDAK DITAMPILKAN
        $excludedStatus = [
            'Menunggu Pembayaran',
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
            'file' => 'required|file|max:10240',
        ]);

        // Hitung revisi
        $revisiCount = $order->orderFiles()
            ->where('tipe_file', 'Revisi')
            ->count();

        // Tentukan tipe file
        if ($order->orderFiles()->count() === 0) {
            $tipe = 'Awal';
        } elseif ($revisiCount >= 2) {
            // Revisi ke-3 → Final
            $tipe = 'Final';
        } else {
            $tipe = 'Revisi';
        }

        // Simpan file
        $path = $request->file('file')
            ->store("order-files/{$order->order_id}", 'public');

        // Simpan ke DB
        $order->orderFiles()->create([
            'tipe_file' => $tipe,
            'path_file' => $path,
        ]);

        // Update status order
        $order->update([
            'status_pesanan' => 'Menunggu Konfirmasi Pelanggan',
        ]);

        return back()->with('success', 'File desain berhasil diunggah.');
    }
}
