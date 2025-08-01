<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class VerificationRequest extends Model
{
    
    use SoftDeletes, HasFactory, LogsActivity;

    
    
    protected $fillable = [
        'user_id',
        'verified_at',
        'status',
        'first_name', 
        'last_name',  
        'nik',        
        'birthdate',  
        'gender',     
        'address',    
        'phone_number', 
        'photo_url',
        'id_card_url',
        'selfie_with_id_card_url',
        'account_name',
        'account_number',

        
        'ocr_nik',
        'ocr_full_name',
        'ocr_birthdate',
        'ocr_gender',
        'ocr_address', 
        'ocr_raw_output',
    ];

    
    protected $casts = [
        'verified_at' => 'datetime',
        'birthdate' => 'date',
        'ocr_birthdate' => 'date', 
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            
            ->logOnly(['status'])
            ->logOnlyDirty()
            ->useLogName('Verification');
    }

    
    public function tapActivity(Activity $activity, string $eventName)
    {
        
        $causerName = $activity->causer ? $activity->causer->first_name : 'Sistem';

        if ($eventName === 'created') {
            
            $requesterName = $this->user ? $this->user->first_name : $this->first_name;
            $activity->description = "Permintaan verifikasi baru telah diajukan oleh {$requesterName}.";
        }

        if ($eventName === 'updated') {
            $newStatus = $this->getDirty()['status'] ?? null;
            $requesterName = $this->user ? $this->user->first_name : $this->first_name;

            if ($newStatus === 'approved') {
                $activity->description = "Permintaan verifikasi untuk {$requesterName} telah disetujui oleh {$causerName}.";
            } elseif ($newStatus === 'rejected') {
                $activity->description = "Permintaan verifikasi untuk {$requesterName} telah ditolak oleh {$causerName}.";
            }
        }
    }

    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public $timestamps = true; 
}
