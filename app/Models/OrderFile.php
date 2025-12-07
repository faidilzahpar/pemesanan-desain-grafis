<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderFile extends Model
{
    protected $primaryKey = 'file_id';
    public $incrementing = false;

    protected $fillable = [
        'file_id',
        'order_id',
        'tipe_file',
        'path_file',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            // Ambil tahun 2 digit
            $year = date('y'); // contoh: 2025 → "25"

            // Ambil order terkait
            $order = \App\Models\Order::where('order_id', $model->order_id)->first();

            // Ambil angka order (mulai index ke-5)
            // ORD25001 → "001", ORD251234 → "1234"
            $angkaOrder = substr($order->order_id, 5);

            // Hitung jumlah file sebelumnya di order ini
            $count = \App\Models\OrderFile::where('order_id', $model->order_id)->count();
            $nomorFile = $count + 1;

            // Format ID: F{nomorFile}{tahun}{angkaOrder}
            $model->file_id = "F{$nomorFile}{$year}{$angkaOrder}";
        });
    }
}
