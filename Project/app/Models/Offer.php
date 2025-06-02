<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Offer extends Model
{
    /** @use HasFactory<\Database\Factories\OfferFactory> */
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'request_id',
        'chat_room_id',
        'requester_id',
        'worker_id',
        'amount',
        'status',
    ];
    protected $casts = [
        'amount' => 'decimal:2',
        'status' => 'string',
    ];
    public function chatRoom() : BelongsTo
    {
        return $this->belongsTo(ChatRoom::class);
    }

    public function request() : BelongsTo
    {
        return $this->belongsTo(Request::class);
    }
    public function worker() : BelongsTo
    {
        return $this->belongsTo(User::class, 'worker_id');
    }
    public function payment() : HasOne
    {
        return $this->HasOne(Payment::class);
    }
    
}
