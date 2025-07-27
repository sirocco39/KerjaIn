@extends('master.master-job-taker')

@section('content')
    <x-search></x-search>

    <div class="container-fluid pembatas-x pembatas-y d-flex gap-4">
        <div class="job-listings col d-flex flex-column gap-4">
            @forelse ($workRequests as $request)
                {{-- Removed onclick from job-card div --}}
                <div class="job-card p-4" data-request-id="{{ $request->id }}">
                    <h3 class="fw-bold">{{ $request->title }}</h3>
                    <ul class="job-card-details">
                        <li class="gap-2">
                            <div class="icon-wrapper">
                                <img src="{{ asset('Image/Icon/icon-profile.svg') }}" alt="Icon Profile">
                            </div>
                            <span>Kak {{ $request->requester->first_name }} {{ $request->id }}</span>
                        </li>

                        <li class="gap-2">
                            <div class="icon-wrapper">
                                <img src="{{ asset('Image/Icon/icon-address.svg') }}" alt="Icon Address">
                            </div>
                            <span>{{ $request->location }}</span>
                        </li>

                        <li class="gap-2">
                            <div class="icon-wrapper">
                                <img src="{{ asset('Image/Icon/icon-date.svg') }}" alt="Icon Address">
                            </div>
                            <span>{{ $request->start_time->format('d M Y, H:i') }}</span>
                        </li>

                        <li class="gap-2">
                            <div class="icon-wrapper">
                                <img src="{{ asset('Image/Icon/icon-clock.svg') }}" alt="Icon Address">
                            </div>
                            <span>{{ $request->end_time->format('d M Y, H:i') }}</span>
                        </li>

                        <li class="gap-2">
                            <div class="icon-wrapper">
                                <img src="{{ asset('Image/Icon/icon-dollar.svg') }}" alt="Icon Address">
                            </div>
                            <span>Rp{{ number_format($request->price, 2, ',', '.') }}</span>
                        </li>
                        <button class="detail-button" data-request-id="{{ $request->id }}">DETAIL</button>
                    </ul>
                </div>
            @empty
                <p>Tidak ada lowongan kerja yang ditemukan.</p>
            @endforelse

            {{-- Pagination Links --}}
            <div class="pagination mt-auto">
                @if ($workRequests->currentPage() > 1)
                    <a href="{{ $workRequests->previousPageUrl() }}" class="pagination-nav" id="prev-page">
                        <img src="{{ asset('Image/Icon/icon-previous.svg') }}" alt="Previous" class="pagination-icon">
                        <span>Sebelumnya</span>
                    </a>
                @endif

                <div class="page-numbering d-flex gap-3">
                    @for ($i = 1; $i <= $workRequests->lastPage(); $i++)
                        <a href="{{ $workRequests->url($i) }}"
                            class="page-number d-flex align-items-center justify-content-center {{ $workRequests->currentPage() == $i ? 'item-active' : '' }}">
                            {{ $i }}
                        </a>
                    @endfor
                </div>

                @if ($workRequests->hasMorePages())
                    <a href="{{ $workRequests->nextPageUrl() }}" class="pagination-nav" id="next-page">
                        <span>Selanjutnya</span>
                        <img src="{{ asset('Image/Icon/icon-next.svg') }}" alt="Next" class="pagination-icon">
                    </a>
                @endif
            </div>
        </div>

        <div class="job-details-placeholder col d-flex flex-column p-4" id="job-details-panel">
            {{-- Initial content for the right panel --}}
            {{-- <i class="fas fa-chevron-left back-arrow" id="back-arrow" onclick="hideRequestDetails()"></i> --}}
            <div class="title-job-detail-placeholder d-flex align-item-center mb-3">
                <img src="{{ asset('Image/Icon/icon-left-arrow.svg') }}" alt="Left Arrow" class="left-arrow"
                    id="left-arrow-icon">
                <h1 class="fw-bold m-0" id="detail-title">Pilih Lowongan Kerja di Kiri</h1>
            </div>
            <p id="detail-instruction">Tampilkan Detail Di sini</p>
            <div class="img-job-detail-placeholder align-items-center justify-content-center"
                id="detail-image-placeholder-wrapper">
                <img src="{{ asset('Image/Icon/people-search.svg') }}" alt="People Search" class="placeholder-img"
                    id="detail-image">
            </div>

            {{-- These elements will be dynamically populated by JavaScript --}}
            {{-- Hide them initially, show when details are loaded --}}
            <div class="mx-3 flex-fill flex-column" id="dynamic-details-content" style="display: none;">
                <ul class="job-card-details">
                    <li class="gap-2">
                        <div class="icon-wrapper">
                            <img src="{{ asset('Image/Icon/icon-profile.svg') }}" alt="Icon Profile">
                        </div>
                        <span>Kak <span id="detail-profile"></span></span>
                    </li>

                    <li class="gap-2">
                        <div class="icon-wrapper">
                            <img src="{{ asset('Image/Icon/icon-address.svg') }}" alt="Icon Address">
                        </div>
                        <span id="detail-location"></span>
                    </li>

                    <li class="gap-2">
                        <div class="icon-wrapper">
                            <img src="{{ asset('Image/Icon/icon-date.svg') }}" alt="Icon Address">
                        </div>
                        <span id="detail-date"></span>
                    </li>

                    <li class="gap-2">
                        <div class="icon-wrapper">
                            <img src="{{ asset('Image/Icon/icon-clock.svg') }}" alt="Icon Address">
                        </div>
                        <span><span id="detail-time"></span></span>
                    </li>

                    <li class="gap-2">
                        <div class="icon-wrapper">
                            <img src="{{ asset('Image/Icon/icon-dollar.svg') }}" alt="Icon Address">
                        </div>
                        Rp<span id="detail-price-value"></span>
                    </li>
                </ul>
                <h5 class="detail-description fw-bold d-flex mt-3">Deskripsi:</h5>
                <div class="wrapDesc_ori">
                    <p class="mb-3" id="detail-description-text"></p>
                </div>

                <div class="detail-buttons-placeholder d-flex gap-2 justify-content-end mt-auto align-items-center">
                    <input type="number" id="offer-amount-input" class="form-control" placeholder="Rp Tawarkan"
                        style="max-width: 150px; height: 38px;">
                    <button class="details-button-item" id="submit-offer-button">Kirim</button>
                    <a href="#" class="details-button-item" id="button-hubungi">Hubungi</a>
                    <button class="details-button-item btn-terima-modal">Terima</button>
                    {{-- <form action="{{ route('job-taker.accept-request', $request->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="details-button-item" id="button-terima">Terima</button>
                    </form> --}}

                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="job-details-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <button type="button" class="btn-close" id="modal-close-button" aria-label="Close"></button>
                </div>
                <div id="modal-content-container" class="p-3">
                    <h1 class="fw-bold mb-3" id="modal-detail-title">Pilih Lowongan Kerja di Kiri</h1>
                    <ul class="job-card-details">
                        <li class="gap-2">
                            <div class="icon-wrapper">
                                <img src="{{ asset('Image/Icon/icon-profile.svg') }}" alt="Icon Profile">
                            </div>
                            <span>Kak <span id="modal-detail-profile"></span></span>
                        </li>

                        <li class="gap-2">
                            <div class="icon-wrapper">
                                <img src="{{ asset('Image/Icon/icon-address.svg') }}" alt="Icon Address">
                            </div>
                            <span id="modal-detail-location"></span>
                        </li>

                        <li class="gap-2">
                            <div class="icon-wrapper">
                                <img src="{{ asset('Image/Icon/icon-date.svg') }}" alt="Icon Date">
                            </div>
                            <span id="modal-detail-date"></span>
                        </li>

                        <li class="gap-2">
                            <div class="icon-wrapper">
                                <img src="{{ asset('Image/Icon/icon-clock.svg') }}" alt="Icon Clock">
                            </div>
                            <span id="modal-detail-time"></span>
                        </li>

                        <li class="gap-2">
                            <div class="icon-wrapper">
                                <img src="{{ asset('Image/Icon/icon-dollar.svg') }}" alt="Icon Dollar">
                            </div>
                            Rp<span id="modal-detail-price-value"></span>
                        </li>
                    </ul>
                    <h5 class="detail-description fw-bold d-flex mt-3">Deskripsi:</h5>
                    <div class="wrapDesc mb-3">
                        <p class="mb-0" id="modal-detail-description-text"></p>
                    </div>

                    <div class="detail-buttons-placeholder d-flex gap-2 justify-content-center mt-auto align-items-center">
                        <input type="number" id="modal-offer-amount-input" class="form-control"
                            placeholder="Rp Tawarkan" style="max-width: 150px; height: 38px;">
                        <button class="details-button-item" id="modal-submit-offer-button">Kirim</button>
                        <a href="#" class="details-button-item btn-hubungi-modal">Hubungi</a>
                        <button class="details-button-item btn-terima-modal">Terima</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="accept-job-confirmation-modal" tabindex="-1" aria-labelledby="acceptJobModalLabel"
        aria-hidden="true">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    {{-- Pastikan icon-warning.svg ada di public/Image/Icon/ atau sesuaikan path --}}
                    <img src="{{ asset('Image/Icon/icon-warning.svg') }}" alt="Warning Icon"
                        style="width: 60px; height: 60px; margin-bottom: 20px;">
                    <h2 class="fw-bold mb-3">Menerima Pekerjaan</h2>
                    <p>Apakah anda yakin ingin menerima pekerjaan ini?</p>
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            id="cancel-accept-job">Kembali</button>
                        <button type="button" class="btn btn-primary" id="confirm-accept-job">Ya, Saya
                            Yakin</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk format mata uang Rupiah
        const formatRupiah = (amount) => {
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(amount);
        };
        let currentRequestId = null;

        // Fungsi untuk mengirim tawaran
        function submitOffer(amount) {
            if (!currentRequestId) {
                let errorMessage = 'Gagal mendapatkan ID pekerjaan. Silakan pilih pekerjaan lagi.';
                window.showCustomAlert(errorMessage, 'error');
                return;
            }
            if (!amount || isNaN(amount) || parseFloat(amount) <= 0) {
                let errorMessage = 'Harap masukkan jumlah tawaran yang valid.';
                window.showCustomAlert(errorMessage, 'error');
                return;
            }

            const urlTemplate = `{{ route('chat.offer', ['requestId' => ':id']) }}`;
            const finalUrl = urlTemplate.replace(':id', currentRequestId);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(finalUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        amount: parseFloat(amount)
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.showCustomAlert(data.message || 'Tawaran berhasil dikirim!', 'success');
                        window.location.href = data.redirect_url;
                    } else {
                        window.showCustomAlert(data.message || 'Terjadi kesalahan saat mengirim tawaran.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.showCustomAlert('Terjadi kesalahan koneksi.' + error.message, 'error');
                });
        }


        // Fungsi untuk menampilkan detail permintaan kerja
        function showRequestDetails(requestId) {
            currentRequestId = requestId;
            // Hapus class 'active' dari semua job-card
            const leftArrowIcon = document.getElementById('left-arrow-icon');
            leftArrowIcon.style.display = 'none'; // tampilkan panah

            document.querySelectorAll('.job-card').forEach(card => {
                card.classList.remove('choosed');
            });

            // Tambahkan class 'active' ke kartu yang sesuai
            const activeCard = document.querySelector(`.job-card[data-request-id="${requestId}"]`);
            if (activeCard) {
                activeCard.classList.add('choosed');
            }
            const detailsPanel = document.getElementById('job-details-panel');
            const detailTitle = document.getElementById('detail-title');
            const detailInstruction = document.getElementById('detail-instruction');
            const detailImage = document.getElementById('detail-image');
            const detailImageWrapper = document.getElementById('detail-image-placeholder-wrapper');
            const dynamicContent = document.getElementById('dynamic-details-content');

            // Show loading state or clear previous content
            detailTitle.textContent = 'Memuat Detail...';
            detailInstruction.textContent = 'Silakan tunggu.';
            detailInstruction.style.display = 'block'; // Ensure instruction is visible during loading
            detailImage.style.display = 'block'; // Show placeholder image
            detailImageWrapper.style.display = 'flex'; // Ensure the image wrapper is visible
            dynamicContent.style.display = 'none'; // Hide dynamic content

            // Construct the URL using Laravel's route helper (via JS global var or direct string)
            const url = `{{ route('work_requests.show', ['request' => ':requestId']) }}`.replace(':requestId', requestId);

            const isMobile = window.innerWidth <= 992;

            const modal = document.getElementById('job-details-modal');

            if (isMobile) {
                modal.classList.add('show');
            }

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    document.querySelector('#button-hubungi').setAttribute(
                        'href',
                        `/hubungi/${requestId}`
                    );
                    document.querySelector('.btn-hubungi-modal').setAttribute(
                        'href',
                        `/hubungi/${requestId}`
                    );

                    // Populate the detail panel with fetched data
                    detailTitle.textContent = data.title;
                    detailInstruction.style.display = 'none'; // Hide "Tampilkan Detail Di sini" after loading
                    detailImage.style.display = 'none'; // Hide the default image
                    detailImageWrapper.style.display = 'none'; // Hide the image wrapper

                    document.getElementById('detail-profile').textContent = data.requester_first_name;
                    document.getElementById('detail-location').textContent = data.location;
                    document.getElementById('detail-date').textContent = data.display_date;
                    document.getElementById('detail-time').textContent = data.display_time_range;
                    document.getElementById('detail-description-text').textContent = data.description;
                    document.getElementById('detail-price-value').textContent = formatRupiah(data.price);

                    dynamicContent.style.display = 'flex'; // Show the populated dynamic content

                    if (isMobile) {
                        document.getElementById('modal-detail-title').textContent = data.title;
                        document.getElementById('modal-detail-profile').textContent = data.requester_first_name;
                        document.getElementById('modal-detail-location').textContent = data.location;
                        document.getElementById('modal-detail-date').textContent = data.display_date;
                        document.getElementById('modal-detail-time').textContent = data.display_time_range;
                        document.getElementById('modal-detail-description-text').textContent = data.description;
                        document.getElementById('modal-detail-price-value').textContent = formatRupiah(data.price);
                    }
                })
                .catch(error => {
                    console.error('Error fetching request details:', error);
                    detailTitle.textContent = 'Gagal Memuat Detail';
                    detailInstruction.textContent =
                        'Terjadi kesalahan saat memuat detail pekerjaan. Silakan coba lagi.';
                    detailInstruction.style.display = 'block';
                    detailImage.style.display = 'block';
                    detailImageWrapper.style.display = 'flex'; // Ensure the image wrapper is visible
                    dynamicContent.style.display = 'none';
                });
        }

        window.addEventListener('resize', () => {
            const isMobile = window.innerWidth <= 992;
            const modal = document.getElementById('job-details-modal');

            if (!isMobile && modal.classList.contains('show')) {
                modal.classList.remove('show');
            }

            if (isMobile && currentRequestId) {
                showRequestDetails(currentRequestId);
            }
        });

        // Fungsi untuk menyembunyikan detail permintaan kerja dan mengembalikan ke tampilan awal
        function hideRequestDetails() {
            const detailTitle = document.getElementById('detail-title');
            const detailInstruction = document.getElementById('detail-instruction');
            const detailImage = document.getElementById('detail-image');
            const detailImageWrapper = document.getElementById('detail-image-placeholder-wrapper');
            const dynamicContent = document.getElementById('dynamic-details-content');

            document.querySelectorAll('.job-card').forEach(card => {
                card.classList.remove('choosed');
            });

            detailTitle.textContent = 'Pilih Lowongan Kerja di Kiri';
            detailInstruction.textContent = 'Tampilkan Detail Di sini';
            detailInstruction.style.display = 'block';
            detailImage.style.display = 'block'; // Show placeholder image
            detailImageWrapper.style.display = 'flex'; // Ensure the image wrapper is visible
            dynamicContent.style.display = 'none'; // Hide dynamic content
            document.getElementById('offer-amount-input').value = '';
            document.getElementById('modal-offer-amount-input').value = '';
        }

        document.addEventListener('DOMContentLoaded', () => {
            hideRequestDetails(); // Set state awal saat halaman dimuat

            // Event listener untuk setiap kartu pekerjaan
            document.querySelectorAll('.job-card').forEach(card => {
                card.addEventListener('click', function(e) {
                    const targetButton = e.target.closest('.detail-button');
                    const requestId = targetButton ?
                        targetButton.getAttribute('data-request-id') :
                        this.getAttribute('data-request-id');
                    if (requestId) {
                        showRequestDetails(requestId);
                    }
                });
            });

            document.getElementById('submit-offer-button').addEventListener('click', function() {
                const amount = document.getElementById('offer-amount-input').value;
                submitOffer(amount);
            });

            document.getElementById('modal-submit-offer-button').addEventListener('click', function() {
                const amount = document.getElementById('modal-offer-amount-input').value;
                submitOffer(amount);
            });

            // Event listener untuk paginasi
            document.querySelectorAll('.page-number').forEach(item => {
                item.addEventListener('click', function(e) {
                    if (item.classList.contains('item-active')) {
                        e.preventDefault();
                    }
                });
            });

            // Event listener untuk tombol close pada modal mobile
            document.getElementById('modal-close-button').addEventListener('click', function() {
                document.getElementById('job-details-modal').classList.remove('show');
            });

            // Event listener untuk menutup modal mobile saat mengklik di luar area konten
            document.getElementById('job-details-modal').addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('show');
                }
            });

            // --- Bagian yang dimodifikasi untuk Pop-up Konfirmasi ---
            // Get the confirmation modal elements
            // PENTING: Pastikan #accept-job-confirmation-modal ada di HTML dan dimuat sebelum script ini.
            const acceptJobConfirmationModalElement = document.getElementById('accept-job-confirmation-modal');
            if (acceptJobConfirmationModalElement) {
                const acceptJobConfirmationModal = new bootstrap.Modal(acceptJobConfirmationModalElement);
                const confirmAcceptJobButton = document.getElementById('confirm-accept-job');
                const cancelAcceptJobButton = document.getElementById('cancel-accept-job');

                // Event listener for "Terima" buttons (both desktop and mobile modal)
                const acceptButtons = document.querySelectorAll('#button-terima, .btn-terima-modal');
                acceptButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        if (!currentRequestId) {
                            window.showCustomAlert('Silakan pilih pekerjaan terlebih dahulu.',
                                'error');
                            return;
                        }
                        // Show the confirmation modal instead of direct confirm()
                        acceptJobConfirmationModal.show();
                    });
                });

                // Event listener for "Ya, Saya Yakin" button inside the confirmation modal
                if (confirmAcceptJobButton) { // Tambahkan cek null
                    confirmAcceptJobButton.addEventListener('click', function() {
                        if (!currentRequestId) {
                            window.showCustomAlert(
                                'Gagal mendapatkan ID pekerjaan. Silakan pilih pekerjaan lagi.', 'error'
                                );
                            acceptJobConfirmationModal.hide(); // Hide modal if no ID
                            return;
                        }
                        const pathTemplate = '/job-taker/cari-kerja/:id';
                        // Ganti :id dengan nilai dari currentRequestId
                        const finalUrl = pathTemplate.replace(':id', currentRequestId);
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content');

                        fetch(finalUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Server response was not ok.');
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    // alert(data.message);
                                    window.location.href = data.redirect_url;
                                } else {
                                    window.showCustomAlert(data.message ||
                                        'Terjadi kesalahan saat memproses permintaan.', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                window.showCustomAlert('Terjadi kesalahan koneksi babi.' + error
                                    .message, 'error');
                            })
                            .finally(() => {
                                acceptJobConfirmationModal
                                    .hide(); // Hide modal after fetch completes (success or failure)
                            });
                    });
                }

                // Event listener for "Kembali" button inside the confirmation modal
                if (cancelAcceptJobButton) { // Tambahkan cek null
                    cancelAcceptJobButton.addEventListener('click', function() {
                        acceptJobConfirmationModal.hide();
                    });
                }
            } else {
                console.error('Elemen modal konfirmasi dengan ID "accept-job-confirmation-modal" tidak ditemukan.');
            }
            // --- Akhir Bagian yang dimodifikasi ---

            // Event listener untuk resize window (sudah benar)
            window.addEventListener('resize', () => {
                const isMobile = window.innerWidth <= 992;
                const modal = document.getElementById('job-details-modal');

                if (!isMobile && modal.classList.contains('show')) {
                    modal.classList.remove('show');
                }
                if (isMobile && currentRequestId) {
                    showRequestDetails(currentRequestId);
                }
            });
        });
    </script>
@endsection
