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
    public $newMessage = ''; // <-- TAMBAHKAN BARIS INI
    public function mount()
    {
        $this->loadRequests();
    }

    public function loadRequests()
    {
        $this->requestsWithChats = Request::where('requester_id', Auth::id())
            ->where('status', 'open') // Hanya ambil permintaan yang masih terbuka
            ->whereHas('chatRooms.chatMessages')
            ->with(['chatRooms.worker', 'chatRooms.lastMessage'])
            ->get()
            ->each(function ($request) {
                $request->chatRooms = $request->chatRooms->sortByDesc(fn($room) => optional($room->lastMessage)->created_at);
            });
    }

    public function toggleExpand($requestId)
    {
        $this->expandedRequestId = $this->expandedRequestId === $requestId ? null : $requestId;
    }

    public function selectChat($chatRoomId)
    {
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

        $offer->update(['status' => 'closed']);

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
        return view('livewire.job-requester.chat');
    }
}
