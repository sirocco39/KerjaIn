@extends('master.master-job-taker')

@section('content')

    <div class="header-wrap" id="header-beranda-job_taker">
        <div class="container-fluid pembatas-x">
            @auth
                <h1 class="fw-bold mb-1">
                    {{ __('home-job-taker.sapaan', ['nama' => auth()->user()->first_name . ' ' . auth()->user()->last_name]) }}
                </h1>
            @else
                <h1 class="fw-bold mb-1">Halo, Nama Pengguna</h1>
            @endauth
            <p class="mb-0">
                {{ __('home-job-taker.deskripsi_sapaan') }}
            </p>
        </div>
        <x-search></x-search>
    </div>


    <div class="container-fluid pembatas-x pembatas-y d-flex flex-column gap-4">
        <h3 class="fw-bold mb-0">{{ __('home-job-taker.ringkasan_pengalaman') }}</h3>

        <div class="container-fluid d-flex row p-0 m-0 align-items-center justify-content-center" id="shortDetail">
            <div class="col d-flex flex-column align-items-center p-0">
                <h4 class="fw-bold title-detail m-0 mb-2"> {{ __('home-job-taker.lama_bekerja') }}</h4>
                @auth
                    <p class="mb-0 text-p"><b class="bold-point">{{ floor(auth()->user()->created_at->diffInYears(now())) }}</b>
                        {{ __('home-job-taker.tahun') }}
                    </p>
                @else
                    <p class="mb-0 text-p"><b class="bold-point">0</b> {{ __('home-job-taker.tahun') }}</p>
                @endauth
            </div>

            <div class="col d-flex flex-column align-items-center p-0" id="detail-tengah">
                <h4 class="fw-bold title-detail m-0 mb-2"> {{ __('home-job-taker.pekerjaan_selesai') }}</h4>
                @auth
                    <p class="mb-0 text-p"><b
                            class="bold-point">{{ \App\Models\Transaction::where('worker_id', Auth::id())->where('status', 'completed')->count() }}</b>
                        {{ __('home-job-taker.pekerjaan') }}
                    </p>
                @else
                    <p class="mb-0 text-p"><b class="bold-point">0</b> {{ __('home-job-taker.pekerjaan') }}</p>
                @endauth
            </div>

            <div class="col d-flex flex-column align-items-center p-0">
                <h4 class="fw-bold title-detail m-0 mb-2"> {{ __('home-job-taker.rating_rata_rata') }}</h4>
                <p class="mb-0"></p>
                @auth
                    <p class="mb-0 text-p"><b class="bold-point">{{ number_format(auth()->user()->rating, 2, ',', '.') }}</b>
                    </p>
                @else
                    <p class="mb-0 text-p"><b class="bold-point">0</b></p>
                @endauth
            </div>
        </div>
    </div>

    <div class="container-fluid pembatas-x pembatas-b d-flex flex-column gap-4">
        <h3 class="fw-bold mb-0"> {{ __('home-job-taker.pekerjaan_diambil') }}</h3>

        <div class="d-flex">
            <div class="col-12 col-xl-8 d-flex flex-column gap-4 beranda-req-kiri">
                @if ($fiveLatestTransaction->isEmpty())
                    <p> {{ __('home-job-taker.belum_ambil_pekerjaan') }}</p>
                @else
                    @foreach ($fiveLatestTransaction as $r)
                        @php
                            
                            $startDateTimeUTC = \Carbon\Carbon::parse($r->request->start_time)->setTimezone('UTC');
                            $endDateTimeUTC = \Carbon\Carbon::parse($r->request->end_time)->setTimezone('UTC');

                            $formattedStartDate = $startDateTimeUTC->format('d M Y');
                            $formattedStartTime = $startDateTimeUTC->format('H.i');
                            $formattedEndTime = $endDateTimeUTC->format('H.i');

                            $displayDateRange = $formattedStartDate;
                            
                            if ($startDateTimeUTC->format('Y-m-d') !== $endDateTimeUTC->format('Y-m-d')) {
                                $displayDateRange .= ' - ' . $endDateTimeUTC->format('d M Y');
                            }
                        @endphp
                        
                        <div class="work-request p-4 d-flex flex-column"
                            data-url="{{ $r->status !== 'cancelled' ? route('job-taker.accepted-work-request', ['id' => $r->id]) : '' }}">

                            <h4 class="fw-bold mb-1">{{ $r->request->title }}</h4>

                            <ul class="job-req-card-details d-flex justify-content-between mt-1 flex-column flex-md-row">
                                <li class="col-12 col-md-2 gap-2 me-2">
                                    <div class="icon-wrapper-beranda align-items-center align-items-md-start">
                                        <img src="{{ asset('Image/Icon/icon-profile.svg') }}" alt="Icon Profile">
                                    </div>
                                    <span> {{ __('home-job-taker.card.klien_prefix') }}
                                        {{ $r->requester->first_name }}</span>
                                </li>

                                <li class="col-12 col-md-3 col-lg-4 gap-2 me-2">
                                    <div class="icon-wrapper-beranda align-items-center align-items-md-start">
                                        <img src="{{ asset('Image/Icon/icon-address.svg') }}" alt="Icon Address">
                                    </div>
                                    <span>{{ $r->request->location }}</span>
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
                                    <span>Rp{{ number_format($r->request->final_price, 2, ',', '.') }}</span>
                                </li>
                            </ul>

                            <div class="details-bottom-segment d-flex justify-content-between mt-2">
                                @if ($r->status == 'accepted')
                                    <div class="status">
                                        <p class="mb-0"> {{ __('home-job-taker.card.status_diterima') }}</p>
                                    </div>
                                @elseif($r->status == 'in progress')
                                    <div class="status" style="background-color: #309FFF">
                                        <p class="mb-0"> {{ __('home-job-taker.card.status_dikerjakan') }}</p>
                                    </div>
                                @elseif($r->status == 'submitted')
                                    <div class="status">
                                        <p class="mb-0"> {{ __('home-job-taker.card.status_ditinjau') }}</p>
                                    </div>
                                @elseif($r->status == 'completed')
                                    <div class="status" style="background-color:#E8FA0D; color: #294287;">
                                        <p class="mb-0"> {{ __('home-job-taker.card.status_selesai') }}</p>
                                    </div>
                                @elseif($r->status == 'cancelled')
                                    <div class="status" style="background-color: #B02A37">
                                        <p class="mb-0"> {{ __('home-job-taker.card.status_dibatalkan') }}</p>
                                    </div>
                                @endif
                                <a class="detail-req-button" data-bs-toggle="modal" data-bs-target="#detailModal"
                                    data-slug="{{ $r->id }}">DETAIL</a>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="col beranda-req-kanan d-none d-xl-flex align-items-center justify-content-end">
                <img class="d-none d-xl-flex" src="{{ asset('Image/orang/ilus-beranda-job-taker.svg') }}"
                    alt="People Give Money" id="ilus-beranda">
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
                    <h1 class="fw-bold mb-3" id="modal-detail-title"> {{ __('home-job-taker.modal.judul_placeholder') }}
                    </h1>
                    <ul class="job-card-details">
                        <li class="gap-2">
                            <div class="icon-wrapper">
                                <img src="{{ asset('Image/Icon/icon-profile.svg') }}" alt="Icon Profile">
                            </div>
                            <span>{{ __('home-job-taker.modal.klien_prefix') }} <span
                                    id="modal-detail-profile"></span></span>
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
                    <h5 class="detail-description fw-bold d-flex mt-3">{{ __('home-job-taker.modal.deskripsi') }}</h5>
                    <div class="wrapDesc mb-3">
                        <p class="mb-0" id="modal-detail-description-text"></p>
                    </div>

                    <h5 class="detail-status fw-bold d-flex mt-3">{{ __('home-job-taker.modal.status') }}</h5>
                    <p class="mb-3" id="modal-detail-status"></p>

                    {{-- <div class="detail-buttons-placeholder d-flex gap-3 justify-content-center mt-auto">
                        <a id="button-action-1"></a>
                        <a id="button-action-2"></a>
                        <a id="button-action-3"></a>
                    </div> --}}
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
                    <h1 class="fw-bold mb-0" style="color: #B02A37">Hapus Tawaran</h1>
                    <img src="{{ asset('Image/Icon/icon-danger.svg') }}" alt="Danger Icon">
                </div>
                <div id="modal-content-container" class="p-3">
                    <p class="mb-3 text-center fw-medium fs-5">Apakah kamu yakin menghapus tawaran?</p>
                </div>
                <div class="detail-buttons-placeholder d-flex gap-3 justify-content-center mt-auto">
                    <a class="details-button-item btn-tawar-modal text-decoration-none" data-bs-target="#detailModal"
                        data-bs-toggle="modal" id="kembali-button-section">Tidak</a>
                    <form id="delete-request-form" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="details-button-item btn-hapus-modal text-decoration-none">Ya</button>
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
            const modalProfile = document.getElementById('modal-detail-profile');
            const modalLocation = document.getElementById('modal-detail-location');
            const modalDate = document.getElementById('modal-detail-date');
            const modalTime = document.getElementById('modal-detail-time');
            const modalPrice = document.getElementById('modal-detail-price-value');
            const modalDescription = document.getElementById('modal-detail-description-text');
            const modalStatus = document.getElementById('modal-detail-status');
            
            
            


            modal.addEventListener('show.bs.modal', function(event) {
                
                const button = event.relatedTarget;
                const slug = button.getAttribute('data-slug');
                console.log("Slug:", slug);

                
                modalTitle.textContent = 'Loading...';
                modalProfile.textContent = '-'
                modalLocation.textContent = '-';
                modalDate.textContent = '-';
                modalTime.textContent = '-';
                modalPrice.textContent = '-';
                
                
                
                modalDescription.textContent = 'Memuat deskripsi...';
                modalStatus.innerHTML = '<p class="mb-0">Memuat status...</p>';

                
                fetch(`/job-taker/beranda/${slug}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Gagal memuat data pekerjaan');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log(data);
                        const requests = data.request;
                        const requester = data.requester;

                        
                        const startDatetime = new Date(requests.start_time);
                        const endDatetime = new Date(requests.end_time);

                        modalTitle.textContent = requests.title || '-';
                        modalLocation.textContent = requests.location || '-';
                        modalProfile.textContent = requester.first_name || '-'

                        
                        
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
                        modalTime.textContent =
                            `${formatTimeInUTC(startDatetime)} - ${formatTimeInUTC(endDatetime)}`;
                        

                        modalPrice.textContent = parseFloat(requests.final_price || 0).toLocaleString(
                            'id-ID', {
                                minimumFractionDigits: 2
                            });
                        modalDescription.textContent = requests.description || '-';

                        
                        const statusText = getStatusText(data.status);

                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        

                        modalStatus.innerHTML = `<p class="mb-0">${statusText}</p>`;

                        
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
            document.querySelectorAll('.detail-req-button').forEach(button => {
                button.addEventListener('click', function(event) {
                    
                    
                    event.stopPropagation();
                });
            });


            
            document.querySelectorAll('.work-request').forEach(card => {
                card.addEventListener('click', function() {
                    
                    document.querySelectorAll('.work-request').forEach(otherCard => {
                        otherCard.classList.remove('choosed');
                    });
                    
                    this.classList.add('choosed');

                    
                    const url = this.dataset.url;
                    if (url) {
                        window.location.href = url;
                    }
                });
            });
            
        });
    </script>
@endsection
