<!-- This file name is index.blade.php in reports -->
@extends('Master.master-admin')

@section('content')
<div class="container-fluid py-4">
    <h1 class="mb-4">{{ __('admin/reports.management_user_reports') }}</h1>

    
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="color:white">
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

    
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ $currentStatus == 'Not Reviewed' ? 'active' : '' }}" href="{{ route('admin.reports.index', ['status' => 'Not Reviewed']) }}" id="notReviewedTab">
                {{ __('admin/reports.review') }} <span class="badge bg-warning">{{ \App\Models\Report::where('status', 'Not Reviewed')->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $currentStatus == 'Reviewed' ? 'active' : '' }}" href="{{ route('admin.reports.index', ['status' => 'Reviewed']) }}" id="reviewedTab">
                {{ __('admin/reports.already_reviewed') }} <span class="badge bg-success">{{ \App\Models\Report::where('status', 'Reviewed')->count() }}</span>
            </a>
        </li>
    </ul>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                @if($reports->isEmpty())
                <div class="text-center py-5">
                    <p class="text-muted">{{ __('admin/reports.no_reports_found_status', ['status' => $currentStatus]) }}</p>
                </div>
                @else
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('admin/reports.id') }}</th>
                            <th>{{ __('admin/reports.date') }}</th>
                            <th>{{ __('admin/reports.reporter') }}</th>
                            <th>{{ __('admin/reports.reported_user') }}</th>
                            <th>{{ __('admin/reports.status') }}</th>
                            <th class="text-end">{{ __('admin/reports.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                        <tr>
                            <td>#{{ $report->id }}</td>
                            <td>{{ $report->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                {{ $report->reporter->first_name . ' ' . $report->reporter->last_name ?? __('admin/reports.n_a') }}
                                <br>
                                <span class="text-muted text-sm">(ID: {{ $report->reporter_id }})</span>
                            </td>
                            <td>
                                {{ $report->reported->first_name . ' ' . $report->reported->last_name ?? __('admin/reports.n_a') }}
                                <br>
                                <span class="text-muted text-sm">(ID: {{ $report->reported_id }})</span>
                            </td>
                            <td>
                                @if ($report->status == 'Reviewed')
                                <span class="badge bg-success">{{ __('admin/reports.reviewed') }}</span>
                                @else
                                <span class="badge bg-warning text-dark">{{ __('admin/reports.not_reviewed') }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                
                                <a href="{{ route('admin.reports.show', $report) }}" class="btn btn-sm btn-info me-2" id="lihat-detail">
                                    {{ __('admin/reports.detail') }}
                                </a>

                                
                                
                                <form action="{{ route('admin.reports.update', $report) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    @if ($report->status == 'Not Reviewed')
                                    <input type="hidden" name="status" value="Reviewed" id="status">
                                    <button type="submit" class="btn btn-sm btn-success" id="tandai-sudah-ditinjau">{{ __('admin/reports.mark_as_reviewed_button') }}</button>
                                    @else
                                    <input type="hidden" name="status" value="Not Reviewed" id="status">
                                    <button type="submit" class="btn btn-sm btn-secondary" id="tandai-sudah-ditinjau">{{ __('admin/reports.mark_as_not_reviewed_button') }}</button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>

            
            <div class="mt-3">
                {{ $reports->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

@endsection