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
        // --- Statistik Pengguna ---
        $totalUsers = User::count();
        $totalWorkers = User::where('is_worker', true)->count();

        // MENGAMBIL DATA LOGIN HARI INI DARI activity_log MENGGUNAKAN LIKE
        $activeUsersToday = Activity::where('description', 'like', 'User telah login%')
            ->whereDate('created_at', Carbon::today())
            ->distinct('causer_id')
            ->count();

        // --- Statistik Laporan ---
        $pendingReportsCount = Report::where('status', 'Not Reviewed')->count();

        // --- Statistik Keuangan Perusahaan ---
        $totalCompanyProfit = ServiceRequest::where('status', 'closed')->sum('service_fee');

        // --- Log Aktivitas Terbaru (Tabel) ---
        $recentActivities = Activity::with('causer')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // --- Data untuk Grafik (Aktivitas Pengguna Mingguan) ---
        $chartLabels = [];
        $chartData = []; // Ini akan menjadi 'earningsData' Anda (jumlah pengguna aktif)
        $jobsCompletedData = []; // Data dummy untuk 'jobsCompletedData'

        for ($i = 6; $i >= 0; $i--) { // Loop untuk 7 hari terakhir
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('D, M d');

            // MENGAMBIL DATA LOGIN UNTUK GRAFIK DARI activity_log MENGGUNAKAN LIKE
            $dailyLogins = Activity::where('description', 'like', 'User telah login%')
                ->whereDate('created_at', $date)
                ->distinct('causer_id')
                ->count();
            $chartData[] = $dailyLogins; // Data jumlah login/pengguna aktif untuk chart

            // Data dummy untuk 'jobsCompletedData' (jika ingin garis kedua)
            $jobsCompletedData[] = rand(5, 20); // Contoh data acak
        }

        // --- Statistik Verifikasi Pending ---
        $pendingVerificationsCount = 0;
        if (class_exists(VerificationRequest::class)) {
            $pendingVerificationsCount = VerificationRequest::where('status', 'pending')->count();
        }

        // Data Breadcrumbs
        $breadcrumbs = [
            'mainPageTitle' => 'Admin',
            'currentPageTitle' => 'Dashboard',
            'currentSectionTitle' => 'Ringkasan Statistik',
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
            'breadcrumbs' // Tambahkan breadcrumbs ke compact
        ));
    }
}
