<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone_number',
        'role',
        'is_worker',
        'rating',
        'job_done',
        'is_blocked',
        'saldokerjain',
        'bank_acc_num',
    ];

    protected $attributes = [
        'role' => 'user',
        'is_worker' => false,
        'rating' => 0,
        'job_done' => 0,
        'is_blocked' => false,
        'saldokerjain' => 0,
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_worker' => 'boolean',
        'is_blocked' => 'boolean',
        'saldokerjain' => 'decimal:2',
        
    ];

    public function scopeNonAdmin($query)
    {   
        return $query->where('role', 'user');
    }

    public function scopeWorker($query)
    {   
        return $query->where('is_worker', true);
    }

    public function scopeNonWorker($query)
    {   
        return $query->where('is_worker', false)->where('role', 'user');
    }

    public function requests() : HasMany
    {
        return $this->hasMany(Request::class, 'requester_id');
    }
    public function transactionsAsRequester() : HasMany
    {
        return $this->hasMany(Transaction::class, 'requester_id');
    }
    public function transactionsAsWorker() : HasMany
    {
        return $this->hasMany(Transaction::class, 'worker_id');
    }

    public function reviewsGiven() : HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function reviewsReceived() : HasMany
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    public function chatRoomsAsRequester() : HasMany
    {
        return $this->hasMany(ChatRoom::class, 'requester_id');
    }
    public function chatRoomsAsWorker() : HasMany
    {
        return $this->hasMany(ChatRoom::class, 'worker_id');
    }
    public function reportsGiven() : HasMany
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }
    public function reportsReceived() : HasMany
    {
        return $this->hasMany(Report::class, 'reported_id');
    }
    public function chatMessagesSent() : HasMany
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }
    public function chatMessagesReceived() : HasMany
    {
        return $this->hasMany(ChatMessage::class, 'receiver_id');
    }
    public function verificationRequests() : HasMany
    {
        return $this->hasMany(VerificationRequest::class, 'user_id');
    }
    public function offers() : HasMany
    {
        return $this->hasMany(Offer::class, 'worker_id');
    }
}
