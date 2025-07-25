<?php

namespace App\Http\Controllers\Admin;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'status' => ['sometimes', 'in:Reviewed,Not Reviewed']
        ]);

        $statusFilter = $request->query('status', 'Not Reviewed');

        $reports = Report::with(['reporter', 'reported'])
            ->where('status', $statusFilter)
            ->latest()
            ->paginate(15);

        // Data Breadcrumbs
        $breadcrumbs = [
            'mainPageTitle' => 'Admin',
            'currentPageTitle' => 'Laporan',
            'currentSectionTitle' => 'Manajemen Laporan',
            'filterStatus' => $statusFilter, // Tambahkan status filter ke breadcrumbs jika perlu
        ];

        return view('admin.reports.index', [
            'reports' => $reports,
            'currentStatus' => $statusFilter,
            'breadcrumbs' => $breadcrumbs, // Tambahkan breadcrumbs
        ]);
    }

    /**
     * Display the specified resource.
     *
     * This method displays the detailed view of a single report.
     */
    public function show(Report $report)
    {
        $report->load(['reporter', 'reported.reportsReceived']);

        // Data Breadcrumbs
        $breadcrumbs = [
            'mainPageTitle' => 'Admin',
            'currentPageTitle' => 'Detail Laporan',
            'currentSectionTitle' => 'Manajemen Laporan',
            'reportId' => $report->id, // Tambahkan ID laporan ke breadcrumbs
        ];

        return view('admin.reports.show', compact('report', 'breadcrumbs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Report $report)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['Reviewed', 'Not Reviewed'])]
        ]);

        $oldStatus = $report->status;
        $newStatus = $validated['status'];

        $report->update([
            'status' => $newStatus
        ]);

        activity()
            ->performedOn($report)
            ->causedBy(Auth::id())
            ->log("Laporan #{$report->id} diubah status dari '{$oldStatus}' menjadi '{$newStatus}'.");

        return redirect()
            ->route('admin.reports.index', ['status' => $report->status])
            ->with('success', "Report #{$report->id} has been marked as {$report->status}.");
    }
}
