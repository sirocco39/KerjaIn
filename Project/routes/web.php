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
    TakerTransactionController,
    Auth\RegisteredUserController,
    Auth\SocialController,
    Auth\AuthenticatedSessionController,
    PusherController,
    ChatController,
    JobTakerRequestController,
    InvoiceController
};
use App\Livewire\JobTaker\Chat;
use App\Livewire\jobTaker\JobTakerChatRoom;
use App\Livewire\JobTakerChatRoom as LivewireJobTakerChatRoom;
use App\Models\ChatRoom;

// =======================
// LANDING PAGE
// =======================
Route::get('/', fn () => view('landing'));

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
// JOB REQUESTER PAGES
// =======================
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

Route::get('/job-req/tawarkan-kerja', fn () => view('job-requester.post-work'));
Route::post('/job-req/tawarkan-kerja', [RequestController::class, 'add']);
Route::post('/postwork', [RequestController::class, 'add']);

Route::get('/job-req/riwayat', [TransactionController::class, 'index'])->name('orders.index');
Route::get('/job-req/pesan', fn () => view('job-requester.chat'))->name('jobrequester.chat');
Route::get('/job-req/on-going-work-request/{transactionId}', [TransactionController::class, 'showOngoing'])->name('request.ongoing');

// Job Requester Request Detail Routes
Route::get('/request/{request:slug}', fn (WorkRequest $request) => view('request', ['workRequest' => $request]));
Route::get('/edit/{request:slug}', fn (WorkRequest $request) => view('edit', ['workRequest' => $request]));

// =======================
// JOB TAKER PAGES
// =======================
Route::get('/job_taker', fn () => view('job-taker.dummy-job_taker-landingpage'));
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
Route::get('/job-taker/pesan/{selectedRoomId?}', fn ($selectedRoomId = null) => view('job-taker.chat', ['chatRoomId' => $selectedRoomId]))->name('chat.job-taker');
Route::get('/job-taker/cari-kerja', [BrowseWorkRequestController::class, 'index'])->name('browse.work.requests.index');

Route::get('/job-taker/cari-kerja', [browseWorkRequestController::class, 'index'])->name('browse.work.requests.index');

Route::get('/job-taker/riwayat', function () {
    return view('Job_Taker.dummy-job_taker-riwayat');
});

// Route monthly-report
Route::get('/job-taker/monthly-report', function () {
    return view('Job_Taker.monthly-report');
})->name('job-taker.monthly-report');

Route::get('/navbar-job_taker', function () {
    return view('Master.master-job_taker');
});

Route::get('/navbar-job_req', function () {
    return view('Master.master-job_req');
});

// Route::get('/browseWorkRequest', [browseWorkRequestController::class, 'index'])->name('browse.work.requests.index');

Route::get('/requests/{request}', [BrowseWorkRequestController::class, 'show'])->name('work_requests.show');

// =======================
// JOB TAKER REQUEST ACTIONS
// =======================
Route::post('/job-taker/cari-kerja/{id}', [JobTakerRequestController::class, 'acceptRequest'])->name('job-taker.accept-request');
Route::get('/job-taker/accepted-work-request/{id}', [WorkerTransactionController::class, 'show'])->name('job-taker.accepted-work-request');
Route::post('/worker/start-work/{id}', [WorkerTransactionController::class, 'startWork'])->name('worker.startWork');
Route::post('/worker/upload-proof/{transaction}', [WorkerTransactionController::class, 'uploadProof'])->name('worker.uploadProof');
Route::post('/worker/submit-report/{transaction}', [WorkerTransactionController::class, 'storeReport'])->name('worker.submitReport');

// =======================
// REVIEW & COMPLETION ROUTES
// =======================
Route::post('/transaction/{id}/cancel', [TransactionController::class, 'cancel'])->name('transaction.cancel');
Route::post('/transaction/{transaction}/mark-complete', [TransactionController::class, 'markComplete'])->name('transaction.markComplete');
Route::post('/user/submit-report/{transaction}', [TransactionController::class, 'submitReport'])->name('user.submitReport');

// Avoid duplicates — keep only one valid review route
Route::post('/reviews/{transaction}', [ReviewController::class, 'store'])->name('reviews.store');

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

Route::get('/job-req/top-up', [TopUpController::class, 'index'])->name('top-up.job-req');
Route::get('/job-taker/top-up', [TopUpController::class, 'index'])->name('top-up.job-taker');

// Memproses form dan membuat invoice Xendit
Route::post('/topup', [TopUpController::class, 'createInvoice'])->name('topup.create');

// Endpoint untuk menerima webhook dari Xendit
Route::post('/webhooks/xendit', [WebhookController::class, 'handleXendit'])->name('webhooks.xendit');

Route::get('/job-req/wallet', [BalanceController::class, 'index'])->name('balance.job-req');
Route::get('/job-taker/wallet', [BalanceController::class, 'index'])->name('balance.job-taker');

Route::get('/wallet/balance', [BalanceController::class, 'getCurrentBalance'])->name('balance.get');

Route::get('/topup/status/{external_id}', [TopUpController::class, 'checkStatus'])->name('topup.status');
// =======================
// INVOICE
// =======================
Route::get('/generate-invoice/{transaction}', [InvoiceController::class, 'generateInvoice'])->name('generate.invoice');

// =======================
// WORKER REGISTRATION FLOW
// =======================
Route::get('/joinworker', fn () => redirect()->route('worker.register.step1'));

// Grup route untuk pendaftaran pekerja tanpa autentikasi
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

// =======================
// MISC / NAVBAR
// =======================
Route::get('/navbar-job_taker', fn () => view('Master.master-job_taker'));
Route::get('/navbar-job_req', fn () => view('Master.master-job_req'));

// =======================
// RESOURCE ROUTES
// =======================
Route::resource('request', RequestController::class);

Route::get('/job-taker/monthly-report', [MonthlyReportController::class, 'index'])->name('monthly.report');
Route::get('/job-taker/monthly-report/download-pdf', [MonthlyReportController::class, 'downloadReportPdf'])->name('monthly.report.download.pdf');
Route::view('/job-taker/pdf', 'Job_Taker.pdf.report-pdf')->name('pdf');