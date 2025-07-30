@extends('Master.master-job-taker')

@section('content')
    <div class="container-fluid pembatas-x pembatas-y">
        {{-- Page Title --}}
        <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b-4 border-yellow-400 pb-2 inline-block">{{ __('history-job-taker.judul_halaman') }}
        </h1>
        <a href="{{ route('monthly.report') }}" class="btn btn-primary btn-lg mb-3 text-white" style="text-decoration: none;">
            {{ __('history-job-taker.tombol_laporan_bulanan') }}
        </a>

        <div class="flex flex-col md:flex-row justify-start items-start md:items-center">
            {{-- Desktop Tab Navigation --}}
            <div class="tabs-wrapper hidden md:flex">
                <div class="col tab-button active" data-tab="all">
                    {{ __('history-job-taker.tab_semua') }} ({{ $allOrders->count() }})
                </div>
                <div class="col tab-button" data-tab="pending">
                    {{ __('history-job-taker.tab_berlangsung') }} ({{ $pendingOrders->count() }})
                </div>
                <div class="col tab-button" data-tab="completed">
                    {{ __('history-job-taker.tab_selesai') }} ({{ $completedOrders->count() }})
                </div>
            <div class="col tab-button" data-tab="cancelled">
                    {{ __('history-job-taker.tab_dibatalkan') }} ({{ $cancelledOrders->count() }})
                </div>
            </div>

            {{-- Mobile Dropdown for Tab Selection --}}
            <div class="tabs-dropdown-wrapper md:hidden w-full mb-4">
                <select id="tab-select"
                    class="form-select w-full border rounded-lg py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:border-blue-500">
                    <option value="all" @if (request('tab') == 'all' || !request('tab')) selected @endif>{{ __('history-job-taker.tab_semua') }}
                        ({{ $allOrders->count() }})</option>
                    <option value="pending" @if (request('tab') == 'pending') selected @endif>{{ __('history-job-taker.tab_berlangsung') }}
                        ({{ $pendingOrders->count() }})</option>
                    <option value="completed" @if (request('tab') == 'completed') selected @endif>{{ __('history-job-taker.tab_selesai') }}
                        ({{ $completedOrders->count() }})</option>
                    <option value="cancelled" @if (request('tab') == 'cancelled') selected @endif>{{ __('history-job-taker.tab_dibatalkan') }}
                        ({{ $cancelledOrders->count() }})</option>
                </select>
            </div>
        </div>

        {{-- Main Order List Display Area --}}
        <div class="w-full bg-white rounded-lg">
            {{-- Table Header Row for Order Details --}}
            <div class="row text-center tableHeader d-flex align-items-center justify-content-center fw-semibold m-0 p-0 text-xs md:text-base"
                style="height: 4rem;">
                {{-- Added ps-3 for left padding on the header column --}}
                <div class="col m-0 p-0 text-start ps-3">{{ __('history-job-taker.header_judul') }}</div>
                <div class="col m-0 p-0">{{ __('history-job-taker.header_status') }}</div>
                <div class="col m-0 p-0">{{ __('history-job-taker.header_tgl_selesai') }}</div>
                <div class="col m-0 p-0">{{ __('history-job-taker.header_klien') }}</div>
                <div class="col m-0 p-0">{{ __('history-job-taker.header_lokasi') }}</div>
                <div class="col m-0 p-0">{{ __('history-job-taker.header_upah') }}</div>
            </div>
            <hr class="mx-auto border-2 opacity-100 my-0 p-0" style="width: 98%; border-color: #294287;">

            {{-- Loop to Display Individual Order Rows --}}
            <div id="order-list-container" class="order-list-fade-in">
                @forelse ($allOrders as $order)
                    <div class="order-row hoverable-row"
                       data-status="{{ $order->status }}"
                        {{-- Determine whether to open modal or redirect based on status and user role --}}
                        @if ($order->status == 'completed') data-bs-toggle="modal"
                                data-bs-target="#completionModal"
                        @else
                            {{-- For requester, always redirect to on-going-work-request for non-completed statuses --}}
                            data-redirect-url="{{ route('job-taker.accepted-work-request', ['id' => $order->id]) }}" @endif
                        data-transaction-id="{{ $order->id }}" data-request-title="{{ $order->request->title ?? '-' }}"
                        data-order-number="{{ $order->order_number ?? '-' }}"
                        data-worker-first-name="{{ $order->worker->first_name ?? '' }}"
                        data-worker-last-name="{{ $order->worker->last_name ?? '' }}"
                        data-requester-first-name="{{ $order->requester->first_name ?? '' }}"
                        data-requester-last-name="{{ $order->requester->last_name ?? '' }}"
                        data-request-location="{{ $order->request->location ?? '-' }}"
                        data-transaction-created-at="{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') ?? '-' }}"
                        data-transaction-updated-at="{{ \Carbon\Carbon::parse($order->updated_at)->format('d M Y') ?? '-' }}"
                        data-request-price="{{ number_format($order->request->price ?? 0, 0, ',', '.') ?? '-' }}"
                        data-start-work="{{ $order->start_work ? \Carbon\Carbon::parse($order->start_work)->format('H.i') : '-' }}"
                        data-finish-work="{{ $order->finish_work ? \Carbon\Carbon::parse($order->finish_work)->format('H.i') : '-' }}"
                        data-requester-id="{{ $order->requester_id ?? '' }}"
                        data-order-status-text="{{ $order->status_text }}"
                        data-has-review="{{ $order->has_review ? 'true' : 'false' }}"
                        data-has-worker-report="{{ $order->has_worker_report ? 'true' : 'false' }}"
                        @if ($order->has_review && $order->received_review) data-received-review-rating="{{ $order->received_review->rating }}"
                                data-received-review-comment="{{ $order->received_review->comment }}" @endif
                        {{-- Pass decoded photo URLs if a report exists, using the new property from controller --}}
                        @if ($order->has_worker_report) data-worker-report-photo-urls="{{ json_encode($order->workerReport->decoded_photo_urls) }}"
                                data-worker-report-reasons="{{ $order->workerReport->reasons }}" @endif>
                        {{-- This inner row's height will now have a minimum height and content will be vertically centered --}}
                        <div class="row text-center text-xs d-flex justify-content-center align-items-center m-0 p-0"
                            style="min-height: 3.5rem;">
                            {{-- Added ps-3 for left padding on the title column, and added 'title-col' class for specific responsive styling --}}
                            <div class="col m-0 p-0 text-xxs text-start ps-3 title-col">
                                {{ $order->request->title ?? '-' }}</div>
                            <div class="col m-0 p-0">
                                {{-- Added status-badge-fixed for fixed width --}}
                                <span class="badge rounded-pill text-xxs status-badge-fixed"
                                    style="
                                        padding: .5em .9em;
                                        font-size: 0.85em;
                                        @if ($order->status == 'completed') background-color: #D3FA0D;
                                            color: #333;
                                        @elseif ($order->status == 'in progress')
                                            background-color: #309FFF;
                                            color: #FFF;
                                        @elseif($order->status == 'accepted' || $order->status == 'submitted')
                                            background-color: #294287;
                                            color: #FFF;
                                        @elseif($order->status == 'cancelled')
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
                                {{ $order->finish_work ? \Carbon\Carbon::parse($order->finish_work)->format('d - m - Y') : '-' }}
                            </div>
                            <div class="col m-0 p-0 text-xxs">{{ $order->requester->full_name ?? '-' }}</div>
                            <div class="col m-0 p-0 text-xxs">{{ $order->request->location ?? '-' }}</div>
                            <div class="col m-0 p-0 text-xxs">Rp
                                {{ number_format($order->request->price ?? 0, 0, ',', '.') ?? '-' }}</div>
                        </div>
                    </div>
                @empty
                    {{-- Message displayed when no orders are found --}}

                    <div id="no-transaction-message"
                        class="mt-3 mb-0 py-6 px-6 text-center text-gray-500 justify-content-center flex items-center w-full"
                        style="height: 3rem">
                        {{ __('history-job-taker.belum_ada_transaksi') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Completion and Review Modal --}}
    <div class="modal fade" id="completionModal" tabindex="-1" aria-labelledby="completionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 1000px; width: 100%; margin-top:5vh;">
            <form id="reviewForm" method="POST">
                @csrf
                <div class="modal-content p-3">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold fs-3" id="completionModalLabel">{{ __('history-job-taker.modal_detail_penyelesaian') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body overflow-auto overflow-lg-visible" style="max-height: 90vh;">
                        <div class="d-flex flex-column flex-lg-row gap-3">
                            {{-- Order details display section within the modal --}}
                            <div class="d-flex flex-column flex-grow-1">
                                <div class="d-flex flex-fill">
                                    <div class="text flex-fill" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">{{ __('history-job-taker.modal_judul_pesanan') }}</p>
                                        <p class="fw-medium" id="modalRequestTitle"></p>
                                    </div>
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">{{ __('history-job-taker.modal_nomor_pesanan') }}</p>
                                        <p class="fw-medium" id="modalOrderNumber"></p>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">{{ __('history-job-taker.modal_nama_klien') }}</p>
                                        <p class="fw-medium" id="modalRequesterName"></p>
                                    </div>
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">{{ __('history-job-taker.modal_lokasi') }}</p>
                                        <p class="fw-medium" id="modalRequestLocation"></p>
                                    </div>
                                </div>
                                <div class="d-flex flex-fill">
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">{{ __('history-job-taker.modal_tgl_pesan') }}</p>
                                        <p class="fw-medium" id="modalTransactionCreatedAt"></p>
                                    </div>
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">{{ __('history-job-taker.modal_tgl_selesai') }}</p>
                                        <p class="fw-medium" id="modalTransactionUpdatedAt"></p>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">{{ __('history-job-taker.modal_mulai_kerja') }}</p>
                                        <p class="fw-medium" id="modalStartWork"></p>
                                    </div>
                                    <div class="text" style="width:50%;">
                                        <p class="m-0 p-0 text-black-50 fw-semibold">{{ __('history-job-taker.modal_selesai_kerja') }}</p>
                                        <p class="fw-medium" id="modalFinishWork"></p>
                                    </div>
                                </div>
                                <hr class="my-1 border border-dark">
                                <div class="d-flex mt-1">
                                    <div class="text d-flex justify-content-between align-items-center"
                                        style="width:50%;">
                                        <p class="p-0 m-0 text-black-50 fw-semibold fs-6">{{ __('history-job-taker.modal_total') }}</p>
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
                                                    fill="#294287" />
                                            </svg>
                                            <div class="ms-2 fw-medium fs-5">{{ __('history-job-taker.modal_invoice') }}</div>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="vr d-none d-lg-block mx-3"></div>

                            {{-- Section for User Review and Report --}}
                            <div class="d-flex flex-column align-items-center justify-content-center flex-grow-1">
                                {{-- The heading will now be dynamic --}}
                                <h4 class="fw-semibold mt-3 mb-1" id="reviewSectionHeading"></h4>

                                {{-- Container for existing review or review form --}}
                                <div id="review-section-container" class="w-100">
                                    {{-- This content will be dynamically populated by JavaScript --}}
                                </div>

                                {{-- Action buttons for review submission and reporting --}}
                                <div class="d-flex flex-column mt-3 justify-content-center">
                                    <button type="submit" class="btn btn-primary fw-medium rounded-3"
                                        id="submitReviewButton">{{ __('history-job-taker.tombol_kirim') }}</button>
                                    <div class="m-1 text-center">{{ __('history-job-taker.atau') }}</div>
                                    <button type="button" class="m-0 p-0 fw-medium btn text-danger"
                                        onclick="openReportModal()" id="reportProblemButton">{{ __('history-job-taker.laporkan_masalah') }}</button>
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
            <div class="modal-content">
                <form id="reportForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    {{-- Hidden inputs for report submission data --}}
                    <input type="hidden" name="transaction_id" id="reportTransactionId">
                    <input type="hidden" name="reporter_id" value="{{ auth()->id() }}">
                    <input type="hidden" name="reported_id" id="reportReportedId">

                    <div class="modal-header border-0 justify-content-center">
                        <h3 class="modal-title fw-bold text-center w-100" id="reportWorkModalLabel">{{ __('history-job-taker.modal_laporan_judul') }}</h3>
                        <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <hr class="mx-auto mb-3" style="width: 50px; height: 4px; background-color: #294287; border: none;">

                    <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">
                        <div class="row mb-3">
                            <div class="col-md-6 col-lg-3">
                                <p class="text-black-50 fw-semibold mb-0">{{ __('history-job-taker.modal_judul_pesanan') }}</p>
                                <p class="fw-medium" id="reportModalRequestTitle"></p>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <p class="text-black-50 fw-semibold mb-0">{{ __('history-job-taker.modal_nomor_pesanan') }}</p>
                                <p class="fw-medium" id="reportModalOrderNumber"></p>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <p class="text-black-50 fw-semibold mb-0">{{ __('history-job-taker.modal_nama_klien') }}</p>
                                <p class="fw-medium" id="reportModalRequesterName"></p>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <p class="text-black-50 fw-semibold mb-0">{{ __('history-job-taker.modal_lokasi') }}</p>
                                <p class="fw-medium" id="reportModalRequestLocation"></p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 col-lg-3">
                                <p class="text-black-50 fw-semibold mb-0">{{ __('history-job-taker.modal_tgl_pesan') }}</p>
                                <p class="fw-medium" id="reportModalTransactionCreatedAt"></p>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <p class="text-black-50 fw-semibold mb-0">{{ __('history-job-taker.modal_tgl_selesai') }}</p>
                                <p class="fw-medium" id="reportModalTransactionUpdatedAt"></p>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <p class="text-black-50 fw-semibold mb-0">{{ __('history-job-taker.modal_mulai_kerja') }}</p>
                                <p class="fw-medium" id="reportModalStartWork"></p>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <p class="text-black-50 fw-semibold mb-0">{{ __('history-job-taker.modal_selesai_kerja') }}</p>
                                <p class="fw-medium" id="reportModalFinishWork"></p>
                            </div>
                        </div>

                        {{-- Total price display in report modal --}}
                        <div class="d-flex justify-content-between mb-3">
                            <p class="text-black-50 fw-semibold mb-0">{{ __('history-job-taker.modal_total') }}</p>
                            <p class="fw-medium fs-5 mb-0">Rp <span id="reportModalRequestPrice"></span></p>
                        </div>

                        {{-- Image upload section for report proof --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('history-job-taker.modal_laporan_upload_bukti') }}</label>
                            <div class="d-flex flex-wrap gap-3 align-items-start" id="reportImagePreviewContainer">
                                {{-- Images will be appended here dynamically by JS --}}
                                <div class="add-image-button pb-2"
                                    onclick="document.getElementById('reportImageInput').click()"
                                    style="width: 80px; height: 80px; border: 2px dashed #294287; background-color: #f7f7ff; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                    <span class="text-center" style="font-size: 32px; color:#294287;">+</span>
                                </div>
                                <span id="image-count"
                                    class="text-secondary fw-medium align-self-center ms-auto">0/7</span>
                            </div>
                            <input type="file" class="d-none" id="reportImageInput" name="photo[]" accept="image/*"
                                multiple>
                        </div>

                        {{-- Textarea for reporting reasons --}}
                        <div class="mb-3">
                            <label for="reportNote" class="form-label fw-semibold">{{ __('history-job-taker.modal_laporan_keluh_kesah') }}</label>
                            <textarea name="reasons" id="reportNote" class="form-control rounded-4" rows="4"
                                placeholder="Ceritakan masalah yang Anda alami..." style="background-color: #f7f7ff; resize: none;"></textarea>
                        </div>

                        {{-- Report submission button --}}
                        <div class="modal-footer border-0 d-flex justify-content-end p-0 m-0">
                            <button type="button" id="submitReportButton" class="btn btn-danger px-4 py-2">{{ __('history-job-taker.tombol_kirim_laporan') }}</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        // Global variables for managing transaction and requester IDs across modals
        let currentTransactionId = null;
        let reportedRequesterId = null; // Changed from reportedWorkerId
        // Store uploaded files globally for the report modal
        let reportFiles = []; // Array of File objects
        const MAX_REPORT_IMAGES = 7; // Define max images constant

        // --- Image Preview Logic for Report Modal ---
        const reportImageInput = document.getElementById('reportImageInput');
        const reportImagePreviewContainer = document.getElementById('reportImagePreviewContainer');
        const addImageButton = reportImagePreviewContainer.querySelector('.add-image-button');
        const imageCountSpan = document.getElementById('image-count');

        /**
         * Updates the display of uploaded report images and the image count.
         * Newest image appears to the left of the '+' button.
         * This function handles both newly added images and pre-existing images from a report.
         */
        function updateReportImagePreview() {
            // Remove all current image preview wrappers to re-render them
            reportImagePreviewContainer.querySelectorAll('.image-preview-wrapper').forEach(el => el.remove());

            // Iterate in reverse to add new images to the left, or in order for existing,
            // and correctly place them relative to the addImageButton
            reportFiles.forEach((fileOrUrl, index) => {
                const isFileObject = fileOrUrl instanceof File;
                const src = isFileObject ? URL.createObjectURL(fileOrUrl) : fileOrUrl;

                const wrapper = document.createElement('div');
                wrapper.className = 'image-preview-wrapper position-relative';
                wrapper.style.width = '80px';
                wrapper.style.height = '80px';

                const img = document.createElement('img');
                img.src = src;
                img.className = 'rounded border img-thumbnail';
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'cover';

                const deleteButton = document.createElement('button');
                deleteButton.className =
                    'btn-close position-absolute top-0 end-0 m-1'; // Removed btn-close-white
                deleteButton.style.fontSize = '0.7rem';
                deleteButton.style.backgroundColor = '#dc3545';
                deleteButton.style.borderRadius = '50%';
                deleteButton.style.padding = '0.25em';
                deleteButton.type = 'button';
                deleteButton.onclick = function() {
                    deleteReportImage(index); // Use captured index for correct deletion
                };

                wrapper.appendChild(img);
                wrapper.appendChild(deleteButton);

                // Insert the new image wrapper right before the 'add-image-button'
                // This ensures new images stack to the right, and the '+' button remains at the end
                reportImagePreviewContainer.insertBefore(wrapper, addImageButton);
            });

            // Update image count and add button visibility
            imageCountSpan.textContent = `${reportFiles.length}/${MAX_REPORT_IMAGES}`;
            if (reportFiles.length >= MAX_REPORT_IMAGES) {
                addImageButton.style.display = 'none'; // Hide the add button
            } else {
                addImageButton.style.display = 'flex'; // Show the add button
            }
        }

        /**
         * Deletes an image from the reportFiles array at the specified index and updates the preview.
         * @param {number} index - The index of the image to delete.
         */
        function deleteReportImage(index) {
            reportFiles.splice(index, 1); // Remove the file from the array
            updateReportImagePreview(); // Re-render previews to reflect deletion and update indices
        }

        reportImageInput.addEventListener('change', function(event) {
            // Iterate over selected files and apply validation
            Array.from(event.target.files).forEach(file => {
                // Client-side validation for file type
                if (!file.type.startsWith('image/')) {
                    window.showCustomAlert('File yang diunggah harus berupa gambar.', 'error');
                    return; // Skip this file and continue to next
                }
                // Client-side validation for file size
                const maxSizeBytes = 5 * 1024 * 1024; // 5 MB
                if (file.size > maxSizeBytes) {
                    window.showCustomAlert('Ukuran foto bukti laporan maksimal 5 MB.', 'error');
                    return; // Skip this file and continue to next
                }

                // Add file to array if max limit not reached
                if (reportFiles.length < MAX_REPORT_IMAGES) {
                    reportFiles.push(file);
                } else {
                    window.showCustomAlert(
                        `Maksimal ${MAX_REPORT_IMAGES} foto bukti laporan dapat diunggah.`, 'error');
                    // Stop processing further files if limit is hit
                    return;
                }
            });
            event.target.value = ''; // Clear the input value to allow re-selection of same files
            updateReportImagePreview(); // Update the visual display
        });

        // --- Function to Open Report Modal ---
        function openReportModal() {
            // Close the completion modal before opening the report modal
            var completionModal = bootstrap.Modal.getInstance(document.getElementById('completionModal'));
            if (completionModal) {
                completionModal.hide();
            }

            // Populate report modal fields with data from the selected order
            const reportModal = new bootstrap.Modal(document.getElementById('reportWorkModal'));
            const rowData = document.querySelector(`.order-row[data-transaction-id="${currentTransactionId}"]`);

            if (rowData) {
                document.getElementById('reportModalRequestTitle').textContent = rowData.dataset.requestTitle;
                document.getElementById('reportModalOrderNumber').textContent = rowData.dataset.orderNumber;
                document.getElementById('reportModalRequesterName').textContent =
                    `${rowData.dataset.requesterFirstName} ${rowData.dataset.requesterLastName}`; // Changed to requester name
                document.getElementById('reportModalRequestLocation').textContent = rowData.dataset.requestLocation;
                document.getElementById('reportModalTransactionCreatedAt').textContent = rowData.dataset
                    .transactionCreatedAt;
                document.getElementById('reportModalTransactionUpdatedAt').textContent = rowData.dataset
                    .transactionUpdatedAt;
                document.getElementById('reportModalRequestPrice').textContent = rowData.dataset.requestPrice;
                document.getElementById('reportModalStartWork').textContent = rowData.dataset.startWork;
                document.getElementById('reportModalFinishWork').textContent = rowData.dataset.finishWork;

                // Set hidden form fields for submission
                document.getElementById('reportTransactionId').value = currentTransactionId;
                document.getElementById('reportReportedId').value = reportedRequesterId; // Changed to reportedRequesterId

                // Set form action for report submission
                document.getElementById('reportForm').action =
                    `/job-taker/submit-report/${currentTransactionId}`; // Correct route for worker reporting

                // Always reset for new report (allow multiple reports)
                reportFiles = [];
                document.getElementById('reportNote').value = '';
                document.getElementById('reportNote').disabled = false;
                reportImageInput.disabled = false;
                document.getElementById('submitReportButton').style.display = 'block'; // Show submit button
                addImageButton.style.display = 'flex'; // Show add image button

                updateReportImagePreview(); // Render initial state (empty for new report)
            }

            // Show the report modal after a brief delay
            setTimeout(() => {
                reportModal.show();
            }, 300);
        }

        // --- Star Rating Functionality for Review Modal ---
        // Updates the visual fill of the stars
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


        // --- Function to Submit Review ---
        function submitReview() {
            const comment = document.getElementById('comment').value.trim();
            const rating = document.getElementById('rating-input').value;

            // Client-side validation for rating and comment
            if (rating == 0) {
                window.showCustomAlert('Silakan pilih rating terlebih dahulu.', 'error');
                return;
            }

            if (comment == '') {
                window.showCustomAlert('Silakan isi komentar.', 'error');
                return;
            }

            // Prepare FormData for submission
            const formData = new FormData();
            formData.append('transaction_id', currentTransactionId);
            formData.append('reviewer_id', `{{ auth()->id() }}`);
            formData.append('reviewee_id', reportedRequesterId); // Reviewing the requester
            formData.append('rating', rating);
            formData.append('comment', comment);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            const submitBtn = document.getElementById('submitReviewButton');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Mengirim...';
            }

            fetch(document.getElementById('reviewForm').action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw errorData;
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        localStorage.setItem('reviewSuccessMessage', data.message); // Store success message
                        // Close the completion modal (optional, but good UX before reload)
                        const completionModal = bootstrap.Modal.getInstance(document.getElementById('completionModal'));
                        if (completionModal) completionModal.hide();
                        location.reload(); // Reload the page to display the alert
                    } else {
                        let errorMessage = data.message || 'Terjadi kesalahan saat menyimpan ulasan.';
                        if (data.errors) {
                            errorMessage = 'Validasi gagal:';
                            for (const key in data.errors) {
                                if (data.errors.hasOwnProperty(key)) {
                                    data.errors[key].forEach(msg => {
                                        errorMessage += `\n- ${msg}`;
                                    });
                                }
                            }
                        }
                        window.showCustomAlert(errorMessage, "error");
                    }
                })
                .catch(error => {
                    console.error('Error submitting review:', error);
                    let errorMessage = 'Terjadi kesalahan saat menyimpan ulasan.';
                    if (error.message) {
                        errorMessage = error.message;
                    } else if (error.errors) { // Handle Laravel validation errors
                        errorMessage = 'Validasi gagal:';
                        for (const key in error.errors) {
                            errorMessage += `\n- ${error.errors[key].join(', ')}`;
                        }
                    }
                    window.showCustomAlert(errorMessage, "error");
                    // NO RELOAD ON ERROR
                })
                .finally(() => {
                    const submitBtn = document.getElementById('submitReviewButton');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Kirim';
                    }
                });
        }

        // --- Function to Submit Report ---
        function submitReport(event) {
            if (event) event.preventDefault(); // Prevent default form submission to handle manually

            const form = document.getElementById('reportForm');
            const reasons = document.getElementById('reportNote').value.trim();

            if (reportFiles.length === 0) {
                window.showCustomAlert("Silakan upload minimal satu foto bukti laporan.", 'error');
                return;
            }
            if (!reasons) {
                window.showCustomAlert('Harap isi keluh kesah Anda terlebih dahulu.', 'error');
                return;
            }

            // Re-validate files in reportFiles array before submission, as user might delete/add
            let hasInvalidFile = false;
            // Filter out existing URLs and only send File objects
            const filesToSend = reportFiles.filter(item => item instanceof File);

            for (const file of filesToSend) { // Use for...of for easy breaking
                if (!file.type.startsWith('image/')) {
                    window.showCustomAlert('File yang diunggah harus berupa gambar.', 'error');
                    hasInvalidFile = true;
                    break; // Exit loop
                }
                const maxSizeBytes = 5 * 1024 * 1024; // 5 MB
                if (file.size > maxSizeBytes) {
                    window.showCustomAlert('Ukuran foto bukti laporan maksimal 5 MB.', 'error');
                    hasInvalidFile = true;
                    break; // Exit loop
                }
            }
            if (hasInvalidFile) {
                return;
            }

            const formData = new FormData(); // Create new FormData object for submission
            // Append static form fields
            formData.append('transaction_id', document.getElementById('reportTransactionId').value);
            formData.append('reporter_id', document.getElementById('reportForm').querySelector('input[name="reporter_id"]')
                .value);
            formData.append('reported_id', document.getElementById('reportReportedId').value);
            formData.append('reasons', reasons);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute(
                'content')); // Manually add CSRF token

            // Append all collected files from our `reportFiles` array
            filesToSend.forEach((file, index) => {
                formData.append(`photo[${index}]`, file);
            });

            const submitBtn = document.getElementById('submitReportButton');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Mengirim Laporan...';

            // Submit the form using fetch, expecting a redirect
            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json', // Expect JSON response
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw errorData;
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        localStorage.setItem('reportSuccessMessage', data.message); // Store success message
                        const reportModal = bootstrap.Modal.getInstance(document.getElementById('reportWorkModal'));
                        if (reportModal) reportModal.hide();
                        location.reload(); // Reload the page to display the alert
                    } else {
                        window.showCustomAlert(data.message || 'Terjadi kesalahan saat mengirim laporan.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error during report submission:', error);
                    let errorMessage = 'Terjadi kesalahan saat mengirim laporan.\n';
                    if (error.errors) {
                        errorMessage += 'Validasi Gagal:\n';
                        for (let key in error.errors) {
                            errorMessage += `- ${error.errors[key].join(', ')}\n`;
                        }
                    } else if (error.message) {
                        errorMessage += error.message;
                    }
                    window.showCustomAlert(errorMessage, 'error');
                    // NO RELOAD ON ERROR
                })
                .finally(() => {
                    const submitBtn = document.getElementById('submitReportButton');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Kirim Laporan';
                    }
                });
        }


        // Handles click event for generating and downloading invoice
        function handleInvoiceLinkClick(e) {
            e.preventDefault();
            const transactionId = this.getAttribute('data-transaction-id');
            if (transactionId) {
                const invoiceUrl = `/generate-invoice/${transactionId}`;
                window.open(invoiceUrl, '_blank');
            } else {
                console.error('Transaction ID not found for invoice generation.');
                window.showCustomAlert('Terjadi kesalahan: ID transaksi tidak ditemukan untuk pembuatan invoice.', 'error');
            }
        }


        document.addEventListener('DOMContentLoaded', function() {
            // No longer checking URL parameters for success messages, Laravel's session will handle it.
            // The global alert display in master-job-req.blade.php handles it.
            const reviewSuccessMessage = localStorage.getItem('reviewSuccessMessage');
            if (reviewSuccessMessage) {
                window.showCustomAlert(reviewSuccessMessage, 'success');
                localStorage.removeItem('reviewSuccessMessage'); // Clear the message
            }

            const reportSuccessMessage = localStorage.getItem('reportSuccessMessage');
            if (reportSuccessMessage) {
                window.showCustomAlert(reportSuccessMessage, 'success');
                localStorage.removeItem('reportSuccessMessage'); // Clear the message
            }

            const orderRows = document.querySelectorAll('.order-row');
            const submitReviewButton = document.getElementById('submitReviewButton');
            const reviewSectionContainer = document.getElementById('review-section-container');
            const reviewSectionHeading = document.getElementById('reviewSectionHeading');
            const reportProblemButton = document.getElementById('reportProblemButton');
            const submitReportButtonForReportModal = document.getElementById('submitReportButton');


            let selectedRating = 0; // Local variable for selected rating within current modal view

            // Function to render the review form
            function renderReviewForm() {
                reviewSectionHeading.textContent = 'Kasih penilaian, yuk!';
                reviewSectionContainer.innerHTML = `
            <div class="text-center mt-0 mb-3 w-100">
                @for ($i = 1; $i <= 5; $i++)
                    <i class="bi bi-star-fill text-secondary star-rating fs-2" data-value="{{ $i }}"></i>
                @endfor
                <input type="hidden" name="rating" id="rating-input" value="0">
            </div>
            <div class="ps-3 flex-fill d-flex flex-column w-100">
                <label for="comment" class="form-label text-start">Komentar</label>
                <textarea name="comment" id="comment" class="form-control" rows="3"
                    placeholder="Tulis komentarmu di sini..." style="border-color:#8a8a8a;  resize: none;"></textarea>
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
                        updateStarDisplay(
                            selectedRating);
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

            // Function to render the existing review display
            function renderExistingReview(rating, comment) {
                reviewSectionHeading.textContent = 'Ini penilaianmu';
                let starHtml = '';
                for (let i = 1; i <= 5; i++) {
                    starHtml +=
                        `<i class="bi bi-star-fill fs-2 ${i <= rating ? 'star-blue' : 'text-secondary'} star-animate"></i>`;
                }

                reviewSectionContainer.innerHTML = `
            <div class="text-center mt-0 mb-3 w-100">
                ${starHtml}
            </div>
            <div class="ps-3 flex-fill d-flex flex-column w-100">
                <label for="comment" class="form-label text-start">Komentar</label>
                <textarea id="comment" class="form-control" rows="3" disabled
                    style="border-color:#8a8a8a; resize: none;">${comment}</textarea>
            </div>
        `;
                if (submitReviewButton) {
                    submitReviewButton.style.display = 'none';
                }
            }


            // Add click listener to each order row to handle redirection or modal display
            orderRows.forEach(row => {
                row.addEventListener('click', function(event) {
                    event.preventDefault();

                    const transactionId = this.getAttribute('data-transaction-id');
                    const orderStatusText = this.getAttribute('data-order-status-text');
                    const hasReview = this.getAttribute('data-has-review') === 'true';
                    const receivedReviewRating = this.getAttribute('data-received-review-rating');
                    const receivedReviewComment = this.getAttribute('data-received-review-comment');
                    const hasWorkerReport = this.getAttribute('data-has-worker-report') === 'true';
                    const orderStatus = row.dataset.status;
                    // Set global variables for use in modals
                    currentTransactionId = transactionId;
                    reportedRequesterId = this.getAttribute(
                        'data-requester-id'); // Changed to requester ID

                    if (orderStatus === 'completed') {
                        const completionModal = new bootstrap.Modal(document.getElementById(
                            'completionModal'));
                        completionModal.show();

                        // Populate the completion modal with data
                        document.getElementById('modalRequestTitle').textContent = this.dataset
                            .requestTitle;
                        document.getElementById('modalOrderNumber').textContent = this.dataset
                            .orderNumber;
                        document.getElementById('modalRequesterName')
                            .textContent = // Changed to requester name
                            `${this.dataset.requesterFirstName} ${this.dataset.requesterLastName}`;
                        document.getElementById('modalRequestLocation').textContent = this.dataset
                            .requestLocation;
                        document.getElementById('modalTransactionCreatedAt').textContent = this
                            .dataset
                            .transactionCreatedAt;
                        document.getElementById('modalTransactionUpdatedAt').textContent = this
                            .dataset
                            .transactionUpdatedAt;
                        document.getElementById('modalRequestPrice').textContent = this.dataset
                            .requestPrice;
                        document.getElementById('modalStartWork').textContent = this.dataset
                            .startWork;
                        document.getElementById('modalFinishWork').textContent = this.dataset
                            .finishWork;

                        // Set form actions dynamically for the completion modal
                        document.getElementById('reviewForm').action =
                            `/reviews`; // Corrected route


                        // Conditional rendering of review section
                        if (hasReview) {
                            renderExistingReview(receivedReviewRating, receivedReviewComment);
                            // Set selectedRating to existing review
                            selectedRating = parseInt(receivedReviewRating);
                        } else {
                            renderReviewForm();
                            selectedRating = 0; // Reset selectedRating for new review
                        }

                        // --- Handle Report Button State ---
                        if (reportProblemButton) {
                            // Always enable report button and reset its text
                            reportProblemButton.textContent = 'Laporkan masalah';
                            reportProblemButton.disabled = false;
                            reportProblemButton.classList.remove('text-secondary');
                            reportProblemButton.classList.add('text-danger');
                            reportProblemButton.onclick =
                                openReportModal; // Re-attach click listener
                        }

                        // Setup invoice link
                        const invoiceLink = document.getElementById('modalInvoiceLink');
                        if (invoiceLink) {
                            invoiceLink.setAttribute('data-transaction-id', transactionId);
                            invoiceLink.removeEventListener('click', handleInvoiceLinkClick);
                            invoiceLink.addEventListener('click', handleInvoiceLinkClick);
                        }


                    } else if (['in progress', 'accepted', 'submitted'].includes(
                            orderStatus)) {
                        // For these specific statuses, redirect to the ongoing request page
                        window.location.href = `/job-taker/accepted-work-request/${transactionId}`;
                    } else {
                        console.log('Clicked on a row with status:', orderStatus,
                            'No specific action defined.');
                    }
                });
            });

            // Attach submitReport to the "Kirim Laporan" button inside the report modal
            if (submitReportButtonForReportModal) {
                submitReportButtonForReportModal.addEventListener('click', submitReport);
            }

            // Attach submitReview to its button
            if (submitReviewButton) {
                submitReviewButton.addEventListener('click', submitReview);
            }


            // Reset review form state when the completion modal is hidden
            const completionModalElement = document.getElementById('completionModal');
            completionModalElement.addEventListener('hidden.bs.modal', function() {
                selectedRating = 0; // Reset selected rating
                // The HTML content of reviewSectionContainer is rebuilt on modal open, so no need to clear its elements here.
            });

            // Clear report form state and re-enable button when the report modal is hidden
            const reportWorkModalElement = document.getElementById('reportWorkModal');
            reportWorkModalElement.addEventListener('hidden.bs.modal', function() {
                document.getElementById('reportNote').value = ''; // Clear textarea
                reportFiles = []; // Clear the global array of files
                updateReportImagePreview
                    (); // Reset preview container (removes all image wrappers and re-adds placeholder)
                reportImageInput.value = ''; // Clear file input (important for re-selecting same files)
                document.getElementById('submitReportButton').disabled = false;
                document.getElementById('submitReportButton').textContent = 'Kirim Laporan';
            });


            // --- Tab and Dropdown Filtering Functionality ---
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabSelect = document.getElementById('tab-select');
            const orderRowsForTabs = document.querySelectorAll('.order-row');
            const noTransactionMessage = document.getElementById('no-transaction-message');
            const orderListContainer = document.getElementById('order-list-container');

            // Filters and displays order rows based on the selected tab/dropdown option
            function updateTabContent(selectedTab) {
                let hasVisibleOrders = false;
                let visibleRows = [];

                // Remove existing fade-in class to re-trigger animation
                orderListContainer.classList.remove('order-list-fade-in');
                void orderListContainer.offsetWidth; // Trigger reflow
                orderListContainer.classList.add('order-list-fade-in');


                orderRowsForTabs.forEach(row => {
                    // Extract status from the badge text content
                    const badgeElement = row.querySelector('.badge');
                    const orderStatus = row.dataset.status;

                    let orderStatusForTab = '';
                    if (orderStatus == 'completed') {
                        orderStatusForTab = 'completed';
                    } else if (['in progress', 'accepted', 'submitted'].includes(orderStatus)) {
                        orderStatusForTab = 'pending';
                    } else if (orderStatus == 'cancelled') {
                        orderStatusForTab = 'cancelled';
                    } else {
                        orderStatusForTab = 'other'; // Fallback for other statuses
                    }

                    if (selectedTab === 'all' || selectedTab === orderStatusForTab) {
                        row.style.display = '';
                        hasVisibleOrders = true;
                        visibleRows.push(row);
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Remove existing separators between rows
                const existingHrs = orderListContainer.querySelectorAll('.order-divider');
                existingHrs.forEach(hr => hr.remove());

                // Add separators only between currently visible rows
                for (let i = 0; i < visibleRows.length - 1; i++) {
                    const hr = document.createElement('hr');
                    hr.classList.add('mx-auto', 'border-1', 'opacity-100', 'my-0', 'p-0', 'order-divider');
                    hr.style.cssText = 'width: 98%; border-color: #294287;';
                    visibleRows[i].parentNode.insertBefore(hr, visibleRows[i].nextSibling);
                }

                // Toggle 'No Transaction' message visibility
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
                    // Update dropdown border color
                    this.style.borderColor = '#bfff00';
                    this.style.borderWidth = '2px';
                });

                // Initialize dropdown border style on page load
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

            // Initial load of tab content based on current screen size
            if (window.innerWidth < 768 && tabSelect) {
                updateTabContent(tabSelect.value);
            } else if (document.querySelector('.tab-button.active')) {
                updateTabContent(document.querySelector('.tab-button.active').dataset.tab);
            }

            // === FIX FOR PERSISTENT OVERLAY ===
            // Listen for any Bootstrap modal to be hidden and manually remove any remaining backdrops.
            document.querySelectorAll('.modal').forEach(modalElement => {
                modalElement.addEventListener('hidden.bs.modal', function() {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());

                    // Also ensure body scrolling is re-enabled, as it sometimes gets stuck
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    // Clear any padding added by Bootstrap for scrollbar
                    document.body.style.paddingRight = '';
                });
            });
            // === END FIX ===
        });
    </script>

    <style>
        /* Star Rating Styles */
        .star-rating,
        .star-animate {
            cursor: pointer;
            transition: color 0.2s ease-in-out;
            margin-right: 0.1em;
            /* Consistent right margin */
            margin-left: 0.1em;
            /* Consistent left margin */
            display: inline-block;
            /* Ensure margins are respected */
        }

        .star-rating:last-of-type,
        .star-animate:last-of-type {
            margin-right: 0;
            /* No right margin for the last star */
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
            /* Adjust this value as needed based on your longest status text */
            text-align: center;
            display: inline-flex;
            /* Use flexbox to center content vertically and horizontally */
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
                /* Default small padding */
                padding-right: 0.3rem;
                /* Default small padding */
                white-space: normal !important;
            }

            /* Adjusted padding for the Judul column header on mobile */
            .tableHeader .col:first-child {
                padding-left: 0.8rem !important;
            }

            /* Adjusted padding for the Judul column in order rows on mobile */
            .order-row .title-col {
                padding-left: 0.8rem !important;
            }

            .order-row .badge {
                font-size: 12px !important;
                padding: .2em .4em !important;
            }

            .order-row .status-badge-fixed {
                min-width: 70px;
                /* Smaller width for mobile */
                text-align: center;
                display: inline-flex;
                /* Use flexbox to center content vertically and horizontally */
                align-items: center;
                justify-content: center;
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
                /* Even smaller width for very small screens */
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
                padding-left: 1rem;
                /* Corresponds to Bootstrap's ps-3 */
            }

            .order-row .col {
                font-size: 14px;
            }

            /* Default padding for desktop Judul column in order rows */
            .order-row .title-col {
                padding-left: 1rem;
                /* Corresponds to Bootstrap's ps-3 */
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
