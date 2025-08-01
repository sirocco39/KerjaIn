<!-- This file name is all-list.blade.php inside users -->
@extends('Master.master-admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>{{ __('admin/users.all_users_list') }}</h6>
                    
                    <div class="p-0 position-relative mt-3">
                        <form id="allUsersSearchForm" action="{{ route('admin.users.all') }}" method="GET" class="mb-0">
                            <div class="input-group rounded-start m-0">
                                <input type="text" id="allUsersSearchInput" name="search_query" class="form-control rounded-start bg-white border border-primary p-2" placeholder="{{ __('admin/users.search_all_users_placeholder') }}" autocomplete="off" value="{{ request('search_query') }}">
                                <button class="btn btn-primary m-2" type="submit" id="btn-cari">{{ __('admin/users.search_button') }}</button>
                            </div>
                        </form>
                        <div id="allUsersSearchResults" class="list-group position-absolute w-100 mt-1" style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none;">
                            
                        </div>
                    </div>
                    
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    @if(request('search_query') && $users->isEmpty())
                    <div class="alert alert-danger d-flex align-items-center mb-3 mx-4" role="alert" style="color:white">
                        <i class="material-symbols-rounded me-2">error</i>
                        <div>
                            {!! __('admin/users.user_not_found', ['query' => request('search_query')]) !!}
                        </div>
                    </div>
                    @endif
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">{{ __('admin/users.name') }}</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">{{ __('admin/users.email') }}</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">{{ __('admin/users.role') }}</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">{{ __('admin/users.status') }}</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">{{ __('admin/users.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $user->first_name }} {{ $user->last_name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $user->email }}</p>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="badge badge-sm bg-gradient-{{ $user->is_worker ? 'info' : 'primary' }}">
                                            {{ $user->is_worker ? __('admin/users.worker') : __('admin/users.regular_user') }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="badge badge-sm bg-gradient-{{ $user->is_blocked ? 'danger' : 'success' }}">
                                            {{ $user->is_blocked ? __('admin/users.blocked') : __('admin/users.active') }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <a href="{{ route('admin.users.activityLog', ['user' => $user->id, 'from' => url()->full()]) }}" class="btn btn-sm btn-outline-primary mb-0" id="SeeActivityBtn">{{ __('admin/users.view_activity') }}</a>
                                        
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-secondary text-sm">{{ __('admin/users.no_users_found') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 px-4">
                        {{ $users->links() }}
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
        const allUsersSearchInput = document.getElementById('allUsersSearchInput');
        const allUsersSearchResults = document.getElementById('allUsersSearchResults');
        const allUsersSearchForm = document.getElementById('allUsersSearchForm');
        let searchTimeoutAllUsers;

        if (allUsersSearchInput) {
            allUsersSearchInput.addEventListener('input', function() {
                clearTimeout(searchTimeoutAllUsers);
                const query = this.value;

                if (query.length > 2) { 
                    searchTimeoutAllUsers = setTimeout(() => {
                        fetch(`{{ route('admin.users.search-ajax') }}?query=${query}`)
                            .then(response => response.json())
                            .then(data => {
                                allUsersSearchResults.innerHTML = ''; 
                                if (data.length > 0) {
                                    data.forEach(item => {
                                        const a = document.createElement('a');
                                        a.href = `{{ route('admin.users.all') }}?search_query=${item.id}`; 
                                        a.classList.add('list-group-item', 'list-group-item-action');
                                        a.innerHTML = `<strong>ID: ${item.id}</strong> - ${item.first_name} ${item.last_name}`;
                                        allUsersSearchResults.appendChild(a);
                                    });
                                } else {
                                    allUsersSearchResults.innerHTML = '<div class="list-group-item">Tidak ada pengguna ditemukan.</div>';
                                }
                                allUsersSearchResults.style.display = 'block'; 
                            })
                            .catch(error => {
                                console.error('Error fetching search results:', error);
                                allUsersSearchResults.innerHTML = '<div class="list-group-item text-danger">Terjadi kesalahan saat mencari.</div>';
                                allUsersSearchResults.style.display = 'block';
                            });
                    }, 300); 
                } else {
                    allUsersSearchResults.innerHTML = ''; 
                    allUsersSearchResults.style.display = 'none'; 
                }
            });

            
            document.addEventListener('click', function(event) {
                if (!allUsersSearchInput.contains(event.target) && !allUsersSearchResults.contains(event.target)) {
                    allUsersSearchResults.innerHTML = '';
                    allUsersSearchResults.style.display = 'none';
                }
            });

            
            allUsersSearchInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault(); 
                    allUsersSearchForm.submit(); 
                }
            });
        }
    });
</script>
@endpush