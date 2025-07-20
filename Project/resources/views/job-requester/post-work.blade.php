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
                                        value="{{ old('workStartDateLabel') }}">
                                    <div class="text-danger small mt-1" id="workStartDateLabel-error"></div>
                                </div>
                                <div class="flex-fill">
                                    <input type="time" class="form-control rounded-3" name="workStartTimeLabel"
                                        value="{{ old('workStartTimeLabel') }}">
                                    <div class="text-danger small mt-1" id="workStartTimeLabel-error"></div>
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
