<?php

namespace App\Livewire\JobRequester;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\Offer;
use App\Models\Request;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chat extends Component
{
    public $requestsWithChats = [];
    public $expandedRequestId = null;
    public $selectedChatRoomId = null;
    public ?ChatRoom $chatRoom = null;
    public ?Offer $activeOffer = null; // Properti untuk menyimpan tawaran aktif
    public $newMessage = '';

    public function mount()
    {
        $this->loadRequests();
    }

    /**
     * Memuat permintaan yang dimiliki oleh pengguna yang sedang login,
     * berstatus 'open', dan memiliki setidaknya satu pesan chat atau satu tawaran.
     * Ruang obrolan di dalam setiap permintaan diurutkan berdasarkan aktivitas terbaru
     * (baik itu pesan terakhir atau tawaran terakhir).
     */
    public function loadRequests()
    {
        $this->requestsWithChats = Request::where('requester_id', Auth::id())
            ->where('status', 'open') // Hanya ambil permintaan yang masih terbuka
            // Tambahkan kondisi: permintaan harus memiliki chat ATAU memiliki tawaran
            ->where(function ($query) {
                $query->whereHas('chatRooms.chatMessages')
                    ->orWhereHas('chatRooms.offers');
            })
            // Eager load relasi yang dibutuhkan untuk optimasi performa
            ->with([
                'chatRooms' => function ($query) {
                    // Pastikan relasi 'offers' di-load bersama dengan relasi lainnya
                    $query->with(['worker', 'lastMessage', 'offers']);
                }
            ])
            ->get()
            // Lakukan iterasi pada setiap request untuk mengurutkan ruang obrolannya
            ->each(function ($request) {
                $request->chatRooms = $request->chatRooms->sortByDesc(function ($room) {
                    // Dapatkan timestamp dari pesan terakhir di dalam room
                    $lastMessageTimestamp = optional($room->lastMessage)->created_at;

                    // Dapatkan timestamp dari tawaran terbaru di dalam room
                    // Asumsi: relasi 'offers' sudah di-eager load
                    $lastOfferTimestamp = $room->offers->max('created_at');

                    // Gunakan timestamp yang paling baru (pesan atau tawaran) sebagai dasar pengurutan
                    return max($lastMessageTimestamp, $lastOfferTimestamp);
                });
            });
    }

    public function toggleExpand($requestId)
    {
        $this->expandedRequestId = $this->expandedRequestId === $requestId ? null : $requestId;
    }

    public function selectChat($chatRoomId)
    {
        ChatMessage::where('chat_room_id', $chatRoomId)
            ->where('receiver_id', Auth::id()) // Pastikan hanya update pesan UNTUK kita
            ->whereNull('read_at')          // Hanya yang belum dibaca
            ->update(['read_at' => now()]); // Isi dengan waktu sekarang
        $this->selectedChatRoomId = $chatRoomId;
        $this->chatRoom = ChatRoom::with(['request', 'worker'])->find($chatRoomId);

        $this->loadActiveOffer(); // Panggil method untuk memuat tawaran
        $this->dispatch('scroll-to-bottom');
    }

    // Method baru untuk memuat tawaran aktif
    public function loadActiveOffer()
    {
        if ($this->chatRoom) {
            $this->activeOffer = Offer::where('chat_room_id', $this->chatRoom->id)->where('status', 'open')
                ->latest() // Ambil tawaran yang paling baru
                ->first();
        }
    }

    // Computed property HANYA untuk pesan chat
    public function getMessagesProperty(): Collection
    {
        if (!$this->selectedChatRoomId) {
            return collect();
        }

        return ChatMessage::where('chat_room_id', $this->selectedChatRoomId)
            ->orderBy('created_at')
            ->get()
            ->groupBy(function ($message) {
                return Carbon::parse($message->created_at)->format('Y-m-d');
            });
    }

    // Method untuk merespon tawaran
    public function respondToOffer(Offer $offer, string $response)
    {
        if ($offer->requester_id !== Auth::id() || $offer->status !== 'open' || !in_array($response, ['accepted', 'rejected'])) {
            return; // Validasi keamanan
        }

        $offer->update(['status' => $response]);

        if ($response === 'accepted') {
            $offer->request->update(['price' => $offer->amount]);
            Request::hireAndFinalize($offer->request, $offer->worker);
        }

        $this->loadActiveOffer(); // Perbarui tampilan panel tawaran
    }

    public function send()
    {
        if (!$this->chatRoom || trim($this->newMessage) === '') {
            return;
        }

        ChatMessage::create([
            'chat_room_id' => $this->chatRoom->id,
            'sender_id'    => Auth::id(),
            'receiver_id'  => $this->chatRoom->worker_id,
            'message'      => $this->newMessage,
        ]);

        $this->reset('newMessage');
        $this->dispatch('clear-input');
        $this->dispatch('scroll-to-bottom');
    }

    public function render()
    {
        $this->selectChat($this->selectedChatRoomId); // Pastikan chat room yang dipilih sudah terisi
        return view('livewire.job-requester.chat');
    }
}
