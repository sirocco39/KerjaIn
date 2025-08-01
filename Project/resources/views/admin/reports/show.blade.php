<!-- This file name is show.blade.php in reports -->
@extends('Master.master-admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-10 col-md-12 mx-auto">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>{{ __('admin/reports.report_detail_id', ['id' => $report->id]) }}</h6>
                    <a href="{{ route('admin.reports.index', ['status' => $report->status]) }}" class="btn btn-sm btn-secondary mb-0" id="KembaliBtn">{{ __('admin/reports.back_to_report_list') }}</a>
                </div>
                <div class="card-body">
                    
                    <h5 class="mb-3">{{ __('admin/reports.report_information') }}</h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>{{ __('admin/reports.report_id_colon') }}</strong> #{{ $report->id }}</p>
                            <p class="mb-1"><strong>{{ __('admin/reports.report_date_colon') }}</strong> {{ $report->created_at->format('F j, Y, H:i') }}</p>
                            <p class="mb-1"><strong>{{ __('admin/reports.status') }}:</strong> <span class="badge {{ $report->status == 'Reviewed' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $report->status }}</span></p>
                            @if ($report->transaction_id)
                            <p class="mb-1"><strong>{{ __('admin/reports.transaction_id_colon') }}</strong> {{ $report->transaction_id }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>{{ __('admin/reports.reporter_colon') }}</strong> {{ $report->reporter->first_name . ' ' . $report->reporter->last_name ?? __('admin/reports.n_a') }} (ID: {{ $report->reporter_id }})</p>
                            <p class="mb-1"><strong>{{ __('admin/reports.reporter_email_colon') }}</strong> {{ $report->reporter->email ?? __('admin/reports.n_a') }}</p>
                            <p class="mb-1"><strong>{{ __('admin/reports.reported_user_colon') }}</strong> {{ $report->reported->first_name . ' ' . $report->reported->last_name ?? __('admin/reports.n_a') }} (ID: {{ $report->reported_id }})</p>
                            <p class="mb-1"><strong>{{ __('admin/reports.reported_user_email_colon') }}</strong> {{ $report->reported->email ?? __('admin/reports.n_a') }}</p>
                            @if($report->reported)
                            <p class="mb-1">
                                <strong>{{ __('admin/reports.total_reports_received') }}</strong>
                                <span class="badge bg-danger">{{ $report->reported->reportsReceived->count() }} {{ __('admin/reports.times') }}</span>
                            </p>
                            @endif
                        </div>
                    </div>

                    <hr>

                    
                    <h5 class="mt-4 mb-3">{{ __('admin/reports.report_reason') }}</h5>
                    <p class="alert alert-light">{{ $report->reasons }}</p>
                    
                    @if ($report->photo_url)
                    @php
                    
                    $photoPaths = json_decode($report->photo_url, true);
                    @endphp

                    @if (!empty($photoPaths) && is_array($photoPaths)) 
                    <h5 class="mt-4 mb-3">{{ __('admin/reports.photo_evidence') }}</h5>
                    <div class="row"> 
                        @foreach ($photoPaths as $path)
                        <div class="col-md-4 col-sm-6 mb-4"> 
                            <div class="card card-body border card-plain border-radius-lg d-flex flex-column justify-content-between h-100">
                                @php
                                
                                $actualPathInStorage = str_replace('/storage/', '', $path);
                                @endphp
                                <a href="{{ Storage::url($actualPathInStorage) }}" target="_blank">
                                    <img src="{{ Storage::url($actualPathInStorage) }}" class="img-fluid border-radius-lg mb-3" alt="{{ __('admin/reports.photo_evidence') }}">
                                </a>
                                
                                
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    
                    <p class="text-muted">{{ __('admin/reports.no_photo_evidence_attached') }}</p>
                    @endif
                    @endif
                    <hr>

                    
                    <h5 class="mt-4 mb-3">{{ __('admin/reports.user_related_actions') }}</h5>
                    <div class="d-flex flex-wrap gap-2">
                        
                        <a href="{{ route('admin.users.activityLog', ['user' => $report->reporter_id, 'from' => url()->full()]) }}" class="btn btn-sm btn-outline-primary mb-2 me-2" id="lihat-aktivitas-pelapor">
                            <i class="material-symbols-rounded text-sm me-1">history</i> {{ __('admin/reports.view_reporter_activity') }}
                        </a>

                        
                        <a href="{{ route('admin.users.activityLog', ['user' => $report->reported_id, 'from' => url()->full()]) }}" class="btn btn-sm btn-outline-info mb-2 me-2" id="lihat-aktivitas-pengguna-dilaporkan">
                            <i class="material-symbols-rounded text-sm me-1">manage_accounts</i> {{ __('admin/reports.view_reported_user_activity') }}
                        </a>

                        
                        @if($report->reported && $report->reported->is_blocked)
                        <button type="button" class="btn btn-sm btn-success mb-2 me-2"
                            data-bs-toggle="modal" data-bs-target="#confirmUnblockModal"
                            data-user-id="{{ $report->reported_id }}"
                            data-user-name="{{ $report->reported->first_name }} {{ $report->reported->last_name }}"
                            id="unblockUserButton">
                            <i class="material-symbols-rounded text-sm me-1">lock_open</i> {{ __('admin/reports.unblock_reported_user') }}
                        </button>
                        @else
                        <button type="button" class="btn btn-sm btn-warning mb-2 me-2"
                            data-bs-toggle="modal" data-bs-target="#confirmBlockModal"
                            data-user-id="{{ $report->reported_id }}"
                            data-user-name="{{ $report->reported->first_name }} {{ $report->reported->last_name }}"
                            id="blockUserButton">
                            <i class="material-symbols-rounded text-sm me-1">lock</i> {{ __('admin/reports.block_reported_user') }}
                        </button>
                        @endif
                    </div>

                    <hr>

                    
                    <h5 class="mt-4 mb-3">{{ __('admin/reports.report_status') }}</h5>
                    <form action="{{ route('admin.reports.update', $report) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        @if ($report->status == 'Not Reviewed')
                        <input type="hidden" name="status" value="Reviewed">
                        <button type="submit" class="btn btn-success btn-lg mt-3" id="tandai-sudah-ditinjau">
                            <i class="material-symbols-rounded text-sm me-1">check_circle</i> {{ __('admin/reports.mark_as_reviewed') }}
                        </button>
                        @else
                        <input type="hidden" name="status" value="Not Reviewed">
                        <button type="submit" class="btn btn-secondary btn-lg mt-3" id="tandai-belum-ditinjau">
                            <i class="material-symbols-rounded text-sm me-1">remove_done</i> {{ __('admin/reports.mark_as_not_reviewed') }}
                        </button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmBlockModal" tabindex="-1" role="dialog" aria-labelledby="confirmBlockModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmBlockModalLabel">{{ __('admin/reports.confirm_block_user') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{!! __('admin/reports.confirm_block_message') !!}</p>
                <p>{{ __('admin/reports.are_you_sure_continue') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="Batalbtn">{{ __('admin/reports.cancel') }}</button>
                <form id="blockUserForm" action="" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-warning" id="BlokirPgn">{{ __('admin/reports.yes_block') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmUnblockModal" tabindex="-1" role="dialog" aria-labelledby="confirmUnblockModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmUnblockModalLabel">{{ __('admin/reports.confirm_unblock_user') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{!! __('admin/reports.confirm_unblock_message') !!}</p>
                <p>{{ __('admin/reports.are_you_sure_continue') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="batalBatalBlokir">{{ __('admin/reports.cancel') }}</button>
                <form id="unblockUserForm" action="" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-success" id="BatalBlokir">{{ __('admin/reports.yes_unblock') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    
    document.getElementById('confirmBlockModal').addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget; 
        var userId = button.getAttribute('data-user-id');
        var userName = button.getAttribute('data-user-name');

        var modalTitle = this.querySelector('.modal-title');
        var modalBodyUserName = this.querySelector('#blockUserName');
        var form = this.querySelector('#blockUserForm');

        modalTitle.textContent = 'Konfirmasi Blokir Pengguna';
        modalBodyUserName.textContent = userName;
        form.action = "{{ url('admin/users') }}/" + userId + "/block"; 
    });

    
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
</script>
@endpush