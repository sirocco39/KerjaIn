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

        .bubble-sender {
            background-color: #309FFF;
            color: white;
            border-bottom-right-radius: 0;
        }

        .bubble-receiver {
            background-color: #E0E0E0;
            color: black;
            border-bottom-left-radius: 0;
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

            
            .chat-list-col {
                width: 100%;
                
                max-width: 100%;
                flex: 0 0 100%;
            }

            .chat-main-col {
                width: 100%;
                
                max-width: 100%;
                flex: 0 0 100%;
            }

            
            .chat-list-col.d-lg-block.d-none-mobile-chat {
                
                display: none !important;
            }

            
            .chat-main-col.d-lg-block.d-none-mobile-list {
                
                display: none !important;
            }

            
            
            .chat-list-col.d-block-mobile-list {
                display: block !important;
            }

            .chat-main-col.d-block-mobile-chat {
                display: block !important;
            }

            
            .h-100-mobile {
                height: 100vh;
                
                
            }
        }

        
        @media (min-width: 992px) {

            
            .chat-list-col,
            .chat-main-col {
                display: flex !important;
                
            }
        }

        .chat-app-height {
            height: calc(100vh - 64px);
            
            
        }

        
        .chat-list-col,
        .chat-main-col {
            height: 100%;
            
            
        }

        
        .chat-list-scroll-area {
            
            height: 100%;
            
            overflow-y: auto;
            
            padding-right: 15px;
            
            box-sizing: border-box;
            
        }

        
        .chat-messages-scroll-area {
            flex-grow: 1;
            
            overflow-y: auto;
            
            padding-right: 15px;
            
            box-sizing: border-box;
            
        }

        

        
    </style>
    <div class="container-fluid vh-100">
        <div class="row h-100">
            
            <div
                class="col-lg-5 border-end bg-light d-flex flex-column h-100 chat-list-col
                @if ($showChatPanel) d-none @else d-block @endif">
                
                <div class="p-3 overflow-auto flex-grow-1">
                    @forelse ($chatRooms as $room)
                        
                        <div wire:key="room-{{ $room->id }}" wire:click="selectRoom({{ $room->id }})"
                            class="px-3 py-2 mb-2 cursor-pointer {{ $selectedRoom && $room->id === $selectedRoom->id ? 'selected-wrapper' : '' }}">
                            
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

            
            <div class="col-lg-7 d-flex flex-column p-0 h-100 chat-main-col
                @if ($showChatPanel) d-block @else d-none @endif "
                wire:poll.3s> 
                @if ($selectedRoom)

                    
                    <div class="chat-header p-3 d-flex align-items-center gap-3 rounded-top-5">
                        
                        <button wire:click="backToChatList" class="btn btn-link text-white d-lg-none p-0 me-2">
                            <i class="bi bi-arrow-left-circle-fill fs-4"></i>
                        </button>

                        <div class="chat-avatar">
                            <img src="{{ $selectedRoom->requester->photo_url_user ? asset($selectedRoom->requester->photo_url_user) : asset('Image/Icon/user-circle.svg') }}"
                                alt="Profil"
                                onerror="this.onerror=null;this.src='{{ asset('Image/Icon/user-circle.svg') }}';"
                                style="width: 50px; height: 50px; border-radius: 50%; object-fit: contain; background-color: #e0e0e0;" />
                        </div>
                        <div class="me-auto">
                            <h5 class="fw-bold mb-0">{{ $selectedRoom->requester->first_name ?? '-' }}</h5>
                        </div>
                    </div>

                    

                    
                    @if ($selectedRoom && $selectedRoom->request->status !== 'closed')

                        <div class="offer-panel" wire:poll.5s="loadActiveOffer">
                            @if ($showOfferForm)
                                <div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        
                                        <div class="input-group" style="max-width: 250px;">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" wire:model.defer="offerAmount" class="form-control"
                                                placeholder="{{ __('chat-job-taker.panel_tawaran.placeholder_nominal') }}">
                                        </div>

                                        
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
                    
                    <div x-data x-ref="chatBody"
                        @scroll-to-bottom.window="$nextTick(() => { $refs.chatBody.scrollTop = $refs.chatBody.scrollHeight; })"
                        class="p-3 chat-body bg-white border chat-messages-scroll-area"> 

                        @forelse ($this->messages as $date => $groupedMessages)
                            <div class="text-center my-3">
                                <span
                                    class="date-separator">{{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</span>
                            </div>

                            @foreach ($groupedMessages as $msg)
                                <div
                                    class="d-flex flex-column {{ $msg->sender_id === auth()->id() ? 'align-items-end' : 'align-items-start' }} mb-3">
                                    <div
                                        class="chat-bubble {{ $msg->sender_id === auth()->id() ? 'bubble-sender' : 'bubble-receiver' }}">
                                        {{ $msg->message }}
                                    </div>
                                    <div class="small text-muted mt-1 px-2 d-flex align-items-center">
                                        <span>{{ $msg->created_at->format('H:i') }}</span>

                                        
                                        @if ($msg->sender_id === auth()->id())
                                            @if ($msg->read_at)
                                                
                                                <i class="bi bi-check-all text-primary ms-1"></i>
                                            @else
                                                
                                                <i class="bi bi-check text-muted ms-1"></i>
                                            @endif
                                        @endif
                                        
                                    </div>
                                </div>
                            @endforeach
                        @empty
                            <div class="text-center text-muted mt-5">{{ __('chat-job-taker.belum_ada_pesan') }}</div>
                        @endforelse
                    </div>

                    
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
