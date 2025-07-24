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
    /** @use HasFactory<\Database\Factories\RequestFactory> */
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'final_price', // Added final_price to fillable
        'service_fee', // Added service_fee to fillable
        'location',
        'requester_id',
        'status',
        'start_time',
        'end_time',
    ];
    protected $attributes = [
        'status' => 'open',
        'service_fee' => 2500, // Default admin fee
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

    // 4. METHOD UNTUK DESKRIPSI KUSTOM (VERSI FINAL)
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

            // Terjemahan nama kolom
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

                    // Format khusus untuk harga
                    if ($field === 'price') {
                        $oldValue = 'Rp' . number_format($oldValue, 0, ',', '.');
                        $newValue = 'Rp' . number_format($newValue, 0, ',', '.');
                    }

                    // Format khusus untuk waktu
                    if (in_array($field, ['start_time', 'end_time'])) {
                        $oldValue = \Carbon\Carbon::parse($oldValue)->format('d M Y, H:i');
                        $newValue = \Carbon\Carbon::parse($newValue)->format('d M Y, H:i');
                    }

                    $changesList[] = "<li><strong>{$fieldName}:</strong> dari '{$oldValue}' menjadi '{$newValue}'</li>";
                }
            }

            if (!empty($changesList)) {
                // Gabungkan semua perubahan menjadi satu blok HTML
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

    public static function hireAndFinalize(Request $request, User $worker): Transaction // Ubah return type menjadi Transaction
    {
        // Cari atau buat ChatRoom pemenang
        $winningChatRoom = ChatRoom::firstOrCreate([
            'request_id'     => $request->id,
            'worker_id'       => $worker->id,
            'requester_id' => $request->requester_id,
        ], ['is_open' => true]);

        // Pastikan room pemenang terbuka
        $winningChatRoom->update(['is_open' => true]);

        // Tutup semua room lainnya
        $request->chatRooms()->where('id', '!=', $winningChatRoom->id)->update(['is_open' => false]);

        // Tutup request
        $request->disableLogging();

        // 2. Tutup request (aksi ini TIDAK akan dicatat di log)
        $request->update(['status' => 'closed']);

        // 3. Nyalakan kembali logging untuk aksi-aksi berikutnya
        $request->enableLogging();

        // 1. Ambil 3 digit terakhir dari setiap ID.
        //    Menggunakan modulo (%) memastikan ID yang besar tetap menjadi 3 digit.
        $requestIdPart      = str_pad($request->id % 1000, 3, '0', STR_PAD_LEFT);
        $requesterIdPart    = str_pad($request->requester_id % 1000, 3, '0', STR_PAD_LEFT);
        $workerIdPart       = str_pad($worker->id % 1000, 3, '0', STR_PAD_LEFT);

        // 2. Gabungkan bagian-bagian ID untuk membentuk 9 digit pertama.
        $baseNumber = $requestIdPart . $requesterIdPart . $workerIdPart;

        // 3. Tambahkan 3 digit dari timestamp untuk keunikan.
        //    Ini mengambil 3 angka terakhir dari detik Unix saat ini.
        $timeSuffix = substr(time(), -3);

        // 4. Gabungkan menjadi nomor order 12 digit.
        $orderNumber = $baseNumber . $timeSuffix;

        // 5. (Pengaman) Pastikan nomor ini belum ada di database.
        //    Ini untuk menangani kasus yang sangat langka jika 2 transaksi terjadi di milidetik yang sama.
        while (\App\Models\Transaction::where('order_number', $orderNumber)->exists()) {
            usleep(1000); // Tunggu 1 milidetik
            $timeSuffix = substr(time(), -3);
            $orderNumber = $baseNumber . $timeSuffix;
        }

        // 6. Gunakan nomor order yang sudah unik.
        $transaction = $request->transaction()->create([
            'order_number' => $orderNumber,
            'request_id'   => $request->id,
            'requester_id' => $request->requester_id,
            'worker_id'    => $worker->id,
            'status'       => 'accepted',
        ]);
        // Kembalikan object transaction
        return $transaction;
    }
}
