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
            background-color: #0d6efd
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

        #confirmAcceptOfferModal .modal-header,
        #confirmAcceptOfferModal .modal-body,
        #confirmAcceptOfferModal .modal-footer {
            background-color: #fefefe;
            /* Berikan background putih solid untuk semua bagian */
        }

        #confirmAcceptOfferModal {
            /* Hapus `height: max-content;` dan `display: flex !important; align-items: center; justify-content: center;` dari sini */
            /* Biarkan Bootstrap mengelola display dan pemusatan backdrop secara default */
            /* Anda mungkin hanya ingin menjaga padding jika modal bisa sangat lebar */
            padding: 1rem;
            /* Opsional: Jika Anda ingin padding di sekitar modal saat layar kecil */
        }

        #confirmAcceptOfferModal .modal-dialog {
            /* Biarkan modal-dialog-centered yang menangani pemusatan */
            /* Hapus `align-items: center;` yang mungkin berlebihan di sini */
            height: auto;
            min-height: auto;
            /* Ini penting, biarkan seperti ini */
            /* background-color: white !important; */
        }

        #confirmAcceptOfferModal .modal-content {
            /* background-color: white; */
            /* UBAH: dari transparent ke white, agar ada latar belakang solid */
            border: none;
            /* Tetap none sesuai keinginan Anda */
            /* box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); */
            /* Tambahkan shadow standar Bootstrap jika ingin */
            border-radius: 0.5rem;
            /* Tambahkan border-radius standar Bootstrap jika ingin */
            height: auto;
            /* Biarkan auto */
            max-height: 90vh;
            /* Batasi tinggi maksimum jika kontennya sangat panjang */
            overflow-y: auto;
            /* Scroll jika konten melebihi max-height */
            /* Penting: Pastikan tidak ada `margin-top` atau `padding-top` yang berlebihan di sini */
            margin-top: 0;
            /* Pastikan tidak ada margin atas berlebihan yang mendorong ke bawah */
            padding-top: 0;
            /* Pastikan tidak ada padding atas berlebihan */
        }

        /* --- Custom CSS for Responsive Behavior --- */
        /* For small screens (mobile-first), hide one panel based on Livewire state */
        @media (max-width: 991.98px) {

            /* Corresponds to Bootstrap's 'lg' breakpoint */
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

        /* --- Perbaikan untuk Desktop --- */
        @media (min-width: 992px) {

            .chat-list-col,
            .chat-main-col {
                display: flex !important;
            }
        }

        /* --- CSS Baru untuk Layout Tinggi Penuh (HEADER DI LUAR container-fluid) --- */
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
    </style>

    {{-- UBAH INI: class vh-100-mobile h-100 menjadi chat-app-height --}}
    <div class="container-fluid chat-app-height">
        <div class="row h-100">

            {{-- Kolom Kiri: Daftar Request & Worker --}}
            <div
                class="col-lg-5 border-end bg-light d-flex flex-column h-100 chat-list-col
                @if ($showChatPanel) d-none @else d-block @endif ">
                {{-- Modifikasi di sini: hapus flex-grow-1 dari `chat-list-scroll-area` --}}
                {{-- .chat-list-col sudah d-flex flex-column h-100, jadi `p-3` dan `chat-list-scroll-area` akan mengisi ruang --}}
                <div class="p-3 chat-list-scroll-area">
                    @forelse ($requestsWithChats as $request)
                        <x-job-requester.card :request="$request" :expandedRequestId="$expandedRequestId" :selectedChatRoomId="$selectedChatRoomId"
                            wire:key="card-{{ $request->id }}" class="mb-3" />
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
            <div class="col-lg-7 d-flex flex-column p-3 h-100 chat-main-col
                @if ($showChatPanel) d-block @else d-none @endif "
                wire:poll.3s>
                @if ($chatRoom)
                    <div class="chat-header p-3 d-flex align-items-center gap-3 rounded-top-5">
                        <button wire:click="backToChatList" class="btn btn-link text-white d-lg-none p-0 me-2">
                            <i class="bi bi-arrow-left-circle-fill fs-4"></i>
                        </button>
                        <div class="chat-avatar">
                            <img src="{{ $chatRoom->worker->profile_picture_url ?? asset('Image/Icon/icon-done.svg') }}"
                                alt="{{ $chatRoom->worker->first_name }}"
                                class="w-100 h-100 rounded-circle object-cover">
                        </div>
                        <div class="me-auto">
                            <h5 class="fw-bold mb-0">{{ $chatRoom->worker->first_name ?? '-' }}</h5>
                        </div>
                    </div>

                    {{-- Panel Tawaran --}}
                    @if ($activeOffer && $activeOffer->status === 'open' && auth()->id() === $activeOffer->requester_id && $chatRoom->request->status !== 'closed')
                        <div class="offer-panel d-flex justify-content-between align-items-center border">
                            <div>
                                <span class="text-muted">Nominal Upah yang Diajukan:</span>
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

        {{-- Modal Konfirmasi --}}
        @if ($activeOffer)
            <div class="modal fade" id="confirmAcceptOfferModal" tabindex="-1"
                aria-labelledby="confirmAcceptOfferModalLabel" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog">
                    <div class="modal-content border-0 rounded-4">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold" id="confirmAcceptOfferModalLabel">Konfirmasi Penerimaan
                                Tawaran</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <p class="text-center text-muted mb-3">Anda akan menerima tawaran upah dari pekerja. Mohon
                                periksa kembali detail di bawah ini:</p>
                            <div class="bg-light p-3 rounded-3 mb-4 text-center">
                                <h6 class="text-muted small fw-normal">JUMLAH TAWARAN</h6>
                                <h2 class="fw-bolder text-success mb-3">
                                    Rp{{ number_format($activeOffer->amount, 0, ',', '.') }}
                                </h2>
                                <h6 class="text-muted small fw-normal">DARI PEKERJA</h6>
                                <h5 class="fw-bold mb-0">{{ $activeOffer->worker->first_name }}</h5>
                            </div>
                            <div class="alert alert-warning d-flex align-items-center" role="alert">
                                <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2"></i>
                                <div>
                                    <strong>Penting:</strong> Tindakan ini akan menyelesaikan proses negosiasi dan
                                    tidak dapat dibatalkan.
                                </div>
                            </div>
                        </div>
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
