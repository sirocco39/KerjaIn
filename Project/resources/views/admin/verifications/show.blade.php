@extends('Master.master-admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-lg mb-4">
                <div class="card-header pb-0 p-3">
                    <div class="row">
                        <div class="col-6 d-flex align-items-center">
                            <h6 class="mb-0">{{ __('admin/verifications.verification_request_detail') }}</h6>
                        </div>
                        <div class="col-6 text-end" style="padding-right: 12px">
                            <a href="{{ route('admin.verifications.index', ['status' => $verificationRequest->status, 'search' => $search ?? '']) }}" class="btn btn-sm btn-outline-dark mb-0" id="backButton">
                                <i class="material-symbols-rounded text-sm">arrow_back</i> {{ __('admin/verifications.back') }}
                            </a>
                        </div>

                    </div>
                </div>
                <div class="card-body p-3">
                    {{-- Search Bar with Recommendations for Show Page --}}
                    <div class="mb-4">
                        <label for="userSearchShow" class="form-label">{{ __('admin/verifications.search_user') }}</label>
                        <div class="row g-0 border rounded overflow-hidden">
                            <div class="col">
                                <input type="text" id="userSearchShow" class="form-control border-0 py-3 px-3"
                                    placeholder="{{ __('admin/verifications.search_placeholder') }}"
                                    value="{{ $search ?? '' }}">
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-primary h-100 border-0 rounded-0"
                                    type="button"
                                    id="clearSearchShow"
                                    style="width: 80px;">
                                    {{ __('admin/verifications.clear') }}
                                </button>
                            </div>
                        </div>
                        <div id="searchResults" class="list-group position-absolute w-75 mt-1" style="z-index: 1000;">
                            {{-- Hasil pencarian --}}
                        </div>
                    </div>

                    {{-- Navigasi Previous/Next dan Dropdown --}}
                    <div class="d-flex justify-content-between align-items-center gap-3 mb-4">
                        <div style="padding-top: 1rem">
                            @if ($previousRequest)
                            <a href="{{ route('admin.verifications.show', ['id' => $previousRequest->id, 'search' => $search ?? '']) }}" class="btn btn-sm btn-outline-secondary order-1" id="previousRequestButton">
                                <i class="material-symbols-rounded text-sm">chevron_left</i> {{ __('admin/verifications.previous') }}
                            </a>
                            @endif
                        </div>
                        <div class="flex-grow-1 mx-1">
                            <select id="statusFilteredUserDropdown" class="form-select" style="border-radius: 1px; text-align: center; background-color: #bcdeff;">
                                <option value="">{{ __('admin/verifications.select_user_status', ['status' => ucfirst($verificationRequest->status)]) }}</option>
                                @foreach ($sameStatusRequests as $req)
                                <option value="{{ $req->id }}" {{ $req->id == $verificationRequest->id ? 'selected' : '' }}>
                                    {{ $req->id }} - {{ $req->first_name }} {{ $req->last_name }} ({{ __('admin/verifications.nik') }} {{ $req->nik }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div style="padding-top: 1rem">
                            @if ($nextRequest)
                            <a href="{{ route('admin.verifications.show', ['id' => $nextRequest->id, 'search' => $search ?? '']) }}" class="btn btn-sm btn-outline-secondary" id="nextRequestButton">
                                {{ __('admin/verifications.next') }} <i class="material-symbols-rounded text-sm">chevron_right</i>
                            </a>
                            @endif
                        </div>
                    </div>

                    @if (session('success'))

                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="color:white">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="color:white">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">{{ __('admin/verifications.user_information') }}</h6>
                    <ul class="list-group">
                        <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-3 text-sm">{{ __('admin/verifications.full_name') }}:
                                    <span class="text-dark font-weight-bold ms-sm-2">
                                        {{ $verificationRequest->first_name }} {{ $verificationRequest->last_name }}
                                    </span>
                                </h6>
                                <span class="mb-2 text-xs">{{ __('admin/verifications.email') }}:
                                    <span class="text-dark font-weight-bold ms-sm-2">
                                        {{ $verificationRequest->user->email ?? 'N/A' }}
                                    </span>
                                </span>
                                <span class="mb-2 text-xs">{{ __('admin/verifications.email') }}:
                                    <span class="text-dark ms-sm-2 font-weight-bold">
                                        {{ $verificationRequest->phone_number ?? 'N/A' }}
                                    </span>
                                </span>
                                <span class="mb-2 text-xs">{{ __('admin/verifications.email') }}:
                                    <span class="text-dark ms-sm-2 font-weight-bold">
                                        {{ \Carbon\Carbon::parse($verificationRequest->birthdate)->format('d M Y') }}
                                    </span>
                                </span>
                                <span class="mb-2 text-xs">{{ __('admin/verifications.gender') }}:
                                    <span class="text-dark ms-sm-2 font-weight-bold">
                                        {{ $verificationRequest->gender ?? 'N/A' }}
                                    </span>
                                </span>
                                <span class="mb-2 text-xs">{{ __('admin/verifications.nik') }}:
                                    <span class="text-dark ms-sm-2 font-weight-bold">
                                        {{ $verificationRequest->nik }}
                                    </span>
                                </span>
                                <span class="mb-2 text-xs">{{ __('admin/verifications.address') }}:
                                    <span class="text-dark ms-sm-2 font-weight-bold">
                                        {{ $verificationRequest->address ?? 'N/A' }}
                                    </span>
                                </span>
                                <span class="mb-2 text-xs">{{ __('admin/verifications.bank_account_name') }}:
                                    <span class="text-dark ms-sm-2 font-weight-bold">
                                        {{ $verificationRequest->account_name ?? 'N/A' }}
                                    </span>
                                </span>
                                <span class="mb-2 text-xs">{{ __('admin/verifications.bank_account_number') }}:
                                    <span class="text-dark ms-sm-2 font-weight-bold">
                                        {{ $verificationRequest->account_number ?? 'N/A' }}
                                    </span>
                                </span>
                                <span class="text-xs">{{ __('admin/verifications.submitted_on') }}:
                                    <span class="text-dark ms-sm-2 font-weight-bold">
                                        {{ $verificationRequest->created_at->format('d M Y H:i') }}
                                    </span>
                                </span>
                                @if ($verificationRequest->status == 'approved' || $verificationRequest->status == 'rejected')
                                <span class="text-xs mt-2">{{ __('admin/verifications.updated_on') }}:
                                    <span class="text-dark ms-sm-2 font-weight-bold">
                                        {{ $verificationRequest->updated_at->format('d M Y H:i') }}
                                    </span>
                                </span>
                                @endif
                                @if ($verificationRequest->status == 'approved')
                                <span class="text-xs mt-2">{{ __('admin/verifications.verified_on') }}:
                                    <span class="text-dark ms-sm-2 font-weight-bold">
                                        {{ $verificationRequest->verified_at ? $verificationRequest->verified_at->format('d M Y H:i') : 'N/A' }}
                                    </span>
                                </span>
                                @endif
                                {{-- Menampilkan alasan penolakan jika ada --}}
                                @if ($verificationRequest->status == 'rejected' && $verificationRequest->rejection_reason)
                                <span class="text-xs mt-2">{{ __('admin/verifications.rejection_reason') }}
                                    <span class="text-danger ms-sm-2 font-weight-bold">
                                        {{ $verificationRequest->rejection_reason }}
                                    </span>
                                </span>
                                @endif
                            </div>
                            <div class="ms-auto text-end">
                                <h6 class="text-sm">{{ __('admin/verifications.status') }}:
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

                    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3 mt-4">{{ __('admin/verifications.supporting_documents') }}</h6>
                    <div class="row">
                        <div class="col-md-6 mb-md-0 mb-4">
                            <div class="card card-body border card-plain border-radius-lg d-flex flex-column justify-content-between h-100">
                                <p class="text-dark text-sm font-weight-bold">{{ __('admin/verifications.user_photo') }}</p>
                                @if ($verificationRequest->photo_url)
                                <a href="{{ Storage::url($verificationRequest->photo_url) }}" target="_blank">
                                    <img src="{{ Storage::url($verificationRequest->photo_url) }}" class="img-fluid border-radius-lg mb-3" alt="{{ __('admin/verifications.user_photo') }}">
                                </a>
                                @else
                                <p class="text-muted">{{ __('admin/verifications.no_user_photo_uploaded') }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-md-0 mb-4">
                            <div class="card card-body border card-plain border-radius-lg d-flex flex-column justify-content-between h-100">
                                <p class="text-dark text-sm font-weight-bold">{{ __('admin/verifications.id_card_photo') }}</p>
                                @if ($verificationRequest->id_card_url)
                                <a href="{{ Storage::url($verificationRequest->id_card_url) }}" target="_blank">
                                    <img src="{{ Storage::url($verificationRequest->id_card_url) }}" class="img-fluid border-radius-lg mb-3" alt="{{ __('admin/verifications.id_card_photo') }}">
                                </a>
                                @else
                                <p class="text-muted">{{ __('admin/verifications.no_id_card_photo_uploaded') }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mt-4">
                            <div class="card card-body border card-plain border-radius-lg d-flex flex-column justify-content-between h-100">
                                <p class="text-dark text-sm font-weight-bold">{{ __('admin/verifications.selfie_with_id_card_photo') }}</p>
                                @if ($verificationRequest->selfie_with_id_card_url)
                                <a href="{{ Storage::url($verificationRequest->selfie_with_id_card_url) }}" target="_blank">
                                    <img src="{{ Storage::url($verificationRequest->selfie_with_id_card_url) }}" class="img-fluid border-radius-lg mb-3" alt="{{ __('admin/verifications.selfie_with_id_card_photo') }}">
                                </a>
                                @else
                                <p class="text-muted">{{ __('admin/verifications.no_selfie_id_card_photo_uploaded') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons (only for pending requests) --}}
                    @if ($verificationRequest->status == 'pending')
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">{{ __('admin/verifications.actions') }}</h6>
                            <form action="{{ route('admin.verifications.approve', $verificationRequest->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="button" class="btn bg-gradient-success mb-0 me-2" data-bs-toggle="modal" data-bs-target="#approveConfirmationModal" id="approveButton">
                                    <i class="material-symbols-rounded text-sm">check_circle</i> {{ __('admin/verifications.approve') }}
                                </button>
                            </form>
                            {{-- Tombol Tolak memicu modal --}}
                            <button type="button" class="btn bg-gradient-danger mb-0" data-bs-toggle="modal" data-bs-target="#rejectReasonModal" id="rejectButton">
                                <i class="material-symbols-rounded text-sm">cancel</i> {{ __('admin/verifications.reject') }}
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
                            <h6 class="mb-0">{{ __('admin/verifications.user_account_details') }}</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3 pb-0">
                    <ul class="list-group">
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark font-weight-bold text-sm">{{ __('admin/verifications.user_id_colon') }}</h6>
                                <span class="text-xs">{{ $verificationRequest->user->id ?? __('admin/verifications.n_a') }}</span>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark font-weight-bold text-sm">{{ __('admin/verifications.username_colon') }}</h6>
                                <span class="text-xs">{{ $verificationRequest->user ? $verificationRequest->user->first_name . ' ' . $verificationRequest->user->last_name : __('admin/verifications.n_a') }}</span>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark font-weight-bold text-sm">{{ __('admin/verifications.role_colon') }}</h6>
                                <span class="text-xs">{{ $verificationRequest->user->role ?? __('admin/verifications.n_a') }}</span>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark font-weight-bold text-sm">{{ __('admin/verifications.saldokerjain_balance') }}</h6>
                                <span class="text-xs">Rp {{ number_format($verificationRequest->user->saldokerjain ?? 0, 2, ',', '.') }}</span>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark font-weight-bold text-sm">{{ __('admin/verifications.worker_status') }}</h6>
                                <span class="text-xs">{{ ($verificationRequest->user->is_worker ?? 0) ? __('admin/verifications.yes') : __('admin/verifications.no') }}</span>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark font-weight-bold text-sm">{{ __('admin/verifications.rating_colon') }}</h6>
                                <span class="text-xs">{{ number_format($verificationRequest->user->rating ?? 0, 2) }}</span>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg {{ ($verificationRequest->user->is_worker ?? 0) ? '' : 'd-none'}}">
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark font-weight-bold text-sm">{{ __('admin/verifications.jobs_completed') }}</h6>
                                <span class="text-xs">{{ $verificationRequest->user->job_done ?? 0 }}</span>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark font-weight-bold text-sm">{{ __('admin/verifications.account_created') }}</h6>
                                <span class="text-xs">{{ $verificationRequest->user?->created_at ? $verificationRequest->user->created_at->format('d M Y H:i') :  __('admin/verifications.n_a') }}</span>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark font-weight-bold text-sm">{{ __('admin/verifications.last_updated') }}</h6>
                                <span class="text-xs">{{ $verificationRequest->user?->updated_at ? $verificationRequest->user->updated_at->format('d M Y H:i') :  __('admin/verifications.n_a') }}</span>
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
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectReasonModalLabel">{{ __('admin/verifications.reject_user_verification') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.verifications.reject', $verificationRequest->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">{{ __('admin/verifications.reason_for_rejection') }}</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" placeholder="{{ __('admin/verifications.provide_rejection_reason_placeholder') }}" required style="resize: none; padding: 0.75em; border: 1px solid #ced4da;"></textarea>
                        @error('rejection_reason')
                        <div class="text-danger text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <p class="text-sm font-weight-bold mb-2">{{ __('admin/verifications.choose_quick_reason') }}</p>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-gradient-secondary reason-chip cursor-pointer" data-reason="{{ __('admin/verifications.blurry_id_card') }}">{{ __('admin/verifications.blurry_id_card') }}</span>
                        <span class="badge bg-gradient-secondary reason-chip cursor-pointer" data-reason="{{ __('admin/verifications.nik_mismatch') }}">{{ __('admin/verifications.nik_mismatch') }}</span>
                        <span class="badge bg-gradient-secondary reason-chip cursor-pointer" data-reason="{{ __('admin/verifications.unclear_face') }}">{{ __('admin/verifications.unclear_face') }}</span>
                        <span class="badge bg-gradient-secondary reason-chip cursor-pointer" data-reason="{{ __('admin/verifications.incorrect_selfie_id_card') }}">{{ __('admin/verifications.incorrect_selfie_id_card') }}</span>
                        <span class="badge bg-gradient-secondary reason-chip cursor-pointer" data-reason="{{ __('admin/verifications.inconsistent_data') }}">{{ __('admin/verifications.inconsistent_data') }}</span>
                        <span class="badge bg-gradient-secondary reason-chip cursor-pointer" data-reason="{{ __('admin/verifications.invalid_document_uploaded') }}">{{ __('admin/verifications.invalid_document_uploaded') }}</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="BatalButton">{{ __('admin/verifications.cancel') }}</button>
                    <button type="submit" class="btn btn-danger" id="TolakVerifikasi">{{ __('admin/verifications.reject_verification') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="approveConfirmationModal" tabindex="-1" aria-labelledby="approveConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveConfirmationModalLabel">{{ __('admin/verifications.approve_confirmation') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{!! __('admin/verifications.confirm_approve_message') !!}</p>
                <p class="text-danger">{{ __('admin/verifications.action_cannot_be_undone') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="CancelBtn">{{ __('admin/verifications.cancel') }}</button>
                <form action="{{ route('admin.verifications.approve', $verificationRequest->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn bg-gradient-success" id="yesApproveBtn">{{ __('admin/verifications.yes_approve') }}</button>
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
                    window.location.href = `{{ url('admin/verifications/show') }}/${selectedId}`;
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
                        fetch(`{{ route('admin.verifications.search-ajax') }}?query=${query}`)
                            .then(response => response.json())
                            .then(data => {
                                searchResults.innerHTML = ''; // Bersihkan hasil sebelumnya
                                if (data.length > 0) {
                                    data.forEach(item => {
                                        const a = document.createElement('a');
                                        a.href = `{{ url('admin/verifications/show') }}/${item.id}`;
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