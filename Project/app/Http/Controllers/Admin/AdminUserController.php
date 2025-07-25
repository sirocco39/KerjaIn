<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Report;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity; // Import model Activity dari Spatie

class AdminUserController extends Controller
{
    /**
     * Menampilkan halaman manajemen pengguna.
     * Termasuk fungsionalitas pencarian dan tabel logging.
     */
    public function index(Request $request)
    {
        // --- Statistik Ringkasan Pengguna ---
        $totalUsers = User::count();
        $activeToday = User::whereDate('last_activity', Carbon::today())->count();
        $newUsersThisWeek = User::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();

        $totalWorkers = User::where('is_worker', true)->count();
        $activeWorkersToday = User::where('is_worker', true)
            ->whereDate('last_activity', Carbon::today())
            ->count();

        $blockedUsersCount = User::where('is_blocked', true)->count();
        $reportedUsersCount = Report::distinct('reported_id')->count('reported_id');

        // --- Pencarian Pengguna dan Filtering Log ---
        $searchedUser = null;
        $searchQuery = $request->input('search_query');
        $activityLogs = Activity::with('causer') // Memuat relasi causer (pengguna yang melakukan aktivitas)
            ->orderByDesc('created_at');

        if ($searchQuery) {
            $searchedUser = User::where('id', $searchQuery)
                ->orWhere('first_name', 'like', '%' . $searchQuery . '%')
                ->orWhere('last_name', 'like', '%' . $searchQuery . '%')
                ->first();

            if ($searchedUser) {
                // Filter log aktivitas berdasarkan pengguna yang dicari
                $activityLogs->where(function ($query) use ($searchedUser) {
                    $query->where('causer_id', $searchedUser->id)
                        ->where('causer_type', get_class($searchedUser));
                });
            } else {
                // Jika user tidak ditemukan, pastikan log juga kosong
                $activityLogs = Activity::whereRaw('1 = 0'); // Query yang selalu false
            }
        }

        $activityLogs = $activityLogs->paginate(10); // Paginate log aktivitas

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
            'activityLogs' // Mengganti recentLogins dengan activityLogs
        ));
    }

    /**
     * Menampilkan daftar semua pengguna.
     */
    public function allUsers(Request $request)
    {
        $users = User::paginate(15); // Tambahkan paginasi
        return view('admin.users.all-list', compact('users'));
    }

    /**
     * Menampilkan daftar semua pekerja.
     */
    public function allWorkers(Request $request)
    {
        $workers = User::where('is_worker', true)->paginate(15); // Tambahkan paginasi
        return view('admin.users.worker-list', compact('workers'));
    }

    /**
     * Menampilkan daftar pengguna yang diblokir.
     */
    public function blockedUsers()
    {
        $blockedUsers = User::where('is_blocked', true)->paginate(15); // Tambahkan paginasi
        return view('admin.users.blocked-list', compact('blockedUsers'));
    }

    /**
     * Memblokir pengguna.
     */
    public function blockUser($id)
    {
        $user = User::findOrFail($id);
        $user->is_blocked = true;
        $user->save();

        activity()
            ->performedOn($user)
            ->causedBy(Auth::id()) // Asumsi admin yang memblokir tercatat
            ->log('Pengguna ' . $user->first_name . ' ' . $user->last_name . ' telah diblokir.');

        return redirect()->back()->with('success', 'Pengguna berhasil diblokir.');
    }

    /**
     * Membuka blokir pengguna.
     */
    public function unblockUser($id)
    {
        $user = User::findOrFail($id);
        $user->is_blocked = false;
        $user->save();

        activity()
            ->performedOn($user)
            ->causedBy(Auth::id()) // Asumsi admin yang membuka blokir tercatat
            ->log('Pengguna ' . $user->first_name . ' ' . $user->last_name . ' telah dibuka blokirnya.');

        return redirect()->back()->with('success', 'Pengguna berhasil dibuka blokirnya.');
    }
    /**
     * Menampilkan daftar pengguna yang dilaporkan.
     * Sesuai permintaan, ini akan mengarah ke admin.transactions.index.
     * Jika Anda ingin halaman terpisah, Anda harus membuat view dan logika terpisah.
     */
    public function reportedUsers()
    {
        // Untuk saat ini, sesuai permintaan, kita akan redirect.
        // Jika Anda ingin menampilkan daftar pengguna yang dilaporkan di halaman terpisah,
        // Anda akan memerlukan view `admin.users.reported-list` dan logikanya di sini.
        // $reportedUsers = User::whereIn('id', function ($query) {
        //     $query->select('reported_id')->from('reports');
        // })->paginate(15);
        // return view('admin.users.reported-list', compact('reportedUsers'));

        // Mengarahkan ke admin.transactions.index sesuai permintaan
        return redirect()->route('admin.transactions.index')->with('info', 'Anda diarahkan ke halaman transaksi untuk melihat laporan.');
    }

    /**
     * Menampilkan semua log aktivitas untuk pengguna tertentu.
     */
    public function userActivityLog($id, Request $request) // Tambahkan Request $request
    {
        $user = User::findOrFail($id);
        $activities = Activity::where('causer_id', $user->id)
            ->where('causer_type', get_class($user))
            ->orderByDesc('created_at')
            ->paginate(20);

        // Dapatkan URL sebelumnya dari parameter 'from', jika ada, atau gunakan admin.users.index sebagai fallback
        $previousUrl = $request->query('from', route('admin.users.index'));

        return view('admin.users.user-activity-log', compact('user', 'activities', 'previousUrl'));
    }
}
