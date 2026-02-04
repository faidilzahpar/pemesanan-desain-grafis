<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'message',
        'attachment',
        'is_read',
        'is_deleted',
        'referenced_file_id',
    ];

    // Relasi ke User pengirim
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Relasi untuk "Context" (File hasil desain yang sedang dibahas)
    public function referencedFile()
    {
        return $this->belongsTo(OrderFile::class, 'referenced_file_id', 'file_id');
    }
}