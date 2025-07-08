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
    public function reviews() : HasMany
    {
        return $this->hasMany(Review::class, 'transaction_id');
    }
    public function completionProof() : HasOne
    {
        return $this->hasOne(CompletionProof::class, 'transaction_id');
    }
    public function report() : HasOne
    {
        return $this->hasOne(Report::class, 'transaction_id');
    }

    // Define status mapping for better readability based on your string statuses
    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case 'accepted': return 'Diterima';
            case 'in progress': return 'Dikerjain';
            case 'completed': return 'Selesai';
            case 'cancelled': return 'Dibatalin';
            case 'submitted': return 'Diselesaiin'; // Assuming 'submitted' exists
            // Add more status mappings as per your database values if they exist
            default: return ucfirst($this->status); // Fallback to capitalized status
        }
    }

    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case 'completed':
                return 'bg-green-200 text-green-800'; // Selesai nih
            case 'in progress':
            case 'accepted':
            case 'pending':
            case 'submitted':
                return 'bg-yellow-200 text-yellow-800'; // Dikerjain, Diterima, Menunggu, Diajukan
            case 'cancelled':
                return 'bg-red-200 text-red-800'; // Dibatalkan
            default:
                return 'bg-gray-200 text-gray-800'; // Unknown
        }
    }
}
