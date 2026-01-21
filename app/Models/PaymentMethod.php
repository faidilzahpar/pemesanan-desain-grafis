<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymentMethod extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'payment_method_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'payment_method_id',
        'nama_metode',
        'nomor_akun',
        'atas_nama',
        'qr_path',
        'is_active',
    ];

    protected static function boot()
        {
            parent::boot();

            static::creating(function ($model) {

                $prefixMetode = strtoupper(substr($model->nama_metode, 0, 1));
                $year = date('y');

                // PREFIX HANYA UNTUK LABEL, BUKAN COUNTER
                $prefix = "PM{$prefixMetode}{$year}";

                // 🔥 CARI TERAKHIR TANPA LIHAT METODE
                $last = self::where('payment_method_id', 'like', "PM%{$year}%")
                    ->orderBy('payment_method_id', 'desc')
                    ->first();

                if ($last) {
                    $num = (int) substr($last->payment_method_id, -2);
                    $num++;
                } else {
                    $num = 1;
                }

                $numFormatted = str_pad($num, 2, '0', STR_PAD_LEFT);

                $model->payment_method_id = $prefix . $numFormatted;
            });
        }

    public function orders()
    {
        return $this->hasMany(Order::class, 'payment_method_id', 'payment_method_id');
    }
}