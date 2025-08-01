<!-- This file name is worker-list.blade.php inside users -->
@extends('Master.master-admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>{{ __('admin/users.worker_list') }}</h6>
                    
                    <div class="p-0 position-relative mt-3">
                        <form id="workersSearchForm" action="{{ route('admin.users.workers') }}" method="GET" class="mb-0">
                            <div class="input-group rounded-start m-0">
                                <input type="text" id="workersSearchInput" name="search_query" class="form-control rounded-start bg-white border border-primary p-2" placeholder="{{ __('admin/users.search_workers_placeholder') }}" autocomplete="off" value="{{ request('search_query') }}">
                                <button class="btn btn-primary m-2" type="submit" id="cariBtn">{{ __('admin/users.search_button') }}</button>
                            </div>
                        </form>
                        <div id="workersSearchResults" class="list-group position-absolute w-100 mt-1" style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none;">
                            
                        </div>
                    </div>
                    
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    @if(request('search_query') && $workers->isEmpty())
                    <div class="alert alert-danger d-flex align-items-center mb-3 mx-4" role="alert" style="color:white">
                        <i class="material-symbols-rounded me-2">error</i>
                        <div>
                            {!! __('admin/users.worker_not_found', ['query' => request('search_query')]) !!}
                        </div>
                    </div>
                    @endif
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">{{ __('admin/users.name') }}</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">{{ __('admin/users.email') }}</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">{{ __('admin/users.rating') }}</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">{{ __('admin/users.job_done') }}</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">{{ __('admin/users.status') }}</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">{{ __('admin/users.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($workers as $worker)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $worker->first_name }} {{ $worker->last_name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $worker->email }}</p>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="badge badge-sm bg-gradient-success">{{ $worker->rating }}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $worker->job_done }}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="badge badge-sm bg-gradient-{{ $worker->is_blocked ? 'danger' : 'success' }}">
                                            {{ $worker->is_blocked ? __('admin/users.blocked') : __('admin/users.active') }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <a href="{{ route('admin.users.activityLog', ['user' => $worker->id, 'from' => url()->full()]) }}" class="btn btn-sm btn-outline-primary mb-0" id="lihat-aktivitas">{{ __('admin/users.view_activity') }}</a>
                                        
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-secondary text-sm">{{ __('admin/users.no_workers_found') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 px-4">
                        {{ $workers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        const workersSearchInput = document.getElementById('workersSearchInput');
        const workersSearchResults = document.getElementById('workersSearchResults');
        const workersSearchForm = document.getElementById('workersSearchForm');
        let searchTimeoutWorkers;

        if (workersSearchInput) {
            workersSearchInput.addEventListener('input', function() {
                clearTimeout(searchTimeoutWorkers);
                const query = this.value;

                if (query.length > 2) { 
                    searchTimeoutWorkers = setTimeout(() => {
                        
                        fetch(`{{ route('admin.users.search-ajax', ['is_worker' => 1]) }}&query=${query}`)
                            .then(response => response.json())
                            .then(data => {
                                workersSearchResults.innerHTML = ''; 
                                if (data.length > 0) {
                                    data.forEach(item => {
                                        const a = document.createElement('a');
                                        
                                        a.href = `{{ route('admin.users.workers') }}?search_query=${item.id}`; 
                                        a.classList.add('list-group-item', 'list-group-item-action');
                                        a.innerHTML = `<strong>ID: ${item.id}</strong> - ${item.first_name} ${item.last_name}`;
                                        workersSearchResults.appendChild(a);
                                    });
                                } else {
                                    workersSearchResults.innerHTML = '<div class="list-group-item">Tidak ada pekerja ditemukan.</div>';
                                }
                                workersSearchResults.style.display = 'block'; 
                            })
                            .catch(error => {
                                console.error('Error fetching search results:', error);
                                workersSearchResults.innerHTML = '<div class="list-group-item text-danger">Terjadi kesalahan saat mencari.</div>';
                                workersSearchResults.style.display = 'block';
                            });
                    }, 300); 
                } else {
                    workersSearchResults.innerHTML = ''; 
                    workersSearchResults.style.display = 'none'; 
                }
            });

            
            document.addEventListener('click', function(event) {
                if (!workersSearchInput.contains(event.target) && !workersSearchResults.contains(event.target)) {
                    workersSearchResults.innerHTML = '';
                    workersSearchResults.style.display = 'none';
                }
            });

            
            workersSearchInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault(); 
                    workersSearchForm.submit(); 
                }
            });
        }
    });
</script>
@endpush