<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        // Filter dasar
        $query = Invoice::with(['order.user', 'order.designType'])
            ->where('status_pembayaran', 'Menunggu Verifikasi');

        // 1. LOGIKA SEARCH
        if ($request->filled('tableSearch')) {
            $search = $request->tableSearch;
            $query->where(function($q) use ($search) {
                $q->where('jenis_invoice', 'like', "%{$search}%")
                // Search ID Pesanan & Nama Customer
                ->orWhereHas('order', function($o) use ($search) {
                    $o->where('order_id', 'like', "%{$search}%")
                        ->orWhereHas('user', function($u) use ($search) {
                            $u->where('name', 'like', "%{$search}%");
                        })
                        // Search Jumlah Bayar (Trik: Replikasi logika Accessor di SQL)
                        ->orWhereHas('designType', function($d) use ($search) {
                            // Rumus: harga * 0.5. Kita ubah jadi string agar bisa di-LIKE
                            $d->whereRaw("CAST(harga * 0.5 AS CHAR) LIKE ?", ["%{$search}%"]);
                        });
                });
            });
        }

        // 2. LOGIKA SORTING (Tetap sama seperti sebelumnya)
        if ($request->filled('tableSortColumn') && $request->filled('tableSortDirection')) {
            $column = $request->tableSortColumn;
            $direction = $request->tableSortDirection === 'desc' ? 'desc' : 'asc';

            if ($column === 'order_id') {
                $query->join('orders', 'invoices.order_id', '=', 'orders.order_id')
                    ->orderBy('orders.order_id', $direction)
                    ->select('invoices.*');
            } 
            elseif ($column === 'user_name') {
                $query->join('orders', 'invoices.order_id', '=', 'orders.order_id')
                    ->join('users', 'orders.user_id', '=', 'users.user_id')
                    ->orderBy('users.name', $direction)
                    ->select('invoices.*');
            } 
            elseif ($column === 'jumlah_bayar') {
                $query->join('orders', 'invoices.order_id', '=', 'orders.order_id')
                    ->join('design_types', 'orders.design_type_id', '=', 'design_types.design_type_id')
                    ->orderBy('design_types.harga', $direction)
                    ->select('invoices.*');
            }
            else {
                $validColumns = ['jenis_invoice', 'created_at']; 
                if (in_array($column, $validColumns)) {
                    $query->orderBy($column, $direction);
                }
            }
        } else {
            $query->orderBy('created_at', 'asc');
        }

        $invoices = $query->paginate(10)->withQueryString();

        return view('admin.payments.index', compact('invoices'));
    }
}
