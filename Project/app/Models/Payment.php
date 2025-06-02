<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;
    protected $fillable = [
        'request_id',
        'amount',
        'status',
        'paid_at',
        'method',
    ];
    protected $attributes = [
        'status' => 'pending',
    ];
    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];
    public function request() : BelongsTo
    {
        return $this->belongsTo(Request::class, 'request_id');
    }
    public function offer() : BelongsTo
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }
}
