<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes, LogsActivity;

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
        'balance',
        'locked_balance',
        'bank_acc_num',
        'google_id',
    ];

    protected $attributes = [
        'role' => 'user',
        'is_worker' => false,
        'rating' => 0,
        'job_done' => 0,
        'is_blocked' => false,
        'balance' => 0,
        'locked_balance' => 0,
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google_id',
    ];

    protected $casts = [
        'is_worker' => 'boolean',
        'is_blocked' => 'boolean',
        'balance' => 'decimal:2',
        'locked_balance' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            // Tentukan kolom mana saja yang ingin kita pantau.
            ->logOnly([
                'is_worker',
                'is_blocked',
            ])
            // Hanya buat log jika ada perubahan pada kolom yang dipantau.
            ->logOnlyDirty()
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->dontSubmitEmptyLogs()
            // Beri nama log spesifik agar mudah difilter.
            ->useLogName('User');
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        // Ambil nama pelaku (causer) untuk deskripsi yang lebih jelas.
        $causerName = $activity->causer ? $activity->causer->first_name : 'Sistem';

        if ($eventName === 'created') {
            $activity->description = "Akun baru untuk {$this->first_name} telah dibuat.";
            return;
        }

        if ($eventName === 'updated') {
            \Illuminate\Support\Facades\Log::info('SPATIE DETECTED CHANGES ON USER MODEL:', [
                'OLD_VALUES' => $activity->properties['old'] ?? [],
                'NEW_VALUES' => $activity->properties['attributes'] ?? [],
            ]);
            // Untuk mendapatkan atribut yang berubah, kita gunakan relasi subject
            $newAttributes = $activity->subject->getDirty();

            if (isset($newAttributes['is_worker'])) {
                $statusPekerja = $newAttributes['is_worker'] ? 'diverifikasi menjadi Pekerja' : 'dihapus status pekerjanya';
                $activity->description = "Pengguna {$this->first_name} telah {$statusPekerja} oleh {$causerName}.";
            } elseif (isset($newAttributes['is_blocked'])) {
                $statusBlokir = $newAttributes['is_blocked'] ? 'diblokir' : 'dibuka blokirnya';
                $activity->description = "Pengguna {$this->first_name} telah {$statusBlokir} oleh {$causerName}.";
            }
        }
    }

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

    public function requests(): HasMany
    {
        return $this->hasMany(Request::class, 'requester_id');
    }
    public function transactionsAsRequester(): HasMany
    {
        return $this->hasMany(Transaction::class, 'requester_id');
    }
    public function transactionsAsWorker(): HasMany
    {
        return $this->hasMany(Transaction::class, 'worker_id');
    }

    public function reviewsGiven(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    public function reviewsReceived(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    public function chatRoomsAsRequester(): HasMany
    {
        return $this->hasMany(ChatRoom::class, 'requester_id');
    }
    public function chatRoomsAsWorker(): HasMany
    {
        return $this->hasMany(ChatRoom::class, 'worker_id');
    }
    public function reportsGiven(): HasMany
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }
    public function reportsReceived(): HasMany
    {
        return $this->hasMany(Report::class, 'reported_id');
    }
    public function chatMessagesSent(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }
    public function chatMessagesReceived(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'receiver_id');
    }
    public function verificationRequests(): HasMany
    {
        return $this->hasMany(VerificationRequest::class, 'user_id');
    }
    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class, 'worker_id');
    }
    public function topUpOrders(): HasMany
    {
        return $this->hasMany(TopUpOrder::class, 'user_id');
    }
    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class, 'user_id');
    }
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
