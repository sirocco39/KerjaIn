@extends('Master.master-admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Log Aktivitas untuk {{ $user->first_name }} {{ $user->last_name }}</h6>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-secondary mb-0">Kembali</a>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Waktu</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Deskripsi</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Log Nama</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Properti</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activities as $activity)
                                <tr>
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
@endsection