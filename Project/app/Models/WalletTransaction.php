<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'type', // 'top_up', 'withdrawal', etc.
        'description_id', // Optional description of the transaction
        'description_en', // Optional description of the transaction
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
