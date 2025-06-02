<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory;
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
