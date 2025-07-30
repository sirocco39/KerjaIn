<!-- This file name is index.blade.php inside users -->
@extends('Master.master-admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        {{-- Card Statistik Atas --}}
        <div class="col-lg-12">
            <div class="row">
                {{-- Total Pengguna --}}
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <a href="{{ route('admin.users.all') }}" class="card-link" id="totalPengguna"> {{-- Tambah link ini --}}
                        <div class="card">
                            <div class="card-header p-3 pt-2">
                                <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                                    <i class="material-symbols-rounded opacity-10">groups</i>
                                </div>
                                <div class="text-end pt-1">
                                    <p class="text-sm mb-0 text-capitalize">{{ __('admin/users.total_users') }}</p>
                                    <h4 class="mb-0">{{ number_format($totalUsers) }}</h4>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-3">
                                <p class="mb-0">
                                    <span class="text-success text-sm font-weight-bolder">{{ $activeToday }}</span> {{ __('admin/users.active_today') }}
                                    <span class="text-dark text-sm ms-2 font-weight-bolder">{{ $newUsersThisWeek }}</span> {{ __('admin/users.new_this_week') }}
                                </p>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Total Pekerja --}}
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <a href="{{ route('admin.users.workers') }}" class="card-link" id="totalPekerja"> {{-- Tambah link ini --}}
                        <div class="card">
                            <div class="card-header p-3 pt-2">
                                <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                                    <i class="material-symbols-rounded opacity-10">engineering</i>
                                </div>
                                <div class="text-end pt-1">
                                    <p class="text-sm mb-0 text-capitalize">{{ __('admin/users.total_workers') }}</p>
                                    <h4 class="mb-0">{{ number_format($totalWorkers) }}</h4>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-3">
                                <p class="mb-0">
                                    <span class="text-success text-sm font-weight-bolder">{{ $activeWorkersToday }}</span> {{ __('admin/users.active_workers_today') }}
                                </p>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Pengguna Diblokir --}}
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <a href="{{ route('admin.users.blockedList') }}" class="card-link" id="PenggunaDiBlok"> {{-- Tambah link ini --}}
                        <div class="card">
                            <div class="card-header p-3 pt-2">
                                <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                                    <i class="material-symbols-rounded opacity-10">block</i>
                                </div>
                                <div class="text-end pt-1">
                                    <p class="text-sm mb-0 text-capitalize">{{ __('admin/users.blocked_users') }}</p>
                                    <h4 class="mb-0">{{ number_format($blockedUsersCount) }}</h4>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-3">
                                <span class="text-primary text-sm font-weight-bolder cursor-pointer">{{ __('admin/users.view_blocked_list') }}</span>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Pengguna Dilaporkan --}}
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                    <a href="{{ route('admin.reports.index') }}" class="card-link" id="LihatDaftarReport"> {{-- Link ini sesuai permintaan --}}
                        <div class="card">
                            <div class="card-header p-3 pt-2">
                                <div class="icon icon-lg icon-shape bg-gradient-danger shadow-danger text-center border-radius-xl mt-n4 position-absolute">
                                    <i class="material-symbols-rounded opacity-10">flag</i>
                                </div>
                                <div class="text-end pt-1">
                                    <p class="text-sm mb-0 text-capitalize">{{ __('admin/users.reported_users') }}</p>
                                    <h4 class="mb-0">{{ number_format($reportedUsersCount) }}</h4>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-3">
                                <span class="text-primary text-sm font-weight-bolder cursor-pointer">{{ __('admin/users.view_report_list') }}</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        {{-- Tabel Log Aktivitas --}}
        <div class="col-lg-12 mt-4 mb-4"> {{-- Ubah ukuran kolom menjadi 12 untuk search bar di atas tabel --}}
            <div class="card z-index-2">
                <div class="card-header pb-0 bg-transparent">
                    <h6 class="mb-0">{{ __('admin/users.recent_activity_logs', ['for_user' => $searchedUser ? 'untuk ' . $searchedUser->first_name . ' ' . $searchedUser->last_name : '']) }}</h6>
                    <p class="text-sm mb-0">
                        <i class="fa fa-info-circle text-info"></i>
                        <span class="font-weight-bold">{{ __('admin/users.user_activity_list') }}</span> {{ __('admin/users.in_the_system') }}
                    </p>

                    {{-- Search Bar for Users Log Activity --}}
                    <div class="p-0 position-relative mt-3">
                        <form id="usersLogSearchForm" action="{{ route('admin.users.index') }}" method="GET" class="mb-0">
                            <div class="input-group m-0">
                                <input type="text" id="usersLogSearchInput" name="search_query" class="form-control bg-white border border-primary p-2" placeholder="{{ __('admin/users.search_for_user_activity') }}" autocomplete="off" value="{{ $searchQuery ?? '' }}">
                                <button class="btn btn-primary m-2" type="submit" id="cari-btn">{{ __('admin/users.search_button') }}</button>
                            </div>
                        </form>
                        <div id="usersLogSearchResults" class="list-group position-absolute w-100 mt-1" style="z-index: 1000; max-height: 200px; overflow-y: auto;">
                            {{-- Hasil pencarian akan ditampilkan di sini --}}
                        </div>
                    </div>
                    {{-- End Search Bar --}}

                </div>
                <div class="card-body p-3">
                    @if($searchQuery && $searchedUser)
                    <div class="alert alert-success d-flex align-items-center mb-3" role="alert" style="color:white">
                        <i class="material-symbols-rounded me-2">check_circle</i>
                        <div>
                            {!! __('admin/users.user_found', ['name' => $searchedUser->first_name . ' ' . $searchedUser->last_name]) !!}
                            <br>
                            {!! __('admin/users.user_id', ['id' => $searchedUser->id]) !!}
                            <br>
                            @if($activityLogs[0])
                            {{ __('admin/users.active_since', ['time_ago' => \Carbon\Carbon::parse($activityLogs[0]->created_at)->diffForHumans(null, false, true)]) }}
                            @else
                            {{ __('admin/users.no_login_activity') }}
                            @endif
                            <br>
                            {{ __('admin/users.status_colon') }} {{ $searchedUser->is_blocked ? __('admin/users.blocked') : __('admin/users.active') }}
                        </div>
                    </div>
                    @elseif($searchQuery && !$searchedUser)
                    <div class="alert alert-danger d-flex align-items-center mb-3" role="alert" style="color:white">
                        <i class="material-symbols-rounded me-2">error</i>
                        <div>
                            {!! __('admin/users.user_not_found', ['query' => $searchQuery]) !!}
                        </div>
                    </div>
                    @endif
                    @if(($searchQuery && $searchedUser) || (!$searchQuery))
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">{{ __('admin/users.time') }}</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">{{ __('admin/users.user') }}</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">{{ __('admin/users.target') }}</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">{{ __('admin/users.activity') }}</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">{{ __('admin/users.log_name') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($activityLogs)
                                @forelse($activityLogs as $log)
                                <tr>
                                    <td class="align-middle">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $log->created_at }}</span>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">
                                            {{ $log->causer ? $log->causer->first_name . ' ' . $log->causer->last_name : __('admin/users.n_a') }}
                                        </p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">
                                            @if($log->subject)
                                            {{ class_basename($log->subject_type)}} (ID :{{$log->subject->id}})
                                            @else -
                                            @endif
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
                                    <td colspan="4" class="text-center text-secondary text-sm">{{ __('admin/users.no_activity_logs_for_user') }}</td>
                                </tr>
                                @endforelse
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        @if($activityLogs)
                        {{ $activityLogs->links() }}
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-link {
        text-decoration: none;
        color: inherit;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- JavaScript for Search Recommendations on Users Log Activity Page ---
        const usersLogSearchInput = document.getElementById('usersLogSearchInput');
        const usersLogSearchResults = document.getElementById('usersLogSearchResults');
        const usersLogSearchForm = document.getElementById('usersLogSearchForm');
        let searchTimeoutUsersLog;

        if (usersLogSearchInput) {
            usersLogSearchInput.addEventListener('input', function() {
                clearTimeout(searchTimeoutUsersLog);
                const query = this.value;

                if (query.length > 2) { // Start searching after 2 characters
                    searchTimeoutUsersLog = setTimeout(() => {
                        fetch(`{{ route('admin.users.search-ajax') }}?query=${query}`)
                            .then(response => response.json())
                            .then(data => {
                                usersLogSearchResults.innerHTML = ''; // Clear previous results
                                if (data.length > 0) {
                                    data.forEach(item => {
                                        const a = document.createElement('a');
                                        // Link back to the users.index page with the search_query parameter
                                        a.href = `{{ route('admin.users.index') }}?search_query=${item.id}`; // Filter by user ID
                                        a.classList.add('list-group-item', 'list-group-item-action');
                                        a.innerHTML = `<strong>ID: ${item.id}</strong> - ${item.first_name} ${item.last_name}`;
                                        usersLogSearchResults.appendChild(a);
                                    });
                                } else {
                                    usersLogSearchResults.innerHTML = '<div class="list-group-item">Tidak ada pengguna ditemukan.</div>';
                                }
                                usersLogSearchResults.style.display = 'block'; // Show results
                            })
                            .catch(error => {
                                console.error('Error fetching search results:', error);
                                usersLogSearchResults.innerHTML = '<div class="list-group-item text-danger">Terjadi kesalahan saat mencari.</div>';
                                usersLogSearchResults.style.display = 'block';
                            });
                    }, 300); // Debounce 300ms
                } else {
                    usersLogSearchResults.innerHTML = ''; // Clear if query is too short
                    usersLogSearchResults.style.display = 'none'; // Hide results
                }
            });

            // Hide search results when clicking outside the input or results
            document.addEventListener('click', function(event) {
                if (!usersLogSearchInput.contains(event.target) && !usersLogSearchResults.contains(event.target)) {
                    usersLogSearchResults.innerHTML = '';
                    usersLogSearchResults.style.display = 'none';
                }
            });

            // Handle Enter key: submit form
            usersLogSearchInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault(); // Prevent default form submission behavior
                    usersLogSearchForm.submit(); // Manually submit the form
                }
            });
        }
    });
</script>
@endpush