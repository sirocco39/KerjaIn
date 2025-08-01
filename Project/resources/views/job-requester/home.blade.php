@extends('master.master-job-req')

@section('content')
    <div class="container-fluid pembatas-x pembatas-y d-flex flex-column gap-3" id="greetings-section">
        @auth
            <h1 class="fw-bold mb-0">{{ __('home-job-req.selamat_datang', ['nama' => Auth::user()->first_name]) }}</h1>
</h1>
        @else
            <h1 class="fw-bold mb-0">Halo, Nama Pengguna</h1>
        @endauth
        <p class="mb-0">
            {{ __('home-job-req.deskripsi_awal') }}
        </p>
        <a href="/job-req/tawarkan-kerja" class="button-switch">
            {{ __('home-job-req.tawarkan_pekerjaan_pertama') }}
        </a>
    </div>

    <div class="container-fluid pembatas-x pembatas-b d-flex flex-column gap-4">
        <h3 class="fw-bold mb-0">{{ __('home-job-req.pekerjaan_terbaru_anda') }}</h3>
        <div class="d-flex">
            <div class="col-12 col-xl-8 d-flex flex-column gap-4 beranda-req-kiri">
                @if ($fiveLatestRequests->isEmpty())
                    <p>{{ __('home-job-req.belum_ada_pekerjaan') }}</p>
                @else
                    @foreach ($fiveLatestRequests as $r)
                        @php
                            $hasTransaction = $r->transaction ? 'true' : 'false';

                            
                            $startDateTimeUTC = \Carbon\Carbon::parse($r->start_time)->setTimezone('UTC');
                            $endDateTimeUTC = \Carbon\Carbon::parse($r->end_time)->setTimezone('UTC');

                            $formattedStartDate = $startDateTimeUTC->format('d M Y');
                            $formattedStartTime = $startDateTimeUTC->format('H.i');
                            $formattedEndTime = $endDateTimeUTC->format('H.i');

                            $displayDateRange = $formattedStartDate;
                            
                            if ($startDateTimeUTC->format('Y-m-d') !== $endDateTimeUTC->format('Y-m-d')) {
                                $displayDateRange .= ' - ' . $endDateTimeUTC->format('d M Y');
                        }
                            
                        @endphp
                        <div class="work-request p-4 d-flex flex-column"
                            data-url="{{ $r->transaction && $r->transaction->status !== 'cancelled' ? route('request.ongoing', ['transactionId' => $r->transaction->id]) : '' }}"
                            data-has-transaction="{{ $r->transaction ? 'true' : 'false' }}">

                            <h4 class="fw-bold mb-1">{{ $r->title }}</h4>

                            <ul class="job-req-card-details d-flex justify-content-between mt-1 flex-column flex-md-row">
                                <li class="col-12 col-md-3 col-lg-4 gap-2 me-2">
                                    <div class="icon-wrapper-beranda align-items-center align-items-md-start">
                                        <img src="{{ asset('Image/Icon/icon-address.svg') }}" alt="Icon Address">
                                    </div>
                                    <span>{{ $r->location }}</span>
                                </li>

                                <li class="col-12 col-md-2 gap-2">
                                    <div class="icon-wrapper-beranda align-items-center align-items-md-start">
                                        <img src="{{ asset('Image/Icon/icon-date.svg') }}" alt="Icon Date">
                                    </div>
                                    
                                    <span>{{ $displayDateRange }}</span>
                                </li>

                                <li class="col-12 col-md-2 gap-2">
                                    <div class="icon-wrapper-beranda align-items-center align-items-md-start">
                                        <img src="{{ asset('Image/Icon/icon-clock.svg') }}" alt="Icon Clock">
                                    </div>
                                    
                                    <span>{{ $formattedStartTime }} - {{ $formattedEndTime }}</span>
                                </li>

                                <li class="col-12 col-md-2 gap-2">
                                    <div class="icon-wrapper-beranda align-items-center align-items-md-start">
                                        <img src="{{ asset('Image/Icon/icon-dollar.svg') }}" alt="Icon Money">
                                    </div>
                                    <span>Rp{{ number_format($r->final_price, 2, ',', '.') }}</span>
                                </li>
                            </ul>

                            <div class="details-bottom-segment d-flex justify-content-between mt-2">
                                @if ($r->status == 'open')
                                    <div class="status">
                                        <p class="mb-0">{{ __('home-job-req.status_menunggu_mitra') }}</p>
                                    </div>
                                @elseif ($r->transaction->status == 'accepted')
                                    <div class="status">
                                        <p class="mb-0">{{ __('home-job-req.status_diterima') }}</p>
                                    </div>
                                @elseif($r->transaction->status == 'in progress' )
                                    <div class="status" style="background-color: #309FFF">
                                        <p class="mb-0">{{ __('home-job-req.status_dikerjakan') }}</p>
                                    </div>
                                @elseif($r->transaction->status == 'submitted')
                                    <div class="status">
                                        <p class="mb-0">{{ __('home-job-req.status_ditinjau') }}</p>
                                    </div>
                                @elseif($r->transaction->status == 'completed')
                                    <div class="status" style="background-color:#E8FA0D; color: #294287;">
                                        <p class="mb-0">{{ __('home-job-req.status_selesai') }}</p>
                                    </div>
                                @elseif($r->transaction->status == 'cancelled')
                                    <div class="status" style="background-color: #B02A37">
                                        <p class="mb-0">{{ __('home-job-req.status_dibatalkan') }}</p>
                                    </div>
                                @endif
                                <a class="detail-req-button" data-bs-toggle="modal" data-bs-target="#detailModal"
                                    data-slug="{{ $r->slug }}"
                                    data-edit-url="{{ route('request.edit', $r->slug) }}"
                                    data-delete-url="{{ route('request.destroy', $r->slug) }}">DETAIL</a>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="col beranda-req-kanan d-xl-flex align-items-center justify-content-end">
                <img class="d-lg-flex" src="{{ asset('Image/orang/ilus-beranda.svg') }}" alt="People Give Money"
                    id="ilus-beranda">
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <button type="button" class="btn-close" id="modal-close-button" aria-label="Close"
                        data-bs-dismiss="modal"></button>
                </div>
                <div id="modal-content-container" class="p-3">
                    <h1 class="fw-bold mb-3" id="modal-detail-title">{{ __('home-job-req.nama_loker') }}</h1>
                    <ul class="job-card-details">
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
                    <h5 class="detail-description fw-bold d-flex mt-3">{{ __('home-job-req.deskripsi') }}:</h5>
                    <div class="wrapDesc mb-3">
                        <p class="mb-0" id="modal-detail-description-text"></p>
                    </div>

                    <h5 class="detail-status fw-bold d-flex mt-3">Status:</h5>
                    <p class="mb-3" id="modal-detail-status"></p>

                    <div class="detail-buttons-placeholder d-flex gap-3 justify-content-center mt-auto">
                        <a id="button-action-1"></a>
                        <a id="button-action-2"></a>
                        <a id="button-action-3"></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteConfirmation" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <img src="{{ asset('Image/Icon/icon-danger.svg') }}" alt="Danger Icon">
                    <h1 class="fw-bold mb-0" style="color: #B02A37">{{ __('home-job-req.hapus_tawaran') }}</h1>
                    <img src="{{ asset('Image/Icon/icon-danger.svg') }}" alt="Danger Icon">
                </div>
                <div id="modal-content-container" class="p-3">
                    <p class="mb-3 text-center fw-medium fs-5">{{ __('home-job-req.konfirmasi_hapus') }}</p>
                </div>
                <div class="detail-buttons-placeholder d-flex gap-3 justify-content-center mt-auto">
                    <a class="details-button-item btn-tawar-modal text-decoration-none" data-bs-target="#detailModal"
                        data-bs-toggle="modal" id="kembali-button-section">{{ __('home-job-req.tidak') }}</a>
                    <form id="delete-request-form" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="details-button-item btn-hapus-modal text-decoration-none">{{ __('home-job-req.ya') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('detailModal');
            const deleteModal = document.getElementById('deleteConfirmation');

            
            const modalTitle = document.getElementById('modal-detail-title');
            const modalLocation = document.getElementById('modal-detail-location');
            const modalDate = document.getElementById('modal-detail-date');
            const modalTime = document.getElementById('modal-detail-time');
            const modalPrice = document.getElementById('modal-detail-price-value');
            const modalDescription = document.getElementById('modal-detail-description-text');
            const modalStatus = document.getElementById('modal-detail-status');
            const buttonAction1 = document.getElementById('button-action-1');
            const buttonAction2 = document.getElementById('button-action-2');
            const buttonAction3 = document.getElementById('button-action-3');
            const deleteForm = document.getElementById('delete-request-form');


            modal.addEventListener('show.bs.modal', function(event) {
                
                const button = event.relatedTarget;
                const slug = button.getAttribute('data-slug');
                const editUrl = button.getAttribute('data-edit-url'); 
                const deleteUrl = button.getAttribute('data-delete-url');

                
                modalTitle.textContent = 'Loading...';
                modalLocation.textContent = '-';
                modalDate.textContent = '-';
                modalTime.textContent = '-';
                modalPrice.textContent = '-';
                
                
                
                modalDescription.textContent = 'Memuat deskripsi...';
                modalStatus.innerHTML = '<p class="mb-0">Memuat status...</p>';

                
                fetch(`/request/${slug}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Gagal memuat data pekerjaan');
                        }
                        return response.json();
                    })
                    .then(data => {
                        const startDatetime = new Date(data.start_time);
                        const endDatetime = new Date(data.end_time);

                        modalTitle.textContent = data.title || '-';
                        modalLocation.textContent = data.location || '-';

                        
                        
                        modalDate.textContent = startDatetime.toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            timeZone: 'UTC' 
                        });
                        
                        if (startDatetime.getUTCFullYear() !== endDatetime.getUTCFullYear() ||
                            startDatetime.getUTCMonth() !== endDatetime.getUTCMonth() ||
                            startDatetime.getUTCDate() !== endDatetime.getUTCDate()) {
                            modalDate.textContent += ` - ${endDatetime.toLocaleDateString('en-GB', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric',
                                timeZone: 'UTC' 
                            })}`;
                        }

                        
                        
                        function formatTimeInUTC(date) {
                            const hours = date.getUTCHours().toString().padStart(2, '0');
                            const minutes = date.getUTCMinutes().toString().padStart(2, '0');
                            return `${hours}.${minutes}`;
                        }
                        modalTime.textContent = `${formatTimeInUTC(startDatetime)} - ${formatTimeInUTC(endDatetime)}`;

                        modalPrice.textContent = parseFloat(data.final_price || 0).toLocaleString('id-ID', {
                            minimumFractionDigits: 2
                        });
                        modalDescription.textContent = data.description || '-';

                        
                        if (data.status === 'open') {
                            modalStatus.innerHTML = `<p class="mb-0">{{ __('home-job-req.status_menunggu_mitra') }}</p>`;
                            buttonAction1.innerHTML =
                                `<a class="details-button-item btn-tawar-modal text-decoration-none" id="button-action-1" href="${editUrl}">{{ __('home-job-req.sunting') }}</a>`;
                            buttonAction2.innerHTML =
                                `<a class="details-button-item btn-hapus-modal text-decoration-none" data-bs-target="#deleteConfirmation" data-bs-toggle="modal" id="button-action-2">{{ __('home-job-req.hapus') }}</a>`
                            deleteForm.setAttribute('action', deleteUrl);
                        } else if (data.status === 'closed') {
                            const transaction = data.transaction;
                            const statusText = getStatusText(transaction?.status);
                            modalStatus.innerHTML = `<p class="mb-0">${statusText}</p>`;
                        } else {
                            modalStatus.innerHTML = `<p class="mb-0">Status Tidak Diketahui</p>`;
                        }

                        
                        const kembaliButton = document.getElementById('kembali-button-section');
                        kembaliButton.setAttribute('data-slug', slug);
                        kembaliButton.setAttribute('data-bs-target', '#detailModal');
                    })
                    .catch(error => {
                        console.error('Error loading job details:', error);
                        modalTitle.textContent = 'Error';
                        modalLocation.textContent = '-';
                        modalDate.textContent = '-';
                        modalTime.textContent = '-';
                        modalPrice.textContent = '-';
                        modalDescription.textContent = 'Gagal memuat deskripsi.';
                        modalStatus.innerHTML = '<p class="mb-0 text-danger">Gagal memuat status.</p>';
                    });
            });

            
            modal.addEventListener('hidden.bs.modal', function() {
                
                const workRequestCards = document.querySelectorAll('.work-request');
                workRequestCards.forEach(card => {
                    card.classList.remove('choosed');
                });
            });

            function getStatusText(status) {
                switch (status?.toLowerCase()) {
                    case 'accepted':
                        return '{{ __('home-job-taker.card.status_diterima') }}';
                    case 'in progress':
                        return '{{ __('home-job-taker.card.status_dikerjakan') }}';
                    case 'submitted':
                        return '{{ __('home-job-taker.card.status_ditinjau') }}';
                    case 'completed':
                        return '{{ __('home-job-taker.card.status_selesai') }}';
                    case 'cancelled':
                        return '{{ __('home-job-taker.card.status_dibatalkan') }}';
                    default:
                        return '-';
                }
            }

            
            document.querySelectorAll('.work-request').forEach(card => {
                card.addEventListener('click', function(event) {

                    
                    const hasTransaction = this.dataset.hasTransaction === 'true';

                    if (hasTransaction) {
                        

                        
                        if (event.target.closest('.detail-req-button')) {
                            
                            
                            return;
                        } else {
                            
                            const url = this.dataset.url;
                            if (url) {
                                window.location.href = url;
                            }
                        }

                    } else {
                        

                        
                        
                        const detailButton = this.querySelector('.detail-req-button');
                        if (detailButton) {
                            detailButton.click();
                        }
                    }
                });
            });

        });
    </script>
@endsection
