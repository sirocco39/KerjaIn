<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    protected $table = 'sessions'; // Nama tabel sudah benar
    protected $primaryKey = 'id'; // Primary key adalah 'id' string
    public $incrementing = false; // Karena id bukan auto-incrementing integer
    protected $keyType = 'string'; // Tipe primary key adalah string

    // Kolom-kolom yang bisa diisi (jika Anda ingin mengelola sesi secara manual)
    protected $fillable = [
        'id',
        'user_id',
        'ip_address',
        'user_agent',
        'payload',
        'last_activity',
    ];

    /**
     * Relasi ke model User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor untuk mengubah last_activity menjadi Carbon instance (timestamp)
    public function getLastActivityAttribute($value)
    {
        return \Carbon\Carbon::createFromTimestamp($value);
    }
}
