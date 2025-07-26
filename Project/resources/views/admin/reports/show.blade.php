@extends('Master.master-admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-10 col-md-12 mx-auto">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Detail Laporan #{{ $report->id }}</h6>
                    <a href="{{ route('admin.reports.index', ['status' => $report->status]) }}" class="btn btn-sm btn-secondary mb-0" id="KembaliBtn">Kembali ke Daftar Laporan</a>
                </div>
                <div class="card-body">
                    {{-- Informasi Dasar Laporan --}}
                    <h5 class="mb-3">Informasi Laporan</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>ID Laporan:</strong> #{{ $report->id }}</p>
                            <p class="mb-1"><strong>Tanggal Laporan:</strong> {{ $report->created_at->format('F j, Y, H:i') }}</p>
                            <p class="mb-1"><strong>Status:</strong> <span class="badge {{ $report->status == 'Reviewed' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $report->status }}</span></p>
                            @if ($report->transaction_id)
                            <p class="mb-1"><strong>ID Transaksi:</strong> {{ $report->transaction_id }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Pelapor:</strong> {{ $report->reporter->first_name . ' ' . $report->reporter->last_name ?? 'N/A' }} (ID: {{ $report->reporter_id }})</p>
                            <p class="mb-1"><strong>Email Pelapor:</strong> {{ $report->reporter->email ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Pengguna Dilaporkan:</strong> {{ $report->reported->first_name . ' ' . $report->reported->last_name ?? 'N/A' }} (ID: {{ $report->reported_id }})</p>
                            <p class="mb-1"><strong>Email Pengguna Dilaporkan:</strong> {{ $report->reported->email ?? 'N/A' }}</p>
                            {{-- Tampilkan riwayat laporan di sini --}}
                            @if($report->reported)
                            <p class="mb-1">
                                <strong>Total Laporan Diterima:</strong>
                                <span class="badge bg-danger">{{ $report->reported->reportsReceived->count() }} kali</span>
                            </p>
                            @endif
                        </div>
                    </div>

                    <hr>

                    {{-- Alasan Laporan --}}
                    <h5 class="mt-4 mb-3">Alasan Laporan</h5>
                    <p class="alert alert-light">{{ $report->reasons }}</p>

                    {{-- Bukti Foto (jika ada) --}}
                    @if ($report->photo_url)
                    @php
                    $photoPaths = json_decode($report->photo_url, true); // Decode to an associative array
                    @endphp

                    @if (!empty($photoPaths) && is_array($photoPaths) && isset($photoPaths[0]))
                    <h5 class="mt-4 mb-3">Bukti Foto</h5>
                    <div class="text-center mb-4">
                        {{-- Ambil elemen pertama dari array dan bersihkan '/storage/' prefix jika ada --}}
                        @php
                        $actualPathInStorage = str_replace('/storage/', '', $photoPaths[0]);
                        @endphp
                        <a href="{{ Storage::url($actualPathInStorage) }}" target="_blank">
                            <img src="{{ Storage::url($actualPathInStorage) }}" class="img-fluid border-radius-lg mb-3" alt="Foto Bukti">
                        </a>
                    </div>
                    @else
                    <p class="text-muted">Tidak ada bukti foto dilampirkan.</p>
                    @endif
                    @endif
                    <!-- {{-- Bukti Foto (jika ada) --}}
                    @if ($report->photo_url)
                    <h5 class="mt-4 mb-3">Bukti Foto</h5>
                    <div class="text-center mb-4">
                        <a href="{{ Storage::url($report->photo_url) }}" target="_blank">
                            <img src="{{ Storage::url($report->photo_url) }}" class="img-fluid border-radius-lg mb-3" alt="Foto KTP">
                        </a>
                    </div>

                    @endif -->

                    <hr>

                    {{-- Aksi Cepat --}}
                    <h5 class="mt-4 mb-3">Aksi Terkait Pengguna</h5>
                    <div class="d-flex flex-wrap gap-2">
                        {{-- Lihat Aktivitas Reporter --}}
                        <a href="{{ route('admin.users.activityLog', ['user' => $report->reporter_id, 'from' => url()->full()]) }}" class="btn btn-sm btn-outline-primary mb-2 me-2" id="lihat-aktivitas-pelapor">
                            <i class="material-symbols-rounded text-sm me-1">history</i> Lihat Aktivitas Pelapor
                        </a>

                        {{-- Lihat Aktivitas Reported User --}}
                        <a href="{{ route('admin.users.activityLog', ['user' => $report->reported_id, 'from' => url()->full()]) }}" class="btn btn-sm btn-outline-info mb-2 me-2" id="lihat-aktivitas-pengguna-dilaporkan">
                            <i class="material-symbols-rounded text-sm me-1">manage_accounts</i> Lihat Aktivitas Pengguna Dilaporkan
                        </a>

                        {{-- Tombol Blokir / Batal Blokir untuk Reported User --}}
                        @if($report->reported && $report->reported->is_blocked)
                        <button type="button" class="btn btn-sm btn-success mb-2 me-2"
                            data-bs-toggle="modal" data-bs-target="#confirmUnblockModal"
                            data-user-id="{{ $report->reported_id }}"
                            data-user-name="{{ $report->reported->first_name }} {{ $report->reported->last_name }}"
                            id="unblockUserButton">
                            <i class="material-symbols-rounded text-sm me-1">lock_open</i> Batal Blokir Pengguna Dilaporkan
                        </button>
                        @else
                        <button type="button" class="btn btn-sm btn-warning mb-2 me-2"
                            data-bs-toggle="modal" data-bs-target="#confirmBlockModal"
                            data-user-id="{{ $report->reported_id }}"
                            data-user-name="{{ $report->reported->first_name }} {{ $report->reported->last_name }}"
                            id="blockUserButton">
                            <i class="material-symbols-rounded text-sm me-1">lock</i> Blokir Pengguna Dilaporkan
                        </button>
                        @endif
                    </div>

                    <hr>

                    {{-- Aksi Mark as Reviewed/Not Reviewed --}}
                    <h5 class="mt-4 mb-3">Status Laporan</h5>
                    <form action="{{ route('admin.reports.update', $report) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        @if ($report->status == 'Not Reviewed')
                        <input type="hidden" name="status" value="Reviewed">
                        <button type="submit" class="btn btn-success btn-lg mt-3" id="tandai-sudah-ditinjau">
                            <i class="material-symbols-rounded text-sm me-1">check_circle</i> Tandai sebagai Ditinjau
                        </button>
                        @else
                        <input type="hidden" name="status" value="Not Reviewed">
                        <button type="submit" class="btn btn-secondary btn-lg mt-3" id="tandai-belum-ditinjau">
                            <i class="material-symbols-rounded text-sm me-1">remove_done</i> Tandai sebagai Belum Ditinjau
                        </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmBlockModal" tabindex="-1" role="dialog" aria-labelledby="confirmBlockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmBlockModalLabel">Konfirmasi Blokir Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Anda akan **memblokir** pengguna <strong id="blockUserName"></strong>. Pengguna ini tidak akan bisa login atau mengakses layanan.</p>
                <p>Apakah Anda yakin ingin melanjutkan?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="Batalbtn">Batal</button>
                <form id="blockUserForm" action="" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-warning" id="BlokirPgn">Ya, Blokir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmUnblockModal" tabindex="-1" role="dialog" aria-labelledby="confirmUnblockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmUnblockModalLabel">Konfirmasi Batal Blokir Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Anda akan **membatalkan blokir** pengguna <strong id="unblockUserName"></strong>. Pengguna ini akan bisa login dan mengakses layanan kembali.</p>
                <p>Apakah Anda yakin ingin melanjutkan?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="batalBatalBlokir">Batal</button>
                <form id="unblockUserForm" action="" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-success" id="BatalBlokir">Ya, Batal Blokir</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Script untuk Modal Blokir
    document.getElementById('confirmBlockModal').addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget; // Button that triggered the modal
        var userId = button.getAttribute('data-user-id');
        var userName = button.getAttribute('data-user-name');

        var modalTitle = this.querySelector('.modal-title');
        var modalBodyUserName = this.querySelector('#blockUserName');
        var form = this.querySelector('#blockUserForm');

        modalTitle.textContent = 'Konfirmasi Blokir Pengguna';
        modalBodyUserName.textContent = userName;
        form.action = "{{ url('admin/users') }}/" + userId + "/block"; // Sesuaikan rute Anda
    });

    // Script untuk Modal Batal Blokir
    document.getElementById('confirmUnblockModal').addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget; // Button that triggered the modal
        var userId = button.getAttribute('data-user-id');
        var userName = button.getAttribute('data-user-name');

        var modalTitle = this.querySelector('.modal-title');
        var modalBodyUserName = this.querySelector('#unblockUserName');
        var form = this.querySelector('#unblockUserForm');

        modalTitle.textContent = 'Konfirmasi Batal Blokir Pengguna';
        modalBodyUserName.textContent = userName;
        form.action = "{{ url('admin/users') }}/" + userId + "/unblock"; // Sesuaikan rute Anda
    });
</script>
@endpush