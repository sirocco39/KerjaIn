@extends('Master.master-job_req')

@section('content')
    <div class="container-fluid pembatas-x pembatas-y">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b-4 border-yellow-400 pb-2 inline-block">Riwayat Pemesanan
        </h1>
        <div class="flex flex-col md:flex-row justify-start items-start md:items-center">
            <div class="tabs-wrapper">
                <div class="col tab-button active" data-tab="all">
                    All Order ({{ $allOrders->count() }})
                </div>
                <div class="col tab-button" data-tab="pending">
                    Pending ({{ $pendingOrders->count() }})
                </div>
                <div class="col tab-button" data-tab="completed">
                    Completed ({{ $completedOrders->count() }})
                </div>
                <div class="col tab-button" data-tab="cancelled">
                    Cancelled ({{ $cancelledOrders->count() }})
                </div>
            </div>
        </div>


        <div class="flex flex-col md:flex-row justify-start items-start md:items-center">
            <div class="w-full bg-white rounded-lg">
                <div class="row text-center tableHeader d-flex align-items-center justify-content-center fw-semibold m-0 p-0"
                    style="height: 4rem;">
                    <div class="col m-0 p-0">Judul</div>
                    <div class="col m-0 p-0">Status</div>
                    <div class="col m-0 p-0">Tanggal Selesai</div>
                    <div class="col m-0 p-0">Klien</div>
                    <div class="col m-0 p-0">Lokasi</div>
                    <div class="col m-0 p-0">Harga</div>
                </div>
                <hr class="mx-auto border-2 opacity-100 my-0 p-0" style="width: 98%; border-color: #294287;">
                <div id="order-list-container">
                    @forelse ($allOrders as $order)
                        <div class="order-row hoverable-row
                            {{-- Add class for tab filtering based on original status --}}
                            {{ str_replace(' ', '-', $order->status) }}-tab
                            {{-- Add no-click class if status_text is NOT 'Selesai' --}}
                            @if ($order->status_text != 'Selesai') no-click @endif"
                            {{-- Conditionally add modal trigger attributes --}}
                            @if ($order->status_text == 'Selesai') data-bs-toggle="modal"
                                data-bs-target="#completionModal" @endif
                            data-transaction-id="{{ $order->id }}"
                            data-request-title="{{ $order->request->title ?? '-' }}"
                            data-order-number="{{ $order->order_number ?? '-' }}"
                            data-worker-first-name="{{ $order->worker->first_name ?? '' }}"
                            data-worker-last-name="{{ $order->worker->last_name ?? '' }}"
                            data-requester-first-name="{{ $order->requester->first_name ?? '' }}"
                            data-requester-last-name="{{ $order->requester->last_name ?? '' }}"
                            data-request-location="{{ $order->request->location ?? '-' }}"
                            data-transaction-created-at="{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') ?? '-' }}"
                            data-transaction-updated-at="{{ \Carbon\Carbon::parse($order->updated_at)->format('d M Y') ?? '-' }}"
                            data-request-price="{{ number_format($order->request->price ?? 0, 0, ',', '.') ?? '-' }}"
                            data-start-work="{{ \Carbon\Carbon::parse($order->start_work)->format('H.i') ?? '-' }}"
                            data-finish-work="{{ \Carbon\Carbon::parse($order->finish_work)->format('H.i') ?? '-' }}">
                            <div class="row text-center text-sm d-flex justify-content-center align-items-center m-0 p-0"
                                style="height: 3.5rem">
                                {{-- Request Title --}}
                                <div class="col m-0 p-0"> {{ $order->request->title ?? '-' }}</div>
                                {{-- Status Badge --}}
                                <div class="col m-0 p-0">
                                    <span class="badge rounded-pill"
                                        style="
                                        padding: .5em .9em;
                                        font-size: 0.85em;
                                        {{-- Use status_text for badge styling as well --}}
                                        @if ($order->status_text == 'Selesai') background-color: #D3FA0D;
                                            color: #333;
                                        @elseif($order->status_text == 'Dikerjain' || $order->status_text == 'Diterima' || $order->status_text == 'Ditinjau')
                                            background-color: #309FFF;
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
                                {{-- Updated At Date (Tanggal Selesai) --}}
                                <div class="col m-0 p-0">
                                    {{ \Carbon\Carbon::parse($order->updated_at)->format('d - m - Y') ?? '-' }}</div>
                                {{-- Requester Name (Klien) --}}
                                <div class="col m-0 p-0">{{ $order->requester->full_name ?? '-' }}</div>
                                {{-- Request Location (Lokasi) --}}
                                <div class="col m-0 p-0">{{ $order->request->location ?? '-' }}</div>
                                {{-- Price (Harga) --}}
                                <div class="col m-0 p-0">Rp
                                    {{ number_format($order->request->price ?? 0, 0, ',', '.') ?? '-' }}</div>
                            </div>
                        </div>
                    @empty
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
                            <div class="d-flex flex-column flex-grow-1">
                                <div class="d-flex flex-fill">
                                    <div class="text flex-fill">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">Judul Pesanan</p>
                                        <p class="fw-medium" id="modalRequestTitle"></p>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <div class="text">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">Nomor Pesanan</p>
                                        <p class="fw-medium" id="modalOrderNumber"></p>
                                    </div>
                                </div>
                                <div class="d-flex flex-fill">
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">Nama Klien</p>
                                        <p class="fw-medium" id="modalRequesterName"></p>
                                    </div>
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">Lokasi</p>
                                        <p class="fw-medium" id="modalRequestLocation"></p>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">Tanggal Pemesanan</p>
                                        <p class="fw-medium" id="modalTransactionCreatedAt"></p>
                                    </div>
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">Tanggal Selesai</p>
                                        <p class="fw-medium" id="modalTransactionUpdatedAt"></p>
                                    </div>
                                </div>
                                <hr class="my-1 border border-dark">
                                <div class="d-flex mt-1">
                                    <div class="text d-flex justify-content-between align-items-center"
                                        style="width:50%;">
                                        <p class="p-0 m-0 text-black-50 fw-semibold fs-6">Total</p>
                                        <p class="p-0 m-0 fw-medium fs-lg-6 text-end">Rp
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
                                                    fill="#309FFF" />
                                            </svg>
                                            <div class="ms-2 fw-medium fs-5">Invoice</div>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="vr d-none d-lg-block mx-3"></div>

                            <div class="d-flex flex-column align-items-center justify-content-center flex-grow-1">
                                <h4 class="fw-semibold mt-3 mb-1">Kasih penilaian, yuk!</h4>
                                <div class="text-center mt-0 mb-3 w-100">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star-fill text-secondary star-rating fs-2"
                                            data-value="{{ $i }}"></i>
                                    @endfor
                                    <input type="hidden" name="rating" id="rating-input" value="0">
                                </div>

                                <div class="ps-3 flex-fill d-flex flex-column w-100">
                                    <label for="comment" class="form-label text-start">Komentar</label>
                                    <textarea name="comment" id="comment" class="form-control" rows="3"
                                        placeholder="Tulis komentarmu di sini..." style="border-color:#8a8a8a;"></textarea>
                                </div>

                                <div class="d-flex flex-column mt-3 justify-content-center">
                                    <button type="button" class="btn btn-primary fw-medium rounded-3"
                                        onclick="submitReview()">Kirim</button>
                                    <div class="m-1 text-center">Atau</div>
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

    <div class="modal fade" id="reportWorkModal" tabindex="-1" aria-labelledby="reportWorkModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" style="max-width: 900px;">
            <form id="reportForm" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header border-0 justify-content-center">
                    <h3 class="modal-title fw-bold text-center w-100" id="reportWorkModalLabel">Laporan</h3>
                    <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <hr class="mx-auto mb-3" style="width: 50px; height: 4px; background-color: #D3FA0D; border: none;">

                <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">
                    <div class="row mb-3">
                        <div class="col-md-6 col-lg-3">
                            <p class="text-black-50 fw-semibold mb-0">Judul Pesanan</p>
                            <p class="fw-medium" id="reportModalRequestTitle"></p>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <p class="text-black-50 fw-semibold mb-0">Nomor Pesanan</p>
                            <p class="fw-medium" id="reportModalOrderNumber"></p>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <p class="text-black-50 fw-semibold mb-0">Nama Klien</p>
                            <p class="fw-medium" id="reportModalRequesterName"></p>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <p class="text-black-50 fw-semibold mb-0">Lokasi</p>
                            <p class="fw-medium" id="reportModalRequestLocation"></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 col-lg-3">
                            <p class="text-black-50 fw-semibold mb-0">Tanggal Pemesanan</p>
                            <p class="fw-medium" id="reportModalTransactionCreatedAt"></p>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <p class="text-black-50 fw-semibold mb-0">Tanggal Selesai</p>
                            <p class="fw-medium" id="reportModalTransactionUpdatedAt"></p>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <p class="text-black-50 fw-semibold mb-0">Waktu Mulai</p>
                            <p class="fw-medium" id="reportModalStartWork"></p>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <p class="text-black-50 fw-semibold mb-0">Waktu Selesai</p>
                            <p class="fw-medium" id="reportModalFinishWork"></p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-4">
                        <p class="text-black-50 fw-semibold mb-0">Total</p>
                        <p class="fw-medium fs-5 mb-0">Rp <span id="reportModalRequestPrice"></span></p>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Upload Bukti (gambar):</label>
                        <div class="d-flex flex-wrap gap-3 align-items-start" id="reportImagePreviewContainer">
                            <div class="pb-2" onclick="document.getElementById('reportImageInput').click()"
                                style="width: 80px; height: 80px; border: 2px dashed #309FFF; background-color: #f7f7ff; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                <span class="text-center" style="font-size: 32px; color:#309FFF;">+</span>
                            </div>
                        </div>
                        <input type="file" class="d-none" id="reportImageInput" name="images[]" accept="image/*"
                            multiple>
                    </div>

                    <div class="mb-4">
                        <label for="reportNote" class="form-label fw-semibold">Keluh Kesah Anda</label>
                        <textarea name="note" id="reportNote" class="form-control rounded-4" rows="4"
                            placeholder="Ceritakan masalah yang Anda alami..." style="background-color: #f7f7ff;"></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0 d-flex justify-content-end">
                    <button type="submit" class="btn btn-danger px-4 py-2">Kirim Laporan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Store the transaction ID globally when a row is clicked
        let currentTransactionId = null;

        // JavaScript for image preview in report modal
        const reportImageInput = document.getElementById('reportImageInput');
        const reportImagePreviewContainer = document.getElementById('reportImagePreviewContainer');

        reportImageInput.addEventListener('change', function(event) {
            reportImagePreviewContainer.innerHTML = `
                <div class="pb-2" onclick="document.getElementById('reportImageInput').click()"
                    style="width: 80px; height: 80px; border: 2px dashed #309FFF; background-color: #f7f7ff; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                    <span class="text-center" style="font-size: 32px; color:#309FFF;">+</span>
                </div>
            `; // Clear existing previews and keep the add button
            Array.from(event.target.files).forEach(file => {
                if (!file.type.startsWith('image/')) return; // Ensure it's an image
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className =
                        'rounded border img-thumbnail'; // Added border and img-thumbnail for better display
                    img.style.width = '80px';
                    img.style.height = '80px';
                    img.style.objectFit = 'cover';
                    reportImagePreviewContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        });

        // Function to open the report modal
        function openReportModal() {
            // Close completionModal
            var completionModal = bootstrap.Modal.getInstance(document.getElementById('completionModal'));
            completionModal.hide();

            // Populate report modal with data from the clicked row
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

                // Set the form action for the report
                document.getElementById('reportForm').action =
                    `/user/report/${currentTransactionId}`; // Adjust this route as needed based on your web.php
            }

            // Open reportModal after a small delay
            setTimeout(() => {
                reportModal.show();
            }, 300); // Small delay to allow completionModal to fully hide
        }

        // Star rating functionality for review modal
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('#completionModal .star-rating');
            const ratingInput = document.getElementById('rating-input');
            let selectedRating = 0;

            function updateStarDisplay(rating) {
                stars.forEach(star => {
                    const sVal = parseInt(star.getAttribute('data-value'));
                    if (sVal <= rating) {
                        star.classList.add(
                            'star-blue'); // Or 'text-warning' if you prefer Bootstrap's yellow
                        star.classList.remove('text-secondary');
                    } else {
                        star.classList.remove('star-blue');
                        star.classList.add('text-secondary');
                    }
                });
            }

            stars.forEach(star => {
                star.addEventListener('mouseover', function() {
                    const val = parseInt(this.getAttribute('data-value'));
                    updateStarDisplay(val);
                });

                star.addEventListener('mouseout', function() {
                    updateStarDisplay(selectedRating);
                });

                star.addEventListener('click', function() {
                    selectedRating = parseInt(this.getAttribute('data-value'));
                    ratingInput.value = selectedRating;
                    updateStarDisplay(selectedRating);
                });
            });

            // Reset stars and comment when the completion modal is hidden
            const completionModalElement = document.getElementById('completionModal');
            completionModalElement.addEventListener('hidden.bs.modal', function() {
                selectedRating = 0;
                ratingInput.value = 0;
                document.getElementById('comment').value = '';
                updateStarDisplay(0); // Reset star display
            });
        });

        // Function to submit the review via AJAX
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

            fetch(`/reviews/${currentTransactionId}`, { // Use currentTransactionId here
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        transaction_id: currentTransactionId, // Pass the dynamic ID
                        rating: rating,
                        comment: comment,
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Review berhasil disimpan!');
                        var completionModal = bootstrap.Modal.getInstance(document.getElementById('completionModal'));
                        completionModal.hide();
                        location.reload(); // Reload to reflect changes
                    } else {
                        alert('Gagal menyimpan review, coba lagi. ' + (data.message || ''));
                    }
                })
                .catch(error => {
                    console.error('Error submitting review:', error);
                    alert('Terjadi kesalahan saat mengirim review, coba lagi.');
                });
        }


        // Global listener for opening the completion modal
        document.getElementById('completionModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget; // Button that triggered the modal (which is the row itself)
            const transactionId = button.getAttribute('data-transaction-id');

            // Store the transaction ID for later use in submitReview and openReportModal
            currentTransactionId = transactionId;

            // Populate the modal fields
            document.getElementById('modalRequestTitle').textContent = button.getAttribute('data-request-title');
            document.getElementById('modalOrderNumber').textContent = button.getAttribute('data-order-number');
            document.getElementById('modalRequesterName').textContent =
                `${button.getAttribute('data-requester-first-name')} ${button.getAttribute('data-requester-last-name')}`;
            document.getElementById('modalRequestLocation').textContent = button.getAttribute(
                'data-request-location');
            document.getElementById('modalTransactionCreatedAt').textContent = button.getAttribute(
                'data-transaction-created-at');
            document.getElementById('modalTransactionUpdatedAt').textContent = button.getAttribute(
                'data-transaction-updated-at');
            document.getElementById('modalRequestPrice').textContent = button.getAttribute('data-request-price');

            // Set the form action for review submission
            document.getElementById('reviewForm').action =
                `/reviews/${transactionId}`; // This sets the form action directly
            document.getElementById('reportForm').action =
                `/user/report/${transactionId}`; // Set for report modal too

            // Set the data-transaction-id on the invoice link within the modal
            const invoiceLink = document.getElementById('modalInvoiceLink');
            if (invoiceLink) {
                invoiceLink.setAttribute('data-transaction-id', transactionId);

                // *** CRITICAL FIX: Attach click listener for invoice link here ***
                // Remove any existing listener to prevent multiple bindings if modal opens/closes frequently
                invoiceLink.removeEventListener('click', handleInvoiceLinkClick);
                // Add the new listener
                invoiceLink.addEventListener('click', handleInvoiceLinkClick);
            }


            // Reset review form state when opening the modal
            document.getElementById('rating-input').value = 0;
            document.getElementById('comment').value = '';
            document.querySelectorAll('#completionModal .star-rating').forEach(star => {
                star.classList.remove('star-blue');
                star.classList.remove('star-green'); // Clear any hover effects
                star.classList.add('text-secondary');
            });
        });

        // *** New function for handling invoice link click to avoid re-creating it multiple times ***
        function handleInvoiceLinkClick(e) {
            e.preventDefault(); // Prevent the default link behavior (navigating)

            const transactionId = this.getAttribute('data-transaction-id');
            if (transactionId) {
                // Construct the URL for the invoice generation route
                const invoiceUrl = `/generate-invoice/${transactionId}`; // This matches your web.php route

                // Open the URL in a new tab. This will trigger the download.
                window.open(invoiceUrl, '_blank');
            } else {
                // You might want a more sophisticated notification than alert
                console.error('Transaction ID not found for invoice generation.');
                alert('Terjadi kesalahan: ID transaksi tidak ditemukan untuk pembuatan invoice.');
            }
        }


        // Tab functionality (existing code, ensure it still works with the data attribute changes)
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-button');
            const orderRows = document.querySelectorAll('.order-row');
            const noTransactionMessage = document.getElementById('no-transaction-message');
            const orderListContainer = document.getElementById('order-list-container');

            function updateTabContent(selectedTab) {
                let hasVisibleOrders = false;
                let visibleRows = [];

                orderRows.forEach(row => {
                    // Extract status directly from the class list
                    const statusClass = Array.from(row.classList).find(cls => cls.endsWith('-tab'));
                    let orderStatus = '';
                    if (statusClass) {
                        orderStatus = statusClass.replace('-tab', '');
                        // Map 'accepted', 'in-progress', 'submitted' to 'pending' for filtering
                        if (['accepted', 'in-progress', 'submitted'].includes(orderStatus)) {
                            orderStatus = 'pending';
                        }
                    }

                    if (selectedTab === 'all' || selectedTab === orderStatus) {
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

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    const selectedTab = this.dataset.tab;
                    updateTabContent(selectedTab);
                });
            });

            updateTabContent(document.querySelector('.tab-button.active').dataset.tab);
        });
    </script>
    <style>
        .star-rating {
            cursor: pointer;
        }

        /* Use gold for selected/hovered stars for consistency with previous discussion, or use star-blue as defined in JS */
        .star-blue {
            color: gold !important;
        }

        .star-green {
            color: gold !important;
            /* This was for hover, if you want a distinct hover, change this */
        }
    </style>
@endsection
