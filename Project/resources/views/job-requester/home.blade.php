@extends('master.master-job-req')

@section('content')
    <div class="container-fluid pembatas-x pembatas-y d-flex flex-column gap-3" id="greetings-section">
        @auth
            <h1 class="fw-bold mb-0">Halo, {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h1>
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
                @if ($fiveLatestRequests->isEmpty())
                    <p>Anda belum pernah menawarkan pekerjaan!</p>
                @else
                    @foreach ($fiveLatestRequests as $r)
                        @php
                            $hasTransaction = $r->transaction ? 'true' : 'false';

                            // NEW: Explicitly format dates/times in UTC for display consistency
                            $startDateTimeUTC = \Carbon\Carbon::parse($r->start_time)->setTimezone('UTC');
                            $endDateTimeUTC = \Carbon\Carbon::parse($r->end_time)->setTimezone('UTC');

                            $formattedStartDate = $startDateTimeUTC->format('d M Y');
                            $formattedStartTime = $startDateTimeUTC->format('H.i');
                            $formattedEndTime = $endDateTimeUTC->format('H.i');

                            $displayDateRange = $formattedStartDate;
                            // Check if the job spans multiple UTC days
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
                                    {{-- Use the new variable for multi-day date display (UTC) --}}
                                    <span>{{ $displayDateRange }}</span>
                                </li>

                                <li class="col-12 col-md-2 gap-2">
                                    <div class="icon-wrapper-beranda align-items-center align-items-md-start">
                                        <img src="{{ asset('Image/Icon/icon-clock.svg') }}" alt="Icon Clock">
                                    </div>
                                    {{-- Use the formatted UTC times directly --}}
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
                                        <p class="mb-0">Menunggu Mitra</p>
                                    </div>
                                @elseif ($r->transaction->status == 'accepted')
                                    <div class="status">
                                        <p class="mb-0">Diterima</p>
                                    </div>
                                @elseif($r->transaction->status == 'in progress' )
                                    <div class="status" style="background-color: #309FFF">
                                        <p class="mb-0">Dikerjain</p>
                                    </div>
                                @elseif($r->transaction->status == 'submitted')
                                    <div class="status">
                                        <p class="mb-0">Ditinjau</p>
                                    </div>
                                @elseif($r->transaction->status == 'completed')
                                    <div class="status" style="background-color:#E8FA0D; color: #294287;">
                                        <p class="mb-0">Selesai</p>
                                    </div>
                                @elseif($r->transaction->status == 'cancelled')
                                    <div class="status" style="background-color: #B02A37">
                                        <p class="mb-0">Dibatalin</p>
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

    {{-- Pop Up Detail --}}
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <button type="button" class="btn-close" id="modal-close-button" aria-label="Close"
                        data-bs-dismiss="modal"></button>
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


            modal.addEventListener('show.bs.modal', function(event) {
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
                // buttonAction1.innerHTML = '';
                // buttonAction2.innerHTML = '';
                // buttonAction3.innerHTML = '';
                modalDescription.textContent = 'Memuat deskripsi...';
                modalStatus.innerHTML = '<p class="mb-0">Memuat status...</p>';

                // Fetch data pekerjaan berdasarkan slug
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

                        // NEW: Format date in UTC for consistency with card
                        // Using 'en-GB' for 'd M Y' format, and timeZone: 'UTC'
                        modalDate.textContent = startDatetime.toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            timeZone: 'UTC' // Display UTC date
                        });
                        // Add check for multi-day span in modal date (still in UTC)
                        if (startDatetime.getUTCFullYear() !== endDatetime.getUTCFullYear() ||
                            startDatetime.getUTCMonth() !== endDatetime.getUTCMonth() ||
                            startDatetime.getUTCDate() !== endDatetime.getUTCDate()) {
                            modalDate.textContent += ` - ${endDatetime.toLocaleDateString('en-GB', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric',
                                timeZone: 'UTC' // Display UTC end date
                            })}`;
                        }

                        // NEW: Format time in UTC for consistency with card
                        // Function to format time in UTC (HH.ii format)
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

                        // Tentukan status berdasarkan data
                        if (data.status === 'open') {
                            modalStatus.innerHTML = `<p class="mb-0">Menunggu Mitra</p>`;
                            buttonAction1.innerHTML =
                                `<a class="details-button-item btn-tawar-modal text-decoration-none" id="button-action-1" href="${editUrl}">Sunting</a>`;
                            buttonAction2.innerHTML =
                                `<a class="details-button-item btn-hapus-modal text-decoration-none" data-bs-target="#deleteConfirmation" data-bs-toggle="modal" id="button-action-2">Hapus</a>`
                            deleteForm.setAttribute('action', deleteUrl);
                        } else if (data.status === 'closed') {
                            const transaction = data.transaction;
                            const statusText = getStatusText(transaction?.status);
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

            // Event listener untuk menutup modal
            modal.addEventListener('hidden.bs.modal', function() {
                // Hapus kelas 'choosed' dari semua elemen 'work-request'
                const workRequestCards = document.querySelectorAll('.work-request');
                workRequestCards.forEach(card => {
                    card.classList.remove('choosed');
                });
            });

            function getStatusText(status) {
                switch (status?.toLowerCase()) {
                    case 'accepted':
                        return 'Diterima';
                    case 'in progress':
                        return 'Dikerjain';
                    case 'submitted':
                        return 'Ditinjau';
                    case 'completed':
                        return 'Selesai';
                    case 'cancelled':
                        return 'Dibatalin';
                    default:
                        return '-';
                }
            }

            // Mendapatkan semua elemen dengan kelas 'work-request'
            document.querySelectorAll('.work-request').forEach(card => {
                card.addEventListener('click', function(event) {

                    // Baca penanda apakah request ini punya transaksi atau tidak.
                    const hasTransaction = this.dataset.hasTransaction === 'true';

                    if (hasTransaction) {
                        // ---> KONDISI 1: Request PUNYA transaksi (mode navigasi aktif)

                        // Cek apakah yang diklik adalah tombol DETAIL itu sendiri atau ikon di dalamnya.
                        if (event.target.closest('.detail-req-button')) {
                            // If yes, do nothing.
                            // Let Bootstrap handle opening the modal.
                            return;
                        } else {
                            // If another area on the card is clicked, navigate to the page.
                            const url = this.dataset.url;
                            if (url) {
                                window.location.href = url;
                            }
                        }

                    } else {
                        // ---> KONDISI 2: Request DOES NOT have a transaction (pop-up mode)

                        // Use old logic: entire card opens modal.
                        // Find the detail button within this card and click it programmatically.
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
