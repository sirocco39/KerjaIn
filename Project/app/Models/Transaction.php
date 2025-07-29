<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth; // Don't forget to import Auth
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Transaction extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
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

    // This relationship retrieves ALL reviews for a transaction (many)
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'transaction_id');
    }

    // This relationship retrieves a single review made by the *current requester* for this transaction
    // This is primarily for the Job Requester's side.
    public function userReview(): HasOne
    {
        return $this->hasOne(Review::class, 'transaction_id')
            ->where('reviewer_id', Auth::id()); // Filter by the authenticated user as the reviewer
    }

    // NEWLY ADDED: Retrieves a single review given TO this worker for this transaction
    // This is for the Job Taker's perspective to see reviews they received.
    public function reviewAboutWorker(): HasOne
    {
        return $this->hasOne(Review::class, 'transaction_id', 'id')
            ->where('reviewee_id', Auth::id()); // Reviewee is the worker (current authenticated user)
    }

    public function reviewAboutRequester(): HasOne
    {
        return $this->hasOne(Review::class, 'transaction_id', 'id')
            ->where('reviewee_id', $this->requester_id); // Reviewee is the requester
    }

    public function completionProof(): HasOne
    {
        return $this->hasOne(CompletionProof::class, 'transaction_id');
    }

    // This is the general report relationship (if a transaction has one main report)
    public function report(): HasOne
    {
        return $this->hasOne(Report::class, 'transaction_id');
    }

    // --- ADDED THIS RELATIONSHIP TO FIX THE ERROR ---
    // This relationship retrieves a single report made by the *current authenticated user* for this transaction
    public function userReport(): HasOne
    {
        return $this->hasOne(Report::class, 'transaction_id', 'id')->where('reporter_id', Auth::id());
    }
    // --- END ADDED RELATIONSHIP ---

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
