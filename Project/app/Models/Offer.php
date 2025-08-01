<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Offer extends Model
{
    
    use HasFactory;
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'request_id',
        'chat_room_id',
        'requester_id',
        'worker_id',
        'amount',
        'status',
    ];
    protected $casts = [
        'amount' => 'decimal:2',
        'status' => 'string',
    ];

       public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            
            ->logOnly(['amount', 'status'])
            ->logOnlyDirty()
            ->useLogName('Offer');
    }

    
    public function tapActivity(Activity $activity, string $eventName)
    {
        $causerName = $activity->causer ? $activity->causer->first_name : 'Sistem';
        
        $jobTitle = $this->request ? $this->request->title : 'pekerjaan yang telah dihapus';
        $amountFormatted = 'Rp' . number_format($this->amount, 0, ',', '.');

        if ($eventName === 'created') {
            $activity->description = "Pekerja {$causerName} telah mengajukan penawaran sebesar {$amountFormatted} untuk pekerjaan '{$jobTitle}'.";
        }

        if ($eventName === 'deleted') {
            $activity->description = "Pekerja {$causerName} telah menarik kembali penawarannya untuk pekerjaan '{$jobTitle}'.";
        }

        if ($eventName === 'updated') {
            $newStatus = $this->getDirty()['status'] ?? null;

            if ($newStatus === 'accepted') {
                $activity->description = "Requester {$causerName} telah menerima penawaran sebesar {$amountFormatted} untuk pekerjaan '{$jobTitle}'.";
            } elseif ($newStatus === 'rejected') {
                $activity->description = "Requester {$causerName} telah menolak penawaran sebesar {$amountFormatted} untuk pekerjaan '{$jobTitle}'.";
            }
        }
    }
    
    public function chatRoom() : BelongsTo
    {
        return $this->belongsTo(ChatRoom::class);
    }

    public function request() : BelongsTo
    {
        return $this->belongsTo(Request::class);
    }
    public function worker() : BelongsTo
    {
        return $this->belongsTo(User::class, 'worker_id');
    }
}
