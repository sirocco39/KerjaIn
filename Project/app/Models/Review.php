<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Review extends Model
{
    
    use HasFactory, LogsActivity;
    protected $fillable = [
        'transaction_id',
        'reviewer_id',
        'reviewee_id',
        'rating',
        'comment',
    ];
    protected $casts = [
        'rating' => 'integer',
        'comment' => 'string',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            
            ->logOnly([]) 
            ->useLogName('Review');
    }

    
    public function tapActivity(Activity $activity, string $eventName)
    {
        
        if ($eventName === 'created') {
            
            $reviewerName = $this->reviewer ? $this->reviewer->first_name : 'Pengguna';
            $revieweeName = $this->reviewee ? $this->reviewee->first_name : 'Pengguna';
            $transactionId = $this->transaction ? $this->transaction->order_number : 'N/A';
            $rating = $this->rating;

            $activity->description = "{$reviewerName} telah memberikan ulasan {$rating} bintang kepada {$revieweeName} untuk transaksi #{$transactionId}.";
        }
    }

    public function transaction() : BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
    public function reviewer() : BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
    public function reviewee() : BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }
}
