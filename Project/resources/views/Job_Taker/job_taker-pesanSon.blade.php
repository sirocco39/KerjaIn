@extends('Master.master-job_taker')

@section('content')
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
            /* Hapus border-left dari sini, kita akan letakkan di .chat-wrapper */
            background-color: #F7F7FF;
            padding-top: 1.6rem;
            /* Kunci penyesuaian */
            padding-bottom: 0;
            padding-left: 0;
            padding-right: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            /* Border radius utama diterapkan di sini */
            border-top-left-radius: 0.5rem;
            /* Setel ulang radius agar sesuai */
            border-top-right-radius: 0.5rem;
            /* Setel ulang radius agar sesuai */
            border-bottom-left-radius: 0.5rem;
            /* Juga untuk bawah */
            border-bottom-right-radius: 0.5rem;
            /* Juga untuk bawah */
            /* Tambahkan overflow hidden agar border radius bekerja sempurna */
            overflow: hidden;
        }

        /* Pastikan header chat memiliki padding internal yang konsisten */
        .header-chat {
            padding: 1rem !important;
            background-color: #133E87;
            color: white;
            display: flex;
            /* Pastikan flex display aktif */
            align-items: center;
            /* Menyelaraskan vertikal di tengah */
            justify-content: space-between;
            /* Mendorong konten ke ujung-ujung */
        }

        /* Tambahkan wrapper untuk border-left */
        .chat-wrapper {
            border-left: 1px solid #ddd;
            /* Border di sini agar tidak mengganggu radius */
            height: 100%;
            /* Pastikan mengisi tinggi kolom */
        }

        /* Pastikan area tombol juga memiliki padding vertikal */
        .chat-button-area {
            padding: 0.5rem 1rem !important;
        }

        /* Pastikan area pesan chat memiliki padding internal */
        .chat-messages-area {
            padding: 1rem !important;
        }

        /* Pastikan input pesan memiliki padding internal */
        .chat-input-area {
            padding: 1rem !important;
        }

        /* CSS untuk mendekatkan "Kak Cung" dan "Online" */
        .header-chat .d-flex.align-items-center>div {
            line-height: 1.2;
        }

        .header-chat .d-flex.align-items-center>div small {
            /* Hapus display: flex; dan align-items: center; dari sini */
            margin-top: -0.2em;
        }

        /* CSS untuk indikator online (bulatan hijau) */
        .online-indicator {
            display: inline-block;
            width: 24px;
            /* Lebar bulatan */
            height: 24px;
            /* Tinggi bulatan */
            background-color: #D3FA0D;
            /* Warna hijau sesuai gambar */
            border-radius: 50%;
            /* Membuatnya bulat sempurna */
            flex-shrink: 0;
            /* Mencegah bulatan mengecil jika ruang terbatas */
            /* Tidak perlu margin-left karena sudah diatur oleh justify-content: space-between */
        }
        .bubble-receive{
            width: 40%;
            border-radius: 0 999px 999px 999px;
        }
        .bubble-sender{
            
        }
    </style>
    <div class="container-fluid">
        <div class="row">
            {{-- Bagian List Card --}}
            <div class="col-lg-5 bg-light overflow-auto job-list-col" style="height: 90vh;">
                <x-job-taker.card-request />
                <x-job-taker.card-request />
                <x-job-taker.card-request />
                </div>

            {{-- Bagian Chat --}}
            <div class="col-lg-7 chat-col">
                {{-- Tambahkan wrapper baru untuk border-left --}}
                <div class="chat-wrapper">
                    <div class="rounded-top-5 chat-container-main p-3">

                        {{-- HEADER CHAT --}}
                        <div
                            class="d-flex align-items-center justify-content-between header-chat rounded-top-5">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-secondary me-3"
                                    style="width: 40px; height: 40px;"></div>
                                <div>
                                    <strong>Kak Cung</strong><br>
                                    <small class="text-success">Online</small>
                                    </div>
                                </div>
                            {{-- Bulatan hijau dipindahkan ke sini --}}
                            <span class="online-indicator me-3"></span>
                            </div>

                        {{-- AREA TOMBOL TAWAR UPAH DAN BATAL AJUKAN --}}
                        <div
                            class="d-flex justify-content-end align-items-center chat-button-area bg-light border-bottom border-start border-end">
                            <div class="d-flex align-items-center gap-2 border-2"
                                id="wageInteractionContainer">
                                <form id="upahForm" class="upah-input-form">
                                    <div
                                        class="input-group rounded-pill shadow-sm border border-2 border-primary"
                                        style="overflow: hidden;">
                                        <span
                                            class="input-group-text bg-white border-0 fw-bold px-3">Rp</span>
                                        <input type="text" id="inputNominal"
                                            class="form-control border-0"
                                            placeholder="150000" style="box-shadow: none;">
                                        </div>
                                    </form>

                                <button id="btnUpah" class="btn fw-bold text-white"
                                    onclick="toggleUpahForm()">
                                    Tawar Upah
                                    </button>
                                </div>
                            <button class="btn btn-danger btn-sm ms-2 rounded-5 fw-bold"
                                style="font-size: 16px;">Batal Ajukan</button>
                            </div>

                        {{-- AREA CHAT UTAMA --}}
                        <div
                            class="flex-grow-1 overflow-auto chat-messages-area border-start border-end"
                            style="background-color: #FFFFFF;">
                            <p class="text-center text-muted small">18 Mei 2025</p>

                            <div class="d-flex justify-content-start mb-2">
                                <div class="p-2 shadow-sm bubble-receive" style="background-color: #E0E0E0;">
                                    <small>Enak aja kalo ngomong, orang lu yang kacung.
                                        Ngaca dong, cepetan jadi kerja gak
                                        nih?</small><br>
                                    <small class="text-muted d-block text-end"
                                        style="font-size: 10px;">17.06</small>
                                    </div>
                                </div>

                            <div class="d-flex justify-content-end mb-2">
                                <div class="bg-primary text-white p-2 shadow-sm bubble-sender">
                                    <small>Selamat siang Kacung! Monyet</small><br>
                                    <small class="text-end d-block"
                                        style="font-size: 10px;">17.06 ✅✅</small>
                                    </div>
                                </div>

                        {{-- INPUT PESAN --}}
                        <div
                            class="bg-white border-top chat-input-area rounded-bottom-5 border-bottom border-end border-start border-top-0">
                            <form class="d-flex">
                                <input type="text"
                                    class="form-control me-2 rounded-pill bg-light" placeholder="Tulis pesan...">
                                <button class="btn btn-primary rounded-circle"
                                    type="submit">
                                    <i class="bi bi-send"></i>
                                    </button>
                                </form>
                            </div>
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
                const nilaiYangDikirim = input.value.replace(/\./g, '');
                console.log('Mengirim tawaran: Rp ' + nilaiYangDikirim);

                // --- Lakukan pengiriman data ke backend di sini ---
                // ... (kode pengiriman data tetap sama) ...

                form.classList.remove('show');
                form.addEventListener('transitionend', function handler() {
                    form.style.visibility = 'hidden';
                    form.removeEventListener('transitionend', handler);
                });

                button.textContent = 'Tawar Upah';
                input.value = '';
            }

            isFormVisible = !isFormVisible;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const inputNominal = document.getElementById('inputNominal');

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
        });
    </script>
@endsection
