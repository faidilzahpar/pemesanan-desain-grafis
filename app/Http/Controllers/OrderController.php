<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\DesignType;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function create(Request $request)
    {
        $designTypes = DesignType::where('is_active', 1)
            ->orderBy('created_at', 'asc')
            ->get();

        $selectedDesignId = $request->query('design');

        $paymentMethods = [
            'Transfer Bank',
            'E-Wallet',
            'QRIS',
        ];

        return view('orders.create', compact(
            'designTypes',
            'selectedDesignId',
            'paymentMethods'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'design_type_id'     => 'required|exists:design_types,design_type_id',
            'deskripsi'          => 'required|string',
            'metode_pembayaran' => 'required|string',
            'referensi_desain'   => 'nullable|file|max:10240', // 10MB
        ]);

        // Ambil jenis desain
        $designType = DesignType::where(
            'design_type_id',
            $request->design_type_id
        )->firstOrFail();

        // Upload file referensi (jika ada)
        $referensiPath = null;
        if ($request->hasFile('referensi_desain')) {
            $referensiPath = $request->file('referensi_desain')
                ->store('referensi-desain', 'public');
        }

        // Simpan order
        $order = Order::create([
            'user_id'           => Auth::id(),
            'design_type_id'    => $designType->design_type_id,
            'deskripsi'         => $request->deskripsi,
            'referensi_desain'  => $referensiPath,
            'metode_pembayaran' => $request->metode_pembayaran,
            'status_pesanan'    => 'Menunggu Pembayaran',
            'deadline'          => null,
        ]);

        // Buat invoice DP otomatis
        $invoice = Invoice::create([
            'order_id'          => $order->order_id,
            'jenis_invoice'     => 'DP',
            'status_pembayaran' => 'Belum Dibayar',
            'tgl_bayar'         => null,
            'bukti_path'        => null,
        ]);

        return redirect()
            ->route('invoices.show', $invoice->invoice_id)
            ->with('success', 'Pesanan berhasil dibuat. Silakan lakukan pembayaran DP.');       
    }
}
