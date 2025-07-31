<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Review;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Models\Request as ModelsRequest; // Renamed to avoid conflict with Illuminate\Http\Request

class MonthlyReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // --- 1. Get Worker Information ---
        // Get the ID of the authenticated user
        $workerId = Auth::id(); // Use Auth::id() for actual authenticated user
        // $workerId = 33; // For testing with a fixed ID, uncomment if needed

        $worker = User::find($workerId); // Assuming User is your "worker" profile

        if (!$worker) {
            // If worker is not found (e.g., not logged in or ID is invalid), redirect with an error.
            return redirect()->back()->with('custom_error_alert', 'Profil pekerja tidak ditemukan. Silakan login.');
        }

        // --- 2. Determine Report Period ---
        // Ensure 'created_at' is cast to 'datetime' in your User model for Carbon functionality
        $joinedAt = $worker->created_at->setTimezone('Asia/Jakarta'); // Get joined date in UTC+7
        $now = Carbon::now('Asia/Jakarta'); // Current time in UTC+7

        $availableMonths = [];
        $currentMonthForDropdown = $joinedAt->copy()->startOfMonth();
        // Loop to generate months from joined date until the current month for the dropdown
        while ($currentMonthForDropdown->lte($now->copy()->startOfMonth())) {
            $availableMonths[] = [
                'value' => $currentMonthForDropdown->format('Y-m'), // e.g., "2023-01" for form submission
                'label' => $currentMonthForDropdown->translatedFormat('F Y'), // e.g., "Januari 2023" for display
            ];
            $currentMonthForDropdown->addMonth(); // Move to the next month
        }

        // Determine the selected report month from the request input.
        // Default to the current month if no specific month is selected.
        $selectedMonthValue = $request->input('report_month', $now->format('Y-m'));

        // Parse the selected month value into a Carbon object
        // This Carbon object will be used for filtering database queries for the current report view
        $parsedSelectedMonth = Carbon::createFromFormat('Y-m', $selectedMonthValue, 'Asia/Jakarta')->startOfMonth(); // Ensure start of month in Asia/Jakarta

        // Set report period display string (e.g., "Juli 2025")
        $reportPeriod = $parsedSelectedMonth->translatedFormat('F Y');

        // Define the start and end of the selected month for database queries
        $startOfMonth = $parsedSelectedMonth->copy()->startOfMonth()->setTimezone('UTC'); // Convert to UTC for DB query
        $endOfMonth = $parsedSelectedMonth->copy()->endOfMonth()->setTimezone('UTC');   // Convert to UTC for DB query

        // --- 3. Fetch Data for Monthly Report ---
        // Fetch completed transactions for the authenticated worker within the selected month.
        // We filter by 'finish_work' as it indicates when the job was completed.
        $completedTransactions = Transaction::where('worker_id', $worker->id)
            ->whereBetween('finish_work', [$startOfMonth, $endOfMonth]) // Filter by finish_work for completed jobs
            ->where('status', 'completed')
            ->with('request') // Eager load the related Request (Job) model
            ->get();

        // A. Summary Statistics
        $totalJobsCompleted = $completedTransactions->count();
        // Calculate total hours worked from start_work and finish_work
        // Ensure start_work and finish_work are cast to 'datetime' in the Transaction model
        $totalHoursWorked = $completedTransactions->sum(function ($transaction) {
            if ($transaction->start_work && $transaction->finish_work) {
                // Parse timestamps to Asia/Jakarta for calculation consistency
                $startWorkAsiaJakarta = $transaction->start_work->setTimezone('Asia/Jakarta');
                $finishWorkAsiaJakarta = $transaction->finish_work->setTimezone('Asia/Jakarta');
                $minutes = $startWorkAsiaJakarta->diffInMinutes($finishWorkAsiaJakarta);
                return $minutes / 60; // Return hours as a float
            }
            return 0; // Return 0 if timestamps are missing
        });
        $totalHoursWorked = number_format($totalHoursWorked, 1);


        // Calculate total earnings from 'price' on the related Request
        $totalEarnings = $completedTransactions->sum(function ($transaction) {
            return $transaction->request ? $transaction->request->price : 0;
        });


        // Calculate average rating for reviews related to completed transactions in this period
        $transactionIdsForReviews = $completedTransactions->pluck('id');
        $averageRating = Review::whereIn('transaction_id', $transactionIdsForReviews)
            ->avg('rating');
        $averageRating = round($averageRating ?? 0, 1);

        // Get distinct clients (requesters) from the transactions
        $distinctClients = $completedTransactions->unique('requester_id')->count();

        // B. Job History (now Transaction History) for the selected month
        $jobHistory = Transaction::where('worker_id', $worker->id)
            ->whereBetween('finish_work', [$startOfMonth, $endOfMonth])
            ->where('status', 'completed')
            ->with(['request', 'requester'])
            ->orderBy('finish_work', 'desc')
            ->get();

        // C. Reviews from Clients for the selected month's completed transactions
        $clientReviews = Review::whereIn('transaction_id', $transactionIdsForReviews)
            ->where('reviewee_id', $worker->id) // Filter reviews for the worker
            ->with('reviewer')
            ->latest()
            ->get();
        // D. Gamification / Achievements (Placeholder - implement your logic)
        $achievements = [
            'Top Worker of the Month' => ($totalJobsCompleted >= 20 && $averageRating >= 4.9),
            '5 Consecutive Successful Jobs' => false,
            '3 Consecutive 5-Star Ratings' => ($clientReviews->where('rating', 5)->count() >= 3),
        ];

        // E. Admin Feedback (Placeholder - could be from a dedicated table)
        $adminFeedback = "Kerja kamu bulan ini luar biasa! Terus pertahankan kualitas ya 👏";

        // F. Verification Status (Placeholder - depends on your system)
        $verificationStatus = $worker->is_verified ? 'Terverifikasi' : 'Belum Terverifikasi';

        // --- Data for Chart (Daily Earnings/Jobs Completed Graph) within the SELECTED MONTH ---
        $chartLabels = [];
        $chartEarningsData = [];
        $chartJobsCompletedData = [];

        // Inisialisasi tanggal awal loop
        $currentDateLoop = $parsedSelectedMonth->copy()->startOfMonth(); // Already Asia/Jakarta
        // Inisialisasi tanggal akhir loop
        $endDateLoop = $parsedSelectedMonth->copy()->endOfMonth(); // Already Asia/Jakarta
        // Jika bulan yang dipilih adalah bulan saat ini
        if ($parsedSelectedMonth->format('Y-m') === $now->format('Y-m')) {
            // Maka tanggal akhir loop adalah tanggal hari ini (in Asia/Jakarta)
            $endDateLoop = $now->copy();
        }

        // Loop from the start of the selected month until endDateLoop
        while ($currentDateLoop->lte($endDateLoop)) {

            $dayLabel = $currentDateLoop->format('d M'); // e.g., "01 Jul", "15 Jul" (already UTC+7)
            $chartLabels[] = $dayLabel;

            // Filter transactions for today (compare finish_work in UTC+7)
            $transactionsForDay = $completedTransactions->filter(function ($transaction) use ($currentDateLoop) {
                // Convert transaction's finish_work to Asia/Jakarta for comparison
                return $transaction->finish_work instanceof Carbon && $transaction->finish_work->setTimezone('Asia/Jakarta')->isSameDay($currentDateLoop);
            });

            // Calculate earnings for today
            $dailyEarnings = $transactionsForDay->sum(function ($transaction) {
                return $transaction->request ? $transaction->request->price : 0;
            });
            $chartEarningsData[] = $dailyEarnings;

            // Calculate completed jobs for today
            $dailyJobsCompleted = $transactionsForDay->count();
            $chartJobsCompletedData[] = $dailyJobsCompleted;

            $currentDateLoop->addDay(); // Move to the next day
        }

        // --- Prepare data for the view ---
        $data = [
            'worker' => $worker,
            'reportPeriod' => $reportPeriod,
            'availableMonths' => $availableMonths,
            'verificationStatus' => $verificationStatus,
            'totalJobsCompleted' => $totalJobsCompleted,
            'totalHoursWorked' => $totalHoursWorked,
            'totalEarnings' => $totalEarnings,
            'averageRating' => $averageRating,
            'distinctClients' => $distinctClients,
            'jobHistory' => $jobHistory,
            'clientReviews' => $clientReviews,
            'achievements' => $achievements,
            'adminFeedback' => $adminFeedback,
            'chartLabels' => json_encode($chartLabels),
            'chartEarningsData' => json_encode($chartEarningsData),
            'chartJobsCompletedData' => json_encode($chartJobsCompletedData),

        ];

        return view('job-taker.monthly-report', $data);
    }

    public function downloadReportPdf(Request $request)
    {
        // For consistency, we need to retrieve the same data as in the index method
        // so that the PDF report has accurate data for the selected period.

        // 1. Get Worker Information (same as in index)
        $workerId = Auth::id();
        // $workerId = 33; // For testing
        $worker = User::find($workerId);

        if (!$worker) {
            return redirect()->back()->with('custom_error_alert', 'Worker profile not found. Please log in.');
        }

        // 2. Determine Report Period (same as in index)
        $now = Carbon::now('Asia/Jakarta'); // Current time in UTC+7
        $selectedMonthValue = $request->input('report_month', $now->format('Y-m'));
        $parsedSelectedMonth = Carbon::createFromFormat('Y-m', $selectedMonthValue, 'Asia/Jakarta')->startOfMonth(); // Ensure start of month in Asia/Jakarta
        $reportPeriod = $parsedSelectedMonth->translatedFormat('F Y');

        $startOfMonth = $parsedSelectedMonth->copy()->startOfMonth()->setTimezone('UTC'); // Convert to UTC for DB query
        $endOfMonth = $parsedSelectedMonth->copy()->endOfMonth()->setTimezone('UTC');   // Convert to UTC for DB query

        // 3. Fetch Report Data (same as in index)
        $completedTransactions = Transaction::where('worker_id', $worker->id)
            ->whereBetween('finish_work', [$startOfMonth, $endOfMonth])
            ->where('status', 'completed')
            ->with('request')
            ->get();

        $totalJobsCompleted = $completedTransactions->count();
        $totalEarnings = $completedTransactions->sum(function ($transaction) {
            return $transaction->request ? $transaction->request->price : 0;
        });

        $transactionIdsForReviews = $completedTransactions->pluck('id');
        $averageRating = Review::whereIn('transaction_id', $transactionIdsForReviews)->avg('rating');
        $averageRating = round($averageRating ?? 0, 1);

        $jobHistory = Transaction::where('worker_id', $worker->id)
            ->whereBetween('finish_work', [$startOfMonth, $endOfMonth])
            ->where('status', 'completed')
            ->with(['request', 'requester'])
            ->orderBy('finish_work', 'desc')
            ->get();

        $clientReviews = Review::whereIn('transaction_id', $transactionIdsForReviews)
            ->where('reviewee_id', $worker->id) // Filter reviews for the worker
            ->with('reviewer')
            ->latest()
            ->get();
        // Prepare data for PDF view
        $data = [
            'worker' => $worker,
            'reportPeriod' => $reportPeriod,
            'totalJobsCompleted' => $totalJobsCompleted,
            'totalEarnings' => $totalEarnings,
            'averageRating' => $averageRating,
            'jobHistory' => $jobHistory,
            'clientReviews' => $clientReviews,
        ];

        // Load Blade view into Dompdf and generate PDF
        $pdf = Pdf::loadView('job-taker.pdf.report-pdf', $data);
        activity()
            ->inLog('Document')
            ->on($worker)
            ->causedBy($worker)
            ->log("Pekerja {$worker->first_name} telah mengunduh laporan bulanan untuk periode {$reportPeriod} pada " . Carbon::now('Asia/Jakarta')->format('d M Y, H:i:s') . "."); // Log in Asia/Jakarta timezone

        // Download PDF with appropriate filename
        return $pdf->download('laporan-bulanan-' . $worker->name . '-' . $parsedSelectedMonth->format('Y-m') . '.pdf');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
