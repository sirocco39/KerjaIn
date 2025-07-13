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
        'request_id',
        'requester_id',
        'worker_id',
        'status',
        'price',
        'accepted_at',
    ];
    protected $attributes = [
        'status' => 'accepted',
    ];
    protected $casts = [
        'accepted_at' => 'datetime',
        'price' => 'decimal:2',
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

    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case 'accepted':
                return 'Diterima';
            case 'in progress':
                return 'Dikerjain';
            case 'completed':
                return 'Selesai';
            case 'cancelled':
                return 'Dibatalin';
            case 'submitted':
                return 'Ditinjau';
            default:
                return ucfirst($this->status); // Fallback to capitalized status
        }
    }
}
