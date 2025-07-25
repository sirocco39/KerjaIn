<?php

use App\Http\Controllers\MonthlyReportController;
use App\Models\Request as WorkRequest;
use App\Http\Controllers\Admin\{
    AdminController,
    AdminTransactionController,
    AdminUserController,
    NotificationController,
    ReportController,
    SettingController,
    VerificationController
};
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use App\Models\Transaction;
use App\Http\Controllers\{
    BalanceController,
    TopUpController,
    WebhookController,
    TransactionController,
    WorkerRegistrationController,
    BrowseWorkRequestController,
    RequestController,
    ReviewController,
    WorkerTransactionController,
    TakerTransactionController,
    Auth\RegisteredUserController,
    Auth\SocialController,
    Auth\AuthenticatedSessionController, // Keep this import
    PusherController,
    ChatController,
    JobTakerRequestController,
    InvoiceController,
    LocalizationController
};
use App\Http\Middleware\IsWorker;
use App\Http\Middleware\PreventReRegistration;
use App\Livewire\JobTaker\Chat;
use App\Livewire\jobTaker\JobTakerChatRoom;
use App\Livewire\JobTakerChatRoom as LivewireJobTakerChatRoom;
use App\Models\ChatRoom;
use Spatie\Activitylog\Models\Activity;

// =======================
// LANDING PAGE
// =======================
Route::get('/', fn() => view('landing'))->name('landing'); // Added .name('landing') here

// =======================
// AUTH ROUTES
// =======================
Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('register', [RegisteredUserController::class, 'store']);
Route::post('/send-otp', [RegisteredUserController::class, 'sendOtp'])->name('send.otp');

// This line remains as it was:
Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');

Route::post('login', [AuthenticatedSessionController::class, 'store']);
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
// ... rute yang sudah ada ...

Route::get('/admin/pilihan', function () {
    // Pastikan hanya admin yang bisa mengakses halaman ini
    if (FacadesAuth::check() && FacadesAuth::user()->role === 'admin') {
        return view('admin.pilihan');
    }
    return redirect()->route('landing'); // Atau halaman lain jika bukan admin
})->name('admin.pilihan')->middleware('auth'); // Tambahkan middleware 'auth' untuk memastikan user sudah login
// =======================
// SOCIAL AUTH
// =======================
Route::get('/auth-google-redirect', [SocialController::class, 'google_redirect'])->name('auth-google-redirect');
Route::get('/auth-google-callback', [SocialController::class, 'google_callback'])->name('auth-google-callback');

// =======================
// JOB REQUESTER PAGES (AUTHENTICATED)
// =======================
Route::middleware('auth')->group(function () { // Apply auth middleware to job requester routes
    Route::get('/job-req/beranda', function () {
        $requesterId = FacadesAuth::id();
        $fiveLatestRequests = WorkRequest::where('requester_id', $requesterId)
            ->whereNull('deleted_at')
            ->latest()
            ->take(5)
            ->with('transaction')
            ->get();
        return view('job-requester.home', compact('fiveLatestRequests'));
    })->name('job-req.beranda');

    Route::get('/job-req/tawarkan-kerja', fn() => view('job-requester.post-work'));
    Route::post('/job-req/tawarkan-kerja', [RequestController::class, 'add']);
    Route::post('/postwork', [RequestController::class, 'add']);

    Route::get('/job-req/riwayat', [TransactionController::class, 'index'])->name('orders.index');
    Route::get('/job-req/pesan', fn() => view('job-requester.chat'))->name('jobrequester.chat');
    Route::get('/job-req/on-going-work-request/{transactionId}', [TransactionController::class, 'showOngoing'])->name('request.ongoing'); // This route is now protected

    // Job Requester Request Detail Routes
    Route::get('/request/{request:slug}', fn(WorkRequest $request) => view('request', ['workRequest' => $request]));
    Route::get('/edit/{request:slug}', fn(WorkRequest $request) => view('edit', ['workRequest' => $request]));

    // =======================
    // JOB TAKER PAGES (AUTHENTICATED)
    // =======================

    Route::middleware(['auth', IsWorker::class])->group(function () {
        Route::get('/job-taker/beranda', function () {
            $workerId = FacadesAuth::id();
            $fiveLatestTransaction = Transaction::where('worker_id', $workerId)
                ->whereNull('deleted_at')
                ->latest()
                ->take(5)
                ->with('requester', 'request')
                ->get();
            return view('job-taker.home', compact('fiveLatestTransaction'));
        })->name('job-taker.home');
        Route::get('/job-taker/beranda/{id}', [TransactionController::class, 'show']);
        Route::get('/job-taker/riwayat', [WorkerTransactionController::class, 'index'])->name('orders.index');
        Route::get('/job-taker/pesan/{selectedRoomId?}', fn($selectedRoomId = null) => view('job-taker.chat', ['chatRoomId' => $selectedRoomId]))->name('chat.job-taker');
        Route::get('/job-taker/cari-kerja', [BrowseWorkRequestController::class, 'index'])->name('browse.work.requests.index');
        Route::get('/job-taker/monthly-report', [MonthlyReportController::class, 'index'])->name('monthly.report');
        Route::get('/job-taker/monthly-report/download-pdf', [MonthlyReportController::class, 'downloadReportPdf'])->name('monthly.report.download.pdf');
        Route::view('/job-taker/pdf', 'Job_Taker.pdf.report-pdf')->name('pdf');
        Route::post('/job-taker/cari-kerja/{id}', [JobTakerRequestController::class, 'acceptRequest'])->name('job-taker.accept-request');
        Route::get('/job-taker/accepted-work-request/{id}', [WorkerTransactionController::class, 'show'])->name('job-taker.accepted-work-request');
        Route::post('/job-taker/start-work/{id}', [WorkerTransactionController::class, 'startWork'])->name('worker.startWork');
        Route::post('/job-taker/upload-proof/{transaction}', [WorkerTransactionController::class, 'uploadProof'])->name('worker.uploadProof');
        Route::post('/job-taker/submit-report/{transaction}', [WorkerTransactionController::class, 'storeReport'])->name('worker.submitReport');
        Route::get('/job-taker/top-up', [TopUpController::class, 'index'])->name('top-up.job-taker');
        Route::get('/job-taker/wallet', [BalanceController::class, 'index'])->name('balance.job-taker');
    });

    // Route::get('/browseWorkRequest', [browseWorkRequestController::class, 'index'])->name('browse.work.requests.index');

    Route::get('/requests/{request}', [BrowseWorkRequestController::class, 'show'])->name('work_requests.show');

    // =======================
    // REVIEW & COMPLETION ROUTES (AUTHENTICATED)
    // =======================
    Route::post('/transaction/{id}/cancel', [TransactionController::class, 'cancel'])->name('transaction.cancel');
    Route::post('/transaction/{transaction}/mark-complete', [TransactionController::class, 'markComplete'])->name('transaction.markComplete');
    Route::post('/user/submit-report/{transaction}', [TransactionController::class, 'submitReport'])->name('user.submitReport');

    // Avoid duplicates — keep only one valid review route
    Route::post('/reviews/{transaction}', [ReviewController::class, 'store'])->name('reviews.store');

    // =======================
    // CHAT / OFFER ROUTES (AUTHENTICATED)
    // =======================
    Route::get('/hubungi/{requestId}', [ChatController::class, 'startChat'])->name('chat.start');
    Route::post('/tawar/{requestId}', [ChatController::class, 'startOffer'])->name('chat.offer');

    // =======================
    // HIRE / ACCEPT REQUEST (AUTHENTICATED)
    // =======================
    Route::post('/requests/{request}/hire/{worker}', [RequestController::class, 'hireWorker'])->name('requests.hire');
    Route::post('/requests/{request}/accept', [RequestController::class, 'acceptRequest'])->name('requests.accept');

    Route::get('/job-req/top-up', [TopUpController::class, 'index'])->name('top-up.job-req');

    // Memproses form dan membuat invoice Xendit
    Route::post('/topup', [TopUpController::class, 'createInvoice'])->name('topup.create');

    Route::get('/job-req/wallet', [BalanceController::class, 'index'])->name('balance.job-req');

    Route::get('/wallet/balance', [BalanceController::class, 'getCurrentBalance'])->name('balance.get');

    Route::get('/topup/status/{external_id}', [TopUpController::class, 'checkStatus'])->name('topup.status');
    // =======================
    // INVOICE (AUTHENTICATED)
    // =======================
    Route::get('/generate-invoice/{transaction}', [InvoiceController::class, 'generateInvoice'])->name('generate.invoice');

    // =======================
    // WORKER REGISTRATION FLOW (AUTHENTICATED)
    // =======================
    Route::get('/joinworker', fn() => redirect()->route('worker.register.step1'));

    // Grup route untuk pendaftaran pekerja tanpa autentikasi

    Route::middleware(['auth', PreventReRegistration::class])->group(function () {
        Route::prefix('joinWorker')->name('worker.register.')->group(function () {
            // Langkah 1: Data Pribadi (Form GET, Proses POST)
            // URL: /joinWorker/join
            Route::get('/join', [WorkerRegistrationController::class, 'createStep1'])->name('step1');
            Route::post('/join', [WorkerRegistrationController::class, 'store1'])->name('store1'); // <-- KEMBALIKAN KE 'store1'

            // Langkah 2: Detail Kontrak (Form GET, Proses POST)
            // URL: /joinWorker/join2
            Route::get('/join2', [WorkerRegistrationController::class, 'createStep2'])->name('step2');
            Route::post('/join2', [WorkerRegistrationController::class, 'store2'])->name('store2');

            // Langkah 3: Verifikasi / Upload Dokumen / Finalisasi (Form GET, Proses POST)
            // URL: /joinWorker/join3
            Route::get('/join3', [WorkerRegistrationController::class, 'createStep3'])->name('step3');
            Route::post('/join3', [WorkerRegistrationController::class, 'finalizeRegistration'])->name('finalize');

            // Halaman Sukses
            // URL: /joinWorker/success
            Route::get('/success', [WorkerRegistrationController::class, 'showSuccessPage'])->name('success');
            Route::get('/pending', [WorkerRegistrationController::class, 'showPendingPage'])->name('pending');
        });
    });

    // =======================
    // MISC / NAVBAR (AUTHENTICATED)
    // =======================
    Route::get('switch-language/{locale}', [LocalizationController::class, 'switch'])->name('language.switch');

    // =======================
    // RESOURCE ROUTES
    // =======================
    // Route::resource('request', RequestController::class);


    // =======================
    // RESOURCE ROUTES (AUTHENTICATED)
    // =======================
    // Route::resource('request', RequestController::class);
    Route::post('/request/validate', [RequestController::class, 'validateRequest'])->name('request.validate');
});


// Endpoint untuk menerima webhook dari Xendit (DO NOT ADD AUTH HERE, as Xendit's server sends this)
Route::post('/webhooks/xendit', [WebhookController::class, 'handleXendit'])->name('webhooks.xendit');
// =======================
// RESOURCE ROUTES
// =======================
Route::resource('request', RequestController::class);
Route::post('/request/validate', [RequestController::class, 'validateRequest'])->name('request.validate');
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/pilihan', function () { // Pindahkan ini ke dalam grup admin
        return view('admin.pilihan');
    })->name('pilihan');

    Route::get('/activity-log', function () {
        $activities = Activity::orderBy('id', 'desc')->take(50)->get();
        return view('admin-test-iwan.activity-log', compact('activities'));
    })->name('activity');

    Route::get('/verifikasi/{status?}', [VerificationController::class, 'index'])->name('verifications.index');
    Route::get('verifications/show/{id}', [VerificationController::class, 'show'])->name('verifications.show');
    Route::post('verifications/{id}/approve', [VerificationController::class, 'approve'])->name('verifications.approve');
    Route::post('verifications/{id}/reject', [VerificationController::class, 'reject'])->name('verifications.reject');
    Route::get('/verifications/search-ajax', [VerificationController::class, 'searchUsersForShow'])->name('verifications.search-ajax');

    // Manajemen Pengguna
    Route::get('users/search', [AdminUserController::class, 'searchUsers'])->name('users.search');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/search-ajax', [AdminUserController::class, 'searchAjax'])->name('users.search-ajax');
    Route::get('/users/{user}/activity-log', [AdminUserController::class, 'userActivityLog'])->name('users.activityLog');
    Route::get('users/all', [AdminUserController::class, 'allUsers'])->name('users.all');
    Route::get('users/workers', [AdminUserController::class, 'allWorkers'])->name('users.workers');
    Route::get('users/blocked', [AdminUserController::class, 'blockedUsers'])->name('users.blockedList');
    Route::post('users/{id}/block', [AdminUserController::class, 'blockUser'])->name('users.block');
    Route::post('users/{id}/unblock', [AdminUserController::class, 'unblockUser'])->name('users.unblock');
    Route::get('users/reported', [AdminUserController::class, 'reportedUsers'])->name('users.reported-list');

    // Manajemen Laporan
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::patch('/reports/{report}', [ReportController::class, 'update'])->name('reports.update');

    // Manajemen Transaksi
    Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{id}/show', [AdminTransactionController::class, 'show'])->name('transactions.show');

    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');

    // Pengaturan
    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
});
// Route::get('/admin/activity-log', function () {
//     // Kode yang benar untuk urutan kronologis
//     $activities = Activity::orderBy('id', 'desc')->take(50)->get(); // Urutkan berdasarkan ID dari yang terkecil
//     return view('admin-test-iwan.activity-log', compact('activities'));
// })->name('admin.activity');

// Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
// Route::get('/admin/verifikasi/{status?}', [VerificationController::class, 'index'])->name('admin.verifications.index');
// Route::get('verifications/show/{id}', [VerificationController::class, 'show'])->name('admin.verifications.show');
// Route::post('verifications/{id}/approve', [VerificationController::class, 'approve'])->name('admin.verifications.approve');
// Route::post('verifications/{id}/reject', [VerificationController::class, 'reject'])->name('admin.verifications.reject');
// Route::get('/admin/verifications/search-ajax', [VerificationController::class, 'searchUsersForShow'])->name('admin.verifications.search-ajax');
// // Manajemen Pengguna (admin.users.*)   
// // Route::resource('users', AdminUserController::class);
// Route::get('users/search', [AdminUserController::class, 'searchUsers'])->name('admin.users.search');
// Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
// Route::get('/users/search-ajax', [AdminUserController::class, 'searchAjax'])->name('admin.users.search-ajax');

// // Contoh rute untuk melihat log aktivitas per user (jika diperlukan)
// Route::get('/users/{user}/activity-log', [AdminUserController::class, 'userActivityLog'])->name('admin.users.activityLog');

// // Rute baru untuk fungsionalitas yang diminta
// Route::get('users/all', [AdminUserController::class, 'allUsers'])->name('admin.users.all');
// Route::get('users/workers', [AdminUserController::class, 'allWorkers'])->name('admin.users.workers');
// Route::get('users/blocked', [AdminUserController::class, 'blockedUsers'])->name('admin.users.blockedList');

// // Rute untuk blokir dan batal blokir
// Route::post('users/{id}/block', [AdminUserController::class, 'blockUser'])->name('admin.users.block'); // Rute baru
// Route::post('users/{id}/unblock', [AdminUserController::class, 'unblockUser'])->name('admin.users.unblock');


// // Rute ini akan mengarah ke transaksi sesuai permintaan
// Route::get('users/reported', [AdminUserController::class, 'reportedUsers'])->name('admin.users.reported-list');

// // Route::resource('reports', ReportController::class)->only(['index', 'show', 'update']); // Pastikan 'show' ada
// Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');
// Route::get('/reports/{report}', [ReportController::class, 'show'])->name('admin.reports.show');
// Route::patch('/reports/{report}', [ReportController::class, 'update'])->name('admin.reports.update');

// // // Contoh rute transaksi, pastikan ini ada atau sesuaikan
// // Route::get('transactions', function () {
// //     return view('admin.transactions.index'); // Buat view ini jika belum ada
// // })->name('transactions.index');


// // Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('admin.users.show'); // Contoh detail pengguna
// // Route::get('/users-blocked-list', [AdminUserController::class, 'blockedUsers'])->name('admin.users.blockedList');
// // Route::get('/users-reported-list', [AdminUserController::class, 'reportedUsers'])->name('admin.users.reported-list');
// // // Tambahkan rute lain seperti edit, update, delete jika diperlukan
// // Route::view('/users/blocked_list', 'admin.users.blocked-list');
// // Manajemen Laporan (admin.reports.*)
// Route::get('/reports', [ReportController::class, 'index'])->name('admin.reports.index');
// Route::patch('/reports/{report}', [ReportController::class, 'update'])->name('admin.reports.update');

// // Anda mungkin ingin rute untuk mengubah status laporan (e.g., 'reviewed')

// // Manajemen Transaksi (admin.transactions.*)
// Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('admin.transactions.index');
// Route::get('/transactions/{id}/show', [AdminTransactionController::class, 'show'])->name('admin.transactions.show'); // Contoh detail transaksi

// // Notifikasi (admin.notifications.*)
// Route::get('/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
// // Mungkin ada rute untuk menandai notifikasi sebagai sudah dibaca, atau menghapus

// // Pengaturan (admin.settings)
// Route::get('/settings', [SettingController::class, 'index'])->name('admin.settings');
// // Jika ada form pengaturan yang bisa di-update
// Route::post('/settings', [SettingController::class, 'update'])->name('admin.settings.update');
