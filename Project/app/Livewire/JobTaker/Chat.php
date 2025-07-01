<?php

namespace App\Livewire\JobTaker;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chat extends Component
{
    public $selectedRoomId = null;
    public $chatRooms;
    public $selectedRoom = null;
    public $body = '';

    public function mount($selectedRoomId = null)
    {
        $this->selectedRoomId = $selectedRoomId;
        // Ambil semua room yang punya pesan
        $this->chatRooms = ChatRoom::with(['request', 'requester', 'lastMessage'])
            ->where('worker_id', Auth::id())
            ->whereHas('chatMessages')
            ->get()
            ->sortByDesc(fn($room) => optional($room->lastMessage)->created_at)
            ->values();
        // Jika ada selectedRoomId dan belum masuk list, tambahkan manual
        if ($selectedRoomId && !$this->chatRooms->contains('id', $selectedRoomId)) {
            $extraRoom = ChatRoom::with(['request', 'requester'])
                ->where('worker_id', Auth::id())
                ->find($selectedRoomId);

            if ($extraRoom) {
                $this->chatRooms->prepend($extraRoom);
            }
        } elseif ($selectedRoomId) {
            // Room udah ada, pindahin ke atas
            $this->chatRooms = $this->chatRooms->sortByDesc(function ($room) use ($selectedRoomId) {
                return $room->id === $selectedRoomId ? now()->addSecond() : optional($room->lastMessage)->created_at;
            })->values();
        }
        if ($selectedRoomId) {
            $this->selectRoom($selectedRoomId);
        }
    }

    public function getMessagesProperty(): Collection
    {
        if (!$this->selectedRoom) {
            return collect();
        }

        return ChatMessage::where('chat_room_id', $this->selectedRoom->id)
            ->orderBy('created_at')
            ->get()
            ->groupBy(function ($message) {
                return Carbon::parse($message->created_at)->format('Y-m-d');
            });
    }

    public function selectRoom($roomId)
    {
        $this->selectedRoom = ChatRoom::with(['request', 'requester'])->find($roomId);

        // Kirim event ke browser untuk scroll ke bawah
        $this->dispatch('scroll-to-bottom');
    }

    public function send()
    {
        if (!$this->selectedRoom || trim($this->body) === '') return;

        ChatMessage::create([
            'chat_room_id' => $this->selectedRoom->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedRoom->requester_id,
            'message' => $this->body,
        ]);

        $this->reset('body');

        // Kirim event ke browser untuk scroll ke bawah
        $this->dispatch('scroll-to-bottom');
    }

    public function render()
    {
        return view('livewire.job-taker.chat');
    }
}
