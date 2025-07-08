@props(['request', 'expandedRequestId', 'selectedChatRoomId'])

<style>
    .job-request-card {
        transition: all 0.2s ease-in-out;
        border: 3px solid #dee2e6;
        border-radius: var(--bs-border-radius-xxl);
    }

    .job-request-card:hover,
    .job-request-card.is-expanded {
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        border: 1px solid #294287;
    }

    .job-request-card:active {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
    }

    .icon-text {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .label-tersedia {
        background-color: #103F91;
        color: white;
        border-radius: 20px;
        padding: 6px 20px;
        border: none;
        height: 35px;
        width: 35%;
        font-size: 14px;
    }

    .detaila,
    .text-muted {
        font-size: 14px;
    }

    /* CSS BAGIAN EXPAND */
    .expanded-chats-container {
        background-color: #103F91;
        padding: 1rem;
    }

    /* Aturan untuk card-body saat diekspansi */
    .job-request-card.is-expanded .card-body {
        border-bottom: none;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
        /* [PERBAIKAN] Menggunakan margin bawah negatif untuk menutup celah */
        margin-bottom: -2px;
    }

    .chat-item-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        background-color: white;
        padding: 1rem;
        border-radius: 1rem;
        margin-bottom: 0.75rem;
        text-decoration: none;
        color: inherit;
        transition: transform 0.2s ease-in-out;
        cursor: pointer;
    }

    .chat-item-card:last-child {
        margin-bottom: 0;
    }

    .chat-item-card:hover {
        transform: scale(1.02);
    }

    .chat-item-card.active {
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.8);
    }

    .chat-item-avatar {
        width: 50px;
        height: 50px;
        background-color: #e9ecef;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .chat-item-text {
        overflow: hidden;
    }
</style>

<div class="card bg-white rounded-5 job-request-card {{ $expandedRequestId === $request->id ? 'is-expanded' : '' }}">
    <div class="card-body p-3">
        {{-- Judul Pekerjaan --}}
        <h5 class="card-title-chat fw-bold mb-2" style="font-size: 20px;">{{ $request->title }}</h5>

        {{-- Detail (Tanggal, Jam, Lokasi, Upah) dengan ikon --}}
        <div class="row g-3 mb-3">
            <div class="col-md-6 mb-1 icon-text">
                <img class="ms-2" src="{{ asset('Image/Icon/icon-clock-fill.svg') }}" alt="Icon Clock">
                <small class="text-muted">Mulai Kerja</small><br>
                <strong class="detaila">{{ $request->start_time }}</strong>
            </div>
            <div class="col-md-6 mb-1 icon-text">
                <img class="ms-2" src="{{ asset('Image/Icon/icon-location-fill.svg') }}" alt="Icon Address">
                <small class="text-muted">Lokasi</small><br>
                <strong>{{ $request->location }}</strong>
            </div>
            <div class="col-md-6 mb-1 icon-text">
                <img class="ms-2" src="{{ asset('Image/Icon/icon-done.svg') }}" alt="Icon Done">
                <small class="text-muted">Selesai Kerja</small><br>
                <strong>{{ $request->end_time }}</strong>
                <div></div>
            </div>
            <div class="col-md-6 mb-1 icon-text">
                <img class="ms-2" src="{{ asset('Image/Icon/icon-stackMoney.svg') }}" alt="Icon Money">
                <small class="text-muted">Upah</small><br>
                <strong>Rp{{ number_format($request->price, 0, ',', '.') }}</strong>
                <div></div>
            </div>
        </div>

        {{-- Status & Tombol Expander --}}
        <div class="d-flex justify-content-between align-items-center">
            <div class="label-tersedia d-flex align-items-center justify-content-center">Tersedia</div>
            <a wire:click="toggleExpand({{ $request->id }})" class="text-secondary fs-5" style="cursor: pointer;">
                <i class="bi {{ $expandedRequestId === $request->id ? 'bi-chevron-up' : 'bi-chevron-down' }}"></i>
            </a>
        </div>
    </div>

    {{-- BAGIAN EXPAND --}}
    @if ($expandedRequestId === $request->id)
        <div class="expanded-chats-container rounded-bottom-5">
            @forelse ($request->chatRooms as $room)
                <div wire:click="selectChat({{ $room->id }})"
                    class="chat-item-card rounded-5 {{ $selectedChatRoomId === $room->id ? 'active' : '' }}">
                    <div class="chat-item-avatar"></div>
                    <div class="chat-item-text">
                        <h6 class="mb-0 fw-bold">{{ $room->worker->first_name ?? 'Worker' }}</h6>
                        @if ($room->lastMessage)
                            <p class="mb-0 small text-muted text-truncate">
                                {{ $room->lastMessage->message }}
                            </p>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-white-50 text-center small p-3">Belum ada percakapan.</p>
            @endforelse
        </div>
    @endif
</div>
