<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;

class PaymentController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with([ 
                'order.user',
                'order.designType'
            ])
            ->where('status_pembayaran', 'Menunggu Verifikasi')
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return view('admin.payments.index', compact('invoices'));
    }
}
