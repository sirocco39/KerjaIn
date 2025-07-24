<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopUpOrder extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'external_id',
        'xendit_invoice_id',
        'invoice_url'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
