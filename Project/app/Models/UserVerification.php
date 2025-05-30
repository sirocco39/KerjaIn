<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserVerification extends Model
{
    protected $fillable = ['user_id', 'code', 'expires_at'];
    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected $casts = [
        'expires_at' => 'datetime',
    ];

}

