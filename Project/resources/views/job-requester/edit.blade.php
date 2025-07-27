@extends('master.master-job-req')

@section('content')
    <div class="container-fluid pembatas-x pembatas-y">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-2">Sunting tawaran kerja</h2>
                <p class="mb-4">Perbarui detail pekerjaan di bawah ini jika ada perubahan.</p>

                <form action="{{ route('request.update', $workRequest->slug) }}" method="POST" id="edit-work-form">
                    @csrf
                    @method('PUT')

                    {{-- Judul Pekerjaan --}}
                    <div class="mb-3">
                        <label for="work-title-text" class="form-label fw-semibold">Judul Pekerjaan</label>
                        <input type="text" class="form-control rounded-3" id="work-title-text" name="workTitleLabel"
                            value="{{ old('workTitleLabel', $workRequest->title) }}">
                        <div class="text-danger small mt-1" id="workTitleLabel-error"></div>
                    </div>
                    {{-- Detail Pekerjaan --}}
                    <div class="mb-3">
                        <label for="work-detail-text" class="form-label fw-semibold">Detail Pekerjaan</label>
                        <textarea class="form-control rounded-3" id="work-detail-text" rows="3" name="workDetailLabel" style="background-color: #f7f7ff; resize: none;">{{ old('workDetailLabel', $workRequest->description) }}</textarea>
                        <div class="text-danger small mt-1" id="workDetailLabel-error"></div>
                    </div>
                    {{-- Alamat --}}
                    <div class="mb-3">
                        <label for="work-address-text" class="form-label fw-semibold">Alamat</label>
                        <input type="text" class="form-control rounded-3" id="work-address-text" name="workAddressLabel"
                            value="{{ old('workAddressLabel', $workRequest->location) }}">
                        <div class="text-danger small mt-1" id="workAddressLabel-error"></div>
                    </div>

                    {{-- ========================================================== --}}
                    {{-- BAGIAN WAKTU YANG DIPERBAIKI --}}
                    {{-- ========================================================== --}}
                    @php
                        // Ensure these are formatted correctly for the HTML input type="date" and type="time"
                        // These values are based on the workRequest's times, which are in UTC (from DB).
                        // So, format them directly as YYYY-MM-DD and HH:i to pre-fill inputs with UTC values.
                        $startDate = \Carbon\Carbon::parse($workRequest->start_time)->format('Y-m-d');
                        $startTime = \Carbon\Carbon::parse($workRequest->start_time)->format('H:i');
                        $endDate = \Carbon\Carbon::parse($workRequest->end_time)->format('Y-m-d');
                        $endTime = \Carbon\Carbon::parse($workRequest->end_time)->format('H:i');
                    @endphp
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Waktu mulai pekerjaan</label>
                            <div class="d-flex gap-2">
                                <div class="flex-fill">
                                    <input type="date" class="form-control rounded-3" name="workStartDateLabel"
                                        value="{{ old('workStartDateLabel', $startDate) }}"
                                        min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                    <div class="text-danger small mt-1" id="workStartDateLabel-error"></div>
                                </div>
                                <div class="flex-fill">
                                    <input type="time" class="form-control rounded-3" name="workStartTimeLabel"
                                        value="{{ old('workStartTimeLabel', $startTime) }}">
                                    <div class="text-danger small mt-1" id="workStartTimeLabel-error"></div>
                                    <div class="text-danger small mt-1" id="workStartTimeLabel-past-error"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <label class="form-label fw-semibold">Waktu selesai pekerjaan</label>
                            <div class="d-flex gap-2">
                                <div class="flex-fill">
                                    <input type="date" class="form-control rounded-3" name="workEndDateLabel"
                                        value="{{ old('workEndDateLabel', $endDate) }}">
                                    <div class="text-danger small mt-1" id="workEndDateLabel-error"></div>
                                </div>
                                <div class="flex-fill">
                                    <input type="time" class="form-control rounded-3" name="workEndTimeLabel"
                                        value="{{ old('workEndTimeLabel', $endTime) }}">
                                    <div class="text-danger small mt-1" id="workEndTimeLabel-error"></div>
                                    <div class="text-danger small mt-1" id="workEndTimeLabel-past-error"></div>
                                </div>
                            </div>
                        </div>
                        {{-- Tempat khusus untuk error perbandingan waktu --}}
                        <div class="col-12">
                            <div class="text-danger small mt-1" id="datetime-error"></div>
                        </div>
                    </div>

                    {{-- Harga --}}
                    <div class="mb-4">
                        <label for="work-price-text" class="form-label fw-semibold">Upah</label>
                        <div class="input-group">
                            <span class="input-group-text rounded-start-3">Rp</span>
                            <input type="number" class="form-control rounded-end-3" min="5000" id="work-price-text"
                                name="workPriceLabel" value="{{ old('workPriceLabel', (int) $workRequest->price) }}">
                        </div>
                        <div class="text-danger small mt-1" id="workPriceLabel-error"></div>
                    </div>

                    <button type="button" id="show-edit-confirmation-button"
                        class="btn btn-primary px-4 py-2 rounded-3">Simpan Perubahan</button>
                </form>
            </div>

            <div class="col-lg-6 text-center mt-5 mt-lg-0">
                <img src="{{ asset('Image/orang/Merah dan Pink Ilustrasi Mochi Logo (12) 1.png') }}" alt="Ilustrasi Orang"
                    class="img-fluid" style="max-height: 400px;">
            </div>
        </div>
    </div>
    <div class="modal fade" id="editConfirmationModal" tabindex="-1" aria-labelledby="editConfirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="editConfirmationModalLabel">Konfirmasi Perubahan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Harap konfirmasi perubahan biaya pekerjaan. Dana yang ditahan akan disesuaikan.</p>

                    <div class="mb-2 d-flex justify-content-between">
                        <span id="modal-cost-label">Biaya tambahan:</span>
                        <span class="fw-bold" id="modal-job-cost-diff">Rp0</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Saldo Anda saat ini:</span>
                        <span class="fw-bold" id="modal-user-balance">Rp0</span>
                    </div>

                    <div id="modal-warning-message" class="alert alert-danger mt-3" style="display: none;">
                        Saldo Anda tidak cukup untuk menutupi biaya tambahan.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary text-light" data-bs-dismiss="modal">Batal</button>
                    <a href="{{ route('top-up.job-req') }}" id="modal-topup-button" class="btn btn-success"
                        style="display: none;">Isi Saldo</a>
                    <button type="button" id="modal-confirm-edit-button" class="btn btn-primary">Ya, Konfirmasi</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('edit-work-form');
            const showConfirmationBtn = document.getElementById('show-edit-confirmation-button');
            const confirmationModal = new bootstrap.Modal(document.getElementById('editConfirmationModal'));

            const modalCostLabel = document.getElementById('modal-cost-label');
            const modalJobCostDiff = document.getElementById('modal-job-cost-diff');
            const modalUserBalance = document.getElementById('modal-user-balance');
            const modalWarning = document.getElementById('modal-warning-message');
            const modalConfirmBtn = document.getElementById('modal-confirm-edit-button');
            const modalTopupBtn = document.getElementById('modal-topup-button');

            // Date and Time inputs
            const workStartDateInput = document.querySelector('input[name="workStartDateLabel"]');
            const workEndDateInput = document.querySelector('input[name="workEndDateLabel"]');
            const workStartTimeInput = document.querySelector('input[name="workStartTimeLabel"]');
            const workEndTimeInput = document.querySelector('input[name="workEndTimeLabel"]');
            const datetimeErrorDiv = document.getElementById('datetime-error');
            const workStartTimeLabelPastErrorDiv = document.getElementById('workStartTimeLabel-past-error');
            const workEndTimeLabelPastErrorDiv = document.getElementById('workEndTimeLabel-past-error');


            const userBalance = parseFloat('{{ auth()->check() ? auth()->user()->balance : 0 }}');
            const originalPrice = parseFloat('{{ (int) $workRequest->price }}');

            const formatRupiah = (number) => {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(number);
            };

            // NEW: Helper function to get current UTC time (HH:MM format)
            function getFormattedCurrentTimeUTC() {
                const now = new Date();
                const hours = String(now.getUTCHours()).padStart(2, '0');
                const minutes = String(now.getUTCMinutes()).padStart(2, '0');
                return `${hours}:${minutes}`;
            }

            // NEW: Helper function to get current UTC date (YYYY-MM-DD format)
            function getFormattedCurrentDateUTC() {
                const now = new Date();
                const year = now.getUTCFullYear();
                const month = String(now.getUTCMonth() + 1).padStart(2, '0');
                const day = String(now.getUTCDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            function validateTimestamps() {
                // Clear all error messages before re-validation
                datetimeErrorDiv.textContent = '';
                workStartTimeLabelPastErrorDiv.textContent = '';
                workEndTimeLabelPastErrorDiv.textContent = '';

                const startDate = workStartDateInput.value;
                const startTime = workStartTimeInput.value;
                const endDate = workEndDateInput.value;
                const endTime = workEndTimeInput.value;

                // Only validate if all fields are filled to avoid premature errors
                if (!startDate || !startTime || !endDate || !endTime) {
                    return true; // Assume valid if not yet complete
                }

                // NEW: Parse input as UTC for client-side comparison
                const startDateTime = new Date(`${startDate}T${startTime}:00Z`);
                const endDateTime = new Date(`${endDate}T${endTime}:00Z`);
                // NEW: Get current UTC time for comparison
                const nowUTCComparison = new Date(`${getFormattedCurrentDateUTC()}T${getFormattedCurrentTimeUTC()}:00Z`);


                // 1. Check if start time is in the past (UTC comparison)
                // (Allow a small buffer like 1 minute to account for input delay)
                if (startDateTime < new Date(nowUTCComparison.getTime() - 60000)) {
                    datetimeErrorDiv.textContent = 'Waktu mulai pekerjaan tidak boleh di masa lalu (UTC).';
                    return false;
                }

                // 2. Check if end time is before or equal to start time (UTC comparison)
                if (endDateTime <= startDateTime) {
                    datetimeErrorDiv.textContent = 'Waktu selesai harus setelah waktu mulai.';
                    return false;
                }

                return true; // All validations passed
            }

            function updateDateTimeConstraints() {
                // NEW: Use current UTC date and time for constraints
                const todayUTC = getFormattedCurrentDateUTC();
                const currentTimeUTC = getFormattedCurrentTimeUTC();

                // Clear all client-side specific date/time errors before updating constraints and re-validating
                datetimeErrorDiv.textContent = '';
                workStartTimeLabelPastErrorDiv.textContent = '';
                workEndTimeLabelPastErrorDiv.textContent = '';

                let minEndDate = workStartDateInput.value; // Default minimum end date is the start date

                // Scenario: Start date and End date are currently the same
                // And both Start Time and End Time are provided.
                // If the End Time is earlier than or equal to Start Time on the same day,
                // then the End Date must be forced to at least the next day.
                if (workStartDateInput.value && workEndDateInput.value === workStartDateInput.value &&
                    workStartTimeInput.value && workEndTimeInput.value) {

                    // NEW: Parse as UTC for this specific proactive check
                    const tempStartDateTime = new Date(`${workStartDateInput.value}T${workStartTimeInput.value}:00Z`);
                    const tempEndDateTime = new Date(`${workEndDateInput.value}T${workEndTimeInput.value}:00Z`);

                    if (tempEndDateTime <= tempStartDateTime) {
                        const nextDay = new Date(tempStartDateTime);
                        nextDay.setUTCDate(tempStartDateTime.getUTCDate() + 1); // Use setUTCDate
                        minEndDate = nextDay.toISOString().split('T')[0]; // Format as YYYY-MM-DD
                    }
                }

                // Apply the determined minimum end date
                workEndDateInput.min = minEndDate || todayUTC; // Fallback to today UTC if minEndDate is somehow null/empty

                // If end date is set and is earlier than the newly calculated minEndDate, reset it to minEndDate
                if (workEndDateInput.value && workEndDateInput.value < workEndDateInput.min) {
                    workEndDateInput.value = workEndDateInput.min;
                }


                // Dynamic min for start time (relative to current UTC date/time)
                if (workStartDateInput.value === todayUTC) {
                    workStartTimeInput.min = currentTimeUTC;
                } else {
                    workStartTimeInput.min = ''; // No minimum time for future dates
                }

                // Dynamic min for end time (relative to current UTC date/time)
                if (workEndDateInput.value === todayUTC) {
                    if (workStartDateInput.value === todayUTC && workStartTimeInput.value) {
                        // If both start and end are today UTC, end time must be after start time UTC
                        workEndTimeInput.min = workStartTimeInput.value;
                    } else {
                        // If only end date is today UTC (and start date is a past day), end time can be current UTC time
                        workEndTimeInput.min = currentTimeUTC;
                    }
                } else {
                    workEndTimeInput.min = ''; // No minimum time for future dates
                }

                validateTimestamps(); // Re-validate on constraint changes
            }

            // Add event listeners for blur
            workStartDateInput.addEventListener('blur', updateDateTimeConstraints);
            workEndDateInput.addEventListener('blur', updateDateTimeConstraints);
            workStartTimeInput.addEventListener('blur', updateDateTimeConstraints);
            workEndTimeInput.addEventListener('blur', updateDateTimeConstraints);

            // Initial call to set up constraints on page load
            updateDateTimeConstraints();


            showConfirmationBtn.addEventListener('click', function() {
                // Perform client-side date/time validation first
                if (!validateTimestamps()) {
                    return; // Stop if client-side validation fails
                }

                let formData = new FormData(form);
                // Remove the PUT method override for the validation endpoint, which expects POST
                formData.delete('_method');

                // Clear all old error messages
                document.querySelectorAll('.text-danger.small').forEach(el => el.textContent = '');

                // Send data to server for validation via AJAX
                fetch('{{ route('request.validate') }}', {
                        method: 'POST', // Validation endpoint is typically POST
                        headers: {
                            'X-CSRF-TOKEN': formData.get('_token'), // Get CSRF token from form data
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        // If validation FAILED
                        if (data.errors) {
                            Object.keys(data.errors).forEach(key => {
                                // Display error messages under the corresponding input
                                const errorElement = document.getElementById(`${key}-error`);
                                if (errorElement) {
                                    errorElement.textContent = data.errors[key][0];
                                }
                            });
                        }
                        // If validation SUCCEEDED
                        else if (data.success) {
                            const newPrice = parseFloat(formData.get('workPriceLabel')) || 0;
                            const priceDifference = newPrice - originalPrice;

                            modalUserBalance.textContent = formatRupiah(userBalance);

                            if (priceDifference > 0) {
                                modalCostLabel.textContent = 'Biaya tambahan:';
                                modalJobCostDiff.textContent = formatRupiah(priceDifference);
                                if (userBalance < priceDifference) {
                                    modalUserBalance.classList.add('text-danger');
                                    modalWarning.style.display = 'block';
                                    modalConfirmBtn.style.display = 'none';
                                    modalTopupBtn.style.display = 'inline-block';
                                } else {
                                    modalUserBalance.classList.remove('text-danger');
                                    modalWarning.style.display = 'none';
                                    modalConfirmBtn.style.display = 'inline-block';
                                    modalTopupBtn.style.display = 'none';
                                }
                            } else if (priceDifference < 0) {
                                modalCostLabel.textContent = 'Dana akan dikembalikan:';
                                modalJobCostDiff.textContent = formatRupiah(Math.abs(priceDifference));
                                modalUserBalance.classList.remove('text-danger');
                                modalWarning.style.display = 'none';
                                modalConfirmBtn.style.display = 'inline-block';
                                modalTopupBtn.style.display = 'none';
                            } else {
                                modalCostLabel.textContent = 'Tidak ada perubahan biaya.';
                                modalJobCostDiff.textContent = formatRupiah(0);
                                modalUserBalance.classList.remove('text-danger');
                                modalWarning.style.display = 'none';
                                modalConfirmBtn.style.display = 'inline-block';
                                modalTopupBtn.style.display = 'none';
                            }

                            // Show the modal
                            confirmationModal.show();
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });

            // If the confirm button in the modal is pressed, submit the form
            modalConfirmBtn.addEventListener('click', function() {
                form.submit();
            });
        });
    </script>
@endsection
