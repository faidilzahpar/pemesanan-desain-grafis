<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\DesignType;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\OrderFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');

        $query = Order::with([
                'designType',
                'invoices'
            ])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        // FILTER STATUS
        match ($status) {
        'unpaid' => $query->whereIn('status_pesanan', [
            'Menunggu DP',
            'Menunggu Pelunasan',
        ]),
        'process' => $query->whereIn('status_pesanan', [
            'Sedang Dikerjakan',
            'Menunggu Konfirmasi Pelanggan',
            'Revisi',
        ]),
        'done' => $query->where('status_pesanan', 'Selesai'),
        'cancel' => $query->where('status_pesanan', 'Dibatalkan'),
        default => null,
    };

        $orders = $query->get();

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Pastikan hanya pemilik order yang bisa melihat
        abort_if($order->user_id !== Auth::id(), 403);

        $order->load([
            'designType',
            'orderFiles' => function ($q) {
                $q->orderBy('created_at', 'asc');
            },
            'invoices'
        ]);

        $activeInvoice = $order->invoices
            ->sortByDesc('created_at')
            ->first();

        $paymentDeadline = $activeInvoice
            ? $activeInvoice->created_at->copy()->addHours(24)
            : null;

        $visibleInvoices = $order->invoices
            ->whereNotNull('bukti_path');

        return view('orders.show', compact(
            'order',
            'activeInvoice',
            'paymentDeadline',
            'visibleInvoices'
        ));

        return view('orders.show', compact('order'));
    }

    public function create(Request $request)
    {
        $designTypes = DesignType::where('is_active', 1)
            ->orderBy('created_at', 'asc')
            ->get();

        $selectedDesignId = $request->query('design');

        $paymentMethods = PaymentMethod::where('is_active', true)
        ->orderBy('nama_metode')
        ->get();

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
            'payment_method_id'  => 'required|exists:payment_methods,payment_method_id',
            'referensi_desain'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ], [
            'referensi_desain.mimes' => 'Format file harus berupa JPG, PNG, atau PDF.',
            'referensi_desain.max'   => 'Ukuran file maksimal 10MB.',
            'referensi_desain.file'  => 'Data yang diupload harus berupa file.',
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
            'user_id'            => Auth::id(),
            'design_type_id'     => $designType->design_type_id,
            'payment_method_id'  => $request->payment_method_id,
            'deskripsi'          => $request->deskripsi,
            'referensi_desain'   => $referensiPath,
            'status_pesanan'     => 'Menunggu DP',
            'deadline'           => null,
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

    public function approve(Order $order)
    {
        abort_if(!Auth::check(), 403);
        abort_if($order->user_id !== Auth::id(), 403);

        if ($order->status_pesanan !== 'Menunggu Konfirmasi Pelanggan') {
            return back()->with('error', 'Pesanan tidak dapat disetujui pada tahap ini.');
        }

        // Pastikan ada file desain
        if ($order->orderFiles()->count() === 0) {
            return back()->with('error', 'Belum ada file desain untuk disetujui.');
        }

        // ❗ CEK: jangan buat invoice pelunasan dobel
        $alreadyHasPelunasan = $order->invoices()
            ->where('jenis_invoice', 'Pelunasan')
            ->exists();

        if ($alreadyHasPelunasan) {
            return back()->with('error', 'Invoice pelunasan sudah tersedia.');
        }

        // Update status order
        $order->update([
            'status_pesanan' => 'Menunggu Pelunasan',
        ]);

        // ✅ BUAT INVOICE PELUNASAN
        $invoice = Invoice::create([
            'order_id'          => $order->order_id,
            'jenis_invoice'     => 'Pelunasan',
            'status_pembayaran' => 'Belum Dibayar',
            'tgl_bayar'         => null,
            'bukti_path'        => null,
        ]);

        return redirect()
            ->route('invoices.show', $invoice->invoice_id)
            ->with('success', 'Desain disetujui. Silakan lakukan pelunasan.');
    }

    public function getStatusHtml(Order $order)
    {
        // 1. Cek Authorisasi
        if (Auth::id() !== $order->user_id && !Auth::user()->is_admin) {
            abort(403);
        }

        // 2. Load ulang relasi Invoice
        $order->load('invoices'); 

        // 3. Ambil Invoice Terakhir
        $activeInvoice = $order->invoices->sortByDesc('created_at')->first();
        
        // 4. Hitung Deadline
        $paymentDeadline = $activeInvoice 
            ? $activeInvoice->created_at->copy()->addHours(24) 
            : null;

        // 5. HITUNG ULANG showPaymentAlert (INI YANG TADI HILANG)
        $showPaymentAlert = 
            $activeInvoice
            && $activeInvoice->jenis_invoice === 'DP'
            && in_array($activeInvoice->status_pembayaran, ['Belum Dibayar', 'Pembayaran Ditolak']);

        // 6. Kirim SEMUA variabel ke partial view
        return view('orders.partials.status-badge', compact(
            'order', 
            'activeInvoice', 
            'paymentDeadline',
            'showPaymentAlert'
        ))->render();
    }

    public function downloadFile(OrderFile $file)
    {
        if (!Auth::check()) {
            abort(403);
        }

        $user = Auth::user();

        // IZINKAN JIKA: User adalah pemilik Order ATAU User adalah Admin
        if ($file->order->user_id !== $user->user_id && $user->is_admin != 1) {
            abort(403, 'Anda tidak memiliki hak akses untuk file ini.');
        }

        if (!Storage::disk('public')->exists($file->path_file)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        $fullPath = Storage::disk('public')->path($file->path_file);
        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
        $downloadName = "Desain-{$file->tipe_file}-{$file->order_id}.{$extension}";

        return response()->download($fullPath, $downloadName);
    }
}
