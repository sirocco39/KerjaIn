@extends('Master.master-admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-lg mb-4">
                <div class="card-header pb-0 p-3">
                    <div class="row">
                        <div class="col-6 d-flex align-items-center">
                            <h6 class="mb-0">Detail Permintaan Verifikasi</h6>
                        </div>
                        <div class="col-6 text-end">
                            {{-- Tombol Kembali --}}
                            <a href="{{ route('admin.verifications.index', ['status' => $verificationRequest->status, 'search' => $search ?? '']) }}" class="btn btn-sm btn-outline-dark mb-0">
                                <i class="material-symbols-rounded text-sm">arrow_back</i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    {{-- Search Bar with Recommendations for Show Page --}}
                    <div class="mb-4">
                        <label for="userSearchShow" class="form-label">Cari Pengguna:</label>
                        <div class="input-group">
                            <input type="text" id="userSearchShow" class="form-control" placeholder="Cari ID, NIK, atau Nama Lengkap pengguna..." value="{{ $search ?? '' }}">
                            <button class="btn btn-primary" type="button" id="clearSearchShow">Clear</button>
                        </div>
                        <div id="searchResults" class="list-group position-absolute w-75 mt-1" style="z-index: 1000;">
                            {{-- Hasil pencarian akan ditampilkan di sini --}}
                        </div>
                    </div>

                    {{-- Navigasi Previous/Next dan Dropdown --}}
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            @if ($previousRequest)
                            <a href="{{ route('admin.verifications.show', ['id' => $previousRequest->id, 'search' => $search ?? '']) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="material-symbols-rounded text-sm">chevron_left</i> Sebelumnya
                            </a>
                            @endif
                        </div>
                        <div class="flex-grow-1 mx-2">
                            <select id="statusFilteredUserDropdown" class="form-select">
                                <option value="">Pilih Pengguna (Status: {{ ucfirst($verificationRequest->status) }})</option>
                                @foreach ($sameStatusRequests as $req)
                                <option value="{{ $req->id }}" {{ $req->id == $verificationRequest->id ? 'selected' : '' }}>
                                    {{ $req->id }} - {{ $req->first_name }} {{ $req->last_name }} (NIK: {{ $req->nik }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            @if ($nextRequest)
                            <a href="{{ route('admin.verifications.show', ['id' => $nextRequest->id, 'search' => $search ?? '']) }}" class="btn btn-sm btn-outline-secondary">
                                Selanjutnya <i class="material-symbols-rounded text-sm">chevron_right</i>
                            </a>
                            @endif
                        </div>
                    </div>

                    @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show text-light" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Informasi Pengguna</h6>
                    <ul class="list-group">
                        <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-3 text-sm">Nama Lengkap:
                                    <span class="text-dark font-weight-bold ms-sm-2">
                                        {{ $verificationRequest->first_name }} {{ $verificationRequest->last_name }}
                                    </span>
                                </h6>
                                <span class="mb-2 text-xs">Email:
                                    <span class="text-dark font-weight-bold ms-sm-2">
                                        {{ $verificationRequest->user->email ?? 'N/A' }}
                                    </span>
                                </span>
                                <span class="mb-2 text-xs">Nomor Telepon:
                                    <span class="text-dark ms-sm-2 font-weight-bold">
                                        {{ $verificationRequest->phone_number ?? 'N/A' }}
                                    </span>
                                </span>
                                <span class="mb-2 text-xs">Tanggal Lahir:
                                    <span class="text-dark ms-sm-2 font-weight-bold">
                                        {{ \Carbon\Carbon::parse($verificationRequest->birthdate)->format('d M Y') }}
                                    </span>
                                </span>
                                <span class="mb-2 text-xs">Jenis Kelamin:
                                    <span class="text-dark ms-sm-2 font-weight-bold">
                                        {{ $verificationRequest->gender ?? 'N/A' }}
                                    </span>
                                </span>
                                <span class="mb-2 text-xs">NIK:
                                    <span class="text-dark ms-sm-2 font-weight-bold">
                                        {{ $verificationRequest->nik }}
                                    </span>
                                </span>
                                <span class="mb-2 text-xs">Alamat:
                                    <span class="text-dark ms-sm-2 font-weight-bold">
                                        {{ $verificationRequest->address ?? 'N/A' }}
                                    </span>
                                </span>
                                <span class="mb-2 text-xs">Nama Rekening Bank:
                                    <span class="text-dark ms-sm-2 font-weight-bold">
                                        {{ $verificationRequest->account_name ?? 'N/A' }}
                                    </span>
                                </span>
                                <span class="mb-2 text-xs">Nomor Rekening Bank:
                                    <span class="text-dark ms-sm-2 font-weight-bold">
                                        {{ $verificationRequest->account_number ?? 'N/A' }}
                                    </span>
                                </span>
                                <span class="text-xs">Diajukan pada:
                                    <span class="text-dark ms-sm-2 font-weight-bold">
                                        {{ $verificationRequest->created_at->format('d M Y H:i') }}
                                    </span>
                                </span>
                                @if ($verificationRequest->status == 'approved' || $verificationRequest->status == 'rejected')
                                <span class="text-xs mt-2">Diperbarui pada:
                                    <span class="text-dark ms-sm-2 font-weight-bold">
                                        {{ $verificationRequest->updated_at->format('d M Y H:i') }}
                                    </span>
                                </span>
                                @endif
                                @if ($verificationRequest->status == 'approved')
                                <span class="text-xs mt-2">Diverifikasi pada:
                                    <span class="text-dark ms-sm-2 font-weight-bold">
                                        {{ $verificationRequest->verified_at ? $verificationRequest->verified_at->format('d M Y H:i') : 'N/A' }}
                                    </span>
                                </span>
                                @endif
                                {{-- Menampilkan alasan penolakan jika ada --}}
                                @if ($verificationRequest->status == 'rejected' && $verificationRequest->rejection_reason)
                                <span class="text-xs mt-2">Alasan Penolakan:
                                    <span class="text-danger ms-sm-2 font-weight-bold">
                                        {{ $verificationRequest->rejection_reason }}
                                    </span>
                                </span>
                                @endif
                            </div>
                            <div class="ms-auto text-end">
                                <h6 class="text-sm">Status:
                                    <span class="badge badge-sm
                                                @if($verificationRequest->status == 'pending') bg-gradient-warning
                                                @elseif($verificationRequest->status == 'approved') bg-gradient-success
                                                @else bg-gradient-danger @endif
                                                ms-sm-2">
                                        {{ ucfirst($verificationRequest->status) }}
                                    </span>
                                </h6>
                            </div>
                        </li>
                    </ul>

                    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3 mt-4">Dokumen Pendukung</h6>
                    <div class="row">
                        <div class="col-md-6 mb-md-0 mb-4">
                            <div class="card card-body border card-plain border-radius-lg d-flex flex-column justify-content-between h-100">
                                <p class="text-dark text-sm font-weight-bold">Foto Pengguna:</p>
                                @if ($verificationRequest->photo_url)
                                <a href="{{ Storage::url($verificationRequest->photo_url) }}" target="_blank">
                                    <img src="{{ Storage::url($verificationRequest->photo_url) }}" class="img-fluid border-radius-lg mb-3" alt="Foto Pengguna">
                                </a>
                                @else
                                <p class="text-muted">Tidak ada foto pengguna yang diunggah.</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-md-0 mb-4">
                            <div class="card card-body border card-plain border-radius-lg d-flex flex-column justify-content-between h-100">
                                <p class="text-dark text-sm font-weight-bold">Foto KTP:</p>
                                @if ($verificationRequest->id_card_url)
                                <a href="{{ Storage::url($verificationRequest->id_card_url) }}" target="_blank">
                                    <img src="{{ Storage::url($verificationRequest->id_card_url) }}" class="img-fluid border-radius-lg mb-3" alt="Foto KTP">
                                </a>
                                @else
                                <p class="text-muted">Tidak ada foto KTP yang diunggah.</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mt-4">
                            <div class="card card-body border card-plain border-radius-lg d-flex flex-column justify-content-between h-100">
                                <p class="text-dark text-sm font-weight-bold">Foto Selfie dengan KTP:</p>
                                @if ($verificationRequest->selfie_with_id_card_url)
                                <a href="{{ Storage::url($verificationRequest->selfie_with_id_card_url) }}" target="_blank">
                                    <img src="{{ Storage::url($verificationRequest->selfie_with_id_card_url) }}" class="img-fluid border-radius-lg mb-3" alt="Foto Selfie dengan KTP">
                                </a>
                                @else
                                <p class="text-muted">Tidak ada foto selfie dengan KTP yang diunggah.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons (only for pending requests) --}}
                    @if ($verificationRequest->status == 'pending')
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Aksi</h6>
                            <form action="{{ route('admin.verifications.approve', $verificationRequest->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="button" class="btn bg-gradient-success mb-0 me-2" data-bs-toggle="modal" data-bs-target="#approveConfirmationModal">
                                    <i class="material-symbols-rounded text-sm">check_circle</i> Setujui
                                </button>
                            </form>
                            {{-- Tombol Tolak memicu modal --}}
                            <button type="button" class="btn bg-gradient-danger mb-0" data-bs-toggle="modal" data-bs-target="#rejectReasonModal">
                                <i class="material-symbols-rounded text-sm">cancel</i> Tolak
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Optional: Add a section for user's past activities or summary if needed --}}
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <div class="row">
                        <div class="col-12 d-flex align-items-center">
                            <h6 class="mb-0">Detail Akun Pengguna</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3 pb-0">
                    <ul class="list-group">
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark font-weight-bold text-sm">ID Pengguna:</h6>
                                <span class="text-xs">{{ $verificationRequest->user->id ?? 'N/A' }}</span>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark font-weight-bold text-sm">Role:</h6>
                                <span class="text-xs">{{ $verificationRequest->user->role ?? 'N/A' }}</span>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark font-weight-bold text-sm">Saldo Kerjain:</h6>
                                <span class="text-xs">Rp {{ number_format($verificationRequest->user->saldokerjain ?? 0, 2, ',', '.') }}</span>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark font-weight-bold text-sm">Status Pekerja:</h6>
                                <span class="text-xs">{{ ($verificationRequest->user->is_worker ?? 0) ? 'Ya' : 'Tidak' }}</span>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark font-weight-bold text-sm">Rating:</h6>
                                <span class="text-xs">{{ number_format($verificationRequest->user->rating ?? 0, 2) }}</span>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark font-weight-bold text-sm">Pekerjaan Selesai:</h6>
                                <span class="text-xs">{{ $verificationRequest->user->job_done ?? 0 }}</span>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark font-weight-bold text-sm">Akun Dibuat:</h6>
                                <span class="text-xs">{{ $verificationRequest->user?->created_at ? $verificationRequest->user->created_at->format('d M Y H:i') : 'N/A' }}</span>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark font-weight-bold text-sm">Terakhir Diperbarui:</h6>
                                <span class="text-xs">{{ $verificationRequest->user?->updated_at ? $verificationRequest->user->updated_at->format('d M Y H:i') : 'N/A' }}</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('modal')
<div class="modal fade" id="rejectReasonModal" tabindex="-1" aria-labelledby="rejectReasonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectReasonModalLabel">Tolak Verifikasi Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.verifications.reject', $verificationRequest->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Alasan Penolakan:</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" placeholder="Berikan alasan mengapa permintaan verifikasi ini ditolak." required></textarea>
                        @error('rejection_reason')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <p class="text-sm font-weight-bold mb-2">Pilih Alasan Cepat:</p>
                    <div class="d-flex flex-wrap gap-2"> {{-- Flexbox untuk layout gelembung --}}
                        <span class="badge bg-gradient-secondary reason-chip cursor-pointer" data-reason="Foto KTP buram atau tidak jelas.">KTP Buram</span>
                        <span class="badge bg-gradient-secondary reason-chip cursor-pointer" data-reason="Data NIK tidak sesuai.">NIK Tidak Sesuai</span>
                        <span class="badge bg-gradient-secondary reason-chip cursor-pointer" data-reason="Wajah di foto selfie tidak terlihat jelas.">Wajah Tidak Jelas</span>
                        <span class="badge bg-gradient-secondary reason-chip cursor-pointer" data-reason="Foto selfie dengan KTP tidak sesuai ketentuan.">Selfie KTP Salah</span>
                        <span class="badge bg-gradient-secondary reason-chip cursor-pointer" data-reason="Data diri tidak konsisten.">Data Tidak Konsisten</span>
                        <span class="badge bg-gradient-secondary reason-chip cursor-pointer" data-reason="Dokumen yang diunggah tidak valid.">Dokumen Tidak Valid</span>
                        {{-- Tambahkan lebih banyak opsi sesuai kebutuhan --}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Verifikasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="approveConfirmationModal" tabindex="-1" aria-labelledby="approveConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveConfirmationModalLabel">Konfirmasi Persetujuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin <b>menyetujui</b> permintaan verifikasi ini?</p>
                <p class="text-danger">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batalkan</button>
                <form action="{{ route('admin.verifications.approve', $verificationRequest->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn bg-gradient-success">Ya, Setujui</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts') {{-- Pastikan master-admin.blade.php memiliki @stack('scripts') sebelum </body> --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reasonChips = document.querySelectorAll('.reason-chip');
        const rejectionReasonTextarea = document.getElementById('rejection_reason');

        reasonChips.forEach(chip => {
            chip.addEventListener('click', function() {
                const reason = this.dataset.reason;
                // Menambahkan alasan, bukan menimpa
                if (rejectionReasonTextarea.value.trim() === '') {
                    rejectionReasonTextarea.value = reason;
                } else {
                    // Cek apakah alasan sudah ada untuk menghindari duplikasi berlebihan
                    if (!rejectionReasonTextarea.value.includes(reason)) {
                        rejectionReasonTextarea.value += '\n' + reason;
                    }
                }
                rejectionReasonTextarea.focus(); // Fokuskan ke textarea
            });
        });

        const rejectReasonModal = document.getElementById('rejectReasonModal');
        rejectReasonModal.addEventListener('hidden.bs.modal', function() {
            rejectionReasonTextarea.value = ''; // Kosongkan textarea saat modal ditutup
        });

        // JavaScript untuk navigasi dropdown di halaman show
        const statusFilteredUserDropdown = document.getElementById('statusFilteredUserDropdown');
        if (statusFilteredUserDropdown) {
            statusFilteredUserDropdown.addEventListener('change', function() {
                const selectedId = this.value;
                if (selectedId) {
                    window.location.href = `{{ url('verifications/show') }}/${selectedId}`;
                }
            });
        }

        // JavaScript untuk pencarian rekomendasi di halaman show
        const userSearchShow = document.getElementById('userSearchShow');
        const searchResults = document.getElementById('searchResults');
        const clearSearchShow = document.getElementById('clearSearchShow');
        let searchTimeout;

        if (userSearchShow) {
            userSearchShow.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value;

                if (query.length > 2) { // Mulai mencari setelah 2 karakter
                    searchTimeout = setTimeout(() => {
                        fetch(`{{ route('admin.verifications.show.search-ajax') }}?query=${query}`)
                            .then(response => response.json())
                            .then(data => {
                                searchResults.innerHTML = ''; // Bersihkan hasil sebelumnya
                                if (data.length > 0) {
                                    data.forEach(item => {
                                        const a = document.createElement('a');
                                        a.href = `{{ url('admin/verifications') }}/${item.id}`;
                                        a.classList.add('list-group-item', 'list-group-item-action');
                                        a.innerHTML = `<strong>ID: ${item.id}</strong> - ${item.first_name} ${item.last_name} (NIK: ${item.nik}) <span class="badge bg-secondary ms-2">${item.status.charAt(0).toUpperCase() + item.status.slice(1)}</span>`;
                                        searchResults.appendChild(a);
                                    });
                                } else {
                                    searchResults.innerHTML = '<div class="list-group-item">Tidak ada hasil ditemukan.</div>';
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching search results:', error);
                                searchResults.innerHTML = '<div class="list-group-item text-danger">Terjadi kesalahan saat mencari.</div>';
                            });
                    }, 300); // Debounce 300ms
                } else {
                    searchResults.innerHTML = ''; // Bersihkan jika kueri terlalu pendek
                }
            });

            // Clear button functionality
            clearSearchShow.addEventListener('click', function() {
                userSearchShow.value = '';
                searchResults.innerHTML = '';
            });

            // Hide search results when clicking outside
            document.addEventListener('click', function(event) {
                if (!userSearchShow.contains(event.target) && !searchResults.contains(event.target)) {
                    searchResults.innerHTML = '';
                }
            });
        }
    });
</script>
@endpush
@endsection