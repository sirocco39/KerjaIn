<?php

namespace App\Livewire\JobRequester;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
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
    public $chatRoom;
    public $newMessage = '';

    public function mount()
    {
        $this->loadRequests();
    }

    public function loadRequests()
    {
        $this->requestsWithChats = Request::where('requester_id', Auth::id())
            ->whereHas('chatRooms.chatMessages')
            ->with(['chatRooms.worker', 'chatRooms.lastMessage']) // Tambahkan lastMessage untuk sorting
            ->get()
            ->each(function ($request) {
                // Urutkan chat rooms berdasarkan pesan terakhir
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

        // Kirim event ke browser untuk scroll ke bawah
        $this->dispatch('scroll-to-bottom');
    }

    // Menggunakan Computed Property untuk pesan
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

    public function sendMessage()
    {
        if (!$this->chatRoom || trim($this->newMessage) === '') return;

        $this->chatRoom->chatMessages()->create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->chatRoom->worker_id,
            'message' => $this->newMessage,
        ]);

        $this->reset('newMessage');

        // Kirim event ke browser untuk scroll ke bawah
        $this->dispatch('scroll-to-bottom');
    }

    public function render()
    {
        // Tidak ada lagi query di sini, lebih efisien!
        return view('livewire.job-requester.chat');
    }
}