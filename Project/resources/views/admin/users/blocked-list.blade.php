@extends('Master.master-admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Daftar Pengguna Diblokir</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Nama</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Email</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">Status</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($blockedUsers as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $user->first_name }} {{ $user->last_name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $user->email }}</p>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="badge badge-sm bg-gradient-danger">Diblokir</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <a href="{{ route('admin.users.activityLog', ['id' => $user->id, 'from' => url()->full()]) }}" class="btn btn-sm btn-outline-primary mb-0 me-2">Lihat Aktivitas</a>
                                        {{-- Tombol Batal Blokir yang memicu modal --}}
                                        <button type="button" class="btn btn-sm btn-success mb-0"
                                            data-bs-toggle="modal" data-bs-target="#confirmUnblockModal"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->first_name }} {{ $user->last_name }}">
                                            Unblock
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-secondary text-sm">Tidak ada pengguna yang diblokir.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 px-4">
                        {{ $blockedUsers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Batal Blokir (Hanya ini yang dibutuhkan di sini) -->
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
        form.action = "{{ url('users') }}/" + userId + "/unblock"; // Sesuaikan rute Anda
    });
</script>
@endpush