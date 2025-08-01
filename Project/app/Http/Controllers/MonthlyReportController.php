<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Review;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Models\Request as ModelsRequest; 

class MonthlyReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        
        $workerId = Auth::id(); 
        

        $worker = User::find($workerId); 

        if (!$worker) {
            
            return redirect()->back()->with('custom_error_alert', 'Profil pekerja tidak ditemukan. Silakan login.');
        }

        
        
        $joinedAt = $worker->created_at->setTimezone('Asia/Jakarta'); 
        $now = Carbon::now('Asia/Jakarta'); 

        $availableMonths = [];
        $currentMonthForDropdown = $joinedAt->copy()->startOfMonth();
        
        while ($currentMonthForDropdown->lte($now->copy()->startOfMonth())) {
            $availableMonths[] = [
                'value' => $currentMonthForDropdown->format('Y-m'), 
                'label' => $currentMonthForDropdown->translatedFormat('F Y'), 
            ];
            $currentMonthForDropdown->addMonth(); 
        }

        
        
        $selectedMonthValue = $request->input('report_month', $now->format('Y-m'));

        
        
        $parsedSelectedMonth = Carbon::createFromFormat('Y-m', $selectedMonthValue, 'Asia/Jakarta')->startOfMonth(); 

        
        $reportPeriod = $parsedSelectedMonth->translatedFormat('F Y');

        
        $startOfMonth = $parsedSelectedMonth->copy()->startOfMonth()->setTimezone('UTC'); 
        $endOfMonth = $parsedSelectedMonth->copy()->endOfMonth()->setTimezone('UTC');   

        
        
        
        $completedTransactions = Transaction::where('worker_id', $worker->id)
            ->whereBetween('finish_work', [$startOfMonth, $endOfMonth]) 
            ->where('status', 'completed')
            ->with('request') 
            ->get();

        
        $totalJobsCompleted = $completedTransactions->count();
        
        
        $totalHoursWorked = $completedTransactions->sum(function ($transaction) {
            if ($transaction->start_work && $transaction->finish_work) {
                
                $startWorkAsiaJakarta = $transaction->start_work->setTimezone('Asia/Jakarta');
                $finishWorkAsiaJakarta = $transaction->finish_work->setTimezone('Asia/Jakarta');
                $minutes = $startWorkAsiaJakarta->diffInMinutes($finishWorkAsiaJakarta);
                return $minutes / 60; 
            }
            return 0; 
        });
        $totalHoursWorked = number_format($totalHoursWorked, 1);


        
        $totalEarnings = $completedTransactions->sum(function ($transaction) {
            return $transaction->request ? $transaction->request->price : 0;
        });


        
        $transactionIdsForReviews = $completedTransactions->pluck('id');
        $averageRating = Review::whereIn('transaction_id', $transactionIdsForReviews)
            ->avg('rating');
        $averageRating = round($averageRating ?? 0, 1);

        
        $distinctClients = $completedTransactions->unique('requester_id')->count();

        
        $jobHistory = Transaction::where('worker_id', $worker->id)
            ->whereBetween('finish_work', [$startOfMonth, $endOfMonth])
            ->where('status', 'completed')
            ->with(['request', 'requester'])
            ->orderBy('finish_work', 'desc')
            ->get();

        
        $clientReviews = Review::whereIn('transaction_id', $transactionIdsForReviews)
            ->where('reviewee_id', $worker->id) 
            ->with('reviewer')
            ->latest()
            ->get();
        
        $achievements = [
            'Top Worker of the Month' => ($totalJobsCompleted >= 20 && $averageRating >= 4.9),
            '5 Consecutive Successful Jobs' => false,
            '3 Consecutive 5-Star Ratings' => ($clientReviews->where('rating', 5)->count() >= 3),
        ];

        
        $adminFeedback = "Kerja kamu bulan ini luar biasa! Terus pertahankan kualitas ya 👏";

        
        $verificationStatus = $worker->is_verified ? 'Terverifikasi' : 'Belum Terverifikasi';

        
        $chartLabels = [];
        $chartEarningsData = [];
        $chartJobsCompletedData = [];

        
        $currentDateLoop = $parsedSelectedMonth->copy()->startOfMonth(); 
        
        $endDateLoop = $parsedSelectedMonth->copy()->endOfMonth(); 
        
        if ($parsedSelectedMonth->format('Y-m') === $now->format('Y-m')) {
            
            $endDateLoop = $now->copy();
        }

        
        while ($currentDateLoop->lte($endDateLoop)) {

            $dayLabel = $currentDateLoop->format('d M'); 
            $chartLabels[] = $dayLabel;

            
            $transactionsForDay = $completedTransactions->filter(function ($transaction) use ($currentDateLoop) {
                
                return $transaction->finish_work instanceof Carbon && $transaction->finish_work->setTimezone('Asia/Jakarta')->isSameDay($currentDateLoop);
            });

            
            $dailyEarnings = $transactionsForDay->sum(function ($transaction) {
                return $transaction->request ? $transaction->request->price : 0;
            });
            $chartEarningsData[] = $dailyEarnings;

            
            $dailyJobsCompleted = $transactionsForDay->count();
            $chartJobsCompletedData[] = $dailyJobsCompleted;

            $currentDateLoop->addDay(); 
        }

        
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
        
        

        
        $workerId = Auth::id();
        
        $worker = User::find($workerId);

        if (!$worker) {
            return redirect()->back()->with('custom_error_alert', 'Worker profile not found. Please log in.');
        }

        
        $now = Carbon::now('Asia/Jakarta'); 
        $selectedMonthValue = $request->input('report_month', $now->format('Y-m'));
        $parsedSelectedMonth = Carbon::createFromFormat('Y-m', $selectedMonthValue, 'Asia/Jakarta')->startOfMonth(); 
        $reportPeriod = $parsedSelectedMonth->translatedFormat('F Y');

        $startOfMonth = $parsedSelectedMonth->copy()->startOfMonth()->setTimezone('UTC'); 
        $endOfMonth = $parsedSelectedMonth->copy()->endOfMonth()->setTimezone('UTC');   

        
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
            ->where('reviewee_id', $worker->id) 
            ->with('reviewer')
            ->latest()
            ->get();
        
        $data = [
            'worker' => $worker,
            'reportPeriod' => $reportPeriod,
            'totalJobsCompleted' => $totalJobsCompleted,
            'totalEarnings' => $totalEarnings,
            'averageRating' => $averageRating,
            'jobHistory' => $jobHistory,
            'clientReviews' => $clientReviews,
        ];

        
        $pdf = Pdf::loadView('job-taker.pdf.report-pdf', $data);
        activity()
            ->inLog('Document')
            ->on($worker)
            ->causedBy($worker)
            ->log("Pekerja {$worker->first_name} telah mengunduh laporan bulanan untuk periode {$reportPeriod} pada " . Carbon::now('Asia/Jakarta')->format('d M Y, H:i:s') . "."); 

        
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
