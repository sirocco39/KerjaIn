<div>
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

        .selected-wrapper .job-taker-card {
            border-color: #0d6efd;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);
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

        .offer-panel {
            background-color: white;
            padding: 0.75rem 1.5rem;
            border-bottom: 1px solid #dee2e6;
        }

        @media (max-width: 991.98px) {

            /* Corresponds to Bootstrap's 'lg' breakpoint */
            .chat-list-col {
                width: 100%;
                /* Take full width */
                max-width: 100%;
                flex: 0 0 100%;
            }

            .chat-main-col {
                width: 100%;
                /* Take full width */
                max-width: 100%;
                flex: 0 0 100%;
            }

            /* Hide the chat list if showChatPanel is true on small screens */
            .chat-list-col.d-lg-block.d-none-mobile-chat {
                /* Hapus d-lg-block dari sini atau ubah logikanya */
                display: none !important;
            }

            /* Hide the chat panel if showChatPanel is false on small screens */
            .chat-main-col.d-lg-block.d-none-mobile-list {
                /* Hapus d-lg-block dari sini atau ubah logikanya */
                display: none !important;
            }

            /* Ensure the selected panel is block */
            /* Ini akan menjadi d-block jika showChatPanel true/false di mobile */
            .chat-list-col.d-block-mobile-list {
                display: block !important;
            }

            .chat-main-col.d-block-mobile-chat {
                display: block !important;
            }

            /* Adjust height for mobile if needed, e.g., to make it full screen less header/footer */
            .h-100-mobile {
                height: 100vh;
                /* Example: full viewport height */
                /* Adjust as needed to fit your header/footer */
            }
        }

        /* --- Perbaikan untuk Desktop --- */
        @media (min-width: 992px) {

            /* Untuk layar desktop (lg dan lebih besar) */
            .chat-list-col,
            .chat-main-col {
                display: flex !important;
                /* Selalu tampilkan keduanya */
            }
        }

        .chat-app-height {
            height: calc(100vh - 64px);
            /* SESUAIKAN DENGAN TINGGI NAVBAR/HEADER ANDA */
            /* Ini akan membuat container-fluid setinggi viewport dikurangi tinggi navbar */
        }

        /* --- Memastikan kolom-kolom mengisi tinggi ini --- */
        .chat-list-col,
        .chat-main-col {
            height: 100%;
            /* Agar mengisi 100% dari .chat-app-height (induknya) */
            /* Pastikan parent (.row) memiliki tinggi yang tepat, yaitu h-100 dari chat-app-height */
        }

        /* --- CSS untuk Area Scrollable pada List Chat --- */
        .chat-list-scroll-area {
            /* flex-grow: 1; Ini dipindahkan ke parent .chat-list-col yang sudah d-flex flex-column */
            height: 100%;
            /* Mengisi sisa tinggi kolomnya setelah dikurangi padding. */
            overflow-y: auto;
            /* Aktifkan scroll vertikal jika konten melebihi max-height */
            padding-right: 15px;
            /* Beri padding untuk scrollbar agar tidak menutupi konten */
            box-sizing: border-box;
            /* Pastikan padding dihitung dalam lebar/tinggi */
        }

        /* --- CSS untuk Area Scrollable pada Body Chat (Pesan) --- */
        .chat-messages-scroll-area {
            flex-grow: 1;
            /* Biarkan dia mengisi sisa ruang yang tersedia di kolom */
            overflow-y: auto;
            /* Aktifkan scroll vertikal */
            padding-right: 15px;
            /* Padding untuk scrollbar */
            box-sizing: border-box;
            /* Tidak perlu max-height absolut di sini karena flex-grow:1 sudah menangani tinggi relatif */
        }

        /* ... CSS Anda yang lain ... */

        /* Aturan CSS yang sudah ada untuk modal */
    </style>
    <div class="container-fluid vh-100">
        <div class="row h-100">
            {{-- Kolom Kiri: Daftar Chat --}}
            <div
                class="col-lg-5 border-end bg-light d-flex flex-column h-100 chat-list-col
                @if ($showChatPanel) d-none @else d-block @endif">
                {{-- Area Daftar Chat (Dibuat scrollable) --}}
                <div class="p-3 overflow-auto flex-grow-1">
                    @forelse ($chatRooms as $room)
                        {{-- div pembungkus ini menangani klik dan status terpilih --}}
                        <div wire:key="room-{{ $room->id }}" wire:click="selectRoom({{ $room->id }})"
                            class="px-3 py-2 mb-2 cursor-pointer {{ $selectedRoom && $room->id === $selectedRoom->id ? 'selected-wrapper' : '' }}">
                            {{-- Memanggil komponen Card dan mengirim data request --}}
                            <x-job-taker.card :request="$room->request" />
                        </div>
                    @empty
                        <div class="text-center text-muted mt-5">
                            <i class="bi bi-chat-left-dots fs-1"></i>
                            <p>{{ __('chat-job-taker.tidak_ada_percakapan') }}</p>
                        </div>
                    @endforelse
                </div>

            </div>

            {{-- Kolom Kanan: Panel Chat --}}
            <div class="col-lg-7 d-flex flex-column p-0 h-100 chat-main-col
                @if ($showChatPanel) d-block @else d-none @endif "
                wire:poll.3s> {{-- KONDISI UNTUK MOBILE --}}
                @if ($selectedRoom)

                    {{-- Header Chat --}}
                    <div class="chat-header p-3 d-flex align-items-center gap-3 rounded-top-5">
                        {{-- TOMBOL KEMBALI (Hanya muncul di mobile) --}}
                        <button wire:click="backToChatList" class="btn btn-link text-white d-lg-none p-0 me-2">
                            <i class="bi bi-arrow-left-circle-fill fs-4"></i>
                        </button>

                        <div class="chat-avatar"></div>
                        <div class="me-auto">
                            <h5 class="fw-bold mb-0">{{ $selectedRoom->requester->first_name ?? '-' }}</h5>
                        </div>
                    </div>

                    {{-- Panel Tawaran --}}

                    {{-- Panel Tawaran --}}
                    @if ($selectedRoom && $selectedRoom->request->status !== 'closed')

                        <div class="offer-panel" wire:poll.5s="loadActiveOffer">
                            @if ($showOfferForm)
                                <div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        {{-- Bagian Kiri: Input Nominal --}}
                                        <div class="input-group" style="max-width: 250px;">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" wire:model.defer="offerAmount" class="form-control"
                                                placeholder="{{ __('chat-job-taker.panel_tawaran.placeholder_nominal') }}">
                                        </div>

                                        {{-- Bagian Kanan: Tombol Aksi --}}
                                        <div class="d-flex gap-2">
                                            <button wire:click="toggleOfferForm"
                                                class="btn btn-outline-secondary btn-pill">{{ __('chat-job-taker.panel_tawaran.tombol_batal') }}</button>
                                            <button wire:click="makeOffer"
                                                class="btn btn-success btn-pill">{{ __('chat-job-taker.panel_tawaran.tombol_kirim_tawaran') }}</button>
                                        </div>
                                    </div>
                                    @error('offerAmount')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            @else
                                {{-- Tampilan default panel tawaran --}}
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        @if ($activeOffer)
                                            <span
                                                class="text-muted">{{ __('chat-job-taker.panel_tawaran.nominal_diajukan') }}</span>
                                            <div class="d-flex align-items-center gap-2">
                                                <strong class="text-dark fs-5">
                                                    Rp{{ number_format($activeOffer->amount, 0, ',', '.') }}
                                                </strong>
                                                <span
                                                    class="badge @if ($activeOffer->status == 'open') bg-warning text-dark @elseif($activeOffer->status == 'accepted') bg-success @else bg-danger @endif">
                                                    {{ __('chat-job-taker.panel_tawaran.status') }}
                                                    @if ($activeOffer->status == 'open')
                                                        {{ __('chat-job-taker.panel_tawaran.menunggu_respon') }}
                                                    @elseif($activeOffer->status == 'accepted')
                                                        {{ __('chat-job-taker.panel_tawaran.tawaran_diterima') }}
                                                    @else
                                                        {{ __('chat-job-taker.panel_tawaran.tawaran_ditolak') }}
                                                    @endif
                                                </span>

                                            </div>
                                        @else
                                            <span
                                                class="text-muted">{{ __('chat-job-taker.panel_tawaran.belum_mengajukan_penawaran') }}</span>
                                        @endif
                                    </div>
                                    <div class="d-flex gap-2">
                                        @if (!$activeOffer || $activeOffer->status === 'rejected')
                                            <button wire:click="toggleOfferForm" class="btn btn-primary btn-pill">
                                                {{ $activeOffer ? __('chat-job-taker.panel_tawaran.tombol_tawar_lagi') : __('chat-job-taker.panel_tawaran.tombol_tawar_upah') }}
                                            </button>
                                        @endif
                                        @if ($activeOffer && $activeOffer->status === 'open')
                                            <button wire:click="deleteOffer" class="btn btn-outline-danger btn-pill">
                                                {{ __('chat-job-taker.panel_tawaran.tombol_batal_ajukan') }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                    @endif
                    {{-- Body Chat dengan Auto-Scroll --}}
                    <div x-data x-ref="chatBody"
                        @scroll-to-bottom.window="$nextTick(() => { $refs.chatBody.scrollTop = $refs.chatBody.scrollHeight; })"
                        class="p-3 chat-body bg-white border chat-messages-scroll-area"> {{-- MODIFIKASI INI --}}

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
                            <div class="text-center text-muted mt-5">{{ __('chat-job-taker.belum_ada_pesan') }}</div>
                        @endforelse
                    </div>

                    {{-- Form Input Chat --}}
                    <form wire:submit.prevent="send" class="chat-input-area rounded-bottom-5 border">
                        <div class="input-group">
                            <input wire:model.defer="newMessage" x-data @clear-input.window="$el.value = ''"
                                type="text" class="form-control chat-input border rounded-5"
                                placeholder="{{ __('chat-job-taker.placeholder_pesan') }}" autocomplete="off">
                            <button type="submit" class="btn btn-send-circle ms-2 rounded-circle">
                                <img src="{{ asset('Image/Icon/icon-send.svg') }}" alt="">
                            </button>
                        </div>
                    </form>
                @else
                    <div class="flex-grow-1 d-flex align-items-center justify-content-center text-muted bg-light">
                        <div class="text-center">
                            <i class="bi bi-chat-left-text" style="font-size: 3rem;"></i>
                            <p class="mt-2">{{ __('chat-job-taker.pilih_percakapan') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
