<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{   
    use HasFactory;

    protected $primaryKey = 'invoice_id';
    public $incrementing = false;
    protected $keyType = 'string';

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

    public function getJumlahBayarAttribute()
    {
        $harga = $this->order->designType->harga;

        return $this->jenis_invoice === 'DP'
            ? $harga * 0.5
            : $harga * 0.5;
    }

    public function isExpired()
    {
        return $this->status_pembayaran === 'Belum Dibayar'
            && $this->created_at->addHours(24)->isPast();
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}
