@extends('master.master-job-req')

@section('content')
    <div class="container-fluid pembatas-x pembatas-y">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-2">Buat tawaran kerja</h2>
                <p class="mb-4">Yuk, mulai! Isi detail pekerjaan agar mitra kami bisa segera membantumu.</p>

                <form action="{{ route('request.store') }}" method="post" id="create-work-form">
                    @csrf
                    {{-- Judul Pekerjaan --}}
                    <div class="mb-3">
                        <label for="work-title-text" class="form-label fw-semibold">Judul Pekerjaan</label>
                        <input type="text" class="form-control rounded-3" id="work-title-text" name="workTitleLabel" placeholder="Contoh: Masangin AC Ruang Tamu" value="{{ old('workTitleLabel') }}" required>
                        <span style="color:red">@error('workTitleLabel'){{ $message }}@enderror</span>
                    </div>
                    {{-- Detail Pekerjaan --}}
                    <div class="mb-3">
                        <label for="work-detail-text" class="form-label fw-semibold">Detail Pekerjaan</label>
                        <textarea class="form-control rounded-3" id="work-detail-text" rows="3" name="workDetailLabel" placeholder="Contoh: Pasang AC 1 PK di ruang tamu bagian atas korden" required>{{ old('workDetailLabel') }}</textarea>
                        <span style="color:red">@error('workDetailLabel'){{ $message }}@enderror</span>
                    </div>
                    {{-- Alamat --}}
                    <div class="mb-3">
                        <label for="work-address-text" class="form-label fw-semibold">Alamat</label>
                        <input type="text" class="form-control rounded-3" id="work-address-text" name="workAddressLabel" placeholder="Contoh: Jalan Pakuan No3, Sentul" value="{{ old('workAddressLabel') }}" required>
                        <span style="color:red">@error('workAddressLabel'){{ $message }}@enderror</span>
                    </div>
                    {{-- Waktu Mulai & Selesai --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="work-start-date-text" class="form-label fw-semibold">Waktu mulai pekerjaan</label>
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control rounded-3" id="work-start-date-text" name="workStartDateLabel" value="{{ old('workStartDateLabel') }}" required>
                                <input type="time" class="form-control rounded-3" id="work-start-time-text" name="workStartTimeLabel" value="{{ old('workStartTimeLabel') }}" required>
                            </div>
                             <span style="color:red">@error('workStartDateLabel'){{ $message }}@enderror</span>
                             <span style="color:red">@error('workStartTimeLabel'){{ $message }}@enderror</span>
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <label for="work-end-date-text" class="form-label fw-semibold">Waktu selesai pekerjaan</label>
                            <div class="d-flex gap-2">
                                <input type="date" class="form-control rounded-3" id="work-end-date-text" name="workEndDateLabel" value="{{ old('workEndDateLabel') }}" required>
                                <input type="time" class="form-control rounded-3" id="work-end-time-text" name="workEndTimeLabel" value="{{ old('workEndTimeLabel') }}" required>
                            </div>
                             <span style="color:red">@error('workEndDateLabel'){{ $message }}@enderror</span>
                             <span style="color:red">@error('workEndTimeLabel'){{ $message }}@enderror</span>
                        </div>
                         @error('datetime')<span style="color:red">{{ $message }}</span>@enderror
                    </div>
                    {{-- Harga --}}
                    <div class="mb-4">
                        <label for="work-price-text" class="form-label fw-semibold">Harga</label>
                        <div class="input-group">
                            <span class="input-group-text rounded-start-3">Rp</span>
                            <input type="number" class="form-control rounded-end-3" min="5000" id="work-price-text" name="workPriceLabel" placeholder="150000" value="{{ old('workPriceLabel') }}" required>
                        </div>
                        <span style="color:red">@error('workPriceLabel'){{ $message }}@enderror</span>
                    </div>

                    <button type="button" id="show-confirmation-button" class="btn btn-primary px-4 py-2 rounded-3">Buat Tawaran Kerja</button>
                </form>
            </div>

            <div class="col-lg-6 text-center mt-5 mt-lg-0">
                <img src="{{ asset('Image/orang/Merah dan Pink Ilustrasi Mochi Logo (12) 1.png') }}" alt="Ilustrasi Orang" class="img-fluid" style="max-height: 400px;">
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="confirmationModalLabel">Konfirmasi Pembuatan Pekerjaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Dana akan ditahan (escrow) dari saldo Anda untuk menjamin pembayaran kepada mitra. Dana hanya akan dilepaskan setelah pekerjaan selesai.</p>
                    
                    <div class="mb-2 d-flex justify-content-between">
                        <span>Biaya yang dibutuhkan:</span>
                        <span class="fw-bold" id="modal-job-cost">Rp0</span>
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
                    <a href="{{ route('top-up.job-req') }}" id="modal-topup-button" class="btn btn-success" style="display: none;">Isi Saldo</a>
                    <button type="button" id="modal-confirm-button" class="btn btn-primary">Ya, Konfirmasi & Tahan Dana</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Kode JavaScript untuk mengontrol modal --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('create-work-form');
            const showConfirmationBtn = document.getElementById('show-confirmation-button');
            const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));

            const modalJobCost = document.getElementById('modal-job-cost');
            const modalUserBalance = document.getElementById('modal-user-balance');
            const modalWarning = document.getElementById('modal-warning-message');
            const modalConfirmBtn = document.getElementById('modal-confirm-button');
            const modalTopupBtn = document.getElementById('modal-topup-button');

            // PERUBAHAN UTAMA DI SINI
            const userBalance = parseFloat('{{ auth()->check() ? auth()->user()->balance : 0 }}');

            const formatRupiah = (number) => {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
            };

            showConfirmationBtn.addEventListener('click', function() {
                const jobPriceInput = document.getElementById('work-price-text');
                const jobCost = parseFloat(jobPriceInput.value) || 0;

                // Validasi sederhana: pastikan semua field required terisi
                if (!form.checkValidity()) {
                    // Memicu validasi bawaan browser
                    form.reportValidity();
                    return;
                }

                modalJobCost.textContent = formatRupiah(jobCost);
                modalUserBalance.textContent = formatRupiah(userBalance);

                if (userBalance < jobCost) {
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
            });

            modalConfirmBtn.addEventListener('click', function () {
                form.submit();
            });
        });
    </script>
@endsection