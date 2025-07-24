<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Report extends Model
{
    /** @use HasFactory<\Database\Factories\ReportFactory> */
    use HasFactory, LogsActivity;
    protected $fillable = [
        'transaction_id',
        'reporter_id',
        'reported_id',
        'photo_url',
        'reasons',
        'status',
    ];
    protected $attributes = [
        'status' => 'Not Reviewed',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            // Kita hanya perlu memantau perubahan pada kolom 'status'.
            ->logOnly(['status'])
            ->logOnlyDirty()
            ->useLogName('Report');
    }

    // 4. METHOD UNTUK DESKRIPSI KUSTOM
    public function tapActivity(Activity $activity, string $eventName)
    {
        $causerName = $activity->causer ? $activity->causer->first_name : 'Sistem';
        // Ambil data dari relasi untuk deskripsi yang lebih kaya
        $reporterName = $this->reporter ? $this->reporter->first_name : 'Pengguna';
        $reportedName = $this->reported ? $this->reported->first_name : 'Pengguna';
        $transactionId = $this->transaction ? $this->transaction->order_number : 'N/A';

        if ($eventName === 'created') {
            $activity->description = "Laporan baru dibuat oleh {$reporterName} terhadap {$reportedName} untuk transaksi #{$transactionId}.";
        }

        if ($eventName === 'updated') {
            $newStatus = $this->getDirty()['status'] ?? null;
            if ($newStatus === 'Reviewed') {
                $activity->description = "Laporan terhadap {$reportedName} (transaksi #{$transactionId}) telah ditinjau oleh {$causerName}.";
            }
        }
    }


    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reported(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_id');
    }
}
