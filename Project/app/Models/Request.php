<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Request extends Model
{
    
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'final_price', 
        'service_fee', 
        'location',
        'requester_id',
        'status',
        'start_time',
        'end_time',
    ];
    protected $attributes = [
        'status' => 'open',
        'service_fee' => 2500, 
    ];
    protected $casts = [
        'price' => 'decimal:2',
        'final_price' => 'decimal:2',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'title',
                'description',
                'price',
                'location',
                'start_time',
                'end_time',
                'status'
            ])
            ->logOnlyDirty()
            ->dontLogIfAttributesChangedOnly(['status'])
            ->useLogName('Request');
    }

    
    public function tapActivity(Activity $activity, string $eventName)
    {
        $causerName = $activity->causer ? $activity->causer->first_name : 'Sistem';

        if ($eventName === 'created') {
            $activity->description = "Pekerjaan baru '{$this->title}' telah dibuat oleh {$causerName}.";
            return;
        }

        if ($eventName === 'deleted') {
            $activity->description = "Pekerjaan '{$this->title}' telah dibatalkan/dihapus oleh {$causerName}.";
            return;
        }

        if ($eventName === 'updated') {
            $changesList = [];
            $old = $activity->properties['old'] ?? [];
            $new = $activity->properties['attributes'] ?? [];

            
            $fieldTranslations = [
                'title' => 'Judul',
                'description' => 'Deskripsi',
                'price' => 'Harga',
                'location' => 'Lokasi',
                'start_time' => 'Waktu Mulai',
                'end_time' => 'Waktu Selesai',
            ];

            foreach ($new as $field => $newValue) {
                if (isset($old[$field])) {
                    $fieldName = $fieldTranslations[$field] ?? $field;
                    $oldValue = $old[$field];

                    
                    if ($field === 'price') {
                        $oldValue = 'Rp' . number_format($oldValue, 0, ',', '.');
                        $newValue = 'Rp' . number_format($newValue, 0, ',', '.');
                    }

                    
                    if (in_array($field, ['start_time', 'end_time'])) {
                        $oldValue = \Carbon\Carbon::parse($oldValue)->format('d M Y, H:i');
                        $newValue = \Carbon\Carbon::parse($newValue)->format('d M Y, H:i');
                    }

                    $changesList[] = "{$fieldName}: dari '{$oldValue}' menjadi '{$newValue}'";
                }
            }

            if (!empty($changesList)) {
                
                $details = '<ul>' . implode('', $changesList) . '</ul>';
                $activity->description = "{$causerName} telah memperbarui detail pekerjaan '{$this->title}':<br>{$details}";
            } else {
                $activity->description = "Detail pekerjaan '{$this->title}' telah diperbarui oleh {$causerName}.";
            }
        }
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }
    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class, 'request_id');
    }
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class, 'request_id');
    }
    public function chatRooms(): HasMany
    {
        return $this->hasMany(ChatRoom::class, 'request_id');
    }

    public static function hireAndFinalize(Request $request, User $worker): Transaction 
    {
        
        $winningChatRoom = ChatRoom::firstOrCreate([
            'request_id'     => $request->id,
            'worker_id'       => $worker->id,
            'requester_id' => $request->requester_id,
        ], ['is_open' => true]);

        
        $winningChatRoom->update(['is_open' => true]);

        
        $request->chatRooms()->where('id', '!=', $winningChatRoom->id)->update(['is_open' => false]);

        
        $request->disableLogging();

        
        $request->update(['status' => 'closed']);

        
        $request->enableLogging();

        
        
        $requestIdPart      = str_pad($request->id % 1000, 3, '0', STR_PAD_LEFT);
        $requesterIdPart    = str_pad($request->requester_id % 1000, 3, '0', STR_PAD_LEFT);
        $workerIdPart       = str_pad($worker->id % 1000, 3, '0', STR_PAD_LEFT);

        
        $baseNumber = $requestIdPart . $requesterIdPart . $workerIdPart;

        
        
        $timeSuffix = substr(time(), -3);

        
        $orderNumber = $baseNumber . $timeSuffix;

        
        
        while (\App\Models\Transaction::where('order_number', $orderNumber)->exists()) {
            usleep(1000); 
            $timeSuffix = substr(time(), -3);
            $orderNumber = $baseNumber . $timeSuffix;
        }

        
        $transaction = $request->transaction()->create([
            'order_number' => $orderNumber,
            'request_id'   => $request->id,
            'requester_id' => $request->requester_id,
            'worker_id'    => $worker->id,
            'status'       => 'accepted',
        ]);
        
        return $transaction;
    }
}
