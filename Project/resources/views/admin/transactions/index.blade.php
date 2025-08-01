@extends('Master.master-admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-symbols-rounded opacity-10">paid</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">{{ __('admin/transactions.company_profit') }}</p>
                        <h4 class="mb-0">Rp{{ number_format($totalCompanyProfitFromServiceFee, 0, ',', '.') }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0">
                        <span class="text-success text-sm font-weight-bolder">{{ __('admin/transactions.from_service_fee') }}</span>
                        {{ __('admin/transactions.from_completed_jobs') }}
                    </p>
                </div>
            </div>
        </div>

        
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-symbols-rounded opacity-10">account_balance</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">{{ __('admin/transactions.total_active_user_balance') }}</p>
                        <h4 class="mb-0">Rp{{ number_format($totalUserBalance, 0, ',', '.') }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0">{{ __('admin/transactions.total_available_balance_desc') }}</p>
                </div>
            </div>
        </div>

        
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-header p-3 pt-2">
                    <div class="icon icon-lg icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl mt-n4 position-absolute">
                        <i class="material-symbols-rounded opacity-10">payments</i>
                    </div>
                    <div class="text-end pt-1">
                        <p class="text-sm mb-0 text-capitalize">{{ __('admin/transactions.total_held_balance') }}</p>
                        <h4 class="mb-0">Rp{{ number_format($totalLockedBalance, 0, ',', '.') }}</h4>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
                <div class="card-footer p-3">
                    <p class="mb-0">{{ __('admin/transactions.funds_held_escrow_desc') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card h-100 mb-4">
                <div class="card-header pb-0 px-3">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-0">{{ __('admin/transactions.list_of_all_requests') }}</h6>
                        </div>
                        <div class="col-md-6 d-flex justify-content-start justify-content-md-end align-items-center">
                            <i class="material-symbols-rounded me-2 text-lg">list_alt</i>
                            <small>{{ __('admin/transactions.displaying_all_requests_desc') }}</small>
                        </div>
                    </div>
                </div>
                <div class="card-body pt-4 p-3">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">{{ __('admin/transactions.request_id') }}</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">{{ __('admin/transactions.service') }}</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">{{ __('admin/transactions.client') }}</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">{{ __('admin/transactions.worker') }}</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">{{ __('admin/transactions.total_price') }}</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">{{ __('admin/transactions.service_fee') }}</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 text-center">{{ __('admin/transactions.request_status') }}</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">{{ __('admin/transactions.created_at') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($Requests as $request) 
                                <tr>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">#{{ $request->id }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">
                                            {{ $request->title }}
                                            
                                            @if($request->transaction)
                                            ({{ $request->transaction->status }})
                                            @else
                                            ({{ $request->status }})
                                            @endif
                                        </p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $request->requester->first_name ?? __('admin/transactions.n_a') }} {{ $request->requester->last_name ?? '' }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $request->transaction->worker->first_name ?? __('admin/transactions.n_a') }} {{ $request->transaction->worker->last_name ?? '' }}</p>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        Rp{{ number_format($request->final_price, 0, ',', '.') }}
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="text-success text-gradient font-weight-bold">Rp{{ number_format($request->service_fee, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        @php
                                        $statusDisplay = ''; 
                                        $deletedBadge = ''; 

                                        if ($request->trashed()) {
                                        
                                        $deletedBadge = '<span class="badge badge-sm bg-gradient-danger ms-1">' . __('admin/transactions.deleted') . '</span>';
                                        
                                        } else {
                                        
                                        $statusClass = '';
                                        switch ($request->status) {
                                        case 'closed': $statusClass = 'bg-gradient-success'; break;
                                        case 'open': $statusClass = 'bg-gradient-info'; break;
                                        default: $statusClass = 'bg-gradient-secondary'; break;
                                        }
                                        $statusDisplay = '<span class="badge badge-sm ' . $statusClass . '">' . ucfirst($request->status) . '</span>';
                                        }
                                        @endphp
                                        {!! $statusDisplay !!}{!! $deletedBadge !!}
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{ $request->created_at->format('d M Y, H:i') }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">{{ __('admin/transactions.no_requests_found') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 px-4">
                        {{ $Requests->links() }} 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('currentPageTitle').innerText = 'Keuangan Perusahaan';
    document.getElementById('currentSectionTitle').innerText = 'Dashboard Keuangan';
</script>
@endpush