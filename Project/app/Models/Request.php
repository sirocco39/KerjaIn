<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Request extends Model
{
    /** @use HasFactory<\Database\Factories\RequestFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'location',
        'requester_id',
        'status',
        'start_time',
        'end_time',
    ];
    protected $attributes = [
        'status' => 'open',
    ];
    protected $casts = [
        'price' => 'decimal:2',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function requester() : BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }
    public function transactions() : HasMany
    {
        return $this->hasMany(Transaction::class, 'request_id');
    }
    public function payments() : HasMany
    {
        return $this->hasMany(Payment::class, 'request_id');
    }
    public function chatRooms() : HasMany
    {
        return $this->hasMany(ChatRoom::class, 'request_id');
    }
}
