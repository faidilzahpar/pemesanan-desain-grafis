<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{   
    protected $primaryKey = 'invoice_id';
    public $incrementing = false;

    protected $fillable = [
        'invoice_id',
        'order_id',
        'jenis_invoice',
        'tgl_bayar',
        'status_pembayaran',
        'bukti_path',
    ];
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            // Tahun 2 digit → 2025 = "25"
            $year = date('y');

            // Ambil order terkait
            $order = \App\Models\Order::where('order_id', $model->order_id)->first();

            // Ambil nomor order dari belakang (bebas berapa digit)
            $angkaOrder = substr($order->order_id, 5);

            // Tentukan kode jenis
            $jenis = strtoupper($model->jenis_invoice); // dp / pelunasan
            $kodeJenis = $jenis === 'DP' ? 'DP' : 'LS';

            // Format ID final
            $model->invoice_id = "INV{$year}{$kodeJenis}{$angkaOrder}";
        });
    }
}
