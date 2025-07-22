@extends('Master.master-job-taker')

@section('content')
    <div class="container-fluid pembatas-x pembatas-y">
    <div class="flex items-center space-x-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b-4 border-yellow-400 pb-2 inline-block">Riwayat Pemesanan
        </h1>
    <a href="{{ route('monthly.report') }}"
    class="btn btn-primary btn-lg mb-3 text-white"
    style="text-decoration: none;">
        Laporan Bulanan
    </a>


    </div>
        <div class="flex flex-col md:flex-row justify-start items-start md:items-center">
            {{-- Desktop Tab Navigation --}}
            <div class="tabs-wrapper hidden md:flex">
                <div class="col tab-button active" data-tab="all">
                    Semua Transaksi ({{ $allOrders->count() }})
                </div>
                <div class="col tab-button" data-tab="pending">
                    Berlangsung ({{ $pendingOrders->count() }})
                </div>
                <div class="col tab-button" data-tab="completed">
                    Selesai ({{ $completedOrders->count() }})
                </div>
                <div class="col tab-button" data-tab="cancelled">
                    Dibatalin ({{ $cancelledOrders->count() }})
                </div>
            </div>

            {{-- Mobile Dropdown for Tab Selection --}}
            <div class="tabs-dropdown-wrapper md:hidden w-full mb-4">
                <select id="tab-select"
                    class="form-select w-full border rounded-lg py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                    <option value="all" @if (request('tab') == 'all' || !request('tab')) selected @endif>Semua Transaksi
                        ({{ $allOrders->count() }})</option>
                    <option value="pending" @if (request('tab') == 'pending') selected @endif>Berlangsung
                        ({{ $pendingOrders->count() }})</option>
                    <option value="completed" @if (request('tab') == 'completed') selected @endif>Selesai
                        ({{ $completedOrders->count() }})</option>
                    <option value="cancelled" @if (request('tab') == 'cancelled') selected @endif>Dibatalin
                        ({{ $cancelledOrders->count() }})</option>
                </select>
            </div>
        </div>


        {{-- Main Order List Display Area --}}
        <div class="flex flex-col md:flex-row justify-start items-start md:items-center">
            <div class="w-full bg-white rounded-lg">
                {{-- Table Header Row --}}
                <div class="row text-center tableHeader d-flex align-items-center justify-content-center fw-semibold m-0 p-0 text-xs md:text-base"
                    style="height: 4rem;">
                    {{-- Added 'ps-3' to the header column for consistency --}}
                    <div class="col m-0 p-0 ps-3 text-start">Judul</div>
                    <div class="col m-0 p-0">Status</div>
                    <div class="col m-0 p-0">Tanggal Selesai</div>
                    <div class="col m-0 p-0">Klien</div>
                    <div class="col m-0 p-0">Lokasi</div>
                    <div class="col m-0 p-0">Upah</div>
                </div>
                <hr class="mx-auto border-2 opacity-100 my-0 p-0" style="width: 98%; border-color: #294287;">
                {{-- Order Rows Loop --}}
                <div id="order-list-container" class="order-list-fade-in">
                    @forelse ($allOrders as $order)
                        <div class="order-row hoverable-row
                            {{ str_replace(' ', '-', $order->status) }}-tab"
                            @if ($order->status_text == 'Selesai') data-bs-toggle="modal" data-bs-target="#completionModal"
                            @else
                                data-redirect-url="{{ route('job-taker.accepted-work-request', ['id' => $order->id]) }}" @endif
                            data-transaction-id="{{ $order->id }}" data-request-id="{{ $order->request->id ?? '' }}"
                            data-request-title="{{ $order->request->title ?? '-' }}"
                            data-order-number="{{ $order->order_number ?? '-' }}"
                            data-worker-first-name="{{ $order->worker->first_name ?? '' }}"
                            data-worker-last-name="{{ $order->worker->last_name ?? '' }}"
                            data-requester-first-name="{{ $order->request->requester->first_name ?? '' }}"
                            data-requester-last-name="{{ $order->request->requester->last_name ?? '' }}"
                            data-request-location="{{ $order->request->location ?? '-' }}"
                            data-transaction-created-at="{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') ?? '-' }}"
                            data-transaction-updated-at="{{ \Carbon\Carbon::parse($order->updated_at)->format('d M Y') ?? '-' }}"
                            data-request-price="{{ number_format($order->request->price ?? 0, 0, ',', '.') ?? '-' }}"
                            data-start-work="{{ \Carbon\Carbon::parse($order->start_work)->format('H.i') ?? '-' }}"
                            data-finish-work="{{ \Carbon\Carbon::parse($order->finish_work)->format('H.i') ?? '-' }}"
                            data-worker-id="{{ $order->worker_id ?? '' }}"
                            data-requester-id="{{ $order->request->requester->id ?? '' }}"
                            data-order-status-text="{{ $order->status_text }}"
                            data-has-review="{{ $order->has_review ? 'true' : 'false' }}"
                            @if ($order->has_review && $order->received_review) data-user-rating="{{ $order->received_review->rating }}"
                                data-user-comment="{{ $order->received_review->comment }}" @endif>
                            <div class="row text-center text-sm d-flex justify-content-center align-items-center m-0 p-0"
                                style="min-height: 3.5rem;">
                                {{-- Added ps-3 for left padding on the title column --}}
                                <div class="col m-0 p-0 text-xxs text-start ps-3 title-col"> {{ $order->request->title ?? '-' }}</div>
                                <div class="col m-0 p-0">
                                    <span class="badge rounded-pill text-xxs status-badge-fixed"
                                        style="
                                        padding: .5em .9em;
                                        font-size: 14px;
                                        @if ($order->status_text == 'Selesai') background-color: #D3FA0D;
                                            color: #333;
                                        @elseif(in_array($order->status_text, ['Dikerjain', 'Diterima', 'Ditinjau']))
                                            background-color: #294287;
                                            color: #FFF;
                                        @elseif($order->status_text == 'Dibatalin')
                                            background-color: #E63C3C;
                                            color: #FFF;
                                        @else
                                            background-color: #6c757d;
                                            color: #FFF; @endif
                                        ">
                                        {{ $order->status_text ?? '-' }}
                                    </span>
                                </div>
                                <div class="col m-0 p-0 text-xxs">
                                    {{ \Carbon\Carbon::parse($order->updated_at)->format('d - m - Y') ?? '-' }}</div>
                                <div class="col m-0 p-0 text-xxs">{{ $order->request->requester->full_name ?? '-' }}</div>
                                <div class="col m-0 p-0 text-xxs">{{ $order->request->location ?? '-' }}</div>
                                <div class="col m-0 p-0 text-xxs">Rp
                                    {{ number_format($order->request->price ?? 0, 0, ',', '.') ?? '-' }}</div>
                            </div>
                        </div>
                    @empty
                        {{-- No Orders Message --}}
                        <div id="no-transaction-message"
                            class="my-0 py-6 px-6 text-center text-gray-500 justify-content-center flex items-center w-full"
                            style="height: 3rem">
                            Belum Ada Transaksi
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Completion Modal --}}
    <div class="modal fade" id="completionModal" tabindex="-1" aria-labelledby="completionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 1000px; width: 100%; margin-top:5vh;">
            <form id="reviewForm" method="POST">
                @csrf
                <div class="modal-content p-3">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold fs-3" id="completionModalLabel">Detail Penyelesaian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body overflow-auto overflow-lg-visible" style="max-height: 90vh;">
                        <div class="d-flex flex-column flex-lg-row gap-3">
                            {{-- Order Details Display --}}
                            <div class="d-flex flex-column flex-grow-1">
                                <div class="d-flex flex-fill">
                                    <div class="text flex-fill" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold" style="font-size: 14px;">Judul Pesanan
                                        </p>
                                        <p class="fw-medium" id="modalRequestTitle" style="font-size: 16px;"></p>
                                    </div>
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold" style="font-size: 14px;">Nomor Pesanan
                                        </p>
                                        <p class="fw-medium" id="modalOrderNumber" style="font-size: 16px;"></p>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold" style="font-size: 14px;">Nama Klien
                                        </p>
                                        <p class="fw-medium" id="modalRequesterName" style="font-size: 16px;"></p>
                                    </div>
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold" style="font-size: 14px;">Lokasi</p>
                                        <p class="fw-medium" id="modalRequestLocation" style="font-size: 16px;"></p>
                                    </div>
                                </div>
                                <div class="d-flex flex-fill">
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold" style="font-size: 14px;">Tanggal
                                            Pemesanan</p>
                                        <p class="fw-medium" id="modalTransactionCreatedAt" style="font-size: 16px;"></p>
                                    </div>
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold" style="font-size: 14px;">Tanggal
                                            Selesai</p>
                                        <p class="fw-medium" id="modalTransactionUpdatedAt" style="font-size: 16px;"></p>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold" style="font-size: 14px;">Mulai Kerja
                                        </p>
                                        <p class="fw-medium" id="modalStartWork" style="font-size: 16px;"></p>
                                    </div>
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold" style="font-size: 14px;">Selesai
                                            Kerja</p>
                                        <p class="fw-medium" id="modalFinishWork" style="font-size: 16px;"></p>
                                    </div>
                                </div>
                                <hr class="my-1 border border-dark">
                                <div class="d-flex mt-1">
                                    <div class="text d-flex justify-content-between align-items-center"
                                        style="width:50%;">
                                        <p class="p-0 m-0 text-black-50 fw-semibold" style="font-size: 16px;">Total</p>
                                        <p class="p-0 m-0 fw-medium text-end" style="font-size: 18px;">Rp
                                            <span id="modalRequestPrice"></span>
                                        </p>
                                    </div>
                                    <div class="text d-flex justify-content-end align-items-center" style="width:50%;">
                                        <a href="#" id="modalInvoiceLink"
                                            class="d-flex text-decoration-none justify-content-center align-items-center">
                                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M8.5029 12.668L3.29334 7.45843L4.75202 5.94766L7.46099 8.65663V0.165039H9.54482V8.65663L12.2538 5.94766L13.7125 7.45843L8.5029 12.668ZM2.25143 16.8356C1.67838 16.8356 1.18781 16.6316 0.779726 16.2235C0.371644 15.8154 0.167603 15.3249 0.167603 14.7518V11.6261H2.25143V14.7518H14.7544V11.6261H16.8382V14.7518C16.8382 15.3249 16.6342 15.8154 16.2261 16.2235C15.818 16.6316 15.3274 16.8356 14.7544 16.8356H2.25143Z"
                                                    fill="#294287" />
                                            </svg>
                                            <div class="ms-2 fw-medium" style="font-size: 18px;">Invoice</div>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="vr d-none d-lg-block mx-3"></div>

                            {{-- Review and Report Section --}}
                            <div class="d-flex flex-column align-items-center justify-content-center flex-grow-1">
                                <h4 class="fw-semibold mt-3 mb-1" id="reviewSectionHeading"></h4>

                                <div id="review-section-container" class="w-100">
                                </div>

                                {{-- Action Buttons --}}
                                <div class="d-flex flex-column mt-3 justify-content-center">
                                    <button type="button" class="btn btn-primary fw-medium rounded-3"
                                        onclick="submitReview()" id="submitReviewButton">Kirim</button>
                                    <div class="m-1 text-center" style="font-size: 14px;">Atau</div>
                                    <button type="button" class="m-0 p-0 fw-medium btn text-danger"
                                        onclick="openReportModal()">Laporkan masalah</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Report Work Modal --}}
    <div class="modal fade" id="reportWorkModal" tabindex="-1" aria-labelledby="reportWorkModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" style="max-width: 900px;">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form id="reportForm" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <input type="hidden" name="transaction_id" id="reportTransactionId">
                <input type="hidden" name="reporter_id" value="{{ auth()->id() }}">
                <input type="hidden" name="reported_id" id="reportReportedId">

                <div class="modal-header border-0 justify-content-center">
                    <h3 class="modal-title fw-bold text-center w-100" id="reportWorkModalLabel">Laporan</h3>
                    <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <hr class="mx-auto mb-3" style="width: 50px; height: 4px; background-color: #D3FA0D; border: none;">

                <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">
                    {{-- Transaction Details for Report --}}
                    <div class="row mb-3">
                        <div class="col-md-6 col-lg-3">
                            <p class="text-black-50 fw-semibold mb-0" style="font-size: 14px;">Judul Pesanan</p>
                            <p class="fw-medium" id="reportModalRequestTitle" style="font-size: 16px;"></p>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <p class="text-black-50 fw-semibold mb-0" style="font-size: 14px;">Nomor Pesanan</p>
                            <p class="fw-medium" id="reportModalOrderNumber" style="font-size: 16px;"></p>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <p class="text-black-50 fw-semibold mb-0" style="font-size: 14px;">Nama Klien</p>
                            <p class="fw-medium" id="reportModalRequesterName" style="font-size: 16px;"></p>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <p class="text-black-50 fw-semibold mb-0" style="font-size: 14px;">Lokasi</p>
                            <p class="fw-medium" id="reportModalRequestLocation" style="font-size: 16px;"></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 col-lg-3">
                            <p class="text-black-50 fw-semibold mb-0" style="font-size: 14px;">Tanggal Pemesanan</p>
                            <p class="fw-medium" id="reportModalTransactionCreatedAt" style="font-size: 16px;"></p>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <p class="text-black-50 fw-semibold mb-0" style="font-size: 14px;">Tanggal Selesai</p>
                            <p class="fw-medium" id="reportModalTransactionUpdatedAt" style="font-size: 16px;"></p>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <p class="text-black-50 fw-semibold mb-0" style="font-size: 14px;">Waktu Mulai</p>
                            <p class="fw-medium" id="reportModalStartWork" style="font-size: 16px;"></p>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <p class="fw-medium" id="reportModalFinishWork" style="font-size: 16px;"></p>
                        </div>
                    </div>

                    {{-- Total Price for Report --}}
                    <div class="d-flex justify-content-between mb-4">
                        <p class="text-black-50 fw-semibold mb-0" style="font-size: 16px;">Total</p>
                        <p class="fw-medium mb-0" style="font-size: 18px;">Rp <span id="reportModalRequestPrice"></span>
                        </p>
                    </div>

                    {{-- Image Upload Section --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="font-size: 16px;">Upload Bukti (gambar):</label>
                        <div class="d-flex flex-wrap gap-3 align-items-start" id="reportImagePreviewContainer">
                            <div class="pb-2" onclick="document.getElementById('reportImageInput').click()"
                                style="width: 80px; height: 80px; border: 2px dashed #294287; background-color: #f7f7ff; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                <span class="text-center" style="font-size: 32px; color:#294287;">+</span>
                            </div>
                        </div>
                        <input type="file" class="d-none" id="reportImageInput" name="photo[]" accept="image/*"
                            multiple>
                    </div>

                    {{-- Reporting Reasons Textarea --}}
                    <div class="mb-4">
                        <label for="reportNote" class="form-label fw-semibold" style="font-size: 16px;">Keluh Kesah
                            Anda</label>
                        <textarea name="reasons" id="reportNote" class="form-control rounded-4" rows="4"
                            placeholder="Ceritakan masalah yang Anda alami..." style="background-color: #f7f7ff; font-size: 16px;"></textarea>
                    </div>
                </div>

                {{-- Report Submission Button --}}
                <div class="modal-footer border-0 d-flex justify-content-end">
                    <button type="button" id="submitReportButton" class="btn btn-danger px-4 py-2">Kirim
                        Laporan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Global Variables
        let currentTransactionId = null;
        let reportedRequesterId = null;
        let selectedRating = 0;

        // Image Preview Logic for Report Modal
        const reportImageInput = document.getElementById('reportImageInput');
        const reportImagePreviewContainer = document.getElementById('reportImagePreviewContainer');

        reportImageInput.addEventListener('change', function(event) {
            reportImagePreviewContainer.innerHTML = `
                <div class="pb-2" onclick="document.getElementById('reportImageInput').click()"
                    style="width: 80px; height: 80px; border: 2px dashed #294287; background-color: #f7f7ff; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                    <span class="text-center" style="font-size: 32px; color:#294287;">+</span>
                </div>
            `;
            Array.from(event.target.files).forEach(file => {
                if (!file.type.startsWith('image/')) return;
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'rounded border img-thumbnail';
                    img.style.width = '80px';
                    img.style.height = '80px';
                    img.style.objectFit = 'cover';
                    reportImagePreviewContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        });

        // Function to Open Report Modal
        function openReportModal() {
            var completionModal = bootstrap.Modal.getInstance(document.getElementById('completionModal'));
            if (completionModal) {
                completionModal.hide();
            }

            const reportModal = new bootstrap.Modal(document.getElementById('reportWorkModal'));
            const rowData = document.querySelector(`.order-row[data-transaction-id="${currentTransactionId}"]`);

            if (rowData) {
                document.getElementById('reportModalRequestTitle').textContent = rowData.dataset.requestTitle;
                document.getElementById('reportModalOrderNumber').textContent = rowData.dataset.orderNumber;
                document.getElementById('reportModalRequesterName').textContent =
                    `${rowData.dataset.requesterFirstName} ${rowData.dataset.requesterLastName}`;
                document.getElementById('reportModalRequestLocation').textContent = rowData.dataset.requestLocation;
                document.getElementById('reportModalTransactionCreatedAt').textContent = rowData.dataset
                    .transactionCreatedAt;
                document.getElementById('reportModalTransactionUpdatedAt').textContent = rowData.dataset
                    .transactionUpdatedAt;
                document.getElementById('reportModalRequestPrice').textContent = rowData.dataset.requestPrice;
                document.getElementById('reportModalStartWork').textContent = rowData.dataset.startWork;
                document.getElementById('reportModalFinishWork').textContent = rowData.dataset.finishWork;

                document.getElementById('reportTransactionId').value = currentTransactionId;
                document.getElementById('reportReportedId').value = reportedRequesterId;

                document.getElementById('reportForm').action = `/worker/submit-report/${currentTransactionId}`;
            }

            setTimeout(() => {
                reportModal.show();
            }, 300);
        }

        // Handle Invoice Link Click
        function handleInvoiceLinkClick(e) {
            e.preventDefault();
            const transactionId = this.getAttribute('data-transaction-id');
            if (transactionId) {
                const invoiceUrl = `/generate-invoice/${transactionId}`;
                window.open(invoiceUrl, '_blank');
            } else {
                console.error('Transaction ID not found for invoice generation.');
                alert('Terjadi kesalahan: ID transaksi tidak ditemukan untuk pembuatan invoice.');
            }
        }

        // Function to Submit Review
        function submitReview() {
            const comment = document.getElementById('comment').value.trim();
            const rating = document.getElementById('rating-input').value;

            if (rating == 0) {
                alert('Silakan pilih rating terlebih dahulu.');
                return;
            }

            if (comment == '') {
                alert('Silakan isi komentar.');
                return;
            }

            fetch(`/reviews/${currentTransactionId}`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        transaction_id: currentTransactionId,
                        reviewer_id: "{{ auth()->id() }}",
                        reviewee_id: reportedRequesterId,
                        rating: rating,
                        comment: comment,
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(errorData.message || 'Server error: ' + response.statusText);
                        }).catch(() => {
                            throw new Error('Network response was not ok or non-JSON error. Status: ' + response
                                .status);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert('Review berhasil disimpan!');
                        var completionModal = bootstrap.Modal.getInstance(document.getElementById('completionModal'));
                        completionModal.hide();
                        location.reload();
                    } else {
                        alert('Gagal menyimpan review, coba lagi. ' + (data.message || ''));
                    }
                })
                .catch(error => {
                    console.error('Error submitting review:', error);
                    alert('Terjadi kesalahan saat mengirim review, coba lagi.\nDetails: ' + error.message);
                });
        }

        // Function to Submit Report
        function submitReport(event) {
            event.preventDefault();

            const form = document.getElementById('reportForm');
            if (!form) {
                console.error('reportForm not found!');
                return;
            }
            const formData = new FormData(form);

            const reasons = document.getElementById('reportNote').value.trim();
            if (!reasons) {
                alert('Harap isi keluh kesah Anda terlebih dahulu.');
                return;
            }
            if (reportImageInput.files.length === 0) {
                alert("Silakan upload minimal satu foto bukti laporan.");
                return;
            }

            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw new Error(errorData.message || 'Server error: ' + response.statusText);
                        }).catch(() => {
                            throw new Error('Network response was not ok or non-JSON error. Status: ' + response
                                .status);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(data);
                    alert(data.message);
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengirim laporan.\nDetails: ' + error.message);
                });
        }

        // DOM Content Loaded
        document.addEventListener('DOMContentLoaded', function() {
            const orderRows = document.querySelectorAll('.order-row');
            const submitReviewButton = document.getElementById('submitReviewButton');
            const reviewSectionContainer = document.getElementById('review-section-container');
            const reviewSectionHeading = document.getElementById('reviewSectionHeading');

            // Function to update star display for reviews
            function updateStarDisplay(rating) {
                const stars = document.querySelectorAll('#review-section-container .star-rating');
                stars.forEach(star => {
                    const sVal = parseInt(star.getAttribute('data-value'));
                    if (sVal <= rating) {
                        star.classList.add('star-blue');
                        star.classList.remove('text-secondary');
                    } else {
                        star.classList.remove('star-blue');
                        star.classList.add('text-secondary');
                    }
                });
            }

            // Function to render the review form
            function renderReviewForm() {
                reviewSectionHeading.textContent = 'Kasih penilaian, yuk!';
                reviewSectionContainer.innerHTML = `
                    <div class="text-center mt-0 mb-3 w-100">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star-fill text-secondary star-rating" style="font-size: 32px;" data-value="{{ $i }}"></i>
                        @endfor
                        <input type="hidden" name="rating" id="rating-input" value="0">
                    </div>
                    <div class="ps-3 flex-fill d-flex flex-column w-100">
                        <label for="comment" class="form-label text-start" style="font-size: 16px;">Komentar</label>
                        <textarea name="comment" id="comment" class="form-control" rows="3"
                            placeholder="Tulis komentarmu di sini..." style="border-color:#8a8a8a; font-size: 16px;"></textarea>
                    </div>
                `;
                const newStars = reviewSectionContainer.querySelectorAll('.star-rating');
                const newRatingInput = document.getElementById('rating-input');
                newStars.forEach(star => {
                    star.addEventListener('mouseover', function() {
                        const val = parseInt(this.getAttribute('data-value'));
                        updateStarDisplay(val);
                    });
                    star.addEventListener('mouseout', function() {
                        updateStarDisplay(selectedRating);
                    });
                    star.addEventListener('click', function() {
                        selectedRating = parseInt(this.getAttribute('data-value'));
                        newRatingInput.value = selectedRating;
                        updateStarDisplay(selectedRating);
                    });
                });
                if (submitReviewButton) {
                    submitReviewButton.style.display = 'block';
                }
            }

            // Function to render existing review display
            function renderExistingReview(rating, comment) {
                reviewSectionHeading.textContent = 'Ini penilaianmu';
                let starHtml = '';
                for (let i = 1; i <= 5; i++) {
                    starHtml +=
                        `<i class="bi bi-star-fill ${i <= rating ? 'star-blue' : 'text-secondary'}" style="font-size: 32px;"></i>`;
                }

                reviewSectionContainer.innerHTML = `
                    <div class="text-center mt-0 mb-3 w-100">
                        ${starHtml}
                    </div>
                    <div class="ps-3 flex-fill d-flex flex-column w-100">
                        <label for="comment" class="form-label text-start" style="font-size: 16px;">Komentar</label>
                        <textarea id="comment" class="form-control" rows="3" disabled
                            style="border-color:#8a8a8a; font-size: 16px;">${comment}</textarea>
                    </div>
                `;
                if (submitReviewButton) {
                    submitReviewButton.style.display = 'none';
                }
            }

            // Order Row Click Listener
            orderRows.forEach(row => {
                row.addEventListener('click', function(event) {
                    event.preventDefault();

                    const transactionId = this.getAttribute('data-transaction-id');
                    const orderStatusText = this.getAttribute('data-order-status-text');
                    const hasReview = this.getAttribute('data-has-review') === 'true';
                    const userRating = this.getAttribute('data-user-rating');
                    const userComment = this.getAttribute('data-user-comment');

                    currentTransactionId = transactionId;
                    reportedRequesterId = this.getAttribute('data-requester-id');

                    if (orderStatusText === 'Selesai') {
                        const completionModal = new bootstrap.Modal(document.getElementById(
                            'completionModal'));
                        completionModal.show();

                        document.getElementById('modalRequestTitle').textContent = this.dataset
                            .requestTitle;
                        document.getElementById('modalOrderNumber').textContent = this.dataset
                            .orderNumber;
                        document.getElementById('modalRequesterName').textContent =
                            `${this.dataset.requesterFirstName} ${this.dataset.requesterLastName}`;
                        document.getElementById('modalRequestLocation').textContent = this.dataset
                            .requestLocation;
                        document.getElementById('modalTransactionCreatedAt').textContent = this
                            .dataset.transactionCreatedAt;
                        document.getElementById('modalTransactionUpdatedAt').textContent = this
                            .dataset.transactionUpdatedAt;
                        document.getElementById('modalRequestPrice').textContent = this.dataset
                            .requestPrice;
                        document.getElementById('modalStartWork').textContent = this.dataset
                            .startWork;
                        document.getElementById('modalFinishWork').textContent = this.dataset
                            .finishWork;

                        document.getElementById('reviewForm').action = `/reviews/${transactionId}`;

                        if (hasReview) {
                            renderExistingReview(userRating, userComment);
                            selectedRating = parseInt(userRating);
                        } else {
                            renderReviewForm();
                            selectedRating = 0;
                        }

                        const invoiceLink = document.getElementById('modalInvoiceLink');
                        if (invoiceLink) {
                            invoiceLink.setAttribute('data-transaction-id', transactionId);
                            invoiceLink.removeEventListener('click', handleInvoiceLinkClick);
                            invoiceLink.addEventListener('click', handleInvoiceLinkClick);
                        }

                    } else if (['Dikerjain', 'Diterima', 'Ditinjau'].includes(orderStatusText)) {
                        window.location.href = `/job-taker/accepted-work-request/${transactionId}`;
                    } else {
                        console.log('Clicked on a row with status:', orderStatusText,
                            'No specific action defined.');
                    }
                });
            });

            // Attach submitReport to the "Kirim Laporan" button
            const submitReportButtonForReportModal = document.getElementById('submitReportButton');
            if (submitReportButtonForReportModal) {
                submitReportButtonForReportModal.addEventListener('click', submitReport);
            }

            // Reset review form state when the completion modal is hidden
            const completionModalElement = document.getElementById('completionModal');
            completionModalElement.addEventListener('hidden.bs.modal', function() {
                selectedRating = 0;
            });

            // Tab and Dropdown Filtering Functionality
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabSelect = document.getElementById('tab-select');
            const orderRowsForTabs = document.querySelectorAll('.order-row');
            const noTransactionMessage = document.getElementById('no-transaction-message');
            const orderListContainer = document.getElementById('order-list-container');

            function updateTabContent(selectedTab) {
                let hasVisibleOrders = false;
                let visibleRows = [];

                orderListContainer.classList.remove('order-list-fade-in');
                void orderListContainer.offsetWidth;
                orderListContainer.classList.add('order-list-fade-in');

                orderRowsForTabs.forEach(row => {
                    const badgeElement = row.querySelector('.badge');
                    let orderStatusText = badgeElement ? badgeElement.textContent.trim() : '';

                    let orderStatusForTab = '';
                    if (orderStatusText === 'Selesai') {
                        orderStatusForTab = 'completed';
                    } else if (['Dikerjain', 'Diterima', 'Ditinjau'].includes(orderStatusText)) {
                        orderStatusForTab = 'pending';
                    } else if (orderStatusText === 'Dibatalin') {
                        orderStatusForTab = 'cancelled';
                    } else {
                        orderStatusForTab = 'other';
                    }

                    if (selectedTab === 'all' || selectedTab === orderStatusForTab) {
                        row.style.display = '';
                        hasVisibleOrders = true;
                        visibleRows.push(row);
                    } else {
                        row.style.display = 'none';
                    }
                });

                const existingHrs = orderListContainer.querySelectorAll('.order-divider');
                existingHrs.forEach(hr => hr.remove());

                for (let i = 0; i < visibleRows.length - 1; i++) {
                    const hr = document.createElement('hr');
                    hr.classList.add('mx-auto', 'border-1', 'opacity-100', 'my-0', 'p-0', 'order-divider');
                    hr.style.cssText = 'width: 98%; border-color: #294287;';
                    visibleRows[i].parentNode.insertBefore(hr, visibleRows[i].nextSibling);
                }

                if (noTransactionMessage) {
                    if (hasVisibleOrders) {
                        noTransactionMessage.style.display = 'none';
                    } else {
                        noTransactionMessage.style.display = '';
                    }
                }
            }

            // Event listeners for desktop tab buttons
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    updateTabContent(this.dataset.tab);
                });
            });

            // Event listener for mobile tab dropdown
            if (tabSelect) {
                tabSelect.addEventListener('change', function() {
                    updateTabContent(this.value);
                    this.style.borderColor = '#bfff00';
                    this.style.borderWidth = '2px';
                });

                if (window.innerWidth < 768) {
                    if (tabSelect.value === 'all' || !tabSelect.value) {
                        tabSelect.style.borderColor = '#bfff00';
                        tabSelect.style.borderWidth = '2px';
                    } else {
                        tabSelect.style.borderColor = '#bfff00';
                        tabSelect.style.borderWidth = '2px';
                    }
                }
            }

            // Initial load of tab content
            if (window.innerWidth < 768 && tabSelect) {
                updateTabContent(tabSelect.value);
            } else if (document.querySelector('.tab-button.active')) {
                updateTabContent(document.querySelector('.tab-button.active').dataset.tab);
            }

            // Fix for persistent modal overlay
            document.querySelectorAll('.modal').forEach(modalElement => {
                modalElement.addEventListener('hidden.bs.modal', function() {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                });
            });
        });
    </script>
    <style>
        /* Star Rating Styles */
        .star-rating {
            cursor: pointer;
            transition: color 0.2s ease-in-out;
        }

        .star-blue {
            color: gold !important;
        }

        /* Tab Navigation Styles */
        .tabs-wrapper {
            height: 3.5rem;
            border-radius: 1.5rem;
            position: relative;
            z-index: 0;
            display: flex;
            flex-wrap: wrap;
        }

        .tab-button {
            border-radius: 0.5rem;
            color: #6b7280;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
            background-color: transparent;
            border: none;
            cursor: pointer;
            white-space: nowrap;
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            padding: 0.5rem 1rem;
        }

        .tab-button:hover {
            color: #4f46e5;
        }

        .tab-button.active {
            background-color: #ffffff;
            color: #1f2937;
            font-weight: 600;
            z-index: 10;
            border-top-left-radius: 1.5rem;
            border-top-right-radius: 1.5rem;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            border-top: 8px solid #bfff00;
            padding-top: calc(0.5rem - 8px);
            padding-bottom: calc(0.5rem - 2px);
            margin-bottom: -4px;
            transition: all 0.3s ease-in-out;
        }

        /* Order List Row Hover Effect */
        .hoverable-row {
            cursor: pointer;
            transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
            border-radius: 0.25rem;
        }

        .hoverable-row:hover {
            background-color: #f8f9fa;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.175);
            transform: translateY(-2px);
        }

        /* Status Badge Styling */
        .status-badge {
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        /* Fixed width for status badge */
        .status-badge-fixed {
            min-width: 90px;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Global Table Cell Styling for Text Wrapping */
        .tableHeader .col,
        .order-row .col {
            word-break: break-word;
            white-space: normal;
        }

        /* Table header font size */
        .tableHeader .col {
            font-size: 14px;
        }

        /* Order row default font size */
        .order-row .col {
            font-size: 14px;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes scaleIn {
            from {
                transform: scale(0.95);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        /* Apply fade-in to the order list container */
        .order-list-fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        /* Apply scale-in to modals */
        .modal.fade .modal-dialog {
            transition: transform 0.3s ease-out, opacity 0.3s ease-out;
            transform: scale(0.9);
            opacity: 0;
        }

        .modal.fade.show .modal-dialog {
            transform: scale(1);
            opacity: 1;
        }

        /* Responsive Adjustments for Mobile (max-width: 767px) */
        @media (max-width: 767px) {
            .tabs-wrapper {
                display: none !important;
            }

            .tabs-dropdown-wrapper {
                display: block !important;
            }

            .tabs-dropdown-wrapper select {
                border-color: #bfff00 !important;
                border-width: 2px !important;
            }

            .tableHeader .col,
            .order-row .col {
                font-size: 12px !important;
                padding-left: 0.3rem;
                padding-right: 0.3rem;
                white-space: normal !important;
            }

            /* Adjust padding for the Judul column header */
            .tableHeader .col:first-child {
                padding-left: 0.8rem !important; /* Adjusted for mobile header */
            }

            /* Adjust padding for the Judul column in order rows */
            .order-row .title-col {
                padding-left: 0.8rem !important; /* Adjusted for mobile rows */
            }

            .order-row .badge {
                font-size: 12px !important;
                padding: .2em .4em !important;
            }

            .order-row .status-badge-fixed {
                min-width: 70px !important;
            }

            .order-row .col.m-0.p-0 {
                margin: 0 !important;
            }

            .modal-body p {
                font-size: 14px !important;
            }

            .modal-body .fw-medium {
                font-size: 16px !important;
            }

            .modal-header h5 {
                font-size: 20px !important;
            }

            .modal-body label {
                font-size: 14px !important;
            }

            .modal-body textarea {
                font-size: 14px !important;
            }

            .modal-footer button {
                font-size: 14px !important;
            }

            #reviewSectionHeading {
                font-size: 18px !important;
            }

            #modalInvoiceLink .ms-2 {
                font-size: 16px !important;
            }

            .star-rating {
                font-size: 28px !important;
            }
        }

        /* Specific Adjustments for Smaller Screens (max-width: 500px) */
        @media (max-width: 500px) {

            .tableHeader .col,
            .order-row .col {
                padding-left: 0.6rem !important;
                padding-right: 0.6rem !important;
            }

            /* Further adjust padding for the Judul column header */
            .tableHeader .col:first-child {
                padding-left: 0.6rem !important;
            }

            /* Further adjust padding for the Judul column in order rows */
            .order-row .title-col {
                padding-left: 0.6rem !important;
            }

            .order-row .badge {
                font-size: 10px !important;
            }

            .order-row .status-badge-fixed {
                min-width: 60px !important;
            }
        }

        /* Specific Adjustments for Very Small Screens (max-width: 433px) */
        @media (max-width: 440px) {

            .tableHeader .col,
            .order-row .col {
                font-size: 11px !important;
                /* Slightly smaller font size */
                padding-left: 0.4rem !important;
                /* Adjust padding if needed */
                padding-right: 0.4rem !important;
                /* Adjust padding if needed */
            }

            /* Even further adjust padding for the Judul column header */
            .tableHeader .col:first-child {
                padding-left: 0.4rem !important;
            }

            /* Even further adjust padding for the Judul column in order rows */
            .order-row .title-col {
                padding-left: 0.4rem !important;
            }

            .order-row .badge {
                font-size: 9px !important;
                /* Slightly smaller badge font size */
            }

            /* You might also need to adjust modal font sizes if they become too large */
            .modal-body p,
            .modal-body label,
            .modal-body textarea {
                font-size: 12px !important;
            }

            .modal-body .fw-medium {
                font-size: 14px !important;
            }

            .modal-header h5 {
                font-size: 18px !important;
            }

            #reviewSectionHeading {
                font-size: 16px !important;
            }

            #modalInvoiceLink .ms-2 {
                font-size: 14px !important;
            }

            .star-rating {
                font-size: 24px !important;
            }
        }

        /* Desktop Specific Styles (min-width: 768px) */
        @media (min-width: 768px) {
            .tabs-wrapper {
                display: flex !important;
            }

            .tabs-dropdown-wrapper {
                display: none !important;
            }

            .tableHeader .col {
                font-size: 16px;
            }

            /* Default padding for desktop Judul column header */
            .tableHeader .col:first-child {
                padding-left: 1rem; /* Corresponds to Bootstrap's ps-3 */
            }

            .order-row .col {
                font-size: 14px;
            }
            /* Default padding for desktop Judul column in order rows */
            .order-row .title-col {
                padding-left: 1rem; /* Corresponds to Bootstrap's ps-3 */
            }


            .order-row .badge {
                font-size: 14px;
            }

            .modal-body p,
            .modal-body label,
            .modal-body textarea {
                font-size: 16px;
            }

            .modal-body .fw-medium {
                font-size: 18px;
            }

            .modal-header h5 {
                font-size: 24px;
            }

            #reviewSectionHeading {
                font-size: 22px;
            }

            #modalInvoiceLink .ms-2 {
                font-size: 18px;
            }

            .star-rating {
                font-size: 32px;
            }
        }
    </style>
@endsection
