<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth; 
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Transaction extends Model
{
    
    use HasFactory, SoftDeletes, LogsActivity;

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
        'price' => 'decimal:2',
        'start_work' => 'datetime',
        'finish_work' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'start_work', 'finish_work'])
            ->logOnlyDirty()
            ->useLogName('Transaction');
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $causerName = $activity->causer ? $activity->causer->first_name : 'Sistem';
        $transactionId = $this->order_number;

        if ($eventName === 'updated') {
            $newAttributes = $activity->subject->getDirty();
            if (isset($newAttributes['status'])) {
                $status = $newAttributes['status'];

                switch ($status) {
                    case 'in progress':
                        $startTime = Carbon::parse($this->start_work)->format('d M Y, H:i');
                        $activity->description = "Pekerja {$causerName} telah mulai bekerja untuk transaksi #{$transactionId} pada {$startTime}.";
                        break;
                    case 'submitted':
                        $finishTime = Carbon::parse($this->finish_work)->format('d M Y, H:i');
                        $activity->description = "Pekerja {$causerName} telah menyelesaikan pekerjaan untuk transaksi #{$transactionId} pada {$finishTime}.";
                        break;
                    case 'completed':
                        $activity->description = "Requester {$causerName} telah menyelesaikan transaksi #{$transactionId}.";
                        break;
                    case 'cancelled':
                        $activity->description = "Transaksi #{$transactionId} telah dibatalkan oleh {$causerName}.";
                        break;
                }
            }
        } elseif ($eventName === 'created') {
            $workerName = $this->worker ? $this->worker->first_name : 'N/A';
            $activity->description = "Pekerjaan untuk transaksi #{$transactionId} telah diterima oleh pekerja {$workerName}.";
        }
    }

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

    
    
    public function userReview(): HasOne
    {
        return $this->hasOne(Review::class, 'transaction_id')
            ->where('reviewer_id', Auth::id()); 
    }

    
    
    public function reviewAboutWorker(): HasOne
    {
        return $this->hasOne(Review::class, 'transaction_id', 'id')
            ->where('reviewee_id', Auth::id()); 
    }

    public function reviewAboutRequester(): HasOne
    {
        return $this->hasOne(Review::class, 'transaction_id', 'id')
            ->where('reviewee_id', $this->requester_id); 
    }

    public function completionProof(): HasOne
    {
        return $this->hasOne(CompletionProof::class, 'transaction_id');
    }

    
    public function report(): HasOne
    {
        return $this->hasOne(Report::class, 'transaction_id');
    }

    
    
    public function userReport(): HasOne
    {
        return $this->hasOne(Report::class, 'transaction_id', 'id')->where('reporter_id', Auth::id());
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
        
        $key = 'history-job-req.status.' . $this->status;

        
        return __($key, [], app()->getLocale()) ?? ucfirst($this->status);
    }
}
