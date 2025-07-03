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
    public ?ChatRoom $selectedRoom = null;
    public ?Offer $activeOffer = null;
    public $newMessage = '';

    public $showOfferForm = false;
    public $offerAmount = '';

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
            ->where('receiver_id', Auth::id()) // Pastikan hanya update pesan UNTUK kita
            ->whereNull('read_at')          // Hanya yang belum dibaca
            ->update(['read_at' => now()]); // Isi dengan waktu sekarang
        // --- AKHIR LOGIKA BARU ---

        // 2. Lanjutkan sisa logika seperti biasa.
        $this->selectedRoomId = $roomId;
        $this->selectedRoom = ChatRoom::with(['request', 'requester'])->find($roomId);
        $this->loadActiveOffer();
        $this->dispatch('scroll-to-bottom');
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
        // 1. Ambil daftar chat room yang sudah ada pesannya.
        $this->selectRoom($this->selectedRoomId);
        $chatRooms = ChatRoom::where('worker_id', Auth::id())
            ->where('is_open', true)
            ->where(function ($query) {
                $query->whereHas('chatMessages')      // di mana memiliki pesan
                    ->orWhereHas('offers');         // ATAU di mana memiliki penawaran
            })
            ->with(['request', 'requester', 'lastMessage', 'offers']) // Tambahkan 'offers'
            ->get()
            ->sortByDesc(function ($room) {
                // Logika sorting disesuaikan untuk mempertimbangkan keduanya
                $lastMessageTime = optional($room->lastMessage)->created_at;
                $lastOfferTime = optional($room->offers->last())->created_at;
                return max($lastMessageTime, $lastOfferTime);
            });

        // --- LOGIKA BARU DITAMBAHKAN DI SINI ---
        // 2. Cek apakah room yang sedang dipilih ada di daftar.
        //    Ini penting jika room baru dibuat dan belum punya pesan.
        if ($this->selectedRoomId && !$chatRooms->contains('id', $this->selectedRoomId)) {
            // Jika tidak ada, ambil datanya secara manual.
            $selectedRoomObject = ChatRoom::with(['request', 'requester', 'lastMessage'])
                ->find($this->selectedRoomId);

            // Jika room ditemukan, tambahkan ke paling atas daftar.
            if ($selectedRoomObject) {
                $chatRooms->prepend($selectedRoomObject);
            }
        }
        // --- AKHIR LOGIKA BARU ---

        // 3. Kirim data yang sudah lengkap ke view.
        return view('livewire.job-taker.chat', [
            'chatRooms' => $chatRooms,
        ]);
    }
}
