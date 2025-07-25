@extends('Master.master-admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Log Aktivitas untuk {{ $user->first_name }} {{ $user->last_name }}</h6>
                    <div class="d-flex">
                        @if($user->is_blocked)
                        <button type="button" class="btn btn-sm btn-success mb-0 me-2"
                            data-bs-toggle="modal" data-bs-target="#confirmUnblockModal"
                            data-user-id="{{ $user->id }}"
                            data-user-name="{{ $user->first_name }} {{ $user->last_name }}">
                            <i class="material-symbols-rounded text-sm">lock_open</i> Batal Blokir
                        </button>
                        @else
                        <button type="button" class="btn btn-sm btn-warning mb-0 me-2"
                            data-bs-toggle="modal" data-bs-target="#confirmBlockModal"
                            data-user-id="{{ $user->id }}"
                            data-user-name="{{ $user->first_name }} {{ $user->last_name }}">
                            <i class="material-symbols-rounded text-sm">lock</i> Blokir Pengguna
                        </button>
                        @endif
                        <a href="{{ $previousUrl }}" class="btn btn-sm btn-secondary mb-0">Kembali</a>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Waktu</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Pengguna</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Target</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Deskripsi</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Log Nama</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Properti</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activities as $activity)
                                <tr>
                                    <td class="align-middle">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $activity->causer ? $activity->causer->first_name . ' ' . $activity->causer->last_name : 'N/A'  }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $activity->subject ? $activity->subject->first_name . ' ' . $activity->subject->last_name : 'N/A'  }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $activity->created_at->format('d M Y, H:i:s') }}</span>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $activity->description }}</p>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $activity->log_name }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <pre class="text-secondary text-xs mb-0" style="white-space: pre-wrap; word-break: break-all;">{{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}</pre>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-secondary text-sm">Tidak ada log aktivitas untuk pengguna ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 px-4">
                        {{ $activities->links() }}
                    </div>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="blockUserForm" action="" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-warning">Ya, Blokir</button>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="unblockUserForm" action="" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-success">Ya, Batal Blokir</button>
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