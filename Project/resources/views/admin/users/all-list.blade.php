@extends('Master.master-admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Daftar Semua Pengguna</h6>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Nama</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Email</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">Peran</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">Status</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
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
                                    <td class="align-middle text-center text-sm">
                                        <span class="badge badge-sm bg-gradient-{{ $user->is_worker ? 'info' : 'primary' }}">
                                            {{ $user->is_worker ? 'Pekerja' : 'Pengguna Biasa' }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="badge badge-sm bg-gradient-{{ $user->is_blocked ? 'danger' : 'success' }}">
                                            {{ $user->is_blocked ? 'Diblokir' : 'Aktif' }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <a href="{{ route('admin.users.activityLog', $user->id) }}" class="btn btn-sm btn-outline-primary mb-0">Lihat Aktivitas</a>
                                        {{-- Tambahkan tombol aksi lain jika diperlukan, misal edit, block/unblock --}}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-secondary text-sm">Tidak ada pengguna ditemukan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 px-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection