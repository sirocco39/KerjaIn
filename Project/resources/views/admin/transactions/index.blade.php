@extends('Master.master-admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        {{-- Card Keuntungan Perusahaan dari Service Fee --}}
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-symbols-rounded opacity-10">paid</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Keuntungan Perusahaan</p>
                        <h4 class="mb-0">Rp{{ number_format($totalCompanyProfitFromServiceFee, 0, ',', '.') }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0">
                        <span class="text-success text-sm font-weight-bolder">dari Biaya Layanan</span>
                        dari pekerjaan yang diselesaikan.
                    </p>
                </div>
            </div>
        </div>

        {{-- Card Total Saldo Pengguna --}}
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-symbols-rounded opacity-10">account_balance</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Saldo Aktif Pengguna</p>
                        <h4 class="mb-0">Rp{{ number_format($totalUserBalance, 0, ',', '.') }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0">Jumlah seluruh saldo yang dapat digunakan oleh user.</p>
                </div>
            </div>
        </div>

        {{-- Card Total Saldo Tertahan --}}
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-symbols-rounded opacity-10">payments</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Saldo Tertahan</p>
                        <h4 class="mb-0">Rp{{ number_format($totalLockedBalance, 0, ',', '.') }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0">Dana yang dipegang sebagai escrow untuk pekerjaan.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card h-100 mb-4">
                <div class="card-header pb-0 px-3">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-0">Daftar Request yang Selesai (Penghasil Service Fee)</h6>
                        </div>
                        <div class="col-md-6 d-flex justify-content-start justify-content-md-end align-items-center">
                            <i class="material-symbols-rounded me-2 text-lg">check_circle</i>
                            <small>Pekerjaan terbaru yang telah diselesaikan dan menghasilkan service fee.</small>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-4 p-3">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">ID Request</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Layanan</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Client</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Worker</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">Harga</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">Biaya Layanan</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">Status Request</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Selesai Pada</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentCompletedRequests as $request)
                                <tr>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">#{{ $request->id }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $request->title }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $request->requester->first_name ?? 'N/A' }} {{ $request->requester->last_name ?? '' }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $request->transaction->worker->first_name ?? 'N/A' }} {{ $request->transaction->worker->last_name ?? '' }}</p>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        Rp{{ number_format($request->final_price, 0, ',', '.') }}
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="text-success text-gradient font-weight-bold">Rp{{ number_format($request->service_fee, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        @php
                                        $statusClass = '';
                                        switch ($request->status) {
                                        case 'closed': $statusClass = 'bg-gradient-success'; break;
                                        case 'open': $statusClass = 'bg-gradient-info'; break;
                                        default: $statusClass = 'bg-gradient-secondary'; break;
                                        }
                                        @endphp
                                        <span class="badge badge-sm {{ $statusClass }}">{{ ucfirst($request->status) }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            @if($request->status == 'closed')
                                            {{ $request->updated_at->format('d M Y, H:i') }}
                                            @else
                                            -
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">Tidak ada request yang telah selesai ditemukan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- Anda bisa menambahkan paginasi di sini jika $recentCompletedRequests adalah paginator --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('currentPageTitle').innerText = 'Keuangan Perusahaan';
    document.getElementById('currentSectionTitle').innerText = 'Dashboard Keuangan';
</script>
@endpush