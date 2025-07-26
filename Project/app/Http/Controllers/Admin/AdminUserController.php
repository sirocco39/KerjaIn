<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Report;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\DB; // Tambahkan ini

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
        $searchQuery = $request->input('search_query'); // Ini akan digunakan untuk menampilkan nilai di input
        $selectedUserId = $request->input('user_id'); // Ini ID pengguna yang dipilih dari autocomplete

        $activityLogs = Activity::with('causer')
            ->orderByDesc('created_at');

        // Logika filtering activityLogs
        if ($selectedUserId) {
            // Jika ada user_id yang dipilih dari autocomplete
            $searchedUser = User::find($selectedUserId);
            if ($searchedUser) {
                $activityLogs->where(function ($query) use ($searchedUser) {
                    $query->where('causer_id', $searchedUser->id)
                        ->where('causer_type', get_class($searchedUser));
                });
            } else {
                // Jika ID tidak valid, tampilkan log kosong
                $activityLogs = Activity::whereRaw('1 = 0');
            }
        } elseif ($searchQuery) {
            // Jika hanya ada search_query (dari form submit biasa, bukan autocomplete)
            // Kita masih ingin menampilkan hasil yang relevan, tapi tanpa memilih satu user spesifik
            $searchedUser = User::where('id', $searchQuery)
                ->orWhere(DB::raw('CONCAT(first_name, " ", last_name)'), 'like', '%' . $searchQuery . '%')
                ->orWhere('first_name', 'like', '%' . $searchQuery . '%')
                ->orWhere('last_name', 'like', '%' . $searchQuery . '%')
                ->first();

            if ($searchedUser) {
                $activityLogs = Activity::where('causer_type', User::class)
                    ->where('causer_id', $searchedUser->id)
                    ->latest()
                    ->paginate(10)
                    // === PENTING: Tambahkan ini ===
                    ->appends(request()->query());
            } else {
                $activityLogs = Activity::whereRaw('1 = 0');
            }
        } else {
            $activityLogs = Activity::latest()->paginate(10)
                // === PENTING: Tambahkan ini juga ===
                ->appends(request()->query());
        }

        // $activityLogs = $activityLogs->paginate(10);

        // Data Breadcrumbs
        $breadcrumbs = [
            'mainPageTitle' => 'Admin',
            'currentPageTitle' => 'Pengguna',
        ];

        return view('admin.users.index', compact(
            'totalUsers',
            'activeToday',
            'newUsersThisWeek',
            'totalWorkers',
            'activeWorkersToday',
            'blockedUsersCount',
            'reportedUsersCount',
            'searchedUser',    // User yang terpilih (dari ID atau yang pertama ditemukan)
            'searchQuery',     // Kueri asli dari input
            'activityLogs',
            'breadcrumbs'
        ));
    }

    /**
     * Endpoint AJAX untuk pencarian rekomendasi pengguna.
     * Ini mirip dengan searchUsersForShow di VerificationController.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchAjax(Request $request)
    {
        $query = $request->input('query');
        $isBlocked = $request->boolean('blocked'); // Check if blocked users are requested
        $isWorker = $request->boolean('is_worker'); // Check if workers are requested

        if (empty($query) || strlen($query) < 3) {
            return response()->json([]);
        }

        $users = User::where(function ($q) use ($query) {
            $q->where('id', 'like', '%' . $query . '%')
                ->orWhere(DB::raw('CONCAT(first_name, " ", last_name)'), 'like', '%' . $query . '%')
                ->orWhere('email', 'like', '%' . $query . '%');
        })
            ->when($isBlocked, function ($q) {
                return $q->where('is_blocked', true);
            })
            ->when($isWorker, function ($q) {
                return $q->where('is_worker', true);
            })
            ->select('id', 'first_name', 'last_name')
            ->limit(5) // Limit the number of recommendations
            ->get();

        return response()->json($users);
        // $query = $request->input('query');



        // // Search users by ID, first name, or last name
        // $users = User::where('id', $query)
        //     ->orWhere(DB::raw('CONCAT(first_name, " ", last_name)'), 'like', '%' . $query . '%')
        //     ->limit(10) // Limit results for recommendations
        //     ->get(['id', 'first_name', 'last_name']); // Only select necessary columns

        // return response()->json($users);
    }

    /**
     * Menampilkan daftar semua pengguna.
     */
    public function allUsers(Request $request)
    {
        $query = User::query();

        if ($searchQuery = $request->input('search_query')) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('id', $searchQuery)
                    ->orWhere(DB::raw('CONCAT(first_name, " ", last_name)'), 'like', '%' . $searchQuery . '%')
                    ->orWhere('email', 'like', '%' . $searchQuery . '%');
            });
        }

        $users = $query->paginate(10)
            ->appends(request()->query()); // Adjust pagination as needed

        // Data Breadcrumbs
        $breadcrumbs = [
            'mainPageTitle' => 'Admin',
            'currentPageTitle' => 'Pengguna',
            'currentSectionTitle' => 'Semua Pengguna',
        ];

        return view('admin.users.all-list', compact('users', 'breadcrumbs'));
    }

    /**
     * Menampilkan daftar semua pekerja.
     */
    public function allWorkers(Request $request)
    {
        $query = User::where('is_worker', true);

        if ($searchQuery = $request->input('search_query')) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('id', $searchQuery)
                    ->orWhere(DB::raw('CONCAT(first_name, " ", last_name)'), 'like', '%' . $searchQuery . '%')
                    ->orWhere('email', 'like', '%' . $searchQuery . '%');
            });
        }

        $workers = $query->paginate(10)
            ->appends(request()->query()); // Adjust pagination as needed
        // Data Breadcrumbs
        $breadcrumbs = [
            'mainPageTitle' => 'Admin',
            'currentPageTitle' => 'Pengguna',
            'currentSectionTitle' => 'Pekerja',
        ];

        return view('admin.users.worker-list', compact('workers', 'breadcrumbs'));
    }

    /**
     * Menampilkan daftar pengguna yang diblokir.
     */
    public function blockedUsers(Request $request)
    {
        $query = User::where('is_blocked', true);

        if ($searchQuery = $request->input('search_query')) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('id', $searchQuery)
                    ->orWhere(DB::raw('CONCAT(first_name, " ", last_name)'), 'like', '%' . $searchQuery . '%')
                    ->orWhere('email', 'like', '%' . $searchQuery . '%');
            });
        }

        $blockedUsers = $query->paginate(10)
            ->appends(request()->query()); // Adjust pagination as needed

        // Data Breadcrumbs
        $breadcrumbs = [
            'mainPageTitle' => 'Admin',
            'currentPageTitle' => 'Pengguna',
            'currentSectionTitle' => 'Pengguna Diblokir',
        ];

        return view('admin.users.blocked-list', compact('blockedUsers', 'breadcrumbs'));
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
            ->causedBy(Auth::id())
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
            ->causedBy(Auth::id())
            ->log('Pengguna ' . $user->first_name . ' ' . $user->last_name . ' telah dibuka blokirnya.');

        return redirect()->back()->with('success', 'Pengguna berhasil dibuka blokirnya.');
    }

    /**
     * Menampilkan daftar pengguna yang dilaporkan.
     */
    public function reportedUsers()
    {
        return redirect()->route('admin.transactions.index')->with('info', 'Anda diarahkan ke halaman transaksi untuk melihat laporan.');
    }

    /**
     * Menampilkan semua log aktivitas untuk pengguna tertentu.
     */
    public function userActivityLog($id, Request $request)
    {
        $user = User::findOrFail($id);
        $activities = Activity::where('causer_id', $user->id)
            ->where('causer_type', get_class($user))
            ->orderByDesc('created_at')
            ->paginate(20);

        $previousUrl = $request->query('from', route('admin.users.index'));

        // Data Breadcrumbs
        $breadcrumbs = [
            'mainPageTitle' => 'Admin',
            'currentPageTitle' => 'Log Aktivitas Pengguna',
            'currentSectionTitle' => 'Manajemen Pengguna',
            'userFullName' => $user->first_name . ' ' . $user->last_name,
        ];

        return view('admin.users.user-activity-log', compact('user', 'activities', 'previousUrl', 'breadcrumbs'));
    }
}
