@extends('master.master-job-req')

@section('content')
    <div class="container-fluid pembatas-x pembatas-y">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-2">{{ __('post-work.judul_halaman') }}</h2>
                <p class="mb-4">{{ __('post-work.deskripsi_halaman') }}</p>

                <form action="{{ route('request.store') }}" method="post" id="create-work-form">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="work-title-text" class="form-label fw-semibold">{{ __('post-work.label_judul') }}</label>
                        <input type="text" class="form-control rounded-3" id="work-title-text" name="workTitleLabel"
                            placeholder="{{ __('post-work.placeholder_judul') }}" value="{{ old('workTitleLabel') }}">
                        <div class="text-danger small mt-1" id="workTitleLabel-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="work-detail-text" class="form-label fw-semibold">{{ __('post-work.label_detail') }}</label>
                        <textarea class="form-control rounded-3" id="work-detail-text" rows="3" name="workDetailLabel"
                            placeholder="{{ __('post-work.placeholder_detail') }}" style="background-color: #f7f7ff; resize: none;">{{ old('workDetailLabel') }}</textarea>
                        <div class="text-danger small mt-1" id="workDetailLabel-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="work-address-text" class="form-label fw-semibold">{{ __('post-work.label_alamat') }}</label>
                        <input type="text" class="form-control rounded-3" id="work-address-text" name="workAddressLabel"
                            placeholder="{{ __('post-work.placeholder_alamat') }}" value="{{ old('workAddressLabel') }}">
                        <div class="text-danger small mt-1" id="workAddressLabel-error"></div>
                    </div>

                    
                    
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('post-work.label_waktu_mulai') }}</label>
                            <div class="d-flex gap-2">
                                <div class="flex-fill">
                                    <input type="date" class="form-control rounded-3" name="workStartDateLabel"
                                        value="{{ old('workStartDateLabel') }}"
                                        min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                    <div class="text-danger small mt-1" id="workStartDateLabel-error"></div>
                                </div>
                                <div class="flex-fill">
                                    <input type="time" class="form-control rounded-3" name="workStartTimeLabel"
                                        value="{{ old('workStartTimeLabel') }}">
                                    <div class="text-danger small mt-1" id="workStartTimeLabel-error"></div>
                                    <div class="text-danger small mt-1" id="workStartTimeLabel-past-error"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <label class="form-label fw-semibold">{{ __('post-work.label_waktu_selesai') }}</label>
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
                                    <div class="text-danger small mt-1" id="workEndTimeLabel-past-error"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="text-danger small mt-1" id="datetime-error"></div>
                        </div>
                    </div>

                    
                    <div class="mb-4">
                        <label for="work-price-text" class="form-label fw-semibold">{{ __('post-work.label_upah') }}</label>
                        <div class="input-group">
                            <span class="input-group-text rounded-start-3">Rp</span>
                            <input type="number" class="form-control rounded-end-3" min="5000" id="work-price-text"
                                name="workPriceLabel" placeholder="{{ __('post-work.placeholder_upah') }}" value="{{ old('workPriceLabel') }}">
                        </div>
                        <div class="text-danger small mt-1" id="workPriceLabel-error"></div>
                    </div>

                    <button type="button" id="show-confirmation-button" class="btn btn-primary px-4 py-2 rounded-3">{{ __('post-work.tombol_buat_tawaran') }}</button>
                </form>
            </div>
            <div class="col-lg-6 text-center mt-5 mt-lg-0">
                <img src="{{ asset('Image/orang/Merah dan Pink Ilustrasi Mochi Logo (12) 1.png') }}"
                    alt="Ilustrasi Orang" class="img-fluid" style="max-height: 400px;">
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="confirmationModalLabel">{{ __('post-work.modal.judul') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>{{ __('post-work.modal.deskripsi_escrow') }}</p>

                    <div class="mb-2 d-flex justify-content-between">
                        <span>{{ __('post-work.modal.upah_tawaran') }}</span>
                        <span class="fw-bold" id="modal-job-cost">Rp0</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span>{{ __('post-work.modal.biaya_layanan') }}</span>
                        <span class="fw-bold">Rp 2500</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span>{{ __('post-work.modal.total_bayar') }}</span>
                        <span class="fw-bold" id="modal-total-cost">Rp0</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>{{ __('post-work.modal.saldo_anda') }}</span>
                        <span class="fw-bold" id="modal-user-balance">Rp0</span>
                    </div>

                    <div id="modal-warning-message" class="alert alert-danger mt-3" style="display: none;">
                        {{ __('post-work.modal.peringatan_saldo_kurang') }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary text-light" data-bs-dismiss="modal">{{ __('post-work.modal.tombol_batal') }}</button>
                    <a href="{{ route('top-up.job-req') }}" id="modal-topup-button" class="btn btn-success"
                        style="display: none;">{{ __('post-work.modal.tombol_isi_saldo') }}</a>
                    <button type="button" id="modal-confirm-button" class="btn btn-primary">{{ __('post-work.modal.tombol_konfirmasi') }}</button>
                </div>
            </div>
        </div>
    </div>

    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('create-work-form');
            const showConfirmationBtn = document.getElementById('show-confirmation-button');
            const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));

            
            const modalTotalCost = document.getElementById('modal-total-cost');
            const modalJobCost = document.getElementById('modal-job-cost');
            const modalUserBalance = document.getElementById('modal-user-balance');
            const modalWarning = document.getElementById('modal-warning-message');
            const modalConfirmBtn = document.getElementById('modal-confirm-button');
            const modalTopupBtn = document.getElementById('modal-topup-button');

            
            const workStartDateInput = document.querySelector('input[name="workStartDateLabel"]');
            const workEndDateInput = document.querySelector('input[name="workEndDateLabel"]');
            const workStartTimeInput = document.querySelector('input[name="workStartTimeLabel"]');
            const workEndTimeInput = document.querySelector('input[name="workEndTimeLabel"]');
            const datetimeErrorDiv = document.getElementById('datetime-error');
            const workStartTimeLabelPastErrorDiv = document.getElementById('workStartTimeLabel-past-error');
            const workEndTimeLabelPastErrorDiv = document.getElementById('workEndTimeLabel-past-error');


            const userBalance = parseFloat('{{ auth()->check() ? auth()->user()->balance : 0 }}');

            const formatRupiah = (number) => {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(number);
            };

            
            function getFormattedCurrentTimeUTC() {
                const now = new Date();
                const hours = String(now.getUTCHours()).padStart(2, '0');
                const minutes = String(now.getUTCMinutes()).padStart(2, '0');
                return `${hours}:${minutes}`;
            }

            
            function getFormattedCurrentDateUTC() {
                const now = new Date();
                const year = now.getUTCFullYear();
                const month = String(now.getUTCMonth() + 1).padStart(2, '0');
                const day = String(now.getUTCDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            function validateTimestamps() {
                
                datetimeErrorDiv.textContent = '';
                workStartTimeLabelPastErrorDiv.textContent = '';
                workEndTimeLabelPastErrorDiv.textContent = '';

                const startDate = workStartDateInput.value;
                const startTime = workStartTimeInput.value;
                const endDate = workEndDateInput.value;
                const endTime = workEndTimeInput.value;

                
                if (!startDate || !startTime || !endDate || !endTime) {
                    return true; 
                }

                
                const startDateTime = new Date(`${startDate}T${startTime}:00Z`);
                const endDateTime = new Date(`${endDate}T${endTime}:00Z`);
                
                const nowUTCComparison = new Date(`${getFormattedCurrentDateUTC()}T${getFormattedCurrentTimeUTC()}:00Z`);


                
                
                if (startDateTime < new Date(nowUTCComparison.getTime() - 60000)) {
                    datetimeErrorDiv.textContent = 'Waktu mulai pekerjaan tidak boleh di masa lalu (UTC).';
                    return false;
                }

                
                if (endDateTime <= startDateTime) {
                    datetimeErrorDiv.textContent = 'Waktu selesai harus setelah waktu mulai.';
                    return false;
                }

                return true; 
            }

            function updateDateTimeConstraints() {
                
                const todayUTC = getFormattedCurrentDateUTC();
                const currentTimeUTC = getFormattedCurrentTimeUTC();

                
                datetimeErrorDiv.textContent = '';
                workStartTimeLabelPastErrorDiv.textContent = '';
                workEndTimeLabelPastErrorDiv.textContent = '';

                let minEndDate = workStartDateInput.value; 

                
                
                
                
                if (workStartDateInput.value && workEndDateInput.value === workStartDateInput.value &&
                    workStartTimeInput.value && workEndTimeInput.value) {

                    
                    const tempStartDateTime = new Date(`${workStartDateInput.value}T${workStartTimeInput.value}:00Z`);
                    const tempEndDateTime = new Date(`${workEndDateInput.value}T${workEndTimeInput.value}:00Z`);

                    if (tempEndDateTime <= tempStartDateTime) {
                        const nextDay = new Date(tempStartDateTime);
                        nextDay.setUTCDate(tempStartDateTime.getUTCDate() + 1); 
                        minEndDate = nextDay.toISOString().split('T')[0]; 
                    }
                }

                
                workEndDateInput.min = minEndDate || todayUTC; 

                
                if (workEndDateInput.value && workEndDateInput.value < workEndDateInput.min) {
                    workEndDateInput.value = workEndDateInput.min;
                }


                
                if (workStartDateInput.value === todayUTC) {
                    workStartTimeInput.min = currentTimeUTC;
                } else {
                    workStartTimeInput.min = ''; 
                }

                
                if (workEndDateInput.value === todayUTC) {
                    if (workStartDateInput.value === todayUTC && workStartTimeInput.value) {
                        
                        workEndTimeInput.min = workStartTimeInput.value;
                    } else {
                        
                        workEndTimeInput.min = currentTimeUTC;
                    }
                } else {
                    workEndTimeInput.min = ''; 
                }

                validateTimestamps(); 
            }

            
            workStartDateInput.addEventListener('blur', updateDateTimeConstraints);
            workEndDateInput.addEventListener('blur', updateDateTimeConstraints);
            workStartTimeInput.addEventListener('blur', updateDateTimeConstraints);
            workEndTimeInput.addEventListener('blur', updateDateTimeConstraints);

            
            updateDateTimeConstraints();


            showConfirmationBtn.addEventListener('click', function(event) {
                event.preventDefault(); 

                
                if (!validateTimestamps()) {
                    return; 
                }

                
                let formData = new FormData(form);

                
                document.querySelectorAll('.text-danger.small').forEach(el => el.textContent = '');

                
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
                        
                        if (data.errors) {
                            Object.keys(data.errors).forEach(key => {
                                
                                const errorElement = document.getElementById(`${key}-error`);
                                if (errorElement) {
                                    errorElement.textContent = data.errors[key][0];
                                }
                            });
                        }
                        
                        else if (data.success) {
                            const jobCost = parseFloat(formData.get('workPriceLabel')) || 0;
                            const totalCost = jobCost + 2500; 
                            
                            modalTotalCost.textContent = formatRupiah(totalCost);
                            modalJobCost.textContent = formatRupiah(jobCost);
                            modalUserBalance.textContent = formatRupiah(userBalance);

                            
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

                            
                            confirmationModal.show();
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });

            
            modalConfirmBtn.addEventListener('click', function() {
                form.submit();
            });
        });
    </script>
@endsection
