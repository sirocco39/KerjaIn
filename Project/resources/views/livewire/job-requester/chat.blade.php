<div>
    {{-- CSS untuk tampilan chat baru --}}
    <style>
        .chat-header {
            background-color: #103F91;
            color: white;
        }

        .chat-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background-color: #e9ecef;
            /* Placeholder color */
        }

        /* [MODIFIKASI] Ukuran dan warna dot disesuaikan, margin dihapus */
        .status-dot {
            width: 20px;
            height: 20px;
            background-color: #D3FA0D;
            /* Warna hijau kekuningan cerah */
            border-radius: 50%;
        }

        .offer-panel {
            background-color: white;
            padding: 0.75rem 1.5rem;
            border-bottom: 1px solid #dee2e6;
        }

        .btn-pill {
            border-radius: 50px;
            padding: 0.375rem 1.25rem;
            font-weight: bold;
        }

        .chat-body {
            background-color: #f8f9fa;
        }

        .date-separator {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .chat-bubble {
            padding: 10px 15px;
            border-radius: 1.25rem;
            max-width: 75%;
            word-wrap: break-word;
        }

        .chat-bubble-sender {
            background-color: #e9ecef;
            color: #212529;
            border-bottom-left-radius: 0.25rem;
        }

        .chat-bubble-receiver {
            background-color: #0d6efd;
            color: white;
            border-bottom-right-radius: 0.25rem;
        }

        .chat-input-area {
            background-color: white;
            padding: 1rem 1.5rem;
            border-top: 1px solid #dee2e6;
        }

        .chat-input {
            border-radius: 50px;
            background-color: #f1f1f1;
            border: none;
            padding: 0.75rem 1.25rem;
        }

        .chat-input:focus {
            background-color: #e9ecef;
            box-shadow: none;
        }

        .btn-send-circle {
            background-color: #103F91;
            color: white;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-send-circle:hover {
            background-color: #0d3273;
            color: white;
        }

        /* ... CSS Anda yang lain ... */

        /* Aturan CSS yang sudah ada untuk modal */
        #confirmAcceptOfferModal .modal-content {
            background-color: transparent;
            border: none;
            box-shadow: none;
            height: auto;
            /* <-- TAMBAHKAN BARIS INI */
        }
    </style>

    <div class="container-fluid vh-100">
        <div class="row h-100">

            {{-- Kolom Kiri: Daftar Request & Worker (TETAP SAMA) --}}
            <div class="col-lg-5 border-end bg-light">
                <div class="p-3">
                    @forelse ($requestsWithChats as $request)
                        <x-job-requester.card :$request :expandedRequestId="$expandedRequestId" :selectedChatRoomId="$selectedChatRoomId" />
                    @empty
                        <div class="text-center text-muted mt-5">
                            <i class="bi bi-chat-left-dots fs-1"></i>
                            <p>Tidak ada pekerjaan dengan percakapan aktif.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- =================================================================== --}}
            {{-- =================== KOLOM KANAN (TAMPILAN BARU) =================== --}}
            {{-- =================================================================== --}}
            <div class="col-lg-7 d-flex flex-column p-3 h-100" wire:poll.3s>
                @if ($chatRoom)
                    <div class="chat-header p-3 d-flex align-items-center gap-3 rounded-top-5">
                        <div class="chat-avatar">
                            {{-- Anda bisa memasukkan tag <img> di sini jika punya URL avatar --}}
                        </div>
                        <div class="me-auto">
                            <h5 class="fw-bold mb-0">{{ $chatRoom->worker->first_name ?? '-' }}</h5>
                        </div>
                    </div>

                    {{-- Panel Tawaran --}}
                    @if ($activeOffer && $activeOffer->status === 'open' && auth()->id() === $activeOffer->requester_id)
                        <div class="offer-panel d-flex justify-content-between align-items-center border">
                            <div>
                                <span class="text-muted">Nominal Upah yang Anda Ajukan:</span>
                                <strong
                                    class="text-dark">Rp{{ number_format($activeOffer->amount, 0, ',', '.') }}</strong>
                            </div>
                            <div class="d-flex gap-2">
                                <button wire:click="respondToOffer({{ $activeOffer->id }}, 'rejected')"
                                    class="btn btn-danger btn-pill">Tolak</button>
                                <button type="button" class="btn btn-success btn-pill" data-bs-toggle="modal"
                                    data-bs-target="#confirmAcceptOfferModal">
                                    Terima
                                </button>
                            </div>
                        </div>
                    @endif


                    {{-- Body Chat --}}
                    <div x-data x-ref="chatBody"
                        @scroll-to-bottom.window="$nextTick(() => { $refs.chatBody.scrollTop = $refs.chatBody.scrollHeight; })"
                        class="flex-grow-1 overflow-auto p-3 chat-body bg-white border" wire:poll.3s>

                        @forelse ($this->messages as $date => $groupedMessages)
                            <div class="text-center my-3">
                                <span
                                    class="date-separator">{{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</span>
                            </div>

                            @foreach ($groupedMessages as $msg)
                                <div
                                    class="d-flex flex-column {{ $msg->sender_id === auth()->id() ? 'align-items-end' : 'align-items-start' }} mb-3">
                                    <div
                                        class="chat-bubble {{ $msg->sender_id === auth()->id() ? 'chat-bubble-receiver' : 'chat-bubble-sender' }}">
                                        {{ $msg->message }}
                                    </div>
                                    <div class="small text-muted mt-1 px-2 d-flex align-items-center">
                                        <span>{{ $msg->created_at->format('H:i') }}</span>

                                        {{-- [MODIFIKASI DIMULAI] --}}
                                        @if ($msg->sender_id === auth()->id())
                                            @if ($msg->read_at)
                                                {{-- Jika sudah dibaca, centang dua biru (read) --}}
                                                <i class="bi bi-check-all text-primary ms-1"></i>
                                            @else
                                                {{-- Jika baru terkirim, centang satu abu-abu (noread) --}}
                                                <i class="bi bi-check text-muted ms-1"></i>
                                            @endif
                                        @endif
                                        {{-- [MODIFIKASI SELESAI] --}}
                                    </div>
                                </div>
                            @endforeach
                        @empty
                            <div class="text-center text-muted mt-5">Belum ada pesan.</div>
                        @endforelse
                    </div>

                    {{-- Form Input Pesan --}}
                    <form wire:submit.prevent="send" class="chat-input-area rounded-bottom-5 border">
                        <div class="input-group">
                            <input wire:model.defer="newMessage" x-data @clear-input.window="$el.value = ''"
                                type="text" class="form-control chat-input border rounded-5"
                                placeholder="Tulis pesan..." autocomplete="off">
                            <button type="submit" class="btn btn-send-circle ms-2 rounded-circle">
                                <img src="{{ asset('Image/Icon/icon-send.svg') }}" alt="">
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

        {{-- Modal Konfirmasi (TETAP SAMA) --}}
        {{-- Modal Konfirmasi Penerimaan Tawaran (REVISED) --}}
        @if ($activeOffer)
            <div class="modal fade" id="confirmAcceptOfferModal" tabindex="-1"
                aria-labelledby="confirmAcceptOfferModalLabel" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 rounded-4">

                        {{-- Header --}}
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold" id="confirmAcceptOfferModalLabel">Konfirmasi Penerimaan
                                Tawaran</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        {{-- Body --}}
                        <div class="modal-body p-4">
                            <p class="text-center text-muted mb-3">Anda akan menerima tawaran upah dari pekerja. Mohon
                                periksa kembali detail di bawah ini:</p>

                            {{-- Detail Tawaran --}}
                            <div class="bg-light p-3 rounded-3 mb-4 text-center">
                                <h6 class="text-muted small fw-normal">JUMLAH TAWARAN</h6>
                                <h2 class="fw-bolder text-success mb-3">
                                    Rp{{ number_format($activeOffer->amount, 0, ',', '.') }}
                                </h2>
                                <h6 class="text-muted small fw-normal">DARI PEKERJA</h6>
                                <h5 class="fw-bold mb-0">{{ $activeOffer->worker->first_name }}</h5>
                            </div>

                            {{-- Peringatan --}}
                            <div class="alert alert-warning d-flex align-items-center" role="alert">
                                <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2"></i>
                                <div>
                                    <strong>Penting:</strong> Tindakan ini akan menyelesaikan proses negosiasi dan
                                    tidak dapat dibatalkan.
                                </div>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-success fw-bold"
                                wire:click="respondToOffer({{ $activeOffer->id }}, 'accepted')"
                                data-bs-dismiss="modal">
                                <i class="bi bi-check-circle-fill me-1"></i> Ya, Saya Terima Tawaran
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
