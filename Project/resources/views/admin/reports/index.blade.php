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

    {{-- Info Message (from AdminUserController) --}}
    @if (session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        {{ session('info') }}
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
                            <td>
                                {{ $report->reporter->first_name . ' ' . $report->reporter->last_name ?? 'N/A' }}
                                <br>
                                <span class="text-muted text-sm">(ID: {{ $report->reporter_id }})</span>
                            </td>
                            <td>
                                {{ $report->reported->first_name . ' ' . $report->reported->last_name ?? 'N/A' }}
                                <br>
                                <span class="text-muted text-sm">(ID: {{ $report->reported_id }})</span>
                            </td>
                            <td>
                                @if ($report->status == 'Reviewed')
                                <span class="badge bg-success">Reviewed</span>
                                @else
                                <span class="badge bg-warning text-dark">Not Reviewed</span>
                                @endif
                            </td>
                            <td class="text-end">
                                {{-- Ubah ini untuk mengarah ke halaman detail --}}
                                <a href="{{ route('admin.reports.show', $report) }}" class="btn btn-sm btn-info me-2">
                                    View Details
                                </a>

                                {{-- Action Form for Mark as Reviewed/Not Reviewed (bisa dipindahkan ke halaman detail) --}}
                                {{-- Jika Anda ingin tetap ada di sini, biarkan seperti ini --}}
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