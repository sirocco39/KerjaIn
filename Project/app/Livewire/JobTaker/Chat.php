<?php

namespace App\Livewire\JobTaker;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\Offer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Chat extends Component
{
    public $selectedRoomId = null;
    public ?ChatRoom $selectedRoom = null;
    public ?Offer $activeOffer = null;
    public $newMessage = '';
    public $showChatPanel = false; 
    public $showOfferForm = false;
    public $offerAmount = '';
    public bool $isChatVisibleOnMobile = false;

    public function mount($selectedRoomId = null)
    {
        $this->selectedRoomId = $selectedRoomId;
        if ($this->selectedRoomId) {
            $this->selectRoom($this->selectedRoomId);
        }
    }

    #[On('chat-selected')]
    public function selectRoom($roomId)
    {
        ChatMessage::where('chat_room_id', $roomId)
            ->where('receiver_id', Auth::id()) 
            ->whereNull('read_at')          
            ->update(['read_at' => now()]); 
        

        
        $this->selectedRoomId = $roomId;
        $this->selectedRoom = ChatRoom::with(['request', 'requester'])->find($roomId);
        $this->loadActiveOffer();

        
        $this->isChatVisibleOnMobile = true;

        $this->dispatch('scroll-to-bottom');
        $this->showChatPanel = true; 
    }

    public function backToChatList()
    {
        $this->showChatPanel = false;
        $this->selectedRoomId = null;
        $this->selectedRoom = null;
    }

    public function hideChatOnMobile()
    {
        $this->isChatVisibleOnMobile = false;
    }
    public function loadActiveOffer()
    {
        if ($this->selectedRoom) {
            $this->activeOffer = Offer::where('chat_room_id', $this->selectedRoom->id)
                ->latest()
                ->first();
        }
    }

    public function deleteOffer()
    {
        if ($this->activeOffer) {
            $this->activeOffer->delete();
            $this->activeOffer = null; 
        }
        $this->loadActiveOffer();
    }

    public function getMessagesProperty(): Collection
    {
        if (!$this->selectedRoom) {
            return collect();
        }

        return ChatMessage::where('chat_room_id', $this->selectedRoom->id)
            ->orderBy('created_at')
            ->get()
            ->groupBy(fn($message) => Carbon::parse($message->created_at)->format('Y-m-d'));
    }

    public function send()
    {
        if (!$this->selectedRoom || trim($this->newMessage) === '') return;

        ChatMessage::create([
            'chat_room_id' => $this->selectedRoom->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedRoom->requester_id,
            'message' => $this->newMessage,
        ]);

        $this->reset('newMessage');
        $this->dispatch('clear-input');
        $this->dispatch('scroll-to-bottom');
    }

    public function toggleOfferForm()
    {
        $this->showOfferForm = !$this->showOfferForm;
        $this->reset('offerAmount');
    }

    public function makeOffer()
    {
        $this->validate(['offerAmount' => 'required|numeric|min:1000']);
        if (!$this->selectedRoom) return;
        $this->selectedRoom->offers()->create([
            'request_id'   => $this->selectedRoom->request_id,
            'requester_id' => $this->selectedRoom->requester_id,
            'worker_id'    => Auth::id(),
            'amount'       => $this->offerAmount,
            'status'       => 'open',
        ]);
        $this->showOfferForm = false;
        $this->reset('offerAmount');
        $this->loadActiveOffer();
    }

    public function render()
    {
        
        $userId = Auth::id();

        $chatRooms = ChatRoom::where('worker_id', $userId)->where('is_open', true)
            
            ->where(function ($query) {
                $query->whereHas('chatMessages')
                    ->orWhereHas('offers');
            })
            ->with(['request', 'requester', 'lastMessage', 'offers',])
            ->get()
            ->sortByDesc(function ($room) {
                $lastMessageTime = optional($room->lastMessage)->created_at;
                $lastOfferTime = optional($room->offers->last())->created_at;
                return max($lastMessageTime, $lastOfferTime);
            });

        
        
        
        if ($this->selectedRoomId && !$chatRooms->contains('id', $this->selectedRoomId)) {
            
            $selectedRoomObject = ChatRoom::with(['request', 'requester', 'lastMessage'])
                ->find($this->selectedRoomId);

            
            if ($selectedRoomObject) {
                $chatRooms->prepend($selectedRoomObject);
            }
        }
        

        
        return view('livewire.job-taker.chat', [
            'chatRooms' => $chatRooms,
        ]);
    }
}
