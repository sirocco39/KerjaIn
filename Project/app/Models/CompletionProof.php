<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CompletionProof extends Model
{
    /** @use HasFactory<\Database\Factories\CompletionProofFactory> */
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'transaction_id',
        'photo_url',
        'note',
        'submitted_at',
    ];

    public function transaction() : HasOne
    {
        return $this->hasOne(Transaction::class, 'id', 'transaction_id');
    }

}
