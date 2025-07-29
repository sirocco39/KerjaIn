<?php

namespace App\Livewire\JobRequester;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\Offer;
use App\Models\Request;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\On; // Penting: import atribut On

class Chat extends Component
{
    public $requestsWithChats = [];
    public $expandedRequestId = null;
    public $selectedChatRoomId = null;
    public ?ChatRoom $chatRoom = null;
    public ?Offer $activeOffer = null;
    public $newMessage = '';
    public $showChatPanel = false; // Default: false (tampilkan list chat di mobile)

    public function mount()
    {
        $this->loadRequests();
    }

    public function loadRequests()
    {
        $this->requestsWithChats = Request::where('requester_id', Auth::id())
            ->where(function ($query) {
                $query->whereHas('chatRooms.chatMessages')
                    ->orWhereHas('chatRooms.offers');
            })
            ->with([
                'chatRooms' => function ($query) {
                    $query->with(['worker', 'lastMessage', 'offers']);
                }
            ])
            ->get()
            ->each(function ($request) {
                $request->chatRooms = $request->chatRooms->sortByDesc(function ($room) {
                    $lastMessageTimestamp = optional($room->lastMessage)->created_at;
                    $lastOfferTimestamp = $room->offers->max('created_at');
                    return max($lastMessageTimestamp, $lastOfferTimestamp);
                });
            });
    }

    public function toggleExpand($requestId)
    {
        $this->expandedRequestId = $this->expandedRequestId === $requestId ? null : $requestId;
    }

    // --- MODIFIKASI INI: Tambahkan atribut #[On] ---
    #[On('chat-selected')]
    public function selectChat($chatRoomId)
    {
        ChatMessage::where('chat_room_id', $chatRoomId)
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->selectedChatRoomId = $chatRoomId;
        $this->chatRoom = ChatRoom::with(['request', 'worker'])->find($chatRoomId);
        $this->loadActiveOffer();
        $this->dispatch('scroll-to-bottom');
        $this->dispatch('chatSelected');
        $this->showChatPanel = true; // Tampilkan panel chat di mobile
    }
    // ------------------------------------------------

    public function backToChatList()
    {
        $this->showChatPanel = false;
        $this->selectedChatRoomId = null;
        $this->chatRoom = null;
    }

    public function loadActiveOffer()
    {
        if ($this->chatRoom) {
            $this->activeOffer = Offer::where('chat_room_id', $this->chatRoom->id)->where('status', 'open')
                ->latest()
                ->first();
        }
    }

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

    public function respondToOffer(Offer $offer, string $response)
    {
        if ($offer->requester_id !== Auth::id() || $offer->status !== 'open' || !in_array($response, ['accepted', 'rejected'])) {
            return;
        }

        $offer->update(['status' => $response]);

        if ($response === 'accepted') {
            DB::transaction(function () use ($offer) {
                $workRequest = $offer->request;
                $requester = $workRequest->requester;
                $originalPrice = $workRequest->price; // Harga awal yang di-lock
                $newPrice = $offer->amount;         // Harga baru dari offer

                // Hitung selisih harga
                $priceDifference = $newPrice - $originalPrice;

                // --- Skenario 1: Harga Penawaran LEBIH TINGGI ---
                if ($priceDifference > 0) {
                    // Cek apakah saldo requester cukup untuk menutupi selisih
                    if ($requester->balance < $priceDifference) {
                        // Jika tidak cukup, batalkan transaksi dan lempar error
                        throw new \Exception('Saldo Anda tidak cukup untuk menerima penawaran ini. Silakan isi saldo terlebih dahulu.');
                    }

                    // Kurangi saldo aktif, tambahkan ke saldo tertahan
                    $requester->balance -= $priceDifference;
                    $requester->locked_balance += $priceDifference;
                    $requester->save();

                    // Catat transaksi penyesuaian di riwayat wallet
                    WalletTransaction::create([
                        'user_id' => $requester->id,
                        'amount' => $priceDifference,
                        'type' => 'credit',
                        'description_id' => 'Penambahan saldo ditahan untuk pekerjaan: ' . $workRequest->title,
                        'description_en' => 'Added balance on hold for the job: ' . $workRequest->title,
                    ]);

                    $user = Auth::user();
                    activity()->inLog('Finance')->causedBy($user)->on($workRequest)
                        ->log("Dana tambahan sebesar Rp" . number_format($priceDifference) . " ditahan dari {$user->first_name} karena perubahan harga melalui tawaran.");
                }

                // --- Skenario 2: Harga Penawaran LEBIH RENDAH ---
                else if ($priceDifference < 0) {
                    $refundAmount = abs($priceDifference); // Ambil nilai absolut untuk refund

                    // Kembalikan selisih dana ke saldo aktif
                    $requester->balance += $refundAmount;
                    $requester->locked_balance -= $refundAmount;
                    $requester->save();

                    // Catat transaksi refund parsial di riwayat wallet
                    WalletTransaction::create([
                        'user_id' => $requester->id,
                        'amount' => $refundAmount,
                        'type' => 'debit',
                        'description_id' => 'Pengembalian saldo ditahan untuk pekerjaan: ' . $workRequest->title,
                        'description_en' => 'Held balance refund for a job: ' . $workRequest->title,
                    ]);
                    $user = Auth::user();
                    activity()->inLog('Finance')->causedBy($user)->on($workRequest)
                        ->log("Dana sebesar Rp" . number_format($refundAmount) . " dikembalikan ke {$user->first_name} karena perubahan harga melalui tawaran.");
                }
            });
            $offer->request->disableLogging();
            $offer->request->update(['final_price' => $offer->amount]);
            $offer->request->enableLogging();
            $transaction = Request::hireAndFinalize($offer->request, $offer->worker);
            return redirect()->route('request.ongoing', ['transactionId' => $transaction->id]);
        }

        $this->loadActiveOffer();
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
        if ($this->selectedChatRoomId && !$this->chatRoom) {
            $this->chatRoom = ChatRoom::with(['request', 'worker'])->find($this->selectedChatRoomId);
        }
        return view('livewire.job-requester.chat');
    }
}
