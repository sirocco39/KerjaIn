<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    /** @use HasFactory<\Database\Factories\ChatMessageFactory> */
    use HasFactory;
    protected $fillable = [
        'chat_room_id',
        'sender_id',
        'receiver_id',
        'message',
        'read_at',
    ];
    protected $casts = [
        'read_at' => 'datetime',
    ];
    public function chatRoom() : BelongsTo
    {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id');
    }
    public function sender() : BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    public function receiver() : BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
