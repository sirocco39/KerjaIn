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
                        <textarea class="form-control rounded-3" id="work-detail-text" rows="3" name="workDetailLabel">{{ old('workDetailLabel', $workRequest->description) }}</textarea>
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
                                        value="{{ old('workStartDateLabel', $startDate) }}">
                                    <div class="text-danger small mt-1" id="workStartDateLabel-error"></div>
                                </div>
                                <div class="flex-fill">
                                    <input type="time" class="form-control rounded-3" name="workStartTimeLabel"
                                        value="{{ old('workStartTimeLabel', $startTime) }}">
                                    <div class="text-danger small mt-1" id="workStartTimeLabel-error"></div>
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
        <div class="modal-dialog modal-dialog-centered">
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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

            const userBalance = parseFloat('{{ auth()->check() ? auth()->user()->balance : 0 }}');
            const originalPrice = parseFloat('{{ (int) $workRequest->price }}');

            const formatRupiah = (number) => {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(number);
            };

            showConfirmationBtn.addEventListener('click', function() {
                let formData = new FormData(form);
                formData.delete('_method');

                document.querySelectorAll('.text-danger.small').forEach(el => el.textContent = '');

                fetch('{{ route('request.validate') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': formData.get('_token'),
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
                        } else if (data.success) {
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
