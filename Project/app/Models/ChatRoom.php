<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatRoom extends Model
{
    /** @use HasFactory<\Database\Factories\ChatRoomFactory> */
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'request_id',
        'requester_id',
        'worker_id',
        'is_open',
    ];
    protected $attributes = [
        'is_open' => true,
    ];
    protected $casts = [
        'is_open' => 'boolean',
    ];
    public function chatMessages() : HasMany
    {
        return $this->hasMany(ChatMessage::class, 'chat_room_id');
    }
    public function request() : BelongsTo
    {
        return $this->belongsTo(Request::class, 'request_id');
    }
    public function requester() : BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }
    public function worker() : BelongsTo
    {
        return $this->belongsTo(User::class, 'worker_id');
    }
    public function offers() : HasMany
    {
        return $this->hasMany(Offer::class, 'chat_room_id');
    }
}
