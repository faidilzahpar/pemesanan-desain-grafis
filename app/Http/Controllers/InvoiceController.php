<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function show(Invoice $invoice)
    {
        $invoice->load([
            'order.user',
            'order.designType',
        ]);

        return view('invoices.show', compact('invoice'));
    }

    public function verify(Request $request, Invoice $invoice)
    {
        // Pastikan hanya admin
        abort_if(
            !Auth::check() || Auth::user()->is_admin != 1,
            403
        );

        // Pastikan status masih menunggu verifikasi
        if ($invoice->status_pembayaran !== 'Menunggu Verifikasi') {
            return back()->with('error', 'Invoice ini sudah diproses.');
        }

        $order = $invoice->order;

        // Update invoice
        $invoice->update([
            'status_pembayaran' => 'Pembayaran Diterima',
        ]);

        // Update order
        $order->update([
            'status_pesanan' => 'Sedang Dikerjakan',
            'deadline' => now()->addDays($order->designType->durasi),
        ]);

        return redirect()
            ->route('invoices.show', $invoice->invoice_id)
            ->with('success', 'Pembayaran berhasil diterima.');
    }
    public function reject(Request $request, Invoice $invoice)
    {
        // Pastikan hanya admin
        abort_if(
            !Auth::check() || Auth::user()->is_admin != 1,
            403
        );

        // Pastikan status masih menunggu verifikasi
        if ($invoice->status_pembayaran !== 'Menunggu Verifikasi') {
            return back()->with('error', 'Invoice ini sudah diproses.');
        }

        $order = $invoice->order;

        // Update invoice
        $invoice->update([
            'status_pembayaran' => 'Pembayaran Ditolak',
        ]);

        // Kembalikan order ke menunggu pembayaran
        $order->update([
            'status_pesanan' => 'Menunggu Pembayaran',
        ]);

        return redirect()
            ->route('invoices.show', $invoice->invoice_id)
            ->with('success', 'Pembayaran ditolak.');
    }
}
