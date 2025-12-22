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
            'Menunggu DP',
            'Menunggu Pelunasan',
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

        $path = $request->file('file')
            ->store("order-files/{$order->order_id}", 'public');

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
