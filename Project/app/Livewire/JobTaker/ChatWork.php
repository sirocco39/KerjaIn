<?php

namespace App\Livewire\JobTaker;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\Offer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ChatWork extends Component
{
    public $selectedRoomId;
    public ?ChatRoom $selectedRoom = null;
    public ?Offer $activeOffer = null;
    public $newMessage = '';

    public function mount($selectedRoomId = null)
    {
        $this->selectedRoomId = $selectedRoomId;
        if ($this->selectedRoomId) {
            $this->selectRoom($this->selectedRoomId);
        }
    }

    public function selectRoom($roomId)
    {
        ChatMessage::where('chat_room_id', $roomId)
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->selectedRoomId = $roomId;
        $this->selectedRoom = ChatRoom::with(['request', 'requester', 'worker'])->find($roomId);
        $this->loadActiveOffer();

        
        $this->dispatch('messageSent');
    }

    public function loadActiveOffer()
    {
        if ($this->selectedRoom) {
            $this->activeOffer = Offer::where('chat_room_id', $this->selectedRoom->id)
                ->latest()
                ->first();
        }
    }

    public function getMessagesProperty(): Collection
    {
        if (!$this->selectedRoom) return collect();

        return ChatMessage::where('chat_room_id', $this->selectedRoom->id)
            ->orderBy('created_at')
            ->get()
            ->groupBy(fn($msg) => Carbon::parse($msg->created_at)->format('Y-m-d'));
    }

    public function send()
    {
        if (!$this->selectedRoom || trim($this->newMessage) === '') return;

        ChatMessage::create([
            'chat_room_id' => $this->selectedRoom->id,
            'sender_id'    => Auth::id(),
            'receiver_id' => $this->selectedRoom->request->requester_id,
            'message'      => $this->newMessage,
        ]);

        $this->reset('newMessage'); 
        $this->dispatch('messageSent'); 
    }

    public function render()
    {
        return view('livewire.job-taker.chat-work');
    }
}
