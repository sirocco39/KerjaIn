<?php

namespace App\Livewire\JobTaker;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\Offer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chat extends Component
{
    public $selectedRoomId = null;
    public $chatRooms;
    public ?ChatRoom $selectedRoom = null;
    public ?Offer $activeOffer = null; // Properti untuk tawaran aktif
    public $newMessage = '';

    // Properti untuk form tawaran
    public $showOfferForm = false;
    public $offerAmount = '';

    public function mount($selectedRoomId = null)
    {
        $this->selectedRoomId = $selectedRoomId;
        // ... (logika mount Anda yang sudah ada) ...
        $existingChatRooms = ChatRoom::where('worker_id', Auth::id())
            ->where('is_open', true)
            ->whereHas('chatMessages')
            ->when($selectedRoomId, function ($query) use ($selectedRoomId) {
                // Pastikan tidak ada duplikasi jika room yang dipilih juga punya pesan
                $query->where('id', '!=', $selectedRoomId);
            })
            ->with(['request', 'requester', 'lastMessage'])
            ->get()
            ->sortByDesc(fn($room) => optional($room->lastMessage)->created_at);

        // 2. Jika ada ID room yang dipilih, ambil datanya secara terpisah.
        if ($selectedRoomId) {
            $selectedRoomObject = ChatRoom::with(['request', 'requester', 'lastMessage'])
                ->find($selectedRoomId);

            // 3. Jika room tersebut ditemukan, letakkan di paling atas daftar.
            if ($selectedRoomObject) {
                $existingChatRooms->prepend($selectedRoomObject);
            }
        }

        // 4. Tetapkan daftar final dan panggil selectRoom jika perlu.
        $this->chatRooms = $existingChatRooms->values();

        if ($selectedRoomId) {
            $this->selectRoom($selectedRoomId);
        }
    }

    public function selectRoom($roomId)
    {
        $this->selectedRoom = ChatRoom::with(['request', 'requester'])->find($roomId);
        $this->loadActiveOffer(); // Muat tawaran saat chat dipilih
        $this->dispatch('scroll-to-bottom');
    }

    // Memuat tawaran aktif
    public function loadActiveOffer()
    {
        if ($this->selectedRoom) {
            $this->activeOffer = Offer::where('chat_room_id', $this->selectedRoom->id)
                ->latest()
                ->first();
        }
    }

    // Computed property untuk pesan chat
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

    // Method untuk menampilkan/menyembunyikan form tawaran
    public function toggleOfferForm()
    {
        $this->showOfferForm = !$this->showOfferForm;
        $this->reset('offerAmount');
    }

    // Method untuk membuat tawaran
    public function makeOffer()
    {
        $this->validate([
            'offerAmount' => 'required|numeric|min:1000',
        ]);

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
        $this->loadActiveOffer(); // Muat ulang tawaran setelah dibuat
    }

    // Method untuk mengirim pesan biasa
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

    public function render()
    {
        return view('livewire.job-taker.chat');
    }
}
