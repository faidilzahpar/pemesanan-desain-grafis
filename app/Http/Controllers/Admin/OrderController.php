<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index()
    {
        // STATUS PESANAN
        $statusBaru = ['Menunggu Pembayaran', 'Menunggu Verifikasi Pembayaran'];
        $statusProses = ['Sedang Dikerjakan', 'Menunggu Konfirmasi Pelanggan', 'Revisi'];

        // HITUNG BADGE JUMLAH
        $countBaru = Order::whereIn('status_pesanan', $statusBaru)->count();
        $countProses = Order::whereIn('status_pesanan', $statusProses)->count();

        // AMBIL DATA UNTUK MASING-MASING TAB
        $ordersBaru = Order::with('user', 'designType')
            ->whereIn('status_pesanan', $statusBaru)
            ->orderBy('created_at', 'desc')
            ->get();

        $ordersProses = Order::with('user', 'designType')
            ->whereIn('status_pesanan', $statusProses)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.orders.index', compact(
            'ordersBaru',
            'ordersProses',
            'countBaru',
            'countProses',
        ));
    }

    public function show(Order $order)
    {
        $order->load('user', 'designType', 'orderFiles');

        return view('admin.orders.show', compact('order'));
    }
}
