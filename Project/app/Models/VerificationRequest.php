<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationRequest extends Model
{
    /** @use HasFactory<\Database\Factories\VerificationRequestFactory> */

    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'verfied_at',
        'first_name',
        'last_name',
        'status',
        'nik',
        'birthdate',
        'gender',
        'address',
        'phone_number',
        'photo_url', 
        'id_card_url',
        'selfie_with_id_card_url',
    ];
    protected $casts = [
        'verfied_at' => 'datetime',
        'birthdate' => 'date',
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
