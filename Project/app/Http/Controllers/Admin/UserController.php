<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Report; // For reported users
use Illuminate\Support\Facades\DB; // For database queries
use App\Models\Session; // Assuming you have a Session model or access to session data

class UserController extends Controller
{
    /**
     * Menampilkan halaman manajemen pengguna.
     * Termasuk fungsionalitas pencarian, statistik, dan grafik pengguna aktif.
     */
    public function index(Request $request)
    {
        // --- Statistik Ringkasan Pengguna ---
        $totalUsers = User::count();
        $activeToday = User::whereDate('last_activity', Carbon::today())->count(); // Assuming 'last_activity' column in users table or sessions
        $newUsersThisWeek = User::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();

        // Total Pekerja dan Aktif hari ini (Jika ada kolom is_worker)
        $totalWorkers = User::where('is_worker', true)->count();
        $activeWorkersToday = User::where('is_worker', true)
            ->whereDate('last_activity', Carbon::today()) // Asumsi pekerja juga punya last_activity
            ->count();

        // Pengguna Diblokir dan Dilaporkan
        $blockedUsersCount = User::where('is_blocked', true)->count(); // Asumsi kolom 'is_blocked' di tabel users
        $reportedUsersCount = Report::distinct('reported_id')->count('reported_id'); // Menghitung jumlah user yang dilaporkan

        // --- Pencarian Pengguna ---
        $searchedUser = null;
        $searchQuery = $request->input('search_query');

        if ($searchQuery) {
            $searchedUser = User::where('id', $searchQuery)
                ->orWhere('first_name', 'like', '%' . $searchQuery . '%')
                ->orWhere('last_name', 'like', '%' . $searchQuery . '%')
                ->first(); // Mengambil satu user yang cocok
        }

        // --- Data Tabel Log Login Terbaru (menggunakan model Session jika ada) ---
        // Jika tabel sessions Anda menyimpan user_id dan last_activity/login_time
        // Jika tidak, Anda perlu cara lain untuk melacak login user
        $recentLogins = Session::whereNotNull('user_id')
            ->with('user') // Memuat data user terkait
            ->orderByDesc('last_activity')
            ->take(7) // Mengambil 7 log login terbaru
            ->get();

        // --- Data untuk Grafik Pengguna Aktif (Line Chart) ---
        $activeUsersChartData = $this->getActiveUsersChartData();


        return view('admin.users.index', compact(
            'totalUsers',
            'activeToday',
            'newUsersThisWeek',
            'totalWorkers',
            'activeWorkersToday',
            'blockedUsersCount',
            'reportedUsersCount',
            'searchedUser',
            'searchQuery',
            'recentLogins',
            'activeUsersChartData'
        ));
    }

    /**
     * Mengambil data pengguna aktif per hari dalam seminggu terakhir.
     */
    private function getActiveUsersChartData()
    {
        $labels = []; // Tanggal
        $data = [];   // Jumlah pengguna aktif

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('D, M d'); // Misal: Mon, Jul 01

            // Menghitung pengguna aktif pada tanggal tersebut
            // Asumsi: last_activity adalah kolom timestamp di tabel users
            // Atau Anda bisa menggunakan tabel sessions dan menghitung distinct user_id per hari
            $activeUsersCount = User::whereDate('last_activity', $date)->count();
            $data[] = $activeUsersCount;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    // Metode untuk menampilkan detail pengguna (jika diperlukan oleh rute admin.users.show)
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    // Metode untuk menampilkan daftar pengguna yang diblokir (jika diperlukan)
    public function blockedUsers()
    {
        $blockedUsers = User::where('is_blocked', true)->get();
        return view('admin.users.blocked-list', compact('blockedUsers'));
    }

    // Metode untuk menampilkan daftar pengguna yang dilaporkan (jika diperlukan)
    public function reportedUsers()
    {
        $reportedUsers = User::whereIn('id', function ($query) {
            $query->select('reported_id')->from('reports');
        })->get();
        return view('admin.users.reported_list', compact('reportedUsers'));
    }
}
