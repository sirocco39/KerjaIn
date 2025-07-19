<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * This method displays reports, allowing filtering by status.
     * It defaults to showing 'Not Reviewed' reports first.
     */
    public function index(Request $request)
    {
        // Validate the incoming status filter
        $request->validate([
            'status' => ['sometimes', 'in:Reviewed,Not Reviewed']
        ]);

        // Get the status from the query string, defaulting to 'Not Reviewed'
        $statusFilter = $request->query('status', 'Not Reviewed');

        // Eager load relationships (reporter, reported) to prevent N+1 query issues
        $reports = Report::with(['reporter', 'reported'])
            ->where('status', $statusFilter)
            ->latest() // Show the newest reports first
            ->paginate(15); // Paginate the results

        return view('admin.reports.index', [
            'reports' => $reports,
            'currentStatus' => $statusFilter
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * This method updates the status of a single report.
     */
    public function update(Request $request, Report $report)
    {
        // Validate that the new status is one of the allowed values
        $validated = $request->validate([
            'status' => ['required', Rule::in(['Reviewed', 'Not Reviewed'])]
        ]);

        // Update the report's status
        $report->update([
            'status' => $validated['status']
        ]);

        // Redirect back to the index page with a success message
        return redirect()
            ->route('admin.reports.index', ['status' => $report->status])
            ->with('success', "Report #{$report->id} has been marked as {$report->status}.");
    }
}
