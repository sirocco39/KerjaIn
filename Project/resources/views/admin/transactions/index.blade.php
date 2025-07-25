@extends('Master.master-admin')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="col-lg-8">
            <div class="row">
                <div class="col-xl-6 mb-xl-0 mb-4">
                    <div class="card bg-transparent shadow-xl">
                        <div class="overflow-hidden position-relative border-radius-xl">
                            <img src="{{ asset('assets/img/illustrations/pattern-tree.svg') }}"
                                class="position-absolute opacity-2 start-0 top-0 w-100 z-index-1 h-100" alt="pattern-tree">
                            <span class="mask bg-gradient-dark opacity-10"></span>
                            <div class="card-body position-relative z-index-1 p-3">
                                <i class="material-symbols-rounded text-white p-2">wifi</i>
                                <h5 class="text-white mt-4 mb-5 pb-2">
                                    3333&nbsp;&nbsp;&nbsp;4444&nbsp;&nbsp;&nbsp;7777&nbsp;&nbsp;&nbsp;1111</h5> {{-- Obscure card number for admin --}}
                                <div class="d-flex">
                                    <div class="d-flex">
                                        <div class="me-4">
                                            <p class="text-white text-sm opacity-8 mb-0">Card Holder</p>
                                            <h6 class="text-white mb-0">Admin Kerjain</h6>
                                        </div>
                                        <div>
                                            <p class="text-white text-sm opacity-8 mb-0">Expires</p>
                                            <h6 class="text-white mb-0">2027</h6>
                                        </div>
                                    </div>
                                    <div class="ms-auto w-20 d-flex align-items-end justify-content-end">
                                        <img class="w-60 mt-2" src="{{ asset('Image/Icon/Icon Kerjain.png') }}" alt="logo">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="row">
                        <div class="col-md-6 col-6">
                            <div class="card">
                                <div class="card-header mx-4 p-3 text-center">
                                    <div class="icon icon-shape icon-lg bg-gradient-primary shadow text-center border-radius-lg">
                                        <i class="material-symbols-rounded opacity-10">account_balance</i>
                                    </div>
                                </div>
                                <div class="card-body pt-0 p-3 text-center">
                                    <h6 class="text-center mb-0">Total Saldo Pengguna</h6>
                                    <span class="text-xs">Jumlah seluruh saldo yang dapat digunakan</span>
                                    <hr class="horizontal dark my-3">
                                    <h5 class="mb-0">Rp{{ number_format($totalUserBalance, 0, ',', '.') }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-6">
                            <div class="card">
                                <div class="card-header mx-4 p-3 text-center">
                                    <div class="icon icon-shape icon-lg bg-gradient-primary shadow text-center border-radius-lg">
                                        <i class="material-symbols-rounded opacity-10">account_balance_wallet</i>
                                    </div>
                                </div>
                                <div class="card-body pt-0 p-3 text-center">
                                    <h6 class="text-center mb-0">Total Saldo Tertahan</h6>
                                    <span class="text-xs">Dana yang dipegang untuk escrow pekerjaan</span>
                                    <hr class="horizontal dark my-3">
                                    <h5 class="mb-0">Rp{{ number_format($totalLockedBalance, 0, ',', '.') }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <div class="row">
                        <div class="col-6 d-flex align-items-center">
                            <h6 class="mb-0">Ringkasan Transaksi</h6>
                        </div>
                        <div class="col-6 text-end">
                            <a href="#" class="btn btn-outline-primary btn-sm mb-0">Lihat Semua</a> {{-- Link to a more detailed report if available --}}
                        </div>
                    </div>
                </div>
                <div class="card-body p-3 pb-0">
                    <ul class="list-group">
                        {{-- New card for Total Service Fee Profit --}}
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark font-weight-bold text-sm">Keuntungan Admin (Service Fee)</h6>
                                <span class="text-xs">Total dari semua biaya layanan pekerjaan</span>
                            </div>
                            <div class="d-flex align-items-center text-sm text-success font-weight-bold">
                                Rp{{ number_format($totalServiceFeeProfit, 0, ',', '.') }}
                                <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i
                                        class="material-symbols-rounded text-lg position-relative me-1">pie_chart</i> Laporan</button>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark font-weight-bold text-sm">Transaksi Terbaru</h6>
                                <span class="text-xs">Dikonfirmasi pada {{ \Carbon\Carbon::now()->format('d F Y') }}</span>
                            </div>
                            <div class="d-flex align-items-center text-sm">
                                Rp{{ number_format($latestTransactionAmount, 0, ',', '.') }}
                                <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i
                                        class="material-symbols-rounded text-lg position-relative me-1">picture_as_pdf</i> PDF</button>
                            </div>
                        </li>
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="text-dark mb-1 font-weight-bold text-sm">Jumlah Transaksi (7 hari)</h6>
                                <span class="text-xs">Total semua transaksi</span>
                            </div>
                            <div class="d-flex align-items-center text-sm">
                                {{ number_format($transactionsLast7Days) }} transaksi
                                <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4"><i
                                        class="material-symbols-rounded text-lg position-relative me-1">visibility</i> Detail</button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mt-4">
            <div class="card h-100 mb-4">
                <div class="card-header pb-0 px-3">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-0">Daftar Transaksi Wallet</h6>
                        </div>
                        <div class="col-md-6 d-flex justify-content-start justify-content-md-end align-items-center">
                            <i class="material-symbols-rounded me-2 text-lg">list_alt</i>
                            <small>Menampilkan semua transaksi terbaru</small>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-4 p-3">
                    <ul class="list-group">
                        @forelse ($transactions as $transaction)
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                {{-- Determine icon and color based on transaction type --}}
                                <button
                                    class="btn btn-icon-only btn-rounded btn-outline-{{ $transaction->type === 'debit' ? 'success' : 'danger' }} mb-0 me-3 p-3 btn-sm d-flex align-items-center justify-content-center">
                                    <i class="material-symbols-rounded text-lg">
                                        {{ $transaction->type === 'debit' ? 'arrow_downward' : 'arrow_upward' }}
                                    </i>
                                </button>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">
                                        {{ $transaction->description }}
                                        @if ($transaction->user)
                                        <span class="text-xs text-muted"> (Pengguna: **{{ $transaction->user->first_name }} {{ $transaction->user->last_name }}**)</span>
                                        @else
                                        <span class="text-xs text-muted"> (Pengguna: N/A)</span>
                                        @endif
                                    </h6>
                                    <span class="text-xs">
                                        {{ $transaction->created_at->format('d F Y, \p\u\k\u\l H:i') }} WIB
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center text-{{ $transaction->type === 'debit' ? 'success' : 'danger' }} text-gradient text-sm font-weight-bold">
                                {{ $transaction->type === 'debit' ? '+' : '-' }} Rp{{ number_format($transaction->amount, 0, ',', '.') }}
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item border-0 ps-0 mb-2 border-radius-lg">
                            <p class="text-center text-muted">Tidak ada transaksi wallet yang ditemukan.</p>
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('currentPageTitle').innerText = 'Penghasilan';
    document.getElementById('currentSectionTitle').innerText = 'Daftar Transaksi Wallet';
</script>
@endpush