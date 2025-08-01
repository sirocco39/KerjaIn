@extends('Master.master-admin')

@section('content')
<div class="container-fluid py-4">
    <h1 class="mb-4">{{ __('admin/dashboard.dasbor') }}</h1>
    
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if (session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    
    <div class="row">
        
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <a href="{{ route('admin.users.all') }}" class="card-link">
                <div class="card" id="totalUsersCard">
                    <div class="card-header p-3 pt-2">
                        <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                            <i class="material-symbols-rounded opacity-10">groups</i>
                        </div>
                        <div class="text-end pt-1">
                            <p class="text-sm mb-0 text-capitalize">{{ __('admin/dashboard.total_pengguna') }}</p>
                            <h4 class="mb-0">{{ number_format($totalUsers) }}</h4>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer p-3">
                        <p class="mb-0 text-dark">
                            <span class="text-success text-sm font-weight-bolder">{{ $activeUsersToday }}</span> {{ __('admin/dashboard.aktif_hari_ini') }}
                        </p>
                    </div>
                </div>
            </a>
        </div>

        
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <a href="{{ class_exists('App\Models\VerificationRequest') ? route('admin.verifications.index', ['status' => 'pending']) : '#' }}" class="card-link" id="pendingVerificationsCard">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                            <i class="material-symbols-rounded opacity-10">how_to_reg</i>
                        </div>
                        <div class="text-end pt-1">
                            <p class="text-sm mb-0 text-capitalize">{{ __('admin/dashboard.verifikasi_pending') }}</p>
                            <h4 class="mb-0">{{ number_format($pendingVerificationsCount) }}</h4>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer p-3">
                        <p class="mb-0 text-dark">
                            {{ __('admin/dashboard.klik_tinjau_verifikasi') }}
                        </p>
                    </div>
                </div>
            </a>
        </div>

        
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <a href="{{ route('admin.reports.index', ['status' => 'Not Reviewed']) }}" class="card-link" id="pendingReportsCard">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div class="icon icon-lg icon-shape bg-gradient-danger shadow-danger text-center border-radius-xl mt-n4 position-absolute">
                            <i class="material-symbols-rounded opacity-10">flag</i>
                        </div>
                        <div class="text-end pt-1">
                            <p class="text-sm mb-0 text-capitalize">{{ __('admin/dashboard.laporan_pending') }}</p>
                            <h4 class="mb-0">{{ number_format($pendingReportsCount) }}</h4>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer p-3">
                        <p class="mb-0 text-dark">{{ __('admin/dashboard.klik_tinjau_laporan') }}</p>
                    </div>
                </div>
            </a>
        </div>

        
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <a href="{{ route('admin.transactions.index') }}" class="card-link" id="companyProfitCard">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                            <i class="material-symbols-rounded opacity-10">paid</i>
                        </div>
                        <div class="text-end pt-1">
                            <p class="text-sm mb-0 text-capitalize">{{ __('admin/dashboard.keuntungan_perusahaan') }}</p>
                            <h4 class="mb-0">Rp{{ number_format($totalCompanyProfit, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer p-3">
                        <p class="mb-0 text-dark">{{ __('admin/dashboard.klik_detail_keuangan') }}</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    
    <div class="row mt-4">
        
        <div class="col-lg-7 mb-lg-0 mb-4">
            <div class="card z-index-2">
                <div class="card-header pb-0 bg-transparent">
                    <h6 class="mb-0">{{ __('admin/dashboard.aktivitas_pengguna_mingguan') }}</h6>
                    <p class="text-sm mb-0">
                        <i class="fa fa-arrow-up text-success"></i>
                        <span class="font-weight-bold">{{ __('admin/dashboard.jumlah_pengguna_aktif') }}</span> {{ __('admin/dashboard.dalam_7_hari_terakhir') }}
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        
                        <canvas id="weekly-active-users-chart" class="chart-canvas" height="300"
                            data-chart-labels='@json($chartLabels)'
                            data-chart-earnings='@json($chartData)' 
                            data-chart-jobs='@json($jobsCompletedData)'> 
                        </canvas>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-lg-5">
            <div class="card z-index-2 h-100">
                <div class="card-header pb-0 bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">{{ __('admin/dashboard.log_aktivitas_terbaru') }}</h6>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary mb-0" id="lihat-log">{{ __('admin/dashboard.lihat_semua_log') }}</a>
                    </div>
                    <p class="text-sm mb-0">
                        <i class="fa fa-info-circle text-info"></i>
                        <span class="font-weight-bold">{{ __('admin/dashboard.daftar_aktivitas_terkini') }}</span> {{ __('admin/dashboard.di_sistem') }}
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">{{ __('admin/dashboard.waktu') }}</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">{{ __('admin/dashboard.pengguna') }}</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">{{ __('admin/dashboard.aktivitas') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActivities as $log)
                                <tr>
                                    <td class="align-middle">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $log->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">
                                            {{ $log->causer ? $log->causer->first_name . ' ' . $log->causer->last_name : 'Sistem' }}
                                        </p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $log->description }}</p>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-secondary text-sm">Tidak ada log aktivitas terbaru.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')