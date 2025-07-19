<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'order_number',
        'request_id',
        'requester_id',
        'worker_id',
        'status',
        'accepted_at',
    ];
    protected $attributes = [
        'status' => 'accepted',
    ];
    protected $casts = [
        'accepted_at' => 'datetime',
    ];
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class, 'request_id');
    }
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }
    public function worker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'worker_id');
    }
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'transaction_id');
    }
    public function completionProof(): HasOne
    {
        return $this->hasOne(CompletionProof::class, 'transaction_id');
    }
    public function report(): HasOne
    {
        return $this->hasOne(Report::class, 'transaction_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateUniqueOrderNumber();
            }
        });
    }

    protected static function generateUniqueOrderNumber()
    {
        do {
            $number = str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
        } while (self::where('order_number', $number)->exists());

        return $number;
    }
}
