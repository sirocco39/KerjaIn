@extends('Master.master-admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Daftar Semua Pengguna</h6>
                    {{-- Search Bar for All Users List --}}
                    <div class="p-0 position-relative mt-3">
                        <form id="allUsersSearchForm" action="{{ route('admin.users.all') }}" method="GET" class="mb-0">
                            <div class="input-group rounded-start m-0">
                                <input type="text" id="allUsersSearchInput" name="search_query" class="rounded-start bg-white border border-primary p-2" placeholder="Cari pengguna berdasarkan ID atau Nama..." autocomplete="off" value="{{ request('search_query') }}">
                                <button class="btn btn-primary m-2" type="submit">Cari</button>
                            </div>
                        </form>
                        <div id="allUsersSearchResults" class="list-group position-absolute w-100 mt-1" style="z-index: 1000; max-height: 200px; overflow-y: auto; display: none;">
                            {{-- Search results will be displayed here --}}
                        </div>
                    </div>
                    {{-- End Search Bar --}}
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    @if(request('search_query') && $users->isEmpty())
                    <div class="alert alert-danger d-flex align-items-center mb-3 mx-4" role="alert">
                        <i class="material-symbols-rounded me-2">error</i>
                        <div>
                            Pengguna dengan ID atau Nama "**{{ request('search_query') }}**" tidak ditemukan.
                        </div>
                    </div>
                    @endif
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Nama</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">Email</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">Peran</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">Status</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">Aksi</th>
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
                                            {{ $user->is_worker ? 'Pekerja' : 'Pengguna Biasa' }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="badge badge-sm bg-gradient-{{ $user->is_blocked ? 'danger' : 'success' }}">
                                            {{ $user->is_blocked ? 'Diblokir' : 'Aktif' }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <a href="{{ route('admin.users.activityLog', ['user' => $user->id, 'from' => url()->full()]) }}" class="btn btn-sm btn-outline-primary mb-0">Lihat Aktivitas</a>
                                        {{-- Add other action buttons if needed, e.g., edit, block/unblock --}}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-secondary text-sm">Tidak ada pengguna ditemukan.</td>
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

                if (query.length > 2) { // Start searching after 2 characters
                    searchTimeoutAllUsers = setTimeout(() => {
                        fetch(`{{ route('admin.users.search-ajax') }}?query=${query}`)
                            .then(response => response.json())
                            .then(data => {
                                allUsersSearchResults.innerHTML = ''; // Clear previous results
                                if (data.length > 0) {
                                    data.forEach(item => {
                                        const a = document.createElement('a');
                                        a.href = `{{ route('admin.users.all') }}?search_query=${item.id}`; // Filter by user ID
                                        a.classList.add('list-group-item', 'list-group-item-action');
                                        a.innerHTML = `<strong>ID: ${item.id}</strong> - ${item.first_name} ${item.last_name}`;
                                        allUsersSearchResults.appendChild(a);
                                    });
                                } else {
                                    allUsersSearchResults.innerHTML = '<div class="list-group-item">Tidak ada pengguna ditemukan.</div>';
                                }
                                allUsersSearchResults.style.display = 'block'; // Show results
                            })
                            .catch(error => {
                                console.error('Error fetching search results:', error);
                                allUsersSearchResults.innerHTML = '<div class="list-group-item text-danger">Terjadi kesalahan saat mencari.</div>';
                                allUsersSearchResults.style.display = 'block';
                            });
                    }, 300); // Debounce 300ms
                } else {
                    allUsersSearchResults.innerHTML = ''; // Clear if query is too short
                    allUsersSearchResults.style.display = 'none'; // Hide results
                }
            });

            // Hide search results when clicking outside the input or results
            document.addEventListener('click', function(event) {
                if (!allUsersSearchInput.contains(event.target) && !allUsersSearchResults.contains(event.target)) {
                    allUsersSearchResults.innerHTML = '';
                    allUsersSearchResults.style.display = 'none';
                }
            });

            // Handle Enter key: submit form
            allUsersSearchInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault(); // Prevent default form submission behavior
                    allUsersSearchForm.submit(); // Manually submit the form
                }
            });
        }
    });
</script>
@endpush