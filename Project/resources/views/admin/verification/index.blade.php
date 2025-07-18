@extends('Master.master-admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4 text-dark">Manajemen Verifikasi Pengguna</h1>

            {{-- Mengubah nav-pills menjadi elemen yang lebih mirip tombol/page separator --}}
            <div class="row gx-2 mb-4"> {{-- Gunakan row dan gx-2 untuk spacing antar kolom --}}
                <div class="col-md-4">
                    <a class="btn btn-block {{ $status == 'pending' ? 'bg-gradient-primary text-white' : 'btn-outline-primary' }} d-flex align-items-center justify-content-center py-3"
                        href="{{ route('admin.verifications.index', ['status' => 'pending']) }}">
                        <span class="me-2">Belum Diverifikasi</span>
                        @if (isset($pendingVerificationsCount) && $pendingVerificationsCount > 0)
                        <span class="badge badge-lg bg-danger rounded-circle text-white ms-1" style="width: 25px; height: 25px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem;">
                            {{ $pendingVerificationsCount }}
                        </span>
                        @endif
                    </a>
                </div>
                <div class="col-md-4">
                    <a class="btn btn-block {{ $status == 'approved' ? 'bg-gradient-primary text-white' : 'btn-outline-primary' }} d-flex align-items-center justify-content-center py-3"
                        href="{{ route('admin.verifications.index', ['status' => 'approved']) }}">
                        Terverifikasi
                    </a>
                </div>
                <div class="col-md-4">
                    <a class="btn btn-block {{ $status == 'rejected' ? 'bg-gradient-primary text-white' : 'btn-outline-primary' }} d-flex align-items-center justify-content-center py-3"
                        href="{{ route('admin.verifications.index', ['status' => 'rejected']) }}">
                        Verifikasi Ditolak
                    </a>
                </div>
            </div>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="{{ $status }}" role="tabpanel" aria-labelledby="{{ $status }}-tab">
                    <div class="card shadow mb-4 mt-3">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Daftar Permintaan Verifikasi ({{ ucfirst($status) }})</h6>
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
                            <p class="text-dark">Tidak ada permintaan verifikasi {{ $status }} saat ini.</p>
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
                                                <a href="{{ route('admin.verifications.show', $request->id) }}" class="btn btn-primary btn-sm">Lihat Detail</a>
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