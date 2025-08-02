<div class="three gx-3 gy-3 col-12 col-lg-9 mt-3 px-md-1 ps-3 mt-lg-0 order-1 order-lg-1" style="height: 100vh;">
    <style>
        .bubble-message {
            padding: 10px 15px;
            border-radius: 18px;
            max-width: 70%;
            word-wrap: break-word;
            position: relative;
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

        .bubble-time {
            font-size: 0.75rem;
            margin-top: 4px;
            text-align: right;
        }

        .bubble-sender .bubble-time {
            color: white;
        }

        .check-icon {
            font-size: 0.75rem;
            margin-left: 5px;
            vertical-align: middle;
            color: #D3FA0D;
        }
    </style>


    @if ($selectedRoom)
        <div class="contain bg-light px-4 py-3 rounded-top-4 d-flex align-items-center"
            style="border: 1px solid #cacadd; max-height:56vh;">
            <div class="atas d-flex justify-content-between align-items-center flex-fill">
                <div class="profile d-flex">
                    <img src="{{ $selectedRoom->requester->photo_url_user ? asset($selectedRoom->requester->photo_url_user) : asset('Image/Icon/user-circle.svg') }}"
                        alt="Profil" alt="" style="width: 48px; height: 48px;" class="rounded-5">
                    <div class="container-name-status ms-2 d-flex align-items-center">
                        <div class="name fw-bold">
                            {{ $selectedRoom->requester->first_name . ' ' . $selectedRoom->requester->last_name }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-4 pb-0 chat-container container-fluid bg-light rounded-bottom-4 flex-fill flex-column justify-content-between"
            style="height:80%; border: 1px solid #cacadd;">

            <div id="chatMessages" class="chat-layout flex-fill overflow-auto" style="flex:6; height:85%;" wire:poll.5s>
                @forelse ($this->messages as $date => $group)
                    <div class="text-center small text-muted my-2">
                        {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</div>
                    @foreach ($group as $msg)
                        <div
                            class="d-flex {{ $msg->sender_id === Auth::id() ? 'justify-content-end' : 'justify-content-start' }} mb-2">
                            <div
                                class="bubble-message {{ $msg->sender_id === Auth::id() ? 'bubble-sender' : 'bubble-receiver' }}">
                                {{ $msg->message }}
                                <div class="bubble-time">
                                    {{ $msg->created_at->setTimezone('Asia/Jakarta')->format('H:i') }}
                                    @if ($msg->sender_id === Auth::id())
                                        @if ($msg->read_at)
                                            <i class="bi bi-check2-all check-icon"></i>
                                        @else
                                            <i class="bi bi-check check-icon"></i>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @empty
                    <div class="text-center text-muted mt-4">{{ __('chat-job-req.belum_ada_pesan') }}</div>
                @endforelse
            </div>

            <div class="send-layout d-flex align-items-center justify-content-between" style="flex:1; height:10%;">
                <form wire:submit.prevent="send" class="d-flex flex-fill">
                    @csrf
                    <input type="text" class="me-2 form-control rounded-5 flex-grow-1" wire:model.defer="newMessage"
                        placeholder="{{ __('chat-job-req.placeholder_pesan') }}">
                    <button type="submit" class="btn rounded-5 d-flex align-items-center justify-content-center"
                        style="background-color:#309FFF; height:100%; aspect-ratio: 1/1;">
                        <svg width="29" height="30" viewBox="0 0 30 30" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M7.80209 15.0002L4.61035 4.62793C12.2454 6.84848 19.4452 10.3563 25.8995 15.0002C19.4456 19.644 12.2461 23.1519 4.61152 25.3725L7.80209 15.0002ZM7.80209 15.0002H16.5674"
                                stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    @else
        <div class="d-flex justify-content-center align-items-center h-100">
            <p class="text-muted">Tidak ada ruang obrolan yang dipilih.</p>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        const chatMessages = document.getElementById('chatMessages');


        const scrollToBottom = () => {
            if (chatMessages) {

                setTimeout(() => {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }, 100);
            }
        };


        scrollToBottom();



        Livewire.on('messageSent', () => {
            scrollToBottom();
        });


        Livewire.hook('morph.updated', ({
            el,
            component
        }) => {


            if (el.id === 'chatMessages' && component.name === 'job-taker.chat-work') {
                scrollToBottom();
            }
        });
    });
</script>
