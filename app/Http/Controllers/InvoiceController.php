<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function show(Invoice $invoice)
    {
        $this->handleAutoExpire($invoice);

        $paymentDeadline = $invoice->created_at->copy()->addHours(24);

        $isExpired =
            $invoice->status_pembayaran === 'Pembayaran Expired';

        $invoice->load([
            'order.user',
            'order.designType',
        ]);

        return view('invoices.show', compact(
            'invoice',
            'isExpired',
            'paymentDeadline'
        ));
    }

    public function verify(Request $request, Invoice $invoice)
    {
        abort_if(!Auth::check() || Auth::user()->is_admin != 1, 403);

        if ($invoice->status_pembayaran !== 'Menunggu Verifikasi') {
            return back()->with('error', 'Invoice ini sudah diproses.');
        }

        $order = $invoice->order;

        $invoice->update([
            'status_pembayaran' => 'Pembayaran Diterima',
        ]);

        // LOGIKA BERDASARKAN JENIS INVOICE
        if ($invoice->jenis_invoice === 'DP') {
            $order->update([
                'status_pesanan' => 'Sedang Dikerjakan',
                'deadline' => now()->addDays($order->designType->durasi),
            ]);
        }

        if ($invoice->jenis_invoice === 'Pelunasan') {
            $order->update([
                'status_pesanan' => 'Selesai',
            ]);
        }

        return redirect()
            ->route('invoices.show', $invoice->invoice_id)
            ->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    public function reject(Request $request, Invoice $invoice)
    {
        abort_if(!Auth::check() || Auth::user()->is_admin != 1, 403);

        if ($invoice->status_pembayaran !== 'Menunggu Verifikasi') {
            return back()->with('error', 'Invoice ini sudah diproses.');
        }

        $invoice->update([
            'status_pembayaran' => 'Pembayaran Ditolak',
        ]);

        // KEMBALIKAN STATUS ORDER SESUAI JENIS INVOICE
        if ($invoice->jenis_invoice === 'DP') {
            $invoice->order->update([
                'status_pesanan' => 'Menunggu DP',
            ]);
        }

        if ($invoice->jenis_invoice === 'Pelunasan') {
            $invoice->order->update([
                'status_pesanan' => 'Menunggu Pelunasan',
            ]);
        }

        return redirect()
            ->route('invoices.show', $invoice->invoice_id)
            ->with('success', 'Pembayaran ditolak.');
    }

    public function upload(Request $request, Invoice $invoice)
    {
        abort_if(!Auth::check() || Auth::user()->is_admin == 1, 403);

        // ❗ CEK EXPIRED
        if ($invoice->status_pembayaran === 'Pembayaran Expired') {
            return back()->with('error', 'Invoice sudah expired.');
        }

        // ❗ CEK STATUS PESANAN
        if (!in_array($invoice->order->status_pesanan, [
            'Menunggu DP',
            'Menunggu Pelunasan',
        ])) {
            return back()->with('error', 'Pesanan tidak berada pada tahap pembayaran.');
        }

        $request->validate([
            'bukti_pembayaran' => 'required|file|max:10240',
        ]);

        $path = $request->file('bukti_pembayaran')
            ->store('bukti-pembayaran', 'public');

        $invoice->update([
            'bukti_path'        => $path,
            'status_pembayaran' => 'Menunggu Verifikasi',
            'tgl_bayar'         => now(),
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah.');
    }

    private function handleAutoExpire(Invoice $invoice)
    {
        if (
            $invoice->status_pembayaran === 'Belum Dibayar' &&
            $invoice->created_at->addHours(24)->isPast()
        ) {
            $invoice->update([
                'status_pembayaran' => 'Pembayaran Expired',
            ]);

            $invoice->order->update([
                'status_pesanan' => 'Dibatalkan',
            ]);
        }
    }
}
