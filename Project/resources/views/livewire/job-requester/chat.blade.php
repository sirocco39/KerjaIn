<div>
    {{-- CSS untuk tampilan chat baru --}}
    <style>
        .chat-header {
            background-color: #103F91;
            color: white;
        }

        .chat-avatar {
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
            /* Memberi warna putih solid sebagai dasar modal */
            border: none;
            /* Menghilangkan border default jika ada, karena shadow sudah cukup */
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            /* Menambahkan bayangan yang lebih kuat agar modal menonjol */
            /* border-radius: 1rem; Ini sudah diatur di HTML dengan rounded-4 */
        }

        /* 2. Jadikan latar belakang header, body, dan footer transparan */
        /* Ini penting agar mereka mewarisi warna putih dari .modal-content */
        #confirmAcceptOfferModal .modal-header,
        #confirmAcceptOfferModal .modal-body,
        #confirmAcceptOfferModal .modal-footer {
            background-color: transparent;
            /* Pastikan elemen-elemen ini tidak memiliki latar belakang yang menimpa .modal-content */
        }

        /* 3. Pastikan div 'jumlah tawaran' memiliki latar belakang abu-abu terang yang diinginkan */
        /* Ini menargetkan div dengan kelas bg-light di dalam modal body */
        #confirmAcceptOfferModal .modal-body .bg-light.p-4.rounded-3 {
            background-color: #f8f9fa !important;
            /* Warna abu-abu terang dari Bootstrap */
            border-color: #e9ecef !important;
            /* Warna border yang cocok */
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
             background-color: transparent;
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
                            <p>{{ __('chat-job-req.tidak_ada_percakapan_aktif') }}</p>
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
                            <img src="{{ $chatRoom->worker->photo_url_user ? asset($chatRoom->worker->photo_url_user) : asset('Image/Icon/user-circle.svg') }}" alt="Profil" id="profileIcon"
                                onerror="this.onerror=null;this.src='{{ asset('Image/Icon/user-circle.svg') }}';"
                                style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;" />
                        </div>
                        <div class="me-auto">
                            <h5 class="fw-bold mb-0">{{ $chatRoom->worker->first_name ?? '-' }}</h5>
                        </div>
                    </div>

                    {{-- Panel Tawaran --}}
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

                    {{-- Form Input Pesan --}}
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
                    {{-- Tampilan Default Saat Belum Ada Chat Terpilih --}}
                    <div class="flex-grow-1 d-flex align-items-center justify-content-center text-muted bg-light">
                        <div class="text-center">
                            <i class="bi bi-chat-left-text" style="font-size: 3rem;"></i>
                            <p class="mt-2">{{ __('chat-job-req.pilih_percakapan') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Modal Konfirmasi --}}
        @if ($activeOffer)
            <div class="modal fade" id="confirmAcceptOfferModal" tabindex="-1"
                aria-labelledby="confirmAcceptOfferModalLabel" aria-hidden="true" wire:ignore.self>
                <div class="modal-dialog modal-dialog-centered modal-md"> {{-- modal-dialog-centered untuk vertikal tengah, modal-md untuk ukuran sedang --}}
                    <div class="modal-content border-0 rounded-4 shadow-lg"> {{-- shadow-lg untuk efek bayangan yang lebih menonjol --}}
                        <div class="modal-header border-0 pb-0 d-flex justify-content-between align-items-center">
                            <h5 class="modal-title fw-bold text-center w-100" id="confirmAcceptOfferModalLabel">
                                {{ __('chat-job-req.modal_judul') }}
                            </h5>
                            <button type="button" class="btn-close position-absolute end-0 me-3"
                                data-bs-dismiss="modal" aria-label="Close"></button> {{-- Tambah position-absolute agar tombol silang di pojok kanan atas --}}
                        </div>
                        <div class="modal-body p-4 text-center"> {{-- text-center untuk menengahkan konten modal body --}}
                            <p class="text-muted mb-4">{{ __('chat-job-req.modal_deskripsi') }}</p>

                            <div class="bg-light p-4 rounded-3 mb-4 border border-1"
                                style="background-color: #f8f9fa !important; border-color: #e9ecef !important;">
                                {{-- Tambah border untuk visual --}}
                                <h6 class="text-muted small fw-normal mb-2">
                                    {{ __('chat-job-req.modal_jumlah_tawaran') }}
                                </h6>
                                <h2 class="fw-bolder text-primary mb-3 display-4"> {{-- display-4 untuk ukuran teks lebih besar --}}
                                    Rp{{ number_format($activeOffer->amount ?? 0, 0, ',', '.') }}
                                </h2>
                                <h6 class="text-muted small fw-normal mb-1">{{ __('chat-job-req.modal_dari_pekerja') }}
                                </h6>
                                <h5 class="fw-bold mb-0">{{ $activeOffer->worker->first_name ?? 'N/A' }}</h5>
                                {{-- Tambah fallback 'N/A' --}}
                            </div>

                            <div class="alert alert-warning d-flex align-items-start p-3" role="alert">
                                {{-- align-items-start agar ikon di atas teks --}}
                                <i class="bi bi-exclamation-triangle-fill flex-shrink-0 me-3 fs-4"></i>
                                {{-- fs-4 untuk ukuran ikon lebih besar --}}
                                <div class="text-start"> {{-- text-start agar teks alert rata kiri --}}
                                    <strong class="d-block mb-1">{{ __('chat-job-req.modal_penting_header') }}</strong>
                                    <small>{{ __('chat-job-req.modal_penting_body') }}</small> {{-- Gunakan small tag untuk teks yang lebih kecil --}}
                                </div>
                            </div>
                        </div>
                        <div
                            class="modal-footer border-0 pt-0 d-flex justify-content-center flex-column flex-sm-row gap-2">
                            {{-- flex-column dan flex-sm-row untuk responsivitas tombol --}}
                            <button type="button" class="btn btn-secondary flex-fill rounded-pill py-2"
                                data-bs-dismiss="modal">{{ __('chat-job-req.modal_tombol_batal') }}</button>
                            <button type="button" class="btn btn-success fw-bold flex-fill rounded-pill py-2"
                                wire:click="respondToOffer({{ $activeOffer->id ?? 'null' }}, 'accepted')"
                                {{-- Tambah fallback 'null' --}} data-bs-dismiss="modal">
                                <i
                                    class="bi bi-check-circle-fill me-1"></i>{{ __('chat-job-req.modal_tombol_konfirmasi') }}
                                {{-- Ganti ke kunci lokalisasi yang lebih sesuai --}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
