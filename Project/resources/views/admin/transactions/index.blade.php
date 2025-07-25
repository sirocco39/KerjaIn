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
                            <h6 class="mb-0">Daftar Semua Request</h6>
                        </div>
                        <div class="col-md-6 d-flex justify-content-start justify-content-md-end align-items-center">
                            <i class="material-symbols-rounded me-2 text-lg">list_alt</i>
                            <small>Menampilkan semua request, termasuk yang sudah selesai atau dibatalkan.</small>
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
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">Harga Total</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">Biaya Layanan</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">Status Request</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Dibuat pada</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($Requests as $request) {{-- UBAH NAMA VARIABEL DI SINI --}}
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
                                        $statusDisplay = ''; // Variabel untuk menyimpan HTML status (badge)
                                        $deletedBadge = ''; // Variabel untuk menyimpan HTML badge "Dihapus"

                                        if ($request->trashed()) {
                                        // Jika request sudah dihapus, hanya tampilkan badge 'Dihapus'
                                        $deletedBadge = '<span class="badge badge-sm bg-gradient-danger ms-1">Dihapus</span>';
                                        // statusDisplay tetap kosong, sehingga badge 'open'/'closed' tidak muncul
                                        } else {
                                        // Jika request TIDAK dihapus, tampilkan status normalnya
                                        $statusClass = '';
                                        switch ($request->status) {
                                        case 'closed': $statusClass = 'bg-gradient-success'; break;
                                        case 'open': $statusClass = 'bg-gradient-info'; break;
                                        default: $statusClass = 'bg-gradient-secondary'; break;
                                        }
                                        $statusDisplay = '<span class="badge badge-sm ' . $statusClass . '">' . ucfirst($request->status) . '</span>';
                                        }
                                        @endphp
                                        {!! $statusDisplay !!}{!! $deletedBadge !!}
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{ $request->created_at->format('d M Y, H:i') }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">Tidak ada request yang ditemukan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- Tambahkan Tautan Paginasi di sini --}}
                    <div class="mt-4 px-4">
                        {{ $Requests->links() }} {{-- UBAH NAMA VARIABEL DI SINI --}}
                    </div>
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