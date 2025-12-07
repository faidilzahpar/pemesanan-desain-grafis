<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'no_hp',
        'is_admin',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            // Tahun 2 digit → 2025 = 25
            $year = date('y');  

            // Ambil user terakhir berdasarkan angka belakang
            $last = User::where('user_id', 'like', "C$year%")
                        ->orderBy('user_id', 'desc')
                        ->first();

            if ($last) {
                // Ambil angka urut dari belakang (setelah "C25")
                $num = (int) substr($last->user_id, 3);
                $num++;
            } else {
                $num = 1;
            }

            // Format angka dengan leading zeros, minimal 3 digit
            $num_formatted = str_pad($num, 3, '0', STR_PAD_LEFT);

            // Buat ID final
            $model->user_id = "C$year" . $num_formatted;
        });
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }
}
