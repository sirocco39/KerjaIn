@extends('Master.master-job_req')

@section('content')
    <div class="container-fluid pembatas-x pembatas-y d-flex flex-column gap-3" id="greetings-section">
        @auth
            <h1 class="fw-bold mb-0">Halo, {{auth()->user()->first_name}}  {{auth()->user()->last_name}}</h1>
        @else
            <h1 class="fw-bold mb-0">Halo, Nama Pengguna</h1>
        @endauth
        <p class="mb-0">
            Selamat datang! Di sini tempat terbaik untuk menemukan mitra kerja yang siap membantu. <br>
            Mulailah dengan membuat permintaan pekerjaan pertamamu.
        </p>
        <a href="/job-req/tawarkan-kerja" class="button-switch">
            Buat Lowongan Baru
        </a>
    </div>

    <div class="container-fluid pembatas-x pembatas-b d-flex flex-column gap-4">
        <h3 class="fw-bold mb-0">Tawaran Pekerjaan Saya Baru-baru Ini</h3>
        <div class="d-flex">
            <div class="col-12 col-xl-8 d-flex flex-column gap-4 beranda-req-kiri">
                @if($fiveLatestRequests->isEmpty())
                    <p>Anda belum pernah menawarkan pekerjaan!</p>
                @else
                    @foreach ($fiveLatestRequests as $r)
                        <div class="work-request p-4 d-flex flex-column">
                            <?php
                            $startdatetime  = strtotime($r->start_time);
                            $enddatetime  = strtotime($r->end_time);
                            $startdate   = date('d M Y', $startdatetime);
                            $starttime = date('H.i', $startdatetime);
                            $enddate  = date('d M Y', $enddatetime);
                            $endtime = date('H.i', $enddatetime);
                            ?>

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
                                    <span>{{ $startdate }}</span>
                                </li>

                                <li class="col-12 col-md-2 gap-2">
                                    <div class="icon-wrapper-beranda align-items-center align-items-md-start">
                                        <img src="{{ asset('Image/Icon/icon-clock.svg') }}" alt="Icon Clock">
                                    </div>
                                    <span>{{ $starttime }} - {{ $endtime }}</span>
                                </li>

                                <li class="col-12 col-md-2 gap-2">
                                    <div class="icon-wrapper-beranda align-items-center align-items-md-start">
                                        <img src="{{ asset('Image/Icon/icon-dollar.svg') }}" alt="Icon Money">
                                    </div>
                                    <span>Rp{{ number_format( $r->price, 2, ',', '.') }}</span>
                                </li>
                            </ul>

                            <div class="details-bottom-segment d-flex justify-content-between mt-2">
                                @if($r->status == "open")
                                <div class="status">
                                    <p class="mb-0">Menunggu Mitra</p>
                                </div>
                                @else
                                    @foreach ($r->transactions as $t )
                                        @if ($t->status == 'accepted')
                                            <div class="status">
                                                <p class="mb-0">Diterima</p>
                                            </div>
                                        @elseif($t->status == 'in progress')
                                            <div class="status">
                                                <p class="mb-0">Dikerjain</p>
                                            </div>
                                        @elseif($t->status == 'submitted')
                                            <div class="status">
                                                <p class="mb-0">Ditinjau</p>
                                            </div>
                                        @elseif($t->status == 'completed')
                                            <div class="status" style="background-color:#E8FA0D; color: #294287;">
                                                <p class="mb-0">Selesai</p>
                                            </div>
                                        @elseif($t->status == 'cancelled')
                                            <div class="status" style="background-color: #B02A37">
                                                <p class="mb-0">Dibatalin</p>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                                <a class="detail-req-button" data-bs-toggle="modal" data-bs-target="#detailModal" data-slug="{{ $r->slug }}" data-edit-url="{{ route('requesttt.edit', $r->slug) }}" data-delete-url="{{ route('requesttt.destroy', $r->slug) }}">DETAIL</a>
                            </div>
                        </div>
                        {{-- <div class="dropdown text-end">
                            <button class="btn btn-light border-0" type="button" id="dropdownMenuButton{{ $loop->index }}" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $loop->index }}">
                                <li><a class="dropdown-item" href="{{ route('requesttt.edit', $r->slug) }}">Edit</a></li>
                                <li>
                                    <form action="{{ route('requesttt.destroy', $r->slug) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="dropdown-item text-danger" type="submit">Delete</button>
                                    </form>
                                </li>
                            </ul>
                        </div> --}}
                    @endforeach
                @endif
            </div>

            <div class="col beranda-req-kanan d-xl-flex align-items-center justify-content-end">
                <img class="d-lg-flex" src="{{ asset('Image/orang/ilus-beranda.svg') }}" alt="People Give Money" id="ilus-beranda">
            </div>
        </div>
    </div>

    {{-- Pop Up Detail --}}
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <button type="button" class="btn-close" id="modal-close-button" aria-label="Close" data-bs-dismiss="modal"></button>
                </div>
                <div id="modal-content-container" class="p-3">
                    <h1 class="fw-bold mb-3" id="modal-detail-title">Nama Lowongan Kerja</h1>
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
                    <h5 class="detail-description fw-bold d-flex mt-3">Deskripsi:</h5>
                    <p class="mb-3" id="modal-detail-description-text"></p>

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

    <div class="modal fade" id="deleteConfirmation" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <img src="{{asset('Image/Icon/icon-danger.svg')}}" alt="Danger Icon">
                    <h1 class="fw-bold mb-0" style="color: #B02A37">Hapus Tawaran</h1>
                    <img src="{{asset('Image/Icon/icon-danger.svg')}}" alt="Danger Icon">
                </div>
                <div id="modal-content-container" class="p-3">
                    <p class="mb-3 text-center fw-medium fs-5">Apakah kamu yakin menghapus tawaran?</p>
                </div>
                <div class="detail-buttons-placeholder d-flex gap-3 justify-content-center mt-auto">
                    <a class="details-button-item btn-tawar-modal text-decoration-none" data-bs-target="#detailModal" data-bs-toggle="modal" id="kembali-button-section">Tidak</a>
                    <form id="delete-request-form" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="details-button-item btn-hapus-modal text-decoration-none">Ya</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- End Pop Up Detail --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('detailModal');
            const deleteModal = document.getElementById('deleteConfirmation');

            // Elemen-elemen dalam modal detail
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


            modal.addEventListener('show.bs.modal', function (event) {
                // Tombol yang memicu modal
                const button = event.relatedTarget;
                const slug = button.getAttribute('data-slug');
                const editUrl = button.getAttribute('data-edit-url'); // Ambil URL edit dari tombol
                const deleteUrl = button.getAttribute('data-delete-url');

                // Reset isi modal untuk menghindari tampilan data lama
                modalTitle.textContent = 'Loading...';
                modalLocation.textContent = '-';
                modalDate.textContent = '-';
                modalTime.textContent = '-';
                modalPrice.textContent = '-';
                buttonAction1.innerHTML = '';
                buttonAction2.innerHTML = '';
                buttonAction3.innerHTML = '';
                modalDescription.textContent = 'Memuat deskripsi...';
                modalStatus.innerHTML = '<p class="mb-0">Memuat status...</p>';

                // Fetch data pekerjaan berdasarkan slug
                fetch(`/requesttt/${slug}`)
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
                        modalDate.textContent = startDatetime.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                        modalTime.textContent = `${startDatetime.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })} - ${endDatetime.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}`;
                        modalPrice.textContent = parseFloat(data.price || 0).toLocaleString('id-ID', { minimumFractionDigits: 2 });
                        modalDescription.textContent = data.description || '-';

                        // Tentukan status berdasarkan data
                        if (data.status === 'open') {
                            modalStatus.innerHTML = `<p class="mb-0">Menunggu Mitra</p>`;
                            buttonAction1.innerHTML = `<a class="details-button-item btn-tawar-modal text-decoration-none" id="button-action-1" href="${editUrl}">Sunting</a>`;
                            buttonAction2.innerHTML = `<a class="details-button-item btn-hapus-modal text-decoration-none" data-bs-target="#deleteConfirmation" data-bs-toggle="modal" id="button-action-2">Hapus</a>`
                            deleteForm.setAttribute('action', deleteUrl);
                        } else if (data.status === 'closed') {
                            const latestTransaction = data.transactions?.slice(-1)[0];
                            const statusText = getStatusText(latestTransaction?.status);

                            if(statusText === 'Diterima'){
                                buttonAction1.innerHTML = `<a class="details-button-item btn-tawar-modal text-decoration-none" id="button-action-1" href="#">Pesan</a>`
                                buttonAction2.innerHTML = `<a class="details-button-item btn-hapus-modal text-decoration-none" id="button-action-2" href="#">Batalin</a>`
                            } else if(statusText === 'Dikerjain'){
                                buttonAction1.innerHTML = `<a class="details-button-item btn-tawar-modal text-decoration-none" id="button-action-1" href="#">Pesan</a>`
                                buttonAction2.innerHTML = `<a class="details-button-item btn-hapus-modal text-decoration-none" id="button-action-2" href="#">Laporin</a>`
                            } else if(statusText === 'Ditinjau'){
                                buttonAction1.innerHTML = `<a class="details-button-item btn-terima-modal text-decoration-none" id="button-action-1" href="#">Konfirmasi</a>`
                                buttonAction2.innerHTML = `<a class="details-button-item btn-tawar-modal text-decoration-none" id="button-action-2" href="#">Pesan</a>`
                                buttonAction3.innerHTML = `<a class="details-button-item btn-hapus-modal text-decoration-none" id="button-action-3" href="#">Laporin</a>`
                            }

                            modalStatus.innerHTML = `<p class="mb-0">${statusText}</p>`;
                        } else {
                            modalStatus.innerHTML = `<p class="mb-0">Status Tidak Diketahui</p>`;
                        }

                        // Perbarui tombol kembali
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

            function getStatusText(status) {
                switch (status?.toLowerCase()) {
                    case 'accepted': return 'Diterima';
                    case 'in progress': return 'Dikerjain';
                    case 'submitted': return 'Ditinjau';
                    case 'completed': return 'Selesai';
                    case 'cancelled': return 'Dibatalin';
                    default: return '-';
                }
            }
        });


    </script>
@endsection

