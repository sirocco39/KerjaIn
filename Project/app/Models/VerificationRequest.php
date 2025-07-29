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
    // soft delete
    use SoftDeletes, HasFactory, LogsActivity;

    // created_at dan updated_at ototmatis dibuat oleh laravel
    // fillable yang berarti akan diisi oleh user ataupun berasal dari API atau tabel lain
    protected $fillable = [
        'user_id',
        'verified_at',
        'status',
        'first_name', // User Input
        'last_name',  // User Input
        'nik',        // User Input
        'birthdate',  // User Input
        'gender',     // User Input
        'address',    // User Input
        'phone_number', // User Input
        'photo_url',
        'id_card_url',
        'selfie_with_id_card_url',
        'account_name',
        'account_number',

        // OCR-extracted fields (only the ones you want to keep)
        'ocr_nik',
        'ocr_full_name',
        'ocr_birthdate',
        'ocr_gender',
        'ocr_address', // Unified address field
        'ocr_raw_output',
    ];

    // protected $casts untuk mengubah tipe data dari database ke tipe data yang sesuai di PHP
    protected $casts = [
        'verified_at' => 'datetime',
        'birthdate' => 'date',
        'ocr_birthdate' => 'date', // Keep this if you want it casted
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            // Kita hanya perlu memantau perubahan pada kolom 'status'.
            ->logOnly(['status'])
            ->logOnlyDirty()
            ->useLogName('Verification');
    }

    // 4. METHOD UNTUK DESKRIPSI KUSTOM
    public function tapActivity(Activity $activity, string $eventName)
    {
        // Karena aksi ini kemungkinan besar dilakukan oleh admin, kita cek siapa pelakunya.
        $causerName = $activity->causer ? $activity->causer->first_name : 'Sistem';

        if ($eventName === 'created') {
            // 'user' adalah relasi ke model User
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

    // ini berarti satu user dapat mengajukan banyak verification request
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public $timestamps = true; // pengaturan created_at dan updated_at otomatis dibuat oleh laravel
}
