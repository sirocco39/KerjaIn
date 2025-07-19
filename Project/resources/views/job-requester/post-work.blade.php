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
                        <input type="text" class="form-control rounded-3" id="work-title-text" name="workTitleLabel"
                            placeholder="Contoh: Masangin AC Ruang Tamu" value="{{ old('workTitleLabel') }}">
                        {{-- Tempat untuk error AJAX --}}
                        <span class="text-danger error-text" id="workTitleLabel-error"></span>
                    </div>
                    <div class="mb-3">
                        <label for="work-detail-text" class="form-label fw-semibold">Detail Pekerjaan</label>
                        <textarea class="form-control rounded-3" id="work-detail-text" rows="3" name="workDetailLabel"
                            placeholder="Contoh: Pasang AC 1 PK di ruang tamu bagian atas korden">{{ old('workDetailLabel') }}</textarea>
                        <span class="text-danger error-text" id="workDetailLabel-error"></span>
                    </div>
                    <div class="mb-3">
                        <label for="work-address-text" class="form-label fw-semibold">Alamat</label>
                        <input type="text" class="form-control rounded-3" id="work-address-text" name="workAddressLabel"
                            placeholder="Contoh: Jalan Pakuan No3, Sentul" value="{{ old('workAddressLabel') }}">
                        <span class="text-danger error-text" id="workAddressLabel-error"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Waktu mulai pekerjaan</label>
                        <div class="d-flex">
                            <input type="date" class="form-control rounded-3 me-2" id="work-start-date-text"
                                name="workStartDateLabel" value="{{ old('workStartDateLabel') }}">
                            <input type="time" class="form-control rounded-3" id="work-start-time-text"
                                name="workStartTimeLabel" value="{{ old('workStartTimeLabel') }}">
                        </div>
                        <span class="text-danger error-text" id="workStartDateLabel-error"></span>
                        <span class="text-danger error-text" id="workStartTimeLabel-error"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Waktu selesai pekerjaan</label>
                        <div class="d-flex">
                            <input type="date" class="form-control rounded-3 me-2" id="work-end-date-text"
                                name="workEndDateLabel" value="{{ old('workEndDateLabel') }}">
                            <input type="time" class="form-control rounded-3" id="work-end-time-text"
                                name="workEndTimeLabel" value="{{ old('workEndTimeLabel') }}">
                        </div>
                        <span class="text-danger error-text" id="workEndDateLabel-error"></span>
                        <span class="text-danger error-text" id="workEndTimeLabel-error"></span>
                        <span class="text-danger error-text" id="datetime-error"></span>
                    </div>
                    <div class="mb-4">
                        <label for="work-price-text" class="form-label fw-semibold">Harga</label>
                        <div class="input-group">
                            <span class="input-group-text rounded-start-3">Rp</span>
                            <input type="number" class="form-control rounded-end-3" min="5000" id="work-price-text"
                                name="workPriceLabel" placeholder="Contoh: 150.000" value="{{ old('workPriceLabel') }}">
                        </div>
                        <span class="text-danger error-text" id="workPriceLabel-error"></span>
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
            const modalJobCost = document.getElementById('modal-job-cost');
            const modalUserBalance = document.getElementById('modal-user-balance');
            const modalWarning = document.getElementById('modal-warning-message');
            const modalConfirmBtn = document.getElementById('modal-confirm-button');
            const modalTopupBtn = document.getElementById('modal-topup-button');

            const userBalance = parseFloat('{{ auth()->check() ? auth()->user()->balance : 0 }}');

            const formatRupiah = (number) => {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(number);
            };

            showConfirmationBtn.addEventListener('click', function(event) {
                event.preventDefault(); // Mencegah form submit secara langsung

                // Ambil data form
                let formData = new FormData(form);

                // Hapus pesan error lama
                document.querySelectorAll('.error-text').forEach(el => el.textContent = '');

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

                            // Isi data modal
                            modalJobCost.textContent = formatRupiah(jobCost);
                            modalUserBalance.textContent = formatRupiah(userBalance);

                            // Cek kecukupan saldo
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
