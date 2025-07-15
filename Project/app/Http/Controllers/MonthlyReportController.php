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
        // $workerId = Auth::id(); // Use Auth::id() for actual authenticated user
        $workerId = 33; // For testing with a fixed ID, uncomment if needed

        $worker = User::find($workerId); // Assuming User is your "worker" profile

        if (!$worker) {
            // If worker is not found (e.g., not logged in or ID is invalid), redirect with an error.
            return redirect()->back()->with('error', 'Worker profile not found. Please log in.');
        }

        // --- 2. Determine Report Period ---
        // Ensure 'created_at' is cast to 'datetime' in your User model for Carbon functionality
        $joinedAt = $worker->created_at;
        $now = Carbon::now(); // Current time: Tuesday, July 15, 2025 at 12:28:39 PM WIB.
        // dd($now, $now->copy()->startOfMonth(), $now);

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
        $parsedSelectedMonth = Carbon::createFromFormat('Y-m', $selectedMonthValue)->startOfMonth(); // Pastikan mulai dari awal bulan

        // Set report period display string (e.g., "Juli 2025")
        $reportPeriod = $parsedSelectedMonth->translatedFormat('F Y');

        // Define the start and end of the selected month for database queries
        $startOfMonth = $parsedSelectedMonth->copy()->startOfMonth();
        $endOfMonth = $parsedSelectedMonth->copy()->endOfMonth();

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
                $minutes = $transaction->start_work->diffInMinutes($transaction->finish_work);
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
        $currentDateLoop = $parsedSelectedMonth->copy()->startOfMonth();
        // Inisialisasi tanggal akhir loop
        $endDateLoop = $parsedSelectedMonth->copy()->endOfMonth();
        // Jika bulan yang dipilih adalah bulan saat ini
        if ($parsedSelectedMonth->format('Y-m') === $now->format('Y-m')) {
            // Maka tanggal akhir loop adalah tanggal hari ini
            $endDateLoop = $now->copy();
        }

        // Loop dari awal bulan yang dipilih hingga endDateLoop
        // dd($currentDateLoop, $endDateLoop);
        while ($currentDateLoop->lte($endDateLoop)) {

            $dayLabel = $currentDateLoop->format('d M'); // e.g., "01 Jul", "15 Jul"
            $chartLabels[] = $dayLabel;

            // Filter transaksi untuk hari ini
            $transactionsForDay = $completedTransactions->filter(function ($transaction) use ($currentDateLoop) {
                // Pastikan 'finish_work' adalah instance Carbon untuk menggunakan isSameDay
                return $transaction->finish_work instanceof Carbon && $transaction->finish_work->isSameDay($currentDateLoop);
            });

            // Hitung pendapatan untuk hari ini
            $dailyEarnings = $transactionsForDay->sum(function ($transaction) {
                return $transaction->request ? $transaction->request->price : 0;
            });
            $chartEarningsData[] = $dailyEarnings;

            // Hitung pekerjaan yang selesai untuk hari ini
            $dailyJobsCompleted = $transactionsForDay->count();
            $chartJobsCompletedData[] = $dailyJobsCompleted;

            $currentDateLoop->addDay(); // Lanjut ke hari berikutnya
        }

        // dd($chartLabels, $chartEarningsData, $chartJobsCompletedData); // Untuk debugging

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

        return view('Job_Taker.monthly-report-b', $data);
    }

    public function downloadReportPdf(Request $request)
    {
        // Untuk konsistensi, kita perlu mengambil data yang sama seperti di metode index
        // agar laporan PDF memiliki data yang akurat untuk periode yang dipilih.

        // 1. Dapatkan informasi Worker (sama seperti di index)
        // $workerId = Auth::id();
        $workerId = 33; // For testing
        $worker = User::find($workerId);

        if (!$worker) {
            return redirect()->back()->with('error', 'Worker profile not found. Please log in.');
        }

        // 2. Tentukan Periode Laporan (sama seperti di index)
        $now = Carbon::now();
        $selectedMonthValue = $request->input('report_month', $now->format('Y-m'));
        $parsedSelectedMonth = Carbon::createFromFormat('Y-m', $selectedMonthValue)->startOfMonth();
        $reportPeriod = $parsedSelectedMonth->translatedFormat('F Y');

        $startOfMonth = $parsedSelectedMonth->copy()->startOfMonth();
        $endOfMonth = $parsedSelectedMonth->copy()->endOfMonth();

        // 3. Fetch Data Laporan (sama seperti di index)
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
            ->with('reviewer')
            ->latest()
            ->get();

        // Siapkan data untuk view PDF
        $data = [
            'worker' => $worker,
            'reportPeriod' => $reportPeriod,
            'totalJobsCompleted' => $totalJobsCompleted,
            'totalEarnings' => $totalEarnings,
            'averageRating' => $averageRating,
            'jobHistory' => $jobHistory,
            'clientReviews' => $clientReviews,
        ];

        // Muat view Blade ke Dompdf dan buat PDF
        $pdf = Pdf::loadView('Job_Taker.pdf.report-pdf', $data);

        // Unduh PDF dengan nama file yang sesuai
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
