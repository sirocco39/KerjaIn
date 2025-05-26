<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    /** @use HasFactory<\Database\Factories\ReportFactory> */
    use HasFactory;
    protected $fillable = [
        'transaction_id',
        'reporter_id',
        'reported_id',
        'reasons',
        'status',
    ];
    protected $attributes = [
        'status' => 'Not Reviewed',
    ];

    public function transaction() : BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function reporter() : BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }
    
    public function reported() : BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_id');
    }
}
