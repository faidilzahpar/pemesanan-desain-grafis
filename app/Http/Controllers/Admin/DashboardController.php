<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\DesignType;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Sedang dikerjakan (ongoing)
        $ordersInProgress = Order::whereIn('status_pesanan', [
            'Sedang Dikerjakan',
            'Menunggu Konfirmasi Pelanggan',
        ])->count();

        // Proses revisi
        $ordersRevisi = Order::where('status_pesanan', 'Revisi')->count();

        // Pembayaran menunggu verifikasi
        $pendingPayments = Invoice::where(
            'status_pembayaran',
            'Menunggu Verifikasi'
        )->count();

        // Mendekati deadline (< 1 hari)
        $nearDeadline = Order::whereNotNull('deadline')
            ->where('deadline', '<=', Carbon::now()->addDay())
            ->whereNotIn('status_pesanan', [
                'Selesai',
                'Dibatalkan',
            ])
            ->count();

        // Total jenis desain
        $totalDesignTypes = DesignType::count();

        return view('admin.dashboard', compact(
            'ordersInProgress',
            'ordersRevisi',
            'pendingPayments',
            'nearDeadline',
            'totalDesignTypes'
        ));
    }
}
