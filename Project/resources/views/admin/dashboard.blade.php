@extends('Master.master-admin')

@section('content')
<div class="container-fluid py-4">
    <h1 class="mb-4">Dashboard Admin</h1>

    {{-- Alert Messages (tetap) --}}
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

    {{-- Row 1: Statistik Utama --}}
    <div class="row">
        {{-- Total Pengguna --}}
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <a href="{{ route('admin.users.all') }}" class="card-link">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                            <i class="material-symbols-rounded opacity-10">groups</i>
                        </div>
                        <div class="text-end pt-1">
                            <p class="text-sm mb-0 text-capitalize">Total Pengguna</p>
                            <h4 class="mb-0">{{ number_format($totalUsers) }}</h4>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer p-3">
                        <p class="mb-0 text-dark">
                            <span class="text-success text-sm font-weight-bolder">{{ $activeUsersToday }}</span> aktif hari ini
                        </p>
                    </div>
                </div>
            </a>
        </div>

        {{-- JUMLAH VERIFIKASI PENDING --}}
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <a href="{{ class_exists('App\Models\VerificationRequest') ? route('admin.verifications.index', ['status' => 'pending']) : '#' }}" class="card-link">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                            <i class="material-symbols-rounded opacity-10">how_to_reg</i>
                        </div>
                        <div class="text-end pt-1">
                            <p class="text-sm mb-0 text-capitalize">Verifikasi Pending</p>
                            <h4 class="mb-0">{{ number_format($pendingVerificationsCount) }}</h4>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer p-3">
                        <p class="mb-0 text-dark">
                            Klik untuk meninjau verifikasi
                        </p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Laporan Pending --}}
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <a href="{{ route('admin.reports.index', ['status' => 'Not Reviewed']) }}" class="card-link">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div class="icon icon-lg icon-shape bg-gradient-danger shadow-danger text-center border-radius-xl mt-n4 position-absolute">
                            <i class="material-symbols-rounded opacity-10">flag</i>
                        </div>
                        <div class="text-end pt-1">
                            <p class="text-sm mb-0 text-capitalize">Laporan Pending</p>
                            <h4 class="mb-0">{{ number_format($pendingReportsCount) }}</h4>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer p-3">
                        <p class="mb-0 text-dark">Klik untuk meninjau laporan</p>
                    </div>
                </div>
            </a>
        </div>

        {{-- Keuntungan Perusahaan --}}
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <a href="{{ route('admin.transactions.index') }}" class="card-link">
                <div class="card">
                    <div class="card-header p-3 pt-2">
                        <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                            <i class="material-symbols-rounded opacity-10">paid</i>
                        </div>
                        <div class="text-end pt-1">
                            <p class="text-sm mb-0 text-capitalize">Keuntungan Perusahaan</p>
                            <h4 class="mb-0">Rp{{ number_format($totalCompanyProfit, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    <div class="card-footer p-3">
                        <p class="mb-0 text-dark">Klik untuk detail keuangan</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Row 2: Grafik Aktivitas & Log Terbaru --}}
    <div class="row mt-4">
        {{-- Grafik Pengguna Aktif Mingguan --}}
        <div class="col-lg-7 mb-lg-0 mb-4">
            <div class="card z-index-2">
                <div class="card-header pb-0 bg-transparent">
                    <h6 class="mb-0">Aktivitas Pengguna Mingguan</h6>
                    <p class="text-sm mb-0">
                        <i class="fa fa-arrow-up text-success"></i>
                        <span class="font-weight-bold">Jumlah pengguna aktif</span> dalam 7 hari terakhir.
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        {{-- DATA ATRIBUT PADA CANVAS --}}
                        <canvas id="weekly-active-users-chart" class="chart-canvas" height="300"
                            data-chart-labels='@json($chartLabels)'
                            data-chart-earnings='@json($chartData)' {{-- $chartData adalah data aktivitas login --}}
                            data-chart-jobs='@json($jobsCompletedData)'> {{-- $jobsCompletedData adalah data dummy --}}
                        </canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabel Log Aktivitas Terbaru --}}
        <div class="col-lg-5">
            <div class="card z-index-2 h-100">
                <div class="card-header pb-0 bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Log Aktivitas Terbaru</h6>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary mb-0">Lihat Semua Log</a>
                    </div>
                    <p class="text-sm mb-0">
                        <i class="fa fa-info-circle text-info"></i>
                        <span class="font-weight-bold">Daftar aktivitas terkini</span> di sistem.
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Waktu</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Pengguna</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Aktivitas</th>
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
{{-- Hapus <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> dari sini --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hapus baris ini karena breadcrumbs sudah diatur oleh controller
        // document.getElementById('currentPageTitle').innerText = 'Dashboard';
        // document.getElementById('currentSectionTitle').innerText = 'Gambaran Umum';

        // --- Weekly Active Users Line Chart (dengan pengambilan data dari data-attributes) ---
        const chartElement = document.getElementById("weekly-active-users-chart");
        if (chartElement) { // Pastikan elemen chart ada
            const labels = JSON.parse(chartElement.dataset.chartLabels);
            console.log("Labels:", labels); // Untuk debugging

            const earningsData = JSON.parse(chartElement.dataset.chartEarnings); // Ini adalah data login (chartData)
            console.log("Earnings Data (Active Users):", earningsData); // Untuk debugging

            const jobsCompletedData = JSON.parse(chartElement.dataset.chartJobs); // Ini adalah data dummy (jobsCompletedData)
            console.log("Jobs Completed Data (Dummy):", jobsCompletedData); // Untuk debugging

            var weeklyActiveUsersCtx = chartElement.getContext("2d");
            new Chart(weeklyActiveUsersCtx, {
                type: "line",
                data: {
                    labels: labels, // Menggunakan variabel `labels`
                    datasets: [{
                        label: "Pengguna Aktif",
                        tension: 0.4,
                        borderWidth: 0,
                        pointRadius: 5,
                        pointBackgroundColor: "#4CAF50", // Green color
                        pointBorderColor: "transparent",
                        borderColor: "#4CAF50", // Green color
                        borderWidth: 4,
                        backgroundColor: "transparent",
                        fill: true,
                        data: earningsData, // Menggunakan `earningsData` untuk data chart
                        maxBarThickness: 6
                    }],
                    // Jika Anda ingin menambahkan jobsCompletedData sebagai garis kedua:
                    // datasets: [
                    //     {
                    //         label: "Pengguna Aktif",
                    //         tension: 0.4,
                    //         borderWidth: 0,
                    //         pointRadius: 5,
                    //         pointBackgroundColor: "#4CAF50",
                    //         pointBorderColor: "transparent",
                    //         borderColor: "#4CAF50",
                    //         borderWidth: 4,
                    //         backgroundColor: "transparent",
                    //         fill: true,
                    //         data: earningsData,
                    //         maxBarThickness: 6
                    //     },
                    //     {
                    //         label: "Pekerjaan Selesai", // Label untuk garis kedua
                    //         tension: 0.4,
                    //         borderWidth: 0,
                    //         pointRadius: 5,
                    //         pointBackgroundColor: "#FFC107", // Contoh warna kuning
                    //         pointBorderColor: "transparent",
                    //         borderColor: "#FFC107",
                    //         borderWidth: 4,
                    //         backgroundColor: "transparent",
                    //         fill: false, // Tidak diisi
                    //         data: jobsCompletedData, // Data dari jobsCompletedData
                    //         maxBarThickness: 6
                    //     }
                    // ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false, // Set to true if you add a second dataset and want a legend
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    scales: {
                        y: {
                            grid: {
                                drawBorder: false,
                                display: true,
                                drawOnChartArea: true,
                                drawTicks: false,
                                borderDash: [5, 5]
                            },
                            ticks: {
                                display: true,
                                padding: 10,
                                color: '#b2b9bf',
                                font: {
                                    size: 11,
                                    family: "Open Sans",
                                    style: 'normal',
                                    lineHeight: 2
                                },
                            }
                        },
                        x: {
                            grid: {
                                drawBorder: false,
                                display: false,
                                drawOnChartArea: false,
                                drawTicks: false,
                                borderDash: [5, 5]
                            },
                            ticks: {
                                display: true,
                                color: '#b2b9bf',
                                padding: 20,
                                font: {
                                    size: 11,
                                    family: "Open Sans",
                                    style: 'normal',
                                    lineHeight: 2
                                },
                            }
                        },
                    },
                },
            });
        } // end if chartElement
    });
</script>
@endpush

<style>
    .card-link {
        text-decoration: none;
        color: inherit;
    }
</style>