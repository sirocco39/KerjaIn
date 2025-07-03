<style>
    /* CSS yang sudah ada */
    #wageInteractionContainer {
        position: relative;
        display: flex;
        align-items: center;
        gap: 0;
    }

    #upahForm {
        opacity: 0;
        transform: translateX(0%);
        pointer-events: none;
        position: absolute;
        right: 0;
        top: 0;
        height: 30px;
        width: 200px;
        transition: opacity 0.3s ease, transform 0.3s ease;
        z-index: 1;
        visibility: hidden;
        flex-shrink: 0;
    }

    #upahForm.show {
        opacity: 1;
        transform: translateX(-48%);
        pointer-events: auto;
        visibility: visible;
    }

    #upahForm .input-group {
        height: 100%;
        display: flex;
        align-items: center;
    }

    #upahForm .input-group-text {
        height: 100%;
        display: flex;
        align-items: center;
        padding-top: 0;
        padding-bottom: 0;
    }

    #inputNominal {
        height: 100%;
        padding-top: 0.375rem;
        padding-bottom: 0.375rem;
        text-align: left;
        padding-right: 12px;
        box-sizing: border-box;
    }

    /* Hapus semua CSS untuk menghilangkan panah pada input type="number"
                karena sekarang sudah menjadi type="text" */
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type="number"] {
        -moz-appearance: textfield;
    }

    #btnUpah {
        position: relative;
        z-index: 2;
        white-space: nowrap;
        width: 120px;
        font-size: 16px;
        height: 30px;
        background-color: #103F91;
        border-radius: 999px;
        transition: background-color 0.3s ease, width 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 15px;
    }

    /* ----- Revisi CSS untuk Penjajaran Margin Atas ----- */
    .col-lg-5.job-list-col {
        padding-top: 0.8rem !important;
        padding-bottom: 1.5rem !important;
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }

    .col-lg-7.chat-col {
        padding: 0 !important;
    }

    .chat-container-main {
        height: 90vh;
        background-color: #F7F7FF;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
        border-bottom-left-radius: 0.5rem;
        border-bottom-right-radius: 0.5rem;
        overflow: hidden;
    }

    /* Pastikan header chat memiliki padding internal yang konsisten */
    .header-chat {
        padding: 1rem !important;
        background-color: #133E87;
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
    }

    /* Tambahkan wrapper untuk border-left */
    .chat-wrapper {
        border-left: 1px solid #ddd;
        height: 100%;
        padding: 0;
    }

    /* Pastikan area tombol juga memiliki padding vertikal dan sejajar */
    .chat-button-area {
        padding: 0.5rem 1rem !important;
        border-bottom: 1px solid #ddd;
        background-color: #F7F7FF;
        display: flex;
        /* Tambahkan flexbox */
        justify-content: space-between;
        /* Sejajarkan item ke ujung */
        align-items: center;
        /* Pusatkan item secara vertikal */
        flex-wrap: wrap;
        /* Biarkan item wrap ke baris baru jika ruang tidak cukup */
    }

    /* Gaya untuk container nominal upah di area tombol */
    #wageDisplayContainer {
        display: flex;
        align-items: center;
        /* Tambahkan sedikit margin agar tidak terlalu mepet dengan tombol */
        margin-right: auto;
        /* Mendorong elemen ke kiri sejauh mungkin */
    }

    #wageDisplayContainer small {
        color: #333;
        /* Warna teks yang cocok dengan background terang */
        white-space: nowrap;
        /* Pastikan teks tidak patah */
    }

    #wageDisplayContainer strong {
        color: #103F91;
        /* Warna harga yang menonjol */
        white-space: nowrap;
        /* Pastikan harga tidak patah */
        margin-left: 5px;
        /* Jarak antara teks dan harga */
    }


    /* Pastikan area pesan chat memiliki padding internal */
    .chat-messages-area {
        padding: 1rem !important;
        flex-grow: 1;
        overflow-y: auto;
        background-color: #FFFFFF;
    }

    /* Pastikan input pesan memiliki padding internal */
    .chat-input-area {
        padding: 1rem !important;
        background-color: #F7F7FF;
        border-bottom-left-radius: 0.5rem;
        border-bottom-right-radius: 0.5rem;
        border-top: 1px solid #ddd;
    }

    /* CSS untuk mendekatkan "Kak Cung" dan "Online" */
    .header-chat .d-flex.align-items-center>div {
        line-height: 1.2;
    }

    .header-chat .d-flex.align-items-center>div small {
        margin-top: -0.2em;
    }

    /* CSS untuk indikator online (bulatan hijau) */
    .online-indicator {
        display: inline-block;
        width: 24px;
        height: 24px;
        background-color: #D3FA0D;
        border-radius: 50%;
        flex-shrink: 0;
    }

    /* ----------- BUBBLE RECEIVE PERBAIKAN JARAK ----------- */
    .bubble-receive {
        max-width: 50%;
        border-radius: 0 999px 999px 999px;
        background-color: #E0E0E0;
        padding: 0.5rem 1rem;
        /* Tambahkan padding di sini */
    }

    .bubble-receive small {
        margin-bottom: 0;
        /* Pastikan small tidak memiliki margin bawah yang tidak diinginkan */
    }

    /* ----------- BUBBLE SENDER (tidak ada perubahan besar pada jarak, sudah ok) ----------- */
    .bubble-sender {
        max-width: 50%;
        border-radius: 999px 0 999px 999px;
        background-color: #103F91;
        color: white;
        padding: 0.5rem 1rem;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        word-wrap: break-word;
        overflow-wrap: break-word;
        white-space: normal;
    }

    .bubble-sender p {
        margin-bottom: 0;
        text-align: right;
        max-width: 100%;
    }

    .bubble-sender .timestamp-status {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 4px;
        font-size: 10px;
        margin-top: 4px;
        color: rgba(255, 255, 255, 0.7);
        white-space: nowrap;
    }

    .bubble-sender .timestamp-status img {
        height: 12px;
        width: auto;
        vertical-align: middle;
    }

    /* Optional: Jika Anda ingin membedakan warna icon read/unread */
    .bubble-sender .timestamp-status img.read {
        filter: brightness(1.2);
    }

    .bubble-sender .timestamp-status img.unread {
        filter: grayscale(1);
    }

    /* --- CSS Responsif --- */
    /* Sembunyikan bagian chat secara default pada layar kecil, dan atur kolom daftar menjadi 100% */
    @media (max-width: 991.98px) {

        /* Bootstrap's lg breakpoint for tablet/desktop */
        .col-lg-5.job-list-col {
            width: 100%;
            /* Ambil seluruh lebar */
            height: auto !important;
            /* Biarkan tinggi menyesuaikan konten */
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }

        .col-lg-7.chat-col {
            display: none;
            /* Sembunyikan kolom chat secara default pada mobile */
            width: 100%;
            padding: 0;
            position: fixed;
            /* Jadikan chat full screen saat aktif */
            top: 0;
            left: 0;
            height: 100vh;
            background-color: #F7F7FF;
            /* Sesuaikan dengan background chat-container-main */
            z-index: 1050;
            /* Pastikan di atas elemen lain */
            overflow-y: auto;
            /* Memungkinkan scroll jika chat panjang */
        }

        /* Tampilkan chat ketika kelas 'active' ditambahkan */
        .col-lg-7.chat-col.active {
            display: block;
        }

        .chat-container-main {
            height: 100vh;
            /* Sesuaikan dengan tinggi viewport pada mobile */
            border-radius: 0;
            /* Hilangkan border-radius pada chat di mobile */
        }

        .header-chat {
            border-radius: 0;
            /* Hilangkan border-radius pada header chat di mobile */
        }

        .chat-wrapper {
            border-left: none;
            /* Hilangkan border-left pada chat di mobile */
            padding: 0 !important;
            /* Hapus padding agar chat menempel penuh */
        }

        .chat-input-area {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        /* Tambahkan tombol kembali pada header chat untuk mobile */
        .header-chat .back-button {
            display: block;
            /* Tampilkan tombol kembali */
            margin-right: 15px;
            /* Jarak antara tombol kembali dan avatar */
            color: white;
            font-size: 1.5rem;
        }

        /* Penyesuaian untuk chat-button-area di mobile */
        .chat-button-area {
            flex-direction: column;
            /* Tumpuk item secara vertikal di mobile */
            align-items: flex-start;
            /* Sejajarkan item ke kiri */
            gap: 0.5rem;
            /* Beri sedikit jarak vertikal antar item */
        }

        #wageDisplayContainer {
            margin-right: 0;
            /* Hapus auto margin di mobile */
        }

        #wageInteractionContainer {
            width: 100%;
            /* Agar tombol dan form mengambil lebar penuh */
            justify-content: flex-end;
            /* Sejajarkan ke kanan di mobile */
        }
    }

    /* Pastikan tombol kembali tersembunyi di desktop */
    @media (min-width: 992px) {
        .header-chat .back-button {
            display: none;
        }
    }
</style>

<div class="container-fluid">
    <div class="row">
        {{-- Bagian List Card --}}
        <div class="col-lg-5 bg-light overflow-auto job-list-col mt-2" id="jobListColumn" style="height: 90vh;">
            <h5 class="fw-bold mb-4 ps-3">Pesan Pekerjaan</h5>
            @forelse ($chatRooms as $room)
                <div wire:click="selectRoom({{ $room->id }})"
                    class="card mb-3 cursor-pointer job-card job-card-wrapper {{ $selectedRoom && $room->id === $selectedRoom->id ? 'bg-light border-start border-primary' : 'hover:bg-light' }}"
                    style="border-left-width: 4px; border-color: transparent;">
                    <div class="card-body">
                        <h6
                            class="card-title mb-1 fw-bold {{ $room->has_unread_messages && (!$selectedRoom || $room->id !== $selectedRoom->id) ? 'fw-bold' : '' }}">
                            {{ $room->request->title }}
                        </h6>
                        <p class="card-text mb-0 text-muted small">Requester: {{ $room->requester->first_name }}</p>
                        @if ($room->lastMessage)
                            <p
                                class="card-text mb-0 small text-truncate {{ !$selectedRoom || $room->id !== $selectedRoom->id ? ($room->has_unread_messages ? 'text-dark fw-bold' : 'text-muted') : 'text-muted' }}">
                                {{ $room->lastMessage->message }}
                            </p>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-muted ps-3">Tidak ada percakapan.</p>
            @endforelse
        </div>

        {{-- Bagian Chat --}}
        <div class="col-lg-7 chat-col" id="chatColumn">
            <div class="chat-wrapper p-4">
                <div class="chat-container-main">
                    @if ($selectedRoom)
                        {{-- HEADER CHAT --}}
                        <div class="d-flex align-items-center justify-content-between header-chat rounded-top-5">
                            <div class="d-flex align-items-center">
                                {{-- Tombol Kembali (hanya terlihat di mobile) --}}
                                <button class="btn btn-link back-button p-0" id="backToJobList">
                                    <i class="bi bi-arrow-left-circle-fill" style="color: white;"></i>
                                </button>
                                <div class="rounded-circle bg-secondary me-3" style="width: 40px; height: 40px;">
                                </div>
                                <div>
                                    <strong>{{ $selectedRoom->requester->first_name }}
                                        {{ $selectedRoom->requester->last_name }}</strong><br>
                                    <small class="text-success">Online</small>
                                </div>
                            </div>
                            {{-- Bulatan hijau dipindahkan ke sini --}}
                            <span class="online-indicator me-2"></span>
                        </div>

                        {{-- AREA TOMBOL TAWAR UPAH, BATAL AJUKAN, dan NOMINAL UPAH --}}
                        <div class="chat-button-area bg-white border" wire:poll.5s="loadActiveOffer">
                            {{-- Elemen untuk Nominal Upah yang Diajukan --}}
                            <div id="wageDisplayContainer">
                                <small>Nominal Upah Anda:</small>
                                <strong id="nominalDiajukan">
                                    @if ($activeOffer)
                                        Rp{{ number_format($activeOffer->amount, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </strong>
                                @if ($activeOffer)
                                    <span
                                        class="badge ms-2 
                                        @switch($activeOffer->status)
                                            @case('pending') bg-warning text-dark @break
                                            @case('rejected') bg-danger @break
                                            @case('accepted') bg-success @break
                                        @endswitch">
                                        Status: {{ ucfirst($activeOffer->status) }}
                                    </span>
                                @endif
                            </div>

                            <div class="d-flex align-items-center gap-2 border-2" id="wageInteractionContainer">
                                <form id="upahForm" class="upah-input-form" wire:submit.prevent="makeOffer">
                                    <div class="input-group rounded-pill shadow-sm border border-2 border-primary"
                                        style="overflow: hidden;">
                                        <span class="input-group-text bg-white border-0 fw-bold px-3">Rp</span>
                                        <input type="text" id="inputNominal" wire:model.defer="offerAmount"
                                            class="form-control border-0" placeholder="150000"
                                            style="box-shadow: none;">
                                    </div>
                                    @error('offerAmount')
                                        <span class="text-danger small mt-1">{{ $message }}</span>
                                    @enderror
                                </form>

                                <button id="btnUpah" class="btn fw-bold text-white"
                                    @if ($activeOffer && $activeOffer->status === 'pending') disabled @endif onclick="toggleUpahForm()">
                                    @if ($activeOffer && $activeOffer->status !== 'pending')
                                        Tawar Upah Baru
                                    @else
                                        Tawar Upah
                                    @endif
                                </button>
                            </div>
                            <button class="btn btn-danger btn-sm ms-2 rounded-5 fw-bold"
                                style="font-size: 16px; height: 30px; align-items: center; display: flex;"
                                wire:click="cancelOffer" @if (!$activeOffer || $activeOffer->status !== 'pending') disabled @endif>
                                Batal Ajukan
                            </button>
                        </div>

                        {{-- AREA CHAT UTAMA --}}
                        <div x-data x-ref="chatBody"
                            @scroll-to-bottom.window="$nextTick(() => { $refs.chatBody.scrollTop = $refs.chatBody.scrollHeight; })"
                            class="chat-messages-area border" wire:poll.3s>
                            @forelse ($this->messages as $date => $groupedMessages)
                                <p class="text-center text-muted small">
                                    {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</p>
                                @foreach ($groupedMessages as $msg)
                                    @if ($msg->sender_id === auth()->id())
                                        {{-- Bubble Pengirim --}}
                                        <div class="d-flex justify-content-end mb-2">
                                            <div class="shadow-sm bubble-sender">
                                                <p>{{ $msg->message }}</p>
                                                <small class="timestamp-status">
                                                    <span>{{ $msg->created_at->format('H:i') }}</span>
                                                    @php
                                                        $isRead = $msg->is_read;
                                                    @endphp
                                                    @if ($isRead)
                                                        <img src="{{ asset('Image/Icon/icon-read.svg') }}"
                                                            alt="Read" class="read">
                                                    @else
                                                        <img src="{{ asset('Image/Icon/icon-Noread.svg') }}"
                                                            alt="Unread" class="unread">
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    @else
                                        {{-- Bubble Penerima --}}
                                        <div class="d-flex justify-content-start mb-2">
                                            <div class="shadow-sm bubble-receive">
                                                <small>{{ $msg->message }}</small>
                                                <small class="text-muted d-block text-end pe-2"
                                                    style="font-size: 10px; margin-top: 4px;">{{ $msg->created_at->format('H:i') }}</small>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @empty
                                <div class="text-center text-muted mt-5">Belum ada pesan.</div>
                            @endforelse
                        </div>

                        {{-- INPUT PESAN --}}
                        <div class="chat-input-area bg-white rounded-bottom-5 border">
                            <form wire:submit.prevent="send" class="d-flex">
                                <input wire:model.defer="newMessage" x-data @clear-input.window="$el.value = ''"
                                    type="text" class="form-control me-2 rounded-pill bg-light"
                                    placeholder="Tulis pesan..." autocomplete="off">
                                <button type="submit" class="btn btn-primary rounded-circle">
                                    <i class="bi bi-send"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <div
                            class="flex-grow-1 d-flex align-items-center justify-content-center text-muted bg-light chat-container-main">
                            <div class="text-center">
                                <i class="bi bi-chat-dots" style="font-size: 3rem;"></i>
                                <p class="mt-2">Pilih percakapan untuk ditampilkan.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    let isFormVisible = false;

    function toggleUpahForm() {
        const form = document.getElementById('upahForm');
        const button = document.getElementById('btnUpah');
        const input = document.getElementById('inputNominal');

        if (!isFormVisible) {
            form.style.visibility = 'visible';
            form.classList.add('show');
            button.textContent = 'Kirim';
            input.focus();
        } else {
            // Ketika tombol 'Kirim' diklik, trigger event submit Livewire form
            Livewire.emit('makeOffer'); // Memanggil metode makeOffer di komponen Livewire

            form.classList.remove('show');
            form.addEventListener('transitionend', function handler() {
                form.style.visibility = 'hidden';
                form.removeEventListener('transitionend', handler);
            });

            button.textContent = 'Tawar Upah';
            input.value = ''; // Kosongkan input setelah mengirim
        }

        isFormVisible = !isFormVisible;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const inputNominal = document.getElementById('inputNominal');
        const jobListColumn = document.getElementById('jobListColumn');
        const chatColumn = document.getElementById('chatColumn');
        const backButton = document.getElementById('backToJobList');
        const jobCards = document.querySelectorAll('.job-card'); // Pilih semua elemen card pekerjaan

        function formatRupiah(angka) {
            let number_string = angka.toString().replace(/[^0-9]/g, '');
            if (number_string === '') return '';

            let sisa = number_string.length % 3;
            let rupiah = number_string.substr(0, sisa);
            let ribuan = number_string.substr(sisa).match(/\d{3}/g);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            return rupiah;
        }

        inputNominal.addEventListener('input', function(e) {
            const originalLength = this.value.length;
            let cursorPosition = this.selectionStart;

            let rawValue = this.value.replace(/[^0-9]/g, '');
            let formattedValue = formatRupiah(rawValue);
            this.value = formattedValue;

            const newLength = this.value.length;
            const lengthDifference = newLength - originalLength;

            if (cursorPosition !== null) {
                cursorPosition += lengthDifference;
                if (cursorPosition < 0) cursorPosition = 0;
                if (cursorPosition > newLength) cursorPosition = newLength;
                this.setSelectionRange(cursorPosition, cursorPosition);
            }
        });

        inputNominal.addEventListener('blur', function() {
            let rawValue = this.value.replace(/[^0-9]/g, '');
            if (rawValue === '') {
                this.value = '';
            } else {
                this.value = formatRupiah(rawValue);
            }
        });

        // --- Logika untuk Responsif Mobile ---
        function checkScreenSize() {
            if (window.innerWidth < 992) { // Ukuran mobile/tablet
                // Sembunyikan chat dan tampilkan daftar pekerjaan saat pertama kali load di mobile
                if (!chatColumn.classList.contains(
                        'active')) { // Pastikan hanya tersembunyi jika chat tidak sedang aktif
                    jobListColumn.style.display = 'block';
                    chatColumn.style.display = 'none';
                }
            } else { // Ukuran desktop
                jobListColumn.style.display = 'block';
                chatColumn.style.display = 'block';
                chatColumn.classList.remove('active'); // Hapus kelas active jika beralih ke desktop
            }
        }

        // Tambahkan event listener untuk setiap job card
        jobCards.forEach(card => {
            card.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    jobListColumn.style.display = 'none'; // Sembunyikan daftar pekerjaan
                    chatColumn.style.display = 'block'; // Tampilkan kolom chat
                    chatColumn.classList.add('active'); // Tambahkan kelas aktif
                }
                // Trigger Livewire action to select the room
                const roomId = this.getAttribute('wire:click').match(/\((\d+)\)/)[1];
                Livewire.emit('selectRoom', roomId);
            });
        });

        // Event listener untuk tombol kembali
        if (backButton) {
            backButton.addEventListener('click', function() {
                jobListColumn.style.display = 'block'; // Tampilkan daftar pekerjaan
                chatColumn.style.display = 'none'; // Sembunyikan kolom chat
                chatColumn.classList.remove('active'); // Hapus kelas aktif
            });
        }

        // Panggil saat halaman dimuat dan saat ukuran layar berubah
        checkScreenSize();
        window.addEventListener('resize', checkScreenSize);

        // Listener untuk event Livewire agar form disembunyikan setelah berhasil submit
        Livewire.on('offerMade', () => {
            const form = document.getElementById('upahForm');
            const button = document.getElementById('btnUpah');
            const input = document.getElementById('inputNominal');

            form.classList.remove('show');
            form.addEventListener('transitionend', function handler() {
                form.style.visibility = 'hidden';
                form.removeEventListener('transitionend', handler);
            });

            button.textContent = 'Tawar Upah';
            input.value = ''; // Kosongkan input setelah mengirim
            isFormVisible = false; // Reset status form
        });
    });
</script>
