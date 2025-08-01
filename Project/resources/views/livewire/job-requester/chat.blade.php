<div>
    
    <style>
        .chat-header {
            background-color: #103F91;
            color: white;
        }

        .chat-avatar {
            border-radius: 50%;
            background-color: #e9ecef;
            
        }

        .status-dot {
            width: 20px;
            height: 20px;
            background-color: #D3FA0D;
            
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

        #confirmAcceptOfferModal .modal-content {
            background-color: white;
            
            border: none;
            
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            
            
        }

        
        
        #confirmAcceptOfferModal .modal-header,
        #confirmAcceptOfferModal .modal-body,
        #confirmAcceptOfferModal .modal-footer {
            background-color: transparent;
            
        }

        
        
        #confirmAcceptOfferModal .modal-body .bg-light.p-4.rounded-3 {
            background-color: #f8f9fa !important;
            
            border-color: #e9ecef !important;
            
        }

        #confirmAcceptOfferModal {
            
            
            
            padding: 1rem;
            
        }

        #confirmAcceptOfferModal .modal-dialog {
            
            
            height: auto;
             background-color: transparent;
            min-height: auto;
            
            
        }

        #confirmAcceptOfferModal .modal-content {
            
            
            border: none;
            
            
            
            border-radius: 0.5rem;
            
            height: auto;
            
            max-height: 90vh;
            
            overflow-y: auto;
            
            
            margin-top: 0;
            
            padding-top: 0;
            
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

    
    <div class="container-fluid chat-app-height">
        <div class="row h-100">

            
            <div
                class="col-lg-5 border-end bg-light d-flex flex-column h-100 chat-list-col
                @if ($showChatPanel) d-none @else d-block @endif ">
                
                
                <div class="p-3 chat-list-scroll-area">
                    @forelse ($requestsWithChats as $request)
                        <x-job-requester.card :request="$request" :expandedRequestId="$expandedRequestId" :selectedChatRoomId="$selectedChatRoomId"
                            wire:key="card-{{ $request->id }}" class="mb-3" />
                    @empty
                        <div class="text-center text-muted mt-5">
                            <i class="bi bi-chat-left-dots fs-1"></i>
                            <p>{{ __('chat-job-req.tidak_ada_percakapan_aktif') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>

            
            
            
            <div class="col-lg-7 d-flex flex-column p-3 h-100 chat-main-col
                @if ($showChatPanel) d-block @else d-none @endif "
                wire:poll.3s>
                @if ($chatRoom)
                    <div class="chat-header p-3 d-flex align-items-center gap-3 rounded-top-5">
                        <button wire:click="backToChatList" class="btn btn-link text-white d-lg-none p-0 me-2">
                            <i class="bi bi-arrow-left-circle-fill fs-4"></i>
                        </button>
                        <div class="chat-avatar">
                            <img src="{{ $chatRoom->worker->photo_url_user ? asset($chatRoom->worker->photo_url_user) : asset('Image/Icon/user-circle.svg') }}" alt="Profil" id="profileIcon"
                                onerror="this.onerror=null;this.src='{{ asset('Image/Icon/user-circle.svg') }}';"
                                style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;" />
                        </div>
                        <div class="me-auto">
                            <h5 class="fw-bold mb-0">{{ $chatRoom->worker->first_name ?? '-' }}</h5>
                        </div>
                    </div>

                    
                    @if (
                        $activeOffer &&
                            $activeOffer->status === 'open' &&
                            auth()->id() === $activeOffer->requester_id &&
                            $chatRoom->request->status !== 'closed')
                        <div class="offer-panel d-flex justify-content-between align-items-center border">
                            <div>
                                <span class="text-muted">{{ __('chat-job-req.tawaran_diajukan') }}</span>
                                <strong
                                    class="text-dark">Rp{{ number_format($activeOffer->amount, 0, ',', '.') }}</strong>
                            </div>
                            <div class="d-flex gap-2">
                                <button wire:click="respondToOffer({{ $activeOffer->id }}, 'rejected')"
                                    class="btn btn-danger btn-pill">{{ __('chat-job-req.tombol_tolak') }}</button>
                                <button type="button" class="btn btn-success btn-pill" data-bs-toggle="modal"
                                    data-bs-target="#confirmAcceptOfferModal">
                                    {{ __('chat-job-req.tombol_terima') }}
                                </button>
                            </div>
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
                            <div class="text-center text-muted mt-5">{{ __('chat-job-req.belum_ada_pesan') }}.</div>
                        @endforelse
                    </div>

                    
                    <form wire:submit.prevent="send" class="chat-input-area rounded-bottom-5 border">
                        <div class="input-group">
                            <input wire:model.defer="newMessage" x-data @clear-input.window="$el.value = ''"
                                type="text" class="form-control chat-input border rounded-5"
                                placeholder="{{ __('chat-job-req.placeholder_pesan') }}" autocomplete="off">
                            <button type="submit" class="btn btn-send-circle ms-2 rounded-circle">
                                <img src="{{ asset('Image/Icon/icon-send.svg') }}" alt="">
                            </button>
                        </div>
                    </form>
                @else
                    
                    <div class="flex-grow-1 d-flex align-items-center justify-content-center text-muted bg-light">
                        <div class="text-center">
                            <i class="bi bi-chat-left-text" style="font-size: 3rem;"></i>
                            <p class="mt-2">{{ __('chat-job-req.pilih_percakapan') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        
        @if ($activeOffer)
            <div class="modal fade" id="confirmAcceptOfferModal" tabindex="-1"
                aria-labelledby="confirmAcceptOfferModalLabel" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog modal-dialog-centered modal-md"> 
                    <div class="modal-content border-0 rounded-4 shadow-lg"> 
                        <div class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center">
                            <h5 class="modal-title fw-bold text-center w-100" id="confirmAcceptOfferModalLabel">
                                {{ __('chat-job-req.modal_judul') }}
                            </h5>
                            <button type="button" class="btn-close position-absolute end-0 me-3"
                                data-bs-dismiss="modal" aria-label="Close"></button> 
                        </div>
                        <div class="modal-body p-4 text-center"> 
                            <p class="text-muted mb-4">{{ __('chat-job-req.modal_deskripsi') }}</p>

                            <div class="bg-light p-4 rounded-3 mb-4 border border-1"
                                style="background-color: #f8f9fa !important; border-color: #e9ecef !important;">
                                
                                <h6 class="text-muted small fw-normal mb-2">
                                    {{ __('chat-job-req.modal_jumlah_tawaran') }}
                                </h6>
                                <h2 class="fw-bolder text-primary mb-3 display-4"> 
                                    Rp{{ number_format($activeOffer->amount ?? 0, 0, ',', '.') }}
                                </h2>
                                <h6 class="text-muted small fw-normal mb-1">{{ __('chat-job-req.modal_dari_pekerja') }}
                                </h6>
                                <h5 class="fw-bold mb-0">{{ $activeOffer->worker->first_name ?? 'N/A' }}</h5>
                                
                            </div>

                            <div class="alert alert-warning d-flex align-items-start p-3" role="alert">
                                
                                <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-3 fs-4"></i>
                                
                                <div class="text-start"> 
                                    <strong class="d-block mb-1">{{ __('chat-job-req.modal_penting_header') }}</strong>
                                    <small>{{ __('chat-job-req.modal_penting_body') }}</small> 
                                </div>
                            </div>
                        </div>
                        <div
                            class="modal-footer border-0 pt-0 d-flex justify-content-center flex-column flex-sm-row gap-2">
                            
                            <button type="button" class="btn btn-secondary flex-fill rounded-pill py-2"
                                data-bs-dismiss="modal">{{ __('chat-job-req.modal_tombol_batal') }}</button>
                            <button type="button" class="btn btn-success fw-bold flex-fill rounded-pill py-2"
                                wire:click="respondToOffer({{ $activeOffer->id ?? 'null' }}, 'accepted')"
                                 data-bs-dismiss="modal">
                                <i
                                    class="bi bi-check-circle-fill me-1"></i>{{ __('chat-job-req.modal_tombol_konfirmasi') }}
                                
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
