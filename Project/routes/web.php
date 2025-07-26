<?php

use App\Http\Controllers\MonthlyReportController;
use App\Models\Request as WorkRequest;
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
    // TakerTransactionController, // This seems unused, consider removing if not needed
    Auth\RegisteredUserController,
    Auth\SocialController,
    Auth\AuthenticatedSessionController,
    PusherController,
    ChatController,
    HomeController, // Make sure HomeController is imported
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
Route::get('/', fn() => view('landing'))->name('landing');

// =======================
// AUTH ROUTES
// =======================
Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('register', [RegisteredUserController::class, 'store']);
Route::post('/send-otp', [RegisteredUserController::class, 'sendOtp'])->name('send.otp');
Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('login', [AuthenticatedSessionController::class, 'store']);
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// =======================
// SOCIAL AUTH
// =======================
Route::get('/auth-google-redirect', [SocialController::class, 'google_redirect'])->name('auth-google-redirect');
Route::get('/auth-google-callback', [SocialController::class, 'google_callback'])->name('auth-google-callback');

// =======================
// AUTHENTICATED ROUTES GROUP
// =======================
Route::middleware('auth')->group(function () {
    // =======================
    // ROLE SWITCHING ROUTES (NEW)
    // These routes handle the redirect with the flash message
    // =======================
    Route::get('/switch-to-job-requester', [HomeController::class, 'switchToRequesterRole'])->name('switch.to.requester');
    Route::get('/switch-to-job-taker', [HomeController::class, 'switchToTakerRole'])->name('switch.to.taker');

    // =======================
    // JOB REQUESTER PAGES
    // These routes are the destinations after role switching
    // =======================
    Route::get('/job-req/beranda', [HomeController::class, 'jobRequesterHome'])->name('job-req.home');
    Route::get('/job-req/tawarkan-kerja', fn() => view('job-requester.post-work'));
    Route::post('/job-req/tawarkan-kerja', [RequestController::class, 'add']);
    Route::post('/postwork', [RequestController::class, 'add']);
    Route::get('/job-req/riwayat', [TransactionController::class, 'index'])->name('orders.index');
    Route::get('/job-req/pesan', fn() => view('job-requester.chat'))->name('jobrequester.chat');
    Route::get('/job-req/on-going-work-request/{transactionId}', [TransactionController::class, 'showOngoing'])->name('request.ongoing');

    // Job Requester Request Detail Routes
    Route::get('/request/{request:slug}', fn(WorkRequest $request) => view('request', ['workRequest' => $request]));
    Route::get('/edit/{request:slug}', fn(WorkRequest $request) => view('edit', ['workRequest' => $request]));

    // =======================
    // JOB TAKER PAGES
    // These routes are the destinations after role switching
    // =======================

    Route::middleware(['auth', IsWorker::class])->group(function () {
        Route::get('/job-taker/beranda', [HomeController::class, 'jobTakerHome'])->name('job-taker.home');
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
    Route::post('/user/submit-report/{transaction}', [TransactionController::class, 'storeReport'])->name('user.submitReport');

    // Avoid duplicates — keep only one valid review route
    Route::post('/reviews/{transaction}', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/transaction-details/{id}', [TransactionController::class, 'getTransactionDetails'])->name('transaction.details');

    // =======================
    // CHAT / OFFER ROUTES
    // =======================
    Route::get('/hubungi/{requestId}', [ChatController::class, 'startChat'])->name('chat.start');
    Route::post('/tawar/{requestId}', [ChatController::class, 'startOffer'])->name('chat.offer');

    // =======================
    // HIRE / ACCEPT REQUEST
    // =======================
    Route::post('/requests/{request}/hire/{worker}', [RequestController::class, 'hireWorker'])->name('requests.hire');
    Route::post('/requests/{request}/accept', [RequestController::class, 'acceptRequest'])->name('requests.accept');

    // =======================
    // TOP-UP & BALANCE
    // =======================
    Route::get('/job-req/top-up', [TopUpController::class, 'index'])->name('top-up.job-req');

    // Memproses form dan membuat invoice Xendit
    Route::post('/topup', [TopUpController::class, 'createInvoice'])->name('topup.create');
    Route::get('/job-req/wallet', [BalanceController::class, 'index'])->name('balance.job-req');
    Route::get('/wallet/balance', [BalanceController::class, 'getCurrentBalance'])->name('balance.get');
    Route::get('/topup/status/{external_id}', [TopUpController::class, 'checkStatus'])->name('topup.status');

    // =======================
    // INVOICE
    // =======================
    Route::get('/generate-invoice/{transaction}', [InvoiceController::class, 'generateInvoice'])->name('generate.invoice');

    // =======================
    // WORKER REGISTRATION FLOW
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
    // MISC / NAVBAR
    // =======================
    Route::get('switch-language/{locale}', [LocalizationController::class, 'switch'])->name('language.switch');

    // =======================
    // RESOURCE ROUTES (within auth middleware)
    // =======================
    Route::resource('request', RequestController::class);
    Route::post('/request/validate', [RequestController::class, 'validateRequest'])->name('request.validate');
});

// Endpoint untuk menerima webhook dari Xendit (DO NOT ADD AUTH HERE, as Xendit's server sends this)
Route::post('/webhooks/xendit', [WebhookController::class, 'handleXendit'])->name('webhooks.xendit');

// =======================
// RESOURCE ROUTES (outside auth middleware - ensure these are intended to be public)
// =======================
Route::resource('request', RequestController::class);
Route::post('/request/validate', [RequestController::class, 'validateRequest'])->name('request.validate');

Route::get('/admin/activity-log', function () {
    // Kode yang benar untuk urutan kronologis
    $activities = Activity::orderBy('id', 'desc')->take(50)->get(); // Urutkan berdasarkan ID dari yang terkecil
    return view('admin-test-iwan.activity-log', compact('activities'));
})->name('admin.activity');
