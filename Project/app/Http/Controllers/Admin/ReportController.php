<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Activitylog\Models\Activity;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Kontroler index ini tidak perlu diubah karena kita tidak menampilkan hitungan di sini.
        $request->validate([
            'status' => ['sometimes', 'in:Reviewed,Not Reviewed']
        ]);

        $statusFilter = $request->query('status', 'Not Reviewed');

        $reports = Report::with(['reporter', 'reported'])
            ->where('status', $statusFilter)
            ->latest()
            ->paginate(15);

        return view('admin.reports.index', [
            'reports' => $reports,
            'currentStatus' => $statusFilter
        ]);
    }

    /**
     * Display the specified resource.
     *
     * This method displays the detailed view of a single report.
     */
    public function show(Report $report)
    {
        // Eager load relationships dan tambahkan hitungan laporan yang diterima oleh reported user
        $report->load(['reporter', 'reported.reportsReceived']); // Load reported user dan relasi reportsReceived-nya

        return view('admin.reports.show', compact('report'));
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
            ->causedBy(auth()->user())
            ->log("Laporan #{$report->id} diubah status dari '{$oldStatus}' menjadi '{$newStatus}'.");

        return redirect()
            ->route('admin.reports.index', ['status' => $report->status])
            ->with('success', "Report #{$report->id} has been marked as {$report->status}.");
    }
}
