<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationRequest extends Model
{
    // soft delete
    use SoftDeletes, HasFactory;

    // created_at dan updated_at ototmatis dibuat oleh laravel
    // fillable yang berarti akan diisi oleh user ataupun berasal dari API atau tabel lain
    protected $fillable = [
        'user_id',
        'verified_at',
        'status',
        'first_name',
        'last_name',
        'nik',
        'birthdate',
        'gender',
        'address',
        'phone_number',
        'photo_url',
        'id_card_url',
        'selfie_with_id_card_url',
        'account_name',
        'account_number'
    ];

    // protected $casts untuk mengubah tipe data dari database ke tipe data yang sesuai di PHP
    protected $casts = [
        'verified_at' => 'datetime',
        'birthdate' => 'date',
    ];

    // ini berarti satu user dapat mengajukan banyak verification request
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public $timestamps = true; // pengaturan created_at dan updated_at otomatis dibuat oleh laravel
}
