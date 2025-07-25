@extends('Master.master-admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        {{-- Card Statistik Atas --}}
        <div class="col-lg-12">
            <div class="row">
                {{-- Total Pengguna --}}
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <a href="{{ route('admin.users.all') }}" class="card-link"> {{-- Tambah link ini --}}
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
                    </a>
                </div>

                {{-- Total Pekerja --}}
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <a href="{{ route('admin.users.workers') }}" class="card-link"> {{-- Tambah link ini --}}
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
                                </p>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Pengguna Diblokir --}}
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <a href="{{ route('admin.users.blockedList') }}" class="card-link"> {{-- Tambah link ini --}}
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
                                <span class="text-primary text-sm font-weight-bolder cursor-pointer">Lihat daftar blokir</span>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Pengguna Dilaporkan --}}
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <a href="{{ route('admin.reports.index') }}" class="card-link"> {{-- Link ini sesuai permintaan --}}
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
                                <span class="text-primary text-sm font-weight-bolder cursor-pointer">Lihat daftar laporan</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        {{-- Pencarian Pengguna --}}
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
                            <label class="form-label">Cari ID atau Nama...</label>
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
                            <br>
                            <a href="{{ route('admin.users.activityLog', $searchedUser->id) }}" class="text-primary text-sm font-weight-bolder mt-2">Lihat Aktivitas</a>
                        </div>
                    </div>
                    @elseif($searchQuery && !$searchedUser)
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="material-symbols-rounded me-2">error</i>
                        <div>
                            Pengguna dengan ID atau Nama "**{{ $searchQuery }}**" tidak ditemukan.
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tabel Log Aktivitas --}}
        <div class="col-lg-8 col-md-6 mt-4 mb-4">
            <div class="card z-index-2">
                <div class="card-header pb-0 bg-transparent">
                    <h6 class="mb-0">Log Aktivitas Terbaru {{ $searchedUser ? 'untuk ' . $searchedUser->first_name . ' ' . $searchedUser->last_name : '' }}</h6>
                    <p class="text-sm mb-0">
                        <i class="fa fa-info-circle text-info"></i>
                        <span class="font-weight-bold">Daftar aktivitas pengguna</span> di sistem.
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
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Log Nama</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activityLogs as $log)
                                @php
                                //dd($log);
                                @endphp
                                <tr>
                                    <td class="align-middle">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $log->created_at->diffForHumans() }}</span>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">
                                            {{ $log->causer ? $log->causer->first_name . ' ' . $log->causer->last_name : 'N/A' }}
                                        </p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $log->description }}</p>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $log->log_name }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-secondary text-sm">Tidak ada log aktivitas {{ $searchedUser ? 'untuk pengguna ini' : 'terbaru' }}.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $activityLogs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Hapus script Chart.js jika tidak ada grafik yang digunakan di halaman ini --}}
@endsection

@push('styles')
<style>
    .card-link {
        text-decoration: none;
        /* Hapus garis bawah */
        color: inherit;
        /* Warisan warna teks dari parent */
    }
</style>
@endpush