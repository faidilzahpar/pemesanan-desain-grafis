<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $primaryKey = 'order_id';
    public $incrementing = false;

    protected $fillable = [
        'order_id',
        'user_id',
        'design_type_id',
        'deskripsi',
        'referensi_desain',
        'metode_pembayaran',
        'status_pesanan',
        'deadline',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            // Ambil tahun 2 digit → 2025 = "25"
            $year = date('y');

            // Cari order terakhir di tahun yang sama
            $last = Order::where('order_id', 'like', "ORD$year%")
                         ->orderBy('order_id', 'desc')
                         ->first();

            if ($last) {
                // Ambil angka setelah prefix "ORD25"
                $num = (int) substr($last->order_id, 5); 
                $num++;
            } else {
                $num = 1; // jika belum ada order di tahun ini → mulai dari 1
            }

            // Format angka jadi minimal 3 digit (001, 002)
            $numFormatted = str_pad($num, 3, '0', STR_PAD_LEFT);

            // Bentuk ID final
            $model->order_id = "ORD$year" . $numFormatted;
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function designType()
    {
        return $this->belongsTo(DesignType::class, 'design_type_id', 'design_type_id');
    }

    public function orderFiles()
    {
        return $this->hasMany(OrderFile::class, 'order_id', 'order_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'order_id', 'order_id');
    }
}
