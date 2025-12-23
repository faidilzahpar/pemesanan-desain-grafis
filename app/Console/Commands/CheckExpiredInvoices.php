<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use Carbon\Carbon;

class CheckExpiredInvoices extends Command
{
    /**
     * Nama perintah yang nanti dipanggil oleh scheduler
     */
    protected $signature = 'invoices:check-expired';

    /**
     * Deskripsi perintah
     */
    protected $description = 'Mengecek dan membatalkan invoice DP yang belum dibayar lebih dari 24 jam';

    /**
     * Eksekusi logika
     */
    public function handle()
    {
        // 1. Cari Invoice yang memenuhi syarat expired:
        // - Jenis Invoice = DP
        // - Status = Belum Dibayar (atau Ditolak)
        // - Dibuat lebih dari 24 jam yang lalu (created_at < now - 24 jam)
        
        $expiredInvoices = Invoice::where('jenis_invoice', 'DP')
            ->whereIn('status_pembayaran', ['Belum Dibayar', 'Pembayaran Ditolak'])
            ->where('created_at', '<', Carbon::now()->subHours(24)) // Logika kuncinya disini
            ->get();

        if ($expiredInvoices->isEmpty()) {
            $this->info('Tidak ada invoice yang expired.');
            return;
        }

        foreach ($expiredInvoices as $invoice) {
            // Update Invoice jadi Expired
            $invoice->update([
                'status_pembayaran' => 'Pembayaran Expired'
            ]);

            // Update Order jadi Dibatalkan
            if ($invoice->order) {
                $invoice->order->update([
                    'status_pesanan' => 'Dibatalkan'
                ]);
            }

            $this->info("Invoice ID {$invoice->invoice_id} telah di-expired.");
        }

        $this->info('Selesai mengecek invoice expired.');
    }
}   