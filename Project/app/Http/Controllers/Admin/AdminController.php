<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Report;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Request as ServiceRequest;
use Spatie\Activitylog\Models\Activity;
use App\Models\VerificationRequest;

class AdminController extends Controller
{
    /**
     * Menampilkan dashboard admin yang sederhana, fungsional, dan estetis.
     */
    public function index(Request $request)
    {
        
        $totalUsers = User::count();
        $totalWorkers = User::where('is_worker', true)->count();
        $activeUsersToday = Activity::whereDate('created_at', Carbon::today('Asia/Jakarta')) 
            ->distinct('causer_id')
            ->count('causer_id');

        
        $pendingReportsCount = Report::where('status', 'Not Reviewed')->count();

        
        $totalCompanyProfit = ServiceRequest::where('status', 'closed')->sum('service_fee');

        
        $recentActivities = Activity::with('causer')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        
        $chartLabels = [];
        $chartData = []; 
        $jobsCompletedData = []; 

        for ($i = 6; $i >= 0; $i--) { 
            $date = Carbon::today('Asia/Jakarta')->subDays($i); 
            $chartLabels[] = $date->format('D, M d'); 

            
            $dailyLogins = Activity::whereDate('created_at', $date)
                ->distinct('causer_id')
                ->count();
            $chartData[] = $dailyLogins; 

            
            $jobsCompletedData[] = rand(5, 20); 
        }

        
        $pendingVerificationsCount = 0;
        if (class_exists(VerificationRequest::class)) {
            $pendingVerificationsCount = VerificationRequest::where('status', 'pending')->count();
        }

        
        $breadcrumbs = [
            'mainPageTitle' => 'Admin',
            'currentPageTitle' => 'Dashboard',
        ];

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalWorkers',
            'activeUsersToday',
            'pendingReportsCount',
            'totalCompanyProfit',
            'recentActivities',
            'chartLabels',
            'chartData',
            'jobsCompletedData',
            'pendingVerificationsCount',
            'breadcrumbs' 
        ));
    }
}
