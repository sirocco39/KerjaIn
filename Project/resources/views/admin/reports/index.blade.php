@extends('Master.master-admin')

@section('content')
<div class="container-fluid py-4">
    <h1 class="mb-4">User Reports Management</h1>

    {{-- Success Message --}}
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Tab Navigation --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ $currentStatus == 'Not Reviewed' ? 'active' : '' }}" href="{{ route('admin.reports.index', ['status' => 'Not Reviewed']) }}">
                Not Reviewed <span class="badge bg-warning">{{ \App\Models\Report::where('status', 'Not Reviewed')->count() }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $currentStatus == 'Reviewed' ? 'active' : '' }}" href="{{ route('admin.reports.index', ['status' => 'Reviewed']) }}">
                Reviewed <span class="badge bg-success">{{ \App\Models\Report::where('status', 'Reviewed')->count() }}</span>
            </a>
        </li>
    </ul>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                @if($reports->isEmpty())
                <div class="text-center py-5">
                    <p class="text-muted">No {{ $currentStatus }} reports found.</p>
                </div>
                @else
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Reporter</th>
                            <th>Reported User</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                        <tr>
                            <td>#{{ $report->id }}</td>
                            <td>{{ $report->created_at->format('M d, Y H:i') }}</td>
                            <td>{{ $report->reporter->first_name . ' ' . $report->reporter->last_name ?? 'N/A' }}</td>
                            <td>{{$report->reported->first_name . ' ' . $report->reported->last_name ?? 'N/A' }}</td>
                            <td>
                                @if ($report->status == 'Reviewed')
                                <span class="badge bg-success">Reviewed</span>
                                @else
                                <span class="badge bg-warning text-dark">Not Reviewed</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#reportDetailsModal-{{ $report->id }}">
                                    View Details
                                </button>

                                {{-- Action Form --}}
                                <form action="{{ route('admin.reports.update', $report) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    @if ($report->status == 'Not Reviewed')
                                    <input type="hidden" name="status" value="Reviewed">
                                    <button type="submit" class="btn btn-sm btn-success">Mark as Reviewed</button>
                                    @else
                                    <input type="hidden" name="status" value="Not Reviewed">
                                    <button type="submit" class="btn btn-sm btn-secondary">Mark as Not Reviewed</button>
                                    @endif
                                </form>
                            </td>
                        </tr>

                        {{-- Details Modal for each report --}}
                        <div class="modal fade" id="reportDetailsModal-{{ $report->id }}" tabindex="-1" aria-labelledby="reportDetailsModalLabel-{{ $report->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="reportDetailsModalLabel-{{ $report->id }}">Report #{{ $report->id }} Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Reporter:</strong> {{ $report->reporter->first_name . ' ' . $report->reporter->last_name ?? 'N/A' }} (ID: {{ $report->reporter_id }})<br>
                                                <strong>Reported User:</strong> {{ $report->reported->name ?? 'N/A' }} (ID: {{ $report->reported_id }})<br>
                                                <strong>Transaction ID:</strong> {{ $report->transaction_id }}<br>
                                                <strong>Date:</strong> {{ $report->created_at->format('F j, Y, g:i a') }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Status:</strong> <span class="badge {{ $report->status == 'Reviewed' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $report->status }}</span>
                                            </div>
                                        </div>
                                        <hr>
                                        <h5>Reasons for Report:</h5>
                                        <p class="text-break">{{ $report->reasons }}</p>

                                        @if ($report->photo_url)
                                        <hr>
                                        <h5>Attached Photo Evidence:</h5>
                                        {{-- IMPORTANT: Ensure you have run `php artisan storage:link` --}}
                                        <img src="{{ asset('storage/' . $report->photo_url) }}" class="img-fluid rounded" alt="Report evidence">
                                        @else
                                        <p class="text-muted">No photo was attached.</p>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>

            {{-- Pagination Links --}}
            <div class="mt-3">
                {{ $reports->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

@endsection