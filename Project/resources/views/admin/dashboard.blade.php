@extends('Master.master-admin')
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-symbols-rounded opacity-10">groups</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Pengguna</p>
                        <h4 class="mb-0">{{ $totalUsers ?? 0 }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+55% </span>dari minggu lalu</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-symbols-rounded opacity-10">account_balance_wallet</i> {{-- Ikon diganti menjadi dompet --}}
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Saldo Kerjain</p> {{-- Teks diganti --}}
                        <h4 class="mb-0">Rp{{ number_format($totalUserSaldokerjain ?? 0, 2, ',', '.') }}</h4> {{-- Menampilkan saldo dengan format mata uang --}}
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-success text-sm font-weight-bolder"></span>Keseluruhan saldo pengguna</p> {{-- Teks deskriptif disesuaikan --}}
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-secondary shadow-secondary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-symbols-rounded opacity-10">request_quote</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Permintaan</p>
                        <h4 class="mb-0">{{ $totalRequests ?? 0 }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-danger text-sm font-weight-bolder">-2% </span>dari kemarin</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-symbols-rounded opacity-10">receipt_long</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">Total Transaksi</p>
                        <h4 class="mb-0">{{ $totalTransactions ?? 0 }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+5% </span>dari kemarin</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <div class="row">
                        <div class="col-6 d-flex align-items-center">
                            <h6 class="mb-0">Laporan Pengguna Terbaru</h6>
                        </div>
                        <div class="col-6 text-end">
                            <a href="#" class="btn btn-link text-dark text-gradient px-3 mb-0">Lihat Semua</a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group">
                        @forelse($latestReports as $report)
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon-shape icon-sm me-3 bg-gradient-danger shadow text-center">
                                    <i class="material-symbols-rounded text-white opacity-10">warning</i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">Laporan dari ID {{ $report->reporter_id }}</h6>
                                    <span class="text-xs">{{ Str::limit($report->reasons, 50) }} - Status: {{ $report->status }}</span>
                                </div>
                            </div>
                            <div class="d-flex">
                                <a href="#" class="btn btn-link btn-icon-only btn-rounded btn-sm text-dark icon-move-right my-auto"><i class="ni ni-bold-right" aria-hidden="true"></i></a>
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item border-0 ps-0 text-dark text-sm">Tidak ada laporan terbaru.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <div class="row">
                        <div class="col-6 d-flex align-items-center">
                            <h6 class="mb-0">Pengguna Terbaru</h6>
                        </div>
                        <div class="col-6 text-end">
                            <a href="#" class="btn btn-link text-dark text-gradient px-3 mb-0">Lihat Semua</a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group">
                        @forelse($latestUsers as $user)
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <div class="icon-shape icon-sm me-3 bg-gradient-primary shadow text-center">
                                    <i class="material-symbols-rounded text-white opacity-10">person</i>
                                </div>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">{{ $user->first_name }} {{ $user->last_name }}</h6>
                                    <span class="text-xs">{{ $user->email }} - Role: {{ $user->role }}</span>
                                </div>
                            </div>
                            <div class="d-flex">
                                <a href="#" class="btn btn-link btn-icon-only btn-rounded btn-sm text-dark icon-move-right my-auto"><i class="ni ni-bold-right" aria-hidden="true"></i></a>
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item border-0 ps-0 text-dark text-sm">Tidak ada pengguna terbaru.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

</main>
@endsection