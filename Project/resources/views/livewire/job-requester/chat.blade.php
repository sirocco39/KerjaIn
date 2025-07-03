<div class="container-fluid vh-100">
    <div class="row h-100">
        {{-- Kolom Kiri: Daftar Request & Worker --}}
        <div class="col-4 border-end overflow-auto py-4">
            <h5 class="fw-bold mb-4 ps-3">Pesan Pekerjaan</h5>
            @forelse ($requestsWithChats as $request)
                <div class="border-bottom mb-2 pb-2">
                    <div wire:click="toggleExpand({{ $request->id }})" class="px-3 py-2" style="cursor: pointer;">
                        <p class="fw-bold mb-1">{{ $request->title }}</p>
                        <p class="mb-0 text-muted small">{{ $request->location }}</p>
                    </div>

                    @if ($expandedRequestId === $request->id)
                        <div class="ps-4 pe-2 mt-2">
                            @forelse ($request->chatRooms as $room)
                                <div wire:click="selectChat({{ $room->id }})"
                                    class="p-2 rounded mb-1 {{ $selectedChatRoomId === $room->id ? 'bg-primary text-white' : 'hover:bg-light' }}"
                                    style="cursor: pointer;">

                                    {{-- Jika ada pesan belum dibaca & room ini tidak sedang dipilih, buat nama jadi tebal --}}
                                    <p
                                        class="mb-0 fw-semibold {{ $room->has_unread_messages && $selectedChatRoomId !== $room->id ? 'fw-bold' : '' }}">
                                        {{ $room->worker->first_name ?? 'Worker' }}
                                    </p>

                                    @if ($room->lastMessage)
                                        {{-- Jika ada pesan belum dibaca & room ini tidak sedang dipilih, buat pesannya jadi tebal dan berwarna gelap --}}
                                        <p
                                            class="mb-0 small text-truncate {{ $selectedChatRoomId === $room->id ? 'text-white-50' : ($room->has_unread_messages ? 'text-dark fw-bold' : 'text-muted') }}">
                                            {{ $room->lastMessage->message }}
                                        </p>
                                    @endif
                                </div>
                            @empty
                                <p class="text-muted small ps-2">Belum ada percakapan.</p>
                            @endforelse
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-muted ps-3">Tidak ada pekerjaan dengan percakapan aktif.</p>
            @endforelse
        </div>

        {{-- Kolom Kanan: Panel Chat --}}
        <div class="col-8 d-flex flex-column p-0 h-100" wire:poll.5s="loadActiveOffer">
            @if ($chatRoom)
                {{-- Header Chat --}}
                <div class="border-bottom p-3">
                    <h5 class="fw-bold mb-1">Chat dengan {{ $chatRoom->worker->first_name ?? '-' }}</h5>
                    <p class="mb-1 text-muted">Pekerjaan: {{ $chatRoom->request->title }}</p>
                </div>

                {{-- Panel Tawaran --}}
                <div class="p-3 border-bottom bg-light">
                    @if ($activeOffer)
                        <div class="text-center">
                            <p class="mb-1 text-muted">Tawaran Upah Terakhir dari
                                {{ $activeOffer->worker->first_name }}:</p>
                            <h4 class="fw-bold">Rp{{ number_format($activeOffer->amount, 0, ',', '.') }}</h4>
                            @if ($activeOffer->status === 'open')
                                @if (auth()->id() === $activeOffer->requester_id)
                                    <div class="d-flex justify-content-center gap-2 mt-2">
                                        <button wire:click="respondToOffer({{ $activeOffer->id }}, 'rejected')"
                                            class="btn btn-danger btn-sm">Tolak</button>

                                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#confirmAcceptOfferModal">
                                            Terima
                                        </button>

                                    </div>
                                @else
                                    <span class="badge bg-warning text-dark">Menunggu Respon Anda</span>
                                @endif
                            @else
                                <span
                                    class="badge fs-6 {{ $activeOffer->status === 'accepted' ? 'bg-success' : 'bg-danger' }}">
                                    Tawaran {{ $activeOffer->status === 'accepted' ? 'Diterima' : 'Ditolak' }}
                                </span>
                            @endif
                        </div>
                    @else
                        <p class="text-center text-muted mb-0">Belum ada tawaran yang diajukan.</p>
                    @endif
                </div>

                {{-- Body Chat dengan Auto-Scroll --}}
                <div x-data x-ref="chatBody"
                    @scroll-to-bottom.window="$nextTick(() => { $refs.chatBody.scrollTop = $refs.chatBody.scrollHeight; })"
                    class="flex-grow-1 overflow-auto p-3" style="background-color: #f5f5f5;" wire:poll.3s>

                    {{-- ... (Isi chat Anda tetap sama) ... --}}
                    @forelse ($this->messages as $date => $groupedMessages)
                        {{-- Tampilan tanggal --}}
                        <div class="text-center my-3">
                            <span class="px-3 py-1 bg-white border rounded-pill text-muted small">
                                {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                            </span>
                        </div>
                        {{-- Tampilan pesan --}}
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

                {{-- Form Input Pesan --}}
                <form wire:submit.prevent="send" class="p-3 bg-white border-top">
                    <div class="input-group">
                        <input wire:model.defer="newMessage" x-data @clear-input.window="$el.value = ''" type="text"
                            class="form-control" placeholder="Tulis pesan..." autocomplete="off">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>
                </form>
            @else
                {{-- Tampilan Default Saat Belum Ada Chat Terpilih --}}
                <div class="flex-grow-1 d-flex align-items-center justify-content-center text-muted bg-light">
                    <div class="text-center">
                        <i class="bi bi-chat-left-text" style="font-size: 3rem;"></i>
                        <p class="mt-2">Pilih percakapan untuk ditampilkan.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if ($activeOffer)
        <div class="modal fade" id="confirmAcceptOfferModal" tabindex="-1"
            aria-labelledby="confirmAcceptOfferModalLabel" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmAcceptOfferModalLabel">Konfirmasi Terima Tawaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menerima tawaran upah sebesar
                            <strong>Rp{{ number_format($activeOffer->amount, 0, ',', '.') }}</strong> dari
                            <strong>{{ $activeOffer->worker->first_name }}</strong>?</p>
                        <p class="text-danger small">Tindakan ini akan menyelesaikan proses tawar-menawar dan tidak
                            dapat dibatalkan.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>

                        <button type="button" class="btn btn-success"
                            wire:click="respondToOffer({{ $activeOffer->id }}, 'accepted')" data-bs-dismiss="modal">
                            Ya, Terima Tawaran
                        </button>

                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
