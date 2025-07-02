<div class="container-fluid vh-100">
    <div class="row h-100">
        {{-- Kolom Kiri: Daftar Chat --}}
        <div class="col-4 border-end overflow-auto py-4">
            <h5 class="fw-bold mb-4 ps-3">Pesan Pekerjaan</h5>
            @forelse ($chatRooms as $room)
                <div wire:click="selectRoom({{ $room->id }})"
                    class="px-3 py-2 mb-2 {{ $selectedRoom && $room->id === $selectedRoom->id ? 'bg-light border-start border-primary' : 'hover:bg-light' }}"
                    style="cursor: pointer; border-left-width: 4px; border-color: transparent;">

                    <p class="mb-1 fw-semibold">{{ $room->request->title }}</p>
                    <p class="mb-0 text-muted small">Requester: {{ $room->requester->first_name }}</p>
                    @if ($room->lastMessage)
                        <p class="mb-0 text-muted small text-truncate">{{ $room->lastMessage->message }}</p>
                    @endif
                </div>
            @empty
                <p class="text-muted ps-3">Tidak ada percakapan.</p>
            @endforelse
        </div>

        {{-- Kolom Kanan: Panel Chat --}}
        <div class="col-8 d-flex flex-column p-0 h-100">
            @if ($selectedRoom)
                {{-- Header Chat --}}
                <div class="border-bottom p-3">
                    <h5 class="mb-1 fw-bold">{{ $selectedRoom->request->title }}</h5>
                    <p class="mb-1">Dengan: <strong>{{ $selectedRoom->requester->first_name }}
                            {{ $selectedRoom->requester->last_name }}</strong></p>
                </div>

                {{-- Panel Tawaran --}}
                <div class="p-3 border-bottom bg-light">
                    @if ($showOfferForm)
                        {{-- Form untuk mengajukan tawaran --}}
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" wire:model.defer="offerAmount" class="form-control"
                                placeholder="Masukkan nominal...">
                            <button wire:click="makeOffer" class="btn btn-success">Kirim</button>
                            <button wire:click="toggleOfferForm" class="btn btn-secondary">Batal</button>
                        </div>
                        @error('offerAmount')
                            <span class="text-danger small mt-1">{{ $message }}</span>
                        @enderror
                    @else
                        {{-- Tombol untuk menampilkan form --}}
                        <button wire:click="toggleOfferForm" class="btn btn-outline-primary w-100"
                            @if ($activeOffer && $activeOffer->status === 'pending') disabled @endif>
                            @if ($activeOffer && $activeOffer->status !== 'pending')
                                Ajukan Tawaran Baru
                            @else
                                Ajukan Penawaran
                            @endif
                        </button>
                    @endif

                    @if ($activeOffer)
                        <div class="mt-3 text-center">
                            <p class="mb-1 text-muted">Tawaran Terakhir Anda:</p>
                            <h4 class="fw-bold">Rp{{ number_format($activeOffer->amount, 0, ',', '.') }}</h4>
                            <span
                                class="badge fs-6 
                                @switch($activeOffer->status)
                                    @case('open') bg-success @break
                                    @case('closed') bg-danger @break
                                @endswitch">
                                Status: {{ ucfirst($activeOffer->status) }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Body Chat dengan Auto-Scroll --}}
                <div x-data x-ref="chatBody"
                    @scroll-to-bottom.window="$nextTick(() => { $refs.chatBody.scrollTop = $refs.chatBody.scrollHeight; })"
                    class="flex-grow-1 overflow-auto p-3" style="background-color: #f5f5f5;" wire:poll.3s>
                    @forelse ($this->messages as $date => $groupedMessages)
                        <div class="text-center my-3">
                            <span
                                class="px-3 py-1 bg-white border rounded-pill text-muted small">{{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</span>
                        </div>
                        @foreach ($groupedMessages as $msg)
                            <div
                                class="d-flex flex-column {{ $msg->sender_id === auth()->id() ? 'align-items-end' : 'align-items-start' }} mb-3">
                                <div
                                    class="px-3 py-2 rounded-3 mw-75 {{ $msg->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-white border' }}">
                                    {{ $msg->message }}
                                </div>
                                <div class="small text-muted mt-1">{{ $msg->created_at->format('H:i') }}</div>
                            </div>
                        @endforeach
                    @empty
                        <div class="text-center text-muted mt-5">Belum ada pesan.</div>
                    @endforelse
                </div>

                {{-- Form Input Chat --}}
                <form wire:submit.prevent="send" class="p-3 bg-white border-top">
                    <div class="input-group">
                        <input wire:model.defer="newMessage" x-data @clear-input.window="$el.value = ''"type="text"
                            class="form-control" placeholder="Tulis pesan..." autocomplete="off">
                        <button type="submit" class="btn btn-primary">Kirim</button>
                    </div>
                </form>
            @else
                <div class="flex-grow-1 d-flex align-items-center justify-content-center text-muted bg-light">
                    <div class="text-center">
                        <i class="bi bi-chat-dots" style="font-size: 3rem;"></i>
                        <p class="mt-2">Pilih percakapan untuk ditampilkan.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
