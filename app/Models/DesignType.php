<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignType extends Model
{
    protected $primaryKey = 'design_type_id';
    public $incrementing = false;

    protected $fillable = [
        'design_type_id',
        'nama_jenis',
        'deskripsi',
        'durasi',
        'harga',
        'is_active',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            $year = date('y'); // contoh: 25 untuk 2025

            // Ambil ID terakhir dengan prefix D25
            $last = DesignType::where('design_type_id', 'like', "D$year%")
                               ->orderBy('design_type_id', 'desc')
                               ->first();

            if ($last) {
                // Ambil angka setelah prefix "D25"
                $num = (int) substr($last->design_type_id, 3);
                $num++;
            } else {
                $num = 1;
            }

            // Minimal 2 digit
            $numFormatted = str_pad($num, 2, '0', STR_PAD_LEFT);

            // Set ID final → D2501
            $model->design_type_id = "D$year" . $numFormatted;
        });
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'design_type_id', 'design_type_id');
    }
}
