@extends('master.master-job-req')

@section('content')
    <div class="container-fluid pembatas-x pembatas-y">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-2">Buat tawaran kerja</h2>
                <p class="mb-4">Yuk, mulai! Isi detail pekerjaan agar mitra kami bisa segera membantumu.</p>

                <form action="{{ route('request.store') }}" method="post" id="create-work-form">
                    @csrf
                    {{-- Judul, Detail, Alamat (Tidak ada perubahan) --}}
                    <div class="mb-3">
                        <label for="work-title-text" class="form-label fw-semibold">Judul Pekerjaan</label>
                        <input type="text" class="form-control rounded-3" id="work-title-text" name="workTitleLabel"
                            placeholder="Contoh: Masangin AC Ruang Tamu" value="{{ old('workTitleLabel') }}">
                        <div class="text-danger small mt-1" id="workTitleLabel-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="work-detail-text" class="form-label fw-semibold">Detail Pekerjaan</label>
                        <textarea class="form-control rounded-3" id="work-detail-text" rows="3" name="workDetailLabel"
                            placeholder="Contoh: Pasang AC 1 PK di ruang tamu bagian atas korden">{{ old('workDetailLabel') }}</textarea>
                        <div class="text-danger small mt-1" id="workDetailLabel-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="work-address-text" class="form-label fw-semibold">Alamat</label>
                        <input type="text" class="form-control rounded-3" id="work-address-text" name="workAddressLabel"
                            placeholder="Contoh: Jalan Pakuan No3, Sentul" value="{{ old('workAddressLabel') }}">
                        <div class="text-danger small mt-1" id="workAddressLabel-error"></div>
                    </div>

                    {{-- ========================================================== --}}
                    {{-- BAGIAN WAKTU YANG DIPERBAIKI --}}
                    {{-- ========================================================== --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Waktu mulai pekerjaan</label>
                            <div class="d-flex gap-2">
                                <div class="flex-fill">
                                    <input type="date" class="form-control rounded-3" name="workStartDateLabel"
                                        value="{{ old('workStartDateLabel') }}" min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                    <div class="text-danger small mt-1" id="workStartDateLabel-error"></div>
                                </div>
                                <div class="flex-fill">
                                    <input type="time" class="form-control rounded-3" name="workStartTimeLabel"
                                        value="{{ old('workStartTimeLabel') }}">
                                    <div class="text-danger small mt-1" id="workStartTimeLabel-error"></div>
                                    <div class="text-danger small mt-1" id="workStartTimeLabel-past-error"></div> <!-- NEW ERROR DIV -->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <label class="form-label fw-semibold">Waktu selesai pekerjaan</label>
                            <div class="d-flex gap-2">
                                <div class="flex-fill">
                                    <input type="date" class="form-control rounded-3" name="workEndDateLabel"
                                        value="{{ old('workEndDateLabel') }}">
                                    <div class="text-danger small mt-1" id="workEndDateLabel-error"></div>
                                </div>
                                <div class="flex-fill">
                                    <input type="time" class="form-control rounded-3" name="workEndTimeLabel"
                                        value="{{ old('workEndTimeLabel') }}">
                                    <div class="text-danger small mt-1" id="workEndTimeLabel-error"></div>
                                    <div class="text-danger small mt-1" id="workEndTimeLabel-past-error"></div> <!-- NEW ERROR DIV -->
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
                        <label for="work-price-text" class="form-label fw-semibold">Harga</label>
                        <div class="input-group">
                            <span class="input-group-text rounded-start-3">Rp</span>
                            <input type="number" class="form-control rounded-end-3" min="5000" id="work-price-text"
                                name="workPriceLabel" placeholder="Contoh: 150.000" value="{{ old('workPriceLabel') }}">
                        </div>
                        <div class="text-danger small mt-1" id="workPriceLabel-error"></div>
                    </div>

                    <button type="button" id="show-confirmation-button" class="btn btn-primary px-4 py-2 rounded-3">Buat
                        Tawaran Kerja</button>
                </form>
            </div>
            <div class="col-lg-6 text-center mt-5 mt-lg-0">
                <img src="{{ asset('Image/orang/Merah dan Pink Ilustrasi Mochi Logo (12) 1.png') }}" alt="Ilustrasi Orang"
                    class="img-fluid" style="max-height: 400px;">
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="confirmationModalLabel">Konfirmasi Pembuatan Pekerjaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Dana akan ditahan (escrow) dari saldo Anda untuk menjamin pembayaran kepada mitra. Dana hanya akan
                        dilepaskan setelah pekerjaan selesai.</p>

                    <div class="mb-2 d-flex justify-content-between">
                        <span>Upah tawaran kerja:</span>
                        <span class="fw-bold" id="modal-job-cost">Rp0</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span>Biaya layanan aplikasi:</span>
                        <span class="fw-bold">Rp 2500</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span>Total yang harus dibayar:</span>
                        <span class="fw-bold" id="modal-total-cost">Rp0</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Saldo Anda saat ini:</span>
                        <span class="fw-bold" id="modal-user-balance">Rp0</span>
                    </div>

                    <div id="modal-warning-message" class="alert alert-danger mt-3" style="display: none;">
                        Saldo Anda tidak cukup. Silakan isi saldo terlebih dahulu.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <a href="{{ route('top-up.job-req') }}" id="modal-topup-button" class="btn btn-success"
                        style="display: none;">Isi Saldo</a>
                    <button type="button" id="modal-confirm-button" class="btn btn-primary">Ya, Konfirmasi & Tahan
                        Dana</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Kode JavaScript untuk mengontrol modal --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('create-work-form');
            const showConfirmationBtn = document.getElementById('show-confirmation-button');
            const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));

            // Elemen-elemen di dalam modal
            const modalTotalCost = document.getElementById('modal-total-cost');
            const modalJobCost = document.getElementById('modal-job-cost');
            const modalUserBalance = document.getElementById('modal-user-balance');
            const modalWarning = document.getElementById('modal-warning-message');
            const modalConfirmBtn = document.getElementById('modal-confirm-button');
            const modalTopupBtn = document.getElementById('modal-topup-button');

            // Date and Time inputs
            const workStartDateInput = document.querySelector('input[name="workStartDateLabel"]');
            const workEndDateInput = document.querySelector('input[name="workEndDateLabel"]');
            const workStartTimeInput = document.querySelector('input[name="workStartTimeLabel"]');
            const workEndTimeInput = document.querySelector('input[name="workEndTimeLabel"]');
            const datetimeErrorDiv = document.getElementById('datetime-error');
            const workStartTimeLabelPastErrorDiv = document.getElementById('workStartTimeLabel-past-error'); // NEW
            const workEndTimeLabelPastErrorDiv = document.getElementById('workEndTimeLabel-past-error'); // NEW


            const userBalance = parseFloat('{{ auth()->check() ? auth()->user()->balance : 0 }}');

            const formatRupiah = (number) => {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(number);
            };

            function getFormattedCurrentTime() {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                return `${hours}:${minutes}`;
            }

            function getFormattedCurrentDate() {
                const now = new Date();
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            function validateDateTimeCombination() {
                // Clear ALL client-side specific date/time errors before re-validating
                datetimeErrorDiv.textContent = '';
                workStartTimeLabelPastErrorDiv.textContent = '';
                workEndTimeLabelPastErrorDiv.textContent = '';

                const startDateValue = workStartDateInput.value;
                const startTimeValue = workStartTimeInput.value;
                const endDateValue = workEndDateInput.value;
                const endTimeValue = workEndTimeInput.value;

                // If any date/time field is empty, return true to allow server-side 'required' validation to handle it.
                // This prevents the 'datetime' error from appearing if the user just hasn't filled all fields yet.
                if (!startDateValue || !startTimeValue || !endDateValue || !endTimeValue) {
                    return true;
                }

                const startDateTime = new Date(`${startDateValue}T${startTimeValue}:00`);
                const endDateTime = new Date(`${endDateValue}T${endTimeValue}:00`);
                const todayString = getFormattedCurrentDate();
                const currentTimeString = getFormattedCurrentTime();

                let isValid = true;

                // 1. Start time cannot be in the past if the start date is today
                if (startDateValue === todayString && startTimeValue < currentTimeString) {
                    workStartTimeLabelPastErrorDiv.textContent = 'Waktu mulai tidak boleh di masa lalu.';
                    isValid = false;
                }

                // 2. End time cannot be in the past if the end date is today AND different from start date
                // This prevents showing a "past time" error if the primary issue is "end before start" on the same day.
                if (endDateValue === todayString && startTimeValue && endTimeValue < currentTimeString && startDateValue !== todayString) {
                    workEndTimeLabelPastErrorDiv.textContent = 'Waktu selesai tidak boleh di masa lalu.';
                    isValid = false;
                }

                // 3. End date/time must be strictly after start date/time (the main combined check)
                // This covers cases where end time is before start time on the same day, or end date is before start date.
                if (endDateTime <= startDateTime) {
                    datetimeErrorDiv.textContent = 'Waktu selesai pekerjaan harus setelah waktu mulai.';
                    isValid = false;
                }

                return isValid; // All client-side date/time validations passed
            }

            function updateDateTimeConstraints() {
                const today = getFormattedCurrentDate();
                const currentTime = getFormattedCurrentTime();

                // Clear all client-side specific date/time errors before updating constraints and re-validating
                datetimeErrorDiv.textContent = '';
                workStartTimeLabelPastErrorDiv.textContent = '';
                workEndTimeLabelPastErrorDiv.textContent = '';

                // Set min for end date: cannot be before start date
                if (workStartDateInput.value) {
                    workEndDateInput.min = workStartDateInput.value;
                } else {
                    workEndDateInput.min = today; // If start date is not set, min for end date is today
                }

                // If end date is set and is earlier than start date, reset it to start date
                if (workEndDateInput.value && workStartDateInput.value && workEndDateInput.value < workStartDateInput.value) {
                    workEndDateInput.value = workStartDateInput.value;
                }

                // Dynamic min for start time
                if (workStartDateInput.value === today) {
                    workStartTimeInput.min = currentTime;
                } else {
                    workStartTimeInput.min = ''; // No minimum time for future dates
                }

                // Dynamic min for end time
                if (workEndDateInput.value === today) {
                    if (workStartDateInput.value === today && workStartTimeInput.value) {
                        // If both start and end are today, end time must be after start time
                        workEndTimeInput.min = workStartTimeInput.value;
                    } else {
                        // If only end date is today (and start date is a past day), end time can be current time
                        workEndTimeInput.min = currentTime;
                    }
                } else {
                    workEndTimeInput.min = ''; // No minimum time for future dates
                }

                validateDateTimeCombination(); // Re-validate on constraint changes
            }

            // Add event listeners
            workStartDateInput.addEventListener('change', updateDateTimeConstraints);
            workEndDateInput.addEventListener('change', updateDateTimeConstraints);
            workStartTimeInput.addEventListener('change', updateDateTimeConstraints);
            workEndTimeInput.addEventListener('change', updateDateTimeConstraints);

            // Initial call to set up constraints on page load
            updateDateTimeConstraints();


            showConfirmationBtn.addEventListener('click', function(event) {
                event.preventDefault(); // Mencegah form submit secara langsung

                // Perform client-side date/time validation first
                if (!validateDateTimeCombination()) {
                    return; // Stop if client-side validation fails
                }

                // Ambil data form
                let formData = new FormData(form);

                // Hapus semua pesan error lama, termasuk yang dari server-side
                document.querySelectorAll('.text-danger.small').forEach(el => el.textContent = '');

                // Kirim data ke server untuk validasi via AJAX
                fetch('{{ route('request.validate') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json',
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Jika validasi GAGAL
                        if (data.errors) {
                            Object.keys(data.errors).forEach(key => {
                                // Tampilkan pesan error di bawah input yang sesuai
                                const errorElement = document.getElementById(`${key}-error`);
                                if (errorElement) {
                                    errorElement.textContent = data.errors[key][0];
                                }
                            });
                        }
                        // Jika validasi SUKSES
                        else if (data.success) {
                            const jobCost = parseFloat(formData.get('workPriceLabel')) || 0;
                            const totalCost = jobCost + 2500; // Biaya layanan aplikasi
                            // Isi data modal
                            modalTotalCost.textContent = formatRupiah(totalCost);
                            modalJobCost.textContent = formatRupiah(jobCost);
                            modalUserBalance.textContent = formatRupiah(userBalance);

                            // Cek kecukupan saldo
                            if (userBalance < totalCost) {
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

                            // Tampilkan modal
                            confirmationModal.show();
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });

            // Jika tombol konfirmasi di modal ditekan, submit form
            modalConfirmBtn.addEventListener('click', function() {
                form.submit();
            });
        });
    </script>
@endsection
