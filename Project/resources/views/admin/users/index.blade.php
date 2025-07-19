@extends('Master.master-admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        {{-- Card Statistik Atas --}}
        <div class="col-lg-12">
            <div class="row">
                {{-- Total Pengguna --}}
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
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
                            <p class="mb-0">
                                <span class="text-success text-sm font-weight-bolder">{{ $activeToday }}</span> aktif hari ini
                                <span class="text-dark text-sm ms-2 font-weight-bolder">{{ $newUsersThisWeek }}</span> baru minggu ini
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Total Pekerja --}}
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-symbols-rounded opacity-10">engineering</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Total Pekerja</p>
                                <h4 class="mb-0">{{ number_format($totalWorkers) }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <p class="mb-0">
                                <span class="text-success text-sm font-weight-bolder">{{ $activeWorkersToday }}</span> aktif hari ini
                                {{-- <span class="text-dark text-sm ms-2 font-weight-bolder">X</span> pekerja baru minggu ini --}}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Pengguna Diblokir --}}
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-symbols-rounded opacity-10">block</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Pengguna Diblokir</p>
                                <h4 class="mb-0">{{ number_format($blockedUsersCount) }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <a href="{{ route('admin.users.blocked-list') }}" class="text-primary text-sm font-weight-bolder cursor-pointer">Lihat daftar blokir</a>
                        </div>
                    </div>
                </div>

                {{-- Pengguna Dilaporkan --}}
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-danger shadow-danger text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-symbols-rounded opacity-10">flag</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Pengguna Dilaporkan</p>
                                <h4 class="mb-0">{{ number_format($reportedUsersCount) }}</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3">
                            <a href="{{ route('admin.reports.index') }}" class="text-primary text-sm font-weight-bolder cursor-pointer">Lihat daftar laporan</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grafik Pengguna Aktif --}}
        <div class="col-lg-8 col-md-6 mt-4 mb-4">
            <div class="card z-index-2">
                <div class="card-header pb-0 bg-transparent">
                    <h6 class="mb-0">Grafik Pengguna Aktif</h6>
                    <p class="text-sm mb-0">
                        <i class="fa fa-arrow-up text-success"></i>
                        <span class="font-weight-bold">Jumlah pengguna aktif</span> dalam 7 hari terakhir.
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="active-users-chart" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pencarian Pengguna & Log Login Terbaru --}}
        <div class="col-lg-4 col-md-6 mt-4 mb-4">
            <div class="card">
                <div class="card-header pb-0 p-3">
                    <div class="row">
                        <div class="col-12 d-flex align-items-center">
                            <h6 class="mb-0">Cari Pengguna</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <form action="{{ route('admin.users.index') }}" method="GET" class="mb-4">
                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Cari ID atau Username...</label>
                            <input type="text" class="form-control" name="search_query" value="{{ $searchQuery ?? '' }}">
                            <button type="submit" class="btn btn-primary mb-0 ms-2">Cari</button>
                        </div>
                    </form>

                    @if($searchQuery && $searchedUser)
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <i class="material-symbols-rounded me-2">check_circle</i>
                        <div>
                            Pengguna **{{ $searchedUser->first_name }} {{ $searchedUser->last_name }}** ditemukan.
                            <br>
                            ID: **{{ $searchedUser->id }}**
                            <br>
                            @if($searchedUser->last_activity)
                            Aktif {{ \Carbon\Carbon::parse($searchedUser->last_activity)->diffForHumans(null, false, true) }}
                            @else
                            Belum ada aktivitas login.
                            @endif
                            <br>
                            Status: {{ $searchedUser->is_blocked ? 'Diblokir' : 'Aktif' }}
                        </div>
                    </div>
                    @elseif($searchQuery && !$searchedUser)
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="material-symbols-rounded me-2">error</i>
                        <div>
                            Pengguna dengan ID atau Username "**{{ $searchQuery }}**" tidak ditemukan.
                        </div>
                    </div>
                    @endif

                    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3 mt-4">Log Login Terbaru</h6>
                    <ul class="list-group">
                        @forelse($recentLogins as $session)
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 text-dark font-weight-bold text-sm">
                                    {{ $session->user ? $session->user->first_name . ' ' . $session->user->last_name : 'Pengguna Dihapus' }}
                                </h6>
                                <span class="text-xs">Log ID: {{ $session->id }}</span>
                            </div>
                            <div class="d-flex align-items-center text-sm">
                                @if($session->user)
                                {{-- Menggunakan 'Aktif X waktu lalu' --}}
                                Aktif {{ $session->last_activity->diffForHumans(null, false, true) }}
                                @else
                                Sesi berakhir.
                                @endif
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item border-0 ps-0 text-dark text-sm">Tidak ada log login terbaru.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data untuk Chart.js (dari controller)
        const activeUsersChartLabels = JSON.parse(chartElement.dataset.chartLabels);
        console.log(activeUsersChartLabels); // Untuk debugging

        // Mengambil data dari atribut data-chart-data dan mem-parse sebagai JSON
        const activeUsersChartData = JSON.parse(chartElement.dataset.chartData);
        console.log(activeUsersChartData); // Untuk debugging

        // Inisialisasi Line Chart
        var ctx = document.getElementById("active-users-chart").getContext("2d");
        new Chart(ctx, {
            type: "line",
            data: {
                labels: activeUsersChartLabels,
                datasets: [{
                    label: "Pengguna Aktif",
                    tension: 0.4,
                    borderWidth: 0,
                    pointRadius: 5,
                    pointBackgroundColor: "#cb0c9f", // primary color
                    pointBorderColor: "transparent",
                    borderColor: "#cb0c9f", // primary color
                    borderWidth: 4,
                    backgroundColor: "transparent",
                    fill: true,
                    data: activeUsersChartData,
                    maxBarThickness: 6
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
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
    });
</script>
@endpush
@endsection