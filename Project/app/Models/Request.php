<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Request extends Model
{
    /** @use HasFactory<\Database\Factories\RequestFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'location',
        'requester_id',
        'status',
        'start_time',
        'end_time',
    ];
    protected $attributes = [
        'status' => 'open',
    ];
    protected $casts = [
        'price' => 'decimal:2',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'request_id');
    }
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'request_id');
    }
    public function chatRooms(): HasMany
    {
        return $this->hasMany(ChatRoom::class, 'request_id');
    }

    public static function hireAndFinalize(Request $request, User $worker): ChatRoom
    {
        // Cari atau buat ChatRoom pemenang
        $winningChatRoom = ChatRoom::firstOrCreate([
            'request_id'   => $request->id,
            'worker_id'    => $worker->id,
            'requester_id' => $request->requester_id,
        ], ['is_open' => true]);

        // Pastikan room pemenang terbuka
        $winningChatRoom->update(['is_open' => true]);

        // Tutup semua room lainnya
        $request->chatRooms()->where('id', '!=', $winningChatRoom->id)->update(['is_open' => false]);

        // Tutup request
        $request->update(['status' => 'closed']);

        $request->transactions()->create([
            'request_id'   => $request->id, // Mengambil ID request
            'requester_id' => $request->requester_id,
            'worker_id'    => $worker->id, // Mengambil harga final dari request
            'status'       => 'accepted',      // Status awal transaksi/ Catat waktu kesepakatan terjadi
        ]);

        // Kembalikan chat room pemenang untuk keperluan redirect
        return $winningChatRoom;
    }
}
