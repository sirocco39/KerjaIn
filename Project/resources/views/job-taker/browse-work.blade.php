@extends('master.master-job-taker')

@section('content')
    <x-search></x-search>

    <div class="container-fluid pembatas-x pembatas-y d-flex gap-4">
        <div class="job-listings col d-flex flex-column gap-4">
            @forelse ($workRequests as $request)
                
                <div class="job-card p-4" data-request-id="{{ $request->id }}">
                    <h3 class="fw-bold">{{ $request->title }}</h3>
                    <ul class="job-card-details">
                        <li class="gap-2">
                            <div class="icon-wrapper">
                                <img src="{{ asset('Image/Icon/icon-profile.svg') }}" alt="Icon Profile">
                            </div>
                            <span>{{ __('browse-work.klien_prefix') }} {{ $request->requester->first_name }}</span>
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
                                <img src="{{ asset('Image/Icon/icon-date.svg') }}" alt="Icon Address">
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
                <p>{{ __('browse-work.pekerjaan_tidak_ditemukan') }}</p>
            @endforelse

            
            <div class="pagination mt-auto">
                @if ($workRequests->currentPage() > 1)
                    <a href="{{ $workRequests->previousPageUrl() }}" class="pagination-nav" id="prev-page">
                        <img src="{{ asset('Image/Icon/icon-previous.svg') }}" alt="Previous" class="pagination-icon">
                        <span>{{ __('browse-work.paginasi_sebelumnya') }}</span>
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
                        <span>{{ __('browse-work.paginasi_selanjutnya') }}</span>
                        <img src="{{ asset('Image/Icon/icon-next.svg') }}" alt="Next" class="pagination-icon">
                    </a>
                @endif
            </div>
        </div>

        <div class="job-details-placeholder col d-flex flex-column p-4" id="job-details-panel">
            
            
            <div class="title-job-detail-placeholder d-flex align-item-center mb-3">
                <img src="{{ asset('Image/Icon/icon-left-arrow.svg') }}" alt="Left Arrow" class="left-arrow"
                    id="left-arrow-icon">
                <h1 class="fw-bold m-0" id="detail-title">{{ __('browse-work.detail_pilih_lowongan') }}</h1>
            </div>
            <p id="detail-instruction">{{ __('browse-work.detail_tampilkan_disini') }}</p>
            <div class="img-job-detail-placeholder align-items-center justify-content-center"
                id="detail-image-placeholder-wrapper">
                <img src="{{ asset('Image/Icon/people-search.svg') }}" alt="People Search" class="placeholder-img"
                    id="detail-image">
            </div>

            
            
            <div class="mx-3 flex-fill flex-column" id="dynamic-details-content" style="display: none;">
                <ul class="job-card-details">
                    <li class="gap-2">
                        <div class="icon-wrapper">
                            <img src="{{ asset('Image/Icon/icon-profile.svg') }}" alt="Icon Profile">
                        </div>
                        <span>{{ __('browse-work.klien_prefix') }} <span id="detail-profile"></span></span>
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
            <h5 class="detail-description fw-bold d-flex mt-3">{{ __('browse-work.detail_deskripsi') }}</h5>
                <div class="wrapDesc_ori">
                    <p class="mb-3" id="detail-description-text"></p>
                </div>

                <div class="detail-buttons-placeholder d-flex gap-2 justify-content-end mt-auto align-items-center">
                    <input type="number" id="offer-amount-input" class="form-control" placeholder="{{ __('browse-work.placeholder_tawarkan') }}"
                        style="max-width: 150px; height: 38px;">
                    <button class="details-button-item"
                        id="submit-offer-button">{{ __('browse-work.tombol_kirim') }}</button>
                    <a href="#" class="details-button-item text-decoration-none"
                        id="button-hubungi">{{ __('browse-work.tombol_hubungi') }}</a>
                    <button class="details-button-item btn-terima-modal">{{ __('browse-work.tombol_terima') }}</button>
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
                    <h1 class="fw-bold mb-3" id="modal-detail-title">{{ __('browse-work.detail_pilih_lowongan') }}</h1>
                    <ul class="job-card-details">
                        <li class="gap-2">
                            <div class="icon-wrapper">
                                <img src="{{ asset('Image/Icon/icon-profile.svg') }}" alt="Icon Profile">
                            </div>
                            <span>{{ __('browse-work.klien_prefix') }} <span id="modal-detail-profile"></span></span>
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
                    <h5 class="detail-description fw-bold d-flex mt-3">{{ __('browse-work.detail_deskripsi') }}</h5>
                    <div class="wrapDesc mb-3">
                        <p class="mb-0" id="modal-detail-description-text"></p>
                    </div>

                    <div class="detail-buttons-placeholder d-flex gap-2 justify-content-center mt-auto align-items-center">
                        <input type="number" id="modal-offer-amount-input" class="form-control"
                            placeholder="{{ __('browse-work.placeholder_tawarkan') }}" style="max-width: 150px; height: 38px;">
                        <button class="details-button-item"
                            id="modal-submit-offer-button">{{ __('browse-work.tombol_kirim') }}</button>
                        <a href="#"
                            class="details-button-item btn-hubungi-modal text-decoration-none">{{ __('browse-work.tombol_hubungi') }}</a>
                        <button
                            class="details-button-item btn-terima-modal">{{ __('browse-work.tombol_terima') }}</button>
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
                    
                    <img src="{{ asset('Image/Icon/icon-warning.svg') }}" alt="Warning Icon"
                        style="width: 60px; height: 60px; margin-bottom: 20px;">
                    <h2 class="fw-bold mb-3">{{ __('browse-work.modal_konfirmasi_judul') }}</h2>
                    <p>{{ __('browse-work.modal_konfirmasi_pesan') }}</p>
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            id="cancel-accept-job">{{ __('browse-work.modal_tombol_kembali') }}</button>
                        <button type="button" class="btn btn-primary"
                            id="confirm-accept-job">{{ __('browse-work.modal_tombol_yakin') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const lang = @json(__('browse-work.js_messages'));

        
        const formatRupiah = (amount) => {
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(amount);
        };
        let currentRequestId = null;

        
        function submitOffer(amount) {
            if (!currentRequestId) {
                window.showCustomAlert(lang.gagal_dapatkan_id, 'error');
                return;
            }
            if (!amount || isNaN(amount) || parseFloat(amount) <= 0) {
                window.showCustomAlert(lang.tawaran_tidak_valid, 'error');
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
                        window.showCustomAlert(data.message || lang.tawaran_berhasil_dikirim, 'success');
                        window.location.href = data.redirect_url;
                    } else {
                        window.showCustomAlert(data.message || lang.kesalahan_mengirim_tawaran, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    window.showCustomAlert(lang.kesalahan_koneksi, 'error');
                });
        }


        
        function showRequestDetails(requestId) {
            currentRequestId = requestId;
            

            const leftArrowIcon = document.getElementById('left-arrow-icon');
            leftArrowIcon.style.display = 'none'; 

            document.querySelectorAll('.job-card').forEach(card => {
                card.classList.remove('choosed');
            });

            
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

            
            detailTitle.textContent = lang.memuat_detail;
            detailInstruction.textContent = lang.silakan_tunggu;
            detailInstruction.style.display = 'block'; 
            detailImage.style.display = 'block'; 
            detailImageWrapper.style.display = 'flex'; 
            dynamicContent.style.display = 'none'; 

            
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

                    
                    detailTitle.textContent = data.title;
                    detailInstruction.style.display = 'none'; 
                    detailImage.style.display = 'none'; 
                    detailImageWrapper.style.display = 'none'; 

                    document.getElementById('detail-profile').textContent = data.requester_first_name;
                    document.getElementById('detail-location').textContent = data.location;
                    document.getElementById('detail-date').textContent = data.display_date;
                    document.getElementById('detail-time').textContent = data.display_time_range;
                    document.getElementById('detail-description-text').textContent = data.description;
                    document.getElementById('detail-price-value').textContent = formatRupiah(data.price);

                    dynamicContent.style.display = 'flex'; 

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
                    detailTitle.textContent = lang.gagal_memuat_detail;
                    detailInstruction.textContent = lang.gagal_memuat_detail_pesan;
                    detailInstruction.style.display = 'block';
                    detailImage.style.display = 'block';
                    detailImageWrapper.style.display = 'flex'; 
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

        
        function hideRequestDetails() {
            const detailTitle = document.getElementById('detail-title');
            const detailInstruction = document.getElementById('detail-instruction');
            const detailImage = document.getElementById('detail-image');
            const detailImageWrapper = document.getElementById('detail-image-placeholder-wrapper');
            const dynamicContent = document.getElementById('dynamic-details-content');

            document.querySelectorAll('.job-card').forEach(card => {
                card.classList.remove('choosed');
            });

            detailTitle.textContent = "{{ __('browse-work.detail_pilih_lowongan') }}";
            detailInstruction.textContent = "{{ __('browse-work.detail_tampilkan_disini') }}";
            detailInstruction.style.display = 'block';
            detailImage.style.display = 'block'; 
            detailImageWrapper.style.display = 'flex'; 
            dynamicContent.style.display = 'none'; 
            document.getElementById('offer-amount-input').value = '';
            document.getElementById('modal-offer-amount-input').value = '';
        }

        document.addEventListener('DOMContentLoaded', () => {
            hideRequestDetails(); 

            
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

            
            document.querySelectorAll('.page-number').forEach(item => {
                item.addEventListener('click', function(e) {
                    if (item.classList.contains('item-active')) {
                        e.preventDefault();
                    }
                });
            });

            
            document.getElementById('modal-close-button').addEventListener('click', function() {
                document.getElementById('job-details-modal').classList.remove('show');
            });

            
            document.getElementById('job-details-modal').addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('show');
                }
            });

            
            
            
            const acceptJobConfirmationModalElement = document.getElementById('accept-job-confirmation-modal');
            if (acceptJobConfirmationModalElement) {
                const acceptJobConfirmationModal = new bootstrap.Modal(acceptJobConfirmationModalElement);
                const confirmAcceptJobButton = document.getElementById('confirm-accept-job');
                const cancelAcceptJobButton = document.getElementById('cancel-accept-job');

                
                const acceptButtons = document.querySelectorAll('#button-terima, .btn-terima-modal');
                acceptButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        if (!currentRequestId) {
                            window.showCustomAlert(lang.pilih_pekerjaan_dulu, 'error');
                            return;
                        }
                        
                        acceptJobConfirmationModal.show();
                    });
                });

                
                if (confirmAcceptJobButton) { 
                    confirmAcceptJobButton.addEventListener('click', function() {
                        if (!currentRequestId) {
                            window.showCustomAlert(lang.gagal_dapatkan_id, 'error');
                            acceptJobConfirmationModal.hide(); 
                            return;
                        }
                        const pathTemplate = '/job-taker/cari-kerja/:id';
                        
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
                                    
                                    window.location.href = data.redirect_url;
                                } else {
                                    window.showCustomAlert(data.message || lang.kesalahan_memproses,
                                        'error');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                window.showCustomAlert(lang.kesalahan_koneksi, 'error');
                            })
                            .finally(() => {
                                acceptJobConfirmationModal
                                    .hide(); 
                            });
                    });
                }

                
                if (cancelAcceptJobButton) { 
                    cancelAcceptJobButton.addEventListener('click', function() {
                        acceptJobConfirmationModal.hide();
                    });
                }
            } else {
                console.error('Elemen modal konfirmasi dengan ID "accept-job-confirmation-modal" tidak ditemukan.');
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
        });
    </script>
@endsection
