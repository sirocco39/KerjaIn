<!-- This file name is blocked-list.blade.php inside users -->

@extends('Master.master-admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>{{ __('admin/users.blocked_users_list') }}</h6>
                    
                    <div class="p-0 position-relative mt-3">
                        <form id="blockedUsersSearchForm" action="{{ route('admin.users.blockedList') }}" method="GET" class="mb-0">
                            <div class="input-group rounded-start m-0">
                                <input type="text" id="blockedUsersSearchInput" name="search_query" class="form-control rounded-start bg-white border border-primary p-2" placeholder="{{ __('admin/users.search_blocked_users_placeholder') }}" autocomplete="off" value="{{ request('search_query') }}">
                                <button class="btn btn-primary m-2" type="submit" id="cariBtn">{{ __('admin/users.search_button') }}</button>
                            </div>
                        </form>
                        <div id="blockedUsersSearchResults" class="list-group position-absolute w-100 mt-1" style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none;">
                            
                        </div>
                    </div>
                    
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    @if(request('search_query') && $blockedUsers->isEmpty())
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
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">{{ __('admin/users.status') }}</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">{{ __('admin/users.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($blockedUsers as $user)
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
                                    <td class="align-middle text-center">
                                        <span class="badge badge-sm bg-gradient-danger">{{ __('admin/users.blocked') }}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <a href="{{ route('admin.users.activityLog', ['user' => $user->id, 'from' => url()->full()]) }}" class="btn btn-sm btn-outline-primary mb-0 me-2" id="SeeActivityBtn">{{ __('admin/users.view_activity') }}</a>
                                        
                                        <button type="button" class="btn btn-sm btn-success mb-0"
                                            data-bs-toggle="modal" data-bs-target="#confirmUnblockModal"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->first_name }} {{ $user->last_name }}"
                                            id="unblockUserButton">
                                            {{ __('admin/users.unblock') }}
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-secondary text-sm">{{ __('admin/users.no_blocked_users_found') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 px-4">
                        {{ $blockedUsers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmUnblockModal" tabindex="-1" role="dialog" aria-labelledby="confirmUnblockModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmUnblockModalLabel">{{ __('admin/users.confirm_unblock_user') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{!! __('admin/users.confirm_unblock_message') !!}</p>
                <p>{{ __('admin/users.are_you_sure_continue') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('admin/users.cancel') }}</button>
                <form id="unblockUserForm" action="" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-success">{{ __('admin/users.yes_unblock') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    
    document.getElementById('confirmUnblockModal').addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget; 
        var userId = button.getAttribute('data-user-id');
        var userName = button.getAttribute('data-user-name');
        var modalTitle = this.querySelector('.modal-title');
        var modalBodyUserName = this.querySelector('#unblockUserName');
        var form = this.querySelector('#unblockUserForm');

        modalTitle.textContent = 'Konfirmasi Batal Blokir Pengguna';
        modalBodyUserName.textContent = userName;
        form.action = "{{ url('admin/users') }}/" + userId + "/unblock"; 
    });

    document.addEventListener('DOMContentLoaded', function() {
        
        const blockedUsersSearchInput = document.getElementById('blockedUsersSearchInput');
        const blockedUsersSearchResults = document.getElementById('blockedUsersSearchResults');
        const blockedUsersSearchForm = document.getElementById('blockedUsersSearchForm');
        let searchTimeoutBlockedUsers;

        if (blockedUsersSearchInput) {
            blockedUsersSearchInput.addEventListener('input', function() {
                clearTimeout(searchTimeoutBlockedUsers);
                const query = this.value;

                if (query.length > 2) { 
                    searchTimeoutBlockedUsers = setTimeout(() => {
                        
                        fetch(`{{ route('admin.users.search-ajax', ['blocked' => 1]) }}&query=${query}`)
                            .then(response => response.json())
                            .then(data => {
                                blockedUsersSearchResults.innerHTML = ''; 
                                if (data.length > 0) {
                                    data.forEach(item => {
                                        const a = document.createElement('a');
                                        
                                        a.href = `{{ route('admin.users.blockedList') }}?search_query=${item.id}`; 
                                        a.classList.add('list-group-item', 'list-group-item-action');
                                        a.innerHTML = `<strong>ID: ${item.id}</strong> - ${item.first_name} ${item.last_name}`;
                                        blockedUsersSearchResults.appendChild(a);
                                    });
                                } else {
                                    blockedUsersSearchResults.innerHTML = '<div class="list-group-item">Tidak ada pengguna diblokir ditemukan.</div>';
                                }
                                blockedUsersSearchResults.style.display = 'block'; 
                            })
                            .catch(error => {
                                console.error('Error fetching search results:', error);
                                blockedUsersSearchResults.innerHTML = '<div class="list-group-item text-danger">Terjadi kesalahan saat mencari.</div>';
                                blockedUsersSearchResults.style.display = 'block';
                            });
                    }, 300); 
                } else {
                    blockedUsersSearchResults.innerHTML = ''; 
                    blockedUsersSearchResults.style.display = 'none'; 
                }
            });

            
            document.addEventListener('click', function(event) {
                if (!blockedUsersSearchInput.contains(event.target) && !blockedUsersSearchResults.contains(event.target)) {
                    blockedUsersSearchResults.innerHTML = '';
                    blockedUsersSearchResults.style.display = 'none';
                }
            });

            
            blockedUsersSearchInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault(); 
                    blockedUsersSearchForm.submit(); 
                }
            });
        }
    });
</script>
@endpush