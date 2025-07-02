    @extends('Master.master-job_taker')

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
                                <span>Kak {{ $request->requester->first_name }}</span>
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
                                <span>{{ $request->start_time->format('d M Y') }}</span>
                            </li>

                            <li class="gap-2">
                                <div class="icon-wrapper">
                                    <img src="{{ asset('Image/Icon/icon-clock.svg') }}" alt="Icon Address">
                                </div>
                                <span>{{ $request->start_time->format('H.i') }} - {{ $request->end_time->format('H.i') }}</span>
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
                    <img src="{{ asset('Image/Icon/icon-left-arrow.svg')}}" alt="Left Arrow" class="left-arrow" id="left-arrow-icon">
                    <h1 class="fw-bold m-0" id="detail-title">Pilih Lowongan Kerja di Kiri</h1>
                </div>
                <p id="detail-instruction">Tampilkan Detail Di sini</p>
                <div class="img-job-detail-placeholder align-items-center justify-content-center" id="detail-image-placeholder-wrapper">
                    <img src="{{ asset('Image/Icon/people-search.svg') }}" alt="People Search" class="placeholder-img" id="detail-image">
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

                    <div class="detail-buttons-placeholder d-flex gap-3 justify-content-end mt-auto">
                        <button class="details-button-item" id="button-tawar">Tawar</button>
                        <a href="#" class="details-button-item" id="button-hubungi">Hubungi</a>
                        <button class="details-button-item" id="button-terima">Terima</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Mobile View -->
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

                        <div class="detail-buttons-placeholder d-flex gap-3 justify-content-center mt-auto">
                            <button class="details-button-item btn-tawar-modal">Tawar</button>
                            <a href="#" class="details-button-item btn-hubungi-modal">Hubungi</a>
                            <button class="details-button-item btn-terima-modal">Terima</button>
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
                // const backArrow = document.getElementById('back-arrow');
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
                // backArrow.style.visibility = 'hidden'; // Hide back arrow temporarily

                // Construct the URL using Laravel's route helper (via JS global var or direct string)
                const url = `{{ route('work_requests.show', ['request' => ':requestId']) }}`.replace(':requestId', requestId);

                const isMobile = window.innerWidth <= 992;

                const modal = document.getElementById('job-details-modal');
                const modalContainer = document.getElementById('modal-content-container');
                const detailPanel = document.getElementById('job-details-panel');

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
                        document.querySelector('.btn-hubungi-modal').setAttribute(
                            'href',
                            `/hubungi/${requestId}`
                        );
                        document.getElementById('button-hubungi').setAttribute(
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
                        // backArrow.style.visibility = 'visible'; // Show back arrow
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
                        detailInstruction.textContent = 'Terjadi kesalahan saat memuat detail pekerjaan. Silakan coba lagi.';
                        detailInstruction.style.display = 'block';
                        detailImage.style.display = 'block';
                        detailImageWrapper.style.display = 'flex'; // Ensure the image wrapper is visible
                        dynamicContent.style.display = 'none';
                        // backArrow.style.visibility = 'hidden';
                    });
            }

            window.addEventListener('resize', () => {
                const isMobile = window.innerWidth <= 992;
                const modal = document.getElementById('job-details-modal');
                const detailPanel = document.getElementById('job-details-panel');
                const originalParent = document.querySelector('.job-details-placeholder').parentNode;

                if (!isMobile && modal.classList.contains('show')) {
                    modal.classList.remove('show');
                }

                if (isMobile && currentRequestId) {
                    showRequestDetails(currentRequestId);
                }
            });

            // Fungsi untuk menyembunyikan detail permintaan kerja dan mengembalikan ke tampilan awal
            function hideRequestDetails() {
                // const backArrow = document.getElementById('back-arrow');
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
                // backArrow.style.visibility = 'hidden'; // Hide back arrow
            }

            // Call hideRequestDetails on page load to ensure initial state is correct
            document.addEventListener('DOMContentLoaded', () => {
                hideRequestDetails(); // tetap panggil untuk set awal

                // Tambahkan event listener untuk semua elemen dengan class "job-card"
                document.querySelectorAll('.job-card').forEach(card => {
                    card.addEventListener('click', function (e) {
                        // Cegah aksi ganda jika tombol DETAIL diklik langsung
                    const targetButton = e.target.closest('.detail-button');

                        const requestId = targetButton
                            ? targetButton.getAttribute('data-request-id')
                            : this.getAttribute('data-request-id');

                        if (requestId) {
                            showRequestDetails(requestId);
                        }
                    });
                });

                document.querySelectorAll('.page-number').forEach(item => {
                    item.addEventListener('click', function (e) {
                        if (item.classList.contains('item-active')) {
                            e.preventDefault();
                        }
                    });
                });
            });

            document.getElementById('modal-close-button').addEventListener('click', function () {
                document.getElementById('job-details-modal').classList.remove('show');
            });

            document.getElementById('job-details-modal').addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('show');
                }
            });
            const acceptButtons = document.querySelectorAll('#button-terima, .btn-terima-modal');

        acceptButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (!currentRequestId) {
                    alert('Silakan pilih pekerjaan terlebih dahulu.');
                    return;
                }

                // Tampilkan konfirmasi
                if (!confirm('Apakah Anda yakin ingin menerima pekerjaan ini?')) {
                    return;
                }

                // URL endpoint yang kita buat di routes/web.php
                const url = `{{ url('/requests') }}/${currentRequestId}/accept`;

                // Ambil CSRF token dari meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // Lakukan Fetch request dengan metode POST
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Tampilkan pesan sukses dan redirect
                        alert(data.message);
                        window.location.href = data.redirect_url;
                    } else {
                        // Handle jika ada error dari server
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan koneksi.' +);
                });
            });
        });
        </script>
    @endsection
