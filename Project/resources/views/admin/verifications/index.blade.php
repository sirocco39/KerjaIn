@extends('Master.master-admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4 text-dark">Manajemen Verifikasi Pengguna</h1>

            {{-- Mengubah nav-pills menjadi elemen yang lebih mirip tombol/page separator --}}
            <div class="row gx-2 mb-4"> {{-- Gunakan row dan gx-2 untuk spacing antar kolom --}}
                <div class="col-md-3">
                    <a class="btn btn-block {{ ($status == 'pending' && $search == null) ? 'bg-gradient-primary text-white' : 'btn-outline-primary' }} d-flex align-items-center justify-content-center py-3"
                        href="{{ route('admin.verifications.index', ['status' => 'pending']) }}">
                        <span class="me-1">Belum Diverifikasi</span>
                        @if (isset($pendingVerificationsCount) && $pendingVerificationsCount > 0)
                        <span class="badge badge-lg bg-danger rounded-circle text-white ms-1" style="width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 0.7rem;">
                            {{ $pendingVerificationsCount }}
                        </span>
                        @endif
                    </a>
                </div>
                <div class="col-md-3">
                    <a class="btn btn-block {{ ($status == 'approved' && $search == null) ? 'bg-gradient-primary text-white' : 'btn-outline-primary' }} d-flex align-items-center justify-content-center py-3"
                        href="{{ route('admin.verifications.index', ['status' => 'approved']) }}">
                        Terverifikasi
                    </a>
                </div>
                <div class="col-md-3">
                    <a class="btn btn-block {{ ($status == 'rejected' && $search == null)  ? 'bg-gradient-primary text-white' : 'btn-outline-primary' }} d-flex align-items-center justify-content-center py-3"
                        href="{{ route('admin.verifications.index', ['status' => 'rejected']) }}">
                        Verifikasi Ditolak
                    </a>
                </div>
                {{-- Search Bar with Recommendations for Index Page --}}
                <div class="col-md-3 p-0">
                    <div class="p-0 position-relative"> {{-- Tambahkan position-relative untuk penempatan hasil pencarian --}}
                        <form id="indexSearchForm" action="{{ route('admin.verifications.index', ['status' => $status]) }}" method="GET" class="mb-0">
                            <div class="input-group rounded-start m-0">
                                <input type="text" id="userSearchIndex" name="search" class="rounded-start bg-white border border-primary p-2" placeholder="Cari pengguna" autocomplete="off" value="{{ $search ?? '' }}">
                                <button class="btn btn-primary m-2" type="submit">Cari</button>
                            </div>
                        </form>
                        <div id="searchResultsIndex" class="list-group position-absolute w-100 mt-1" style="z-index: 1000;">
                            {{-- Hasil pencarian akan ditampilkan di sini --}}
                        </div>
                    </div>
                </div>
            </div>


            <div class="tab-content">
                <div class="tab-pane fade show active" id="{{ $status }}" role="tabpanel" aria-labelledby="{{ $status }}-tab">
                    <div class="card shadow mb-4 mt-3">
                        <div class="card-header py-3">
                            @if (empty($search))
                            <h6 class="m-0 font-weight-bold text-primary">Daftar Permintaan Verifikasi ({{ ucfirst($status) }})</h6>
                            @else
                            <h6 class="m-0 font-weight-bold text-primary">Hasil Pencarian</h6>
                            @endif
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
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

                            @if ($verificationRequests->isEmpty())
                            <p class="text-dark">
                                @if (empty($search))
                                Tidak ada permintaan verifikasi {{ $status }} saat ini.
                                @else
                                Tidak ada hasil ditemukan untuk "{{ $search }}".
                                @endif
                            </p>
                            @else
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>ID</th>
                                            <th>User_ID</th>
                                            <th>Nama Pemohon</th>
                                            <th>NIK</th>
                                            <th>Status</th>
                                            <th>Diajukan Pada</th>
                                            @if ($status == 'approved')
                                            <th>Diverifikasi Pada</th>
                                            @endif
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($verificationRequests as $request)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $request->id }}</td>
                                            <td>{{ $request->user_id }}</td>
                                            <td>
                                                {{ $request->first_name }} {{ $request->last_name }}
                                            </td>
                                            <td>{{ $request->nik }}</td>
                                            <td>{{ ucfirst($request->status) }}</td>
                                            <td>{{ $request->created_at->format('d M Y H:i') }}</td>
                                            @if ($status == 'approved')
                                            <td>{{ $request->verified_at ? $request->verified_at->format('d M Y H:i') : '-' }}</td>
                                            @endif
                                            <td>
                                                {{-- Pass the current search term when linking to show page --}}
                                                <a href="{{ route('admin.verifications.show', ['id' => $request->id]) }}" class="btn btn-primary btn-sm">Lihat Detail</a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Logic for Rejection Reason Chips (from previous implementation) ---
        const reasonChips = document.querySelectorAll('.reason-chip');
        const rejectionReasonTextarea = document.getElementById('rejection_reason');

        if (rejectionReasonTextarea) { // Only run if the element exists (i.e., on show page)
            reasonChips.forEach(chip => {
                chip.addEventListener('click', function() {
                    const reason = this.dataset.reason;
                    if (rejectionReasonTextarea.value.trim() === '') {
                        rejectionReasonTextarea.value = reason;
                    } else {
                        if (!rejectionReasonTextarea.value.includes(reason)) {
                            rejectionReasonTextarea.value += '\n' + reason;
                        }
                    }
                    rejectionReasonTextarea.focus();
                });
            });

            const rejectReasonModal = document.getElementById('rejectReasonModal');
            if (rejectReasonModal) {
                rejectReasonModal.addEventListener('hidden.bs.modal', function() {
                    rejectionReasonTextarea.value = '';
                });
            }
        }

        // --- JavaScript for Search Recommendations on INDEX Page ---
        const userSearchIndex = document.getElementById('userSearchIndex');
        const searchResultsIndex = document.getElementById('searchResultsIndex');
        const indexSearchForm = document.getElementById('indexSearchForm');
        let searchTimeoutIndex;

        if (userSearchIndex) {
            userSearchIndex.addEventListener('input', function() {
                clearTimeout(searchTimeoutIndex);
                const query = this.value;

                if (query.length > 2) { // Mulai mencari setelah 2 karakter
                    searchTimeoutIndex = setTimeout(() => {
                        fetch(`{{ route('admin.verifications.search-ajax') }}?query=${query}`)
                            .then(response => response.json())
                            .then(data => {
                                searchResultsIndex.innerHTML = ''; // Bersihkan hasil sebelumnya
                                if (data.length > 0) {
                                    data.forEach(item => {
                                        const a = document.createElement('a');
                                        // Link ke halaman show saat rekomendasi dipilih
                                        a.href = `{{ route('admin.verifications.show', '') }}/${item.id}`;
                                        a.classList.add('list-group-item', 'list-group-item-action');
                                        a.innerHTML = `<strong>ID: ${item.id}</strong> - ${item.first_name} ${item.last_name} (NIK: ${item.nik}) <span class="badge bg-secondary ms-2">${item.status.charAt(0).toUpperCase() + item.status.slice(1)}</span>`;
                                        searchResultsIndex.appendChild(a);
                                    });
                                } else {
                                    searchResultsIndex.innerHTML = '<div class="list-group-item">Tidak ada hasil ditemukan.</div>';
                                }
                                searchResultsIndex.style.display = 'block'; // Tampilkan hasil
                            })
                            .catch(error => {
                                console.error('Error fetching search results:', error);
                                searchResultsIndex.innerHTML = '<div class="list-group-item text-danger">Terjadi kesalahan saat mencari.</div>';
                                searchResultsIndex.style.display = 'block';
                            });
                    }, 300); // Debounce 300ms
                } else {
                    searchResultsIndex.innerHTML = ''; // Bersihkan jika kueri terlalu pendek
                    searchResultsIndex.style.display = 'none'; // Sembunyikan hasil
                }
            });

            // Sembunyikan hasil pencarian saat mengklik di luar input atau hasil
            document.addEventListener('click', function(event) {
                if (!userSearchIndex.contains(event.target) && !searchResultsIndex.contains(event.target)) {
                    searchResultsIndex.innerHTML = '';
                    searchResultsIndex.style.display = 'none';
                }
            });

            // Handle Enter key: submit form (stay on index)
            userSearchIndex.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault(); // Mencegah submit default dari form
                    indexSearchForm.submit(); // Submit form secara manual
                }
            });
        }

        // --- JavaScript for Navigation Dropdown on SHOW Page (from show_view_with_navigation_and_search) ---
        const statusFilteredUserDropdown = document.getElementById('statusFilteredUserDropdown');
        if (statusFilteredUserDropdown) {
            statusFilteredUserDropdown.addEventListener('change', function() {
                const selectedId = this.value;
                if (selectedId) {
                    window.location.href = `{{ route('admin.verifications.show', '') }}/${selectedId}`;
                }
            });
        }

        // --- JavaScript for Search Recommendations on SHOW Page (from show_view_with_navigation_and_search) ---
        // Note: This part is already in show.blade.php.
        // If this script block is included in master-admin.blade.php and loaded on both index and show,
        // you might need to ensure IDs are unique or wrap them in conditions to prevent conflicts.
        // For simplicity, I'm keeping them separate here, assuming they are loaded only on their respective pages.
        // If you load this script on master-admin, consider wrapping the show-specific logic:
        // if (document.getElementById('userSearchShow')) { ... }
    });
</script>
@endpush