<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use App\Models\Request as WorkRequest;
use App\Models\Transaction;
use App\Http\Controllers\{
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
        ->take(6)
        ->with('transaction')
        ->get();
    return view('Job_Requester.beranda', compact('fiveLatestRequests'));
})->name('job-req.beranda');

Route::get('/job-req/tawarkan-kerja', fn () => view('Job_Requester.postwork'));
Route::post('/job-req/tawarkan-kerja', [RequestController::class, 'add']);
Route::post('/postwork', [RequestController::class, 'add']);

Route::get('/job-req/riwayat', [TransactionController::class, 'index'])->name('orders.index');
Route::get('/job-req/pesan', fn () => view('Job_Requester.pesan'))->name('jobrequester.chat');
Route::get('/job-req/on-going-work-request/{transactionId}', [TransactionController::class, 'showOngoing'])->name('request.ongoing');

// Job Requester Request Detail Routes
Route::get('/request/{request:slug}', fn (WorkRequest $request) => view('request', ['workRequest' => $request]));
Route::get('/edit/{request:slug}', fn (WorkRequest $request) => view('edit', ['workRequest' => $request]));

// =======================
// JOB TAKER PAGES
// =======================
Route::get('/job_taker', fn () => view('Job_Taker.dummy-job_taker-landingpage'));
Route::get('/job-taker/beranda', function () {
    $workerId = FacadesAuth::id();
    $fiveLatestTransaction = Transaction::where('worker_id', $workerId)
        ->whereNull('deleted_at')
        ->latest()
        ->take(5)
        ->with('requester', 'request')
        ->get();
    return view('Job_Taker.job_taker-beranda', compact('fiveLatestTransaction'));
})->name('job-taker.beranda');
Route::get('/job-taker/beranda/{id}', [TransactionController::class, 'show']);

Route::get('/job-taker/riwayat', [WorkerTransactionController::class, 'index'])->name('orders.index');
Route::get('/job-taker/pesan/{selectedRoomId?}', fn ($selectedRoomId = null) => view('Job_Taker.pesan', ['chatRoomId' => $selectedRoomId]))->name('chat.job-taker');
Route::get('/job-taker/cari-kerja', [BrowseWorkRequestController::class, 'index'])->name('browse.work.requests.index');
Route::get('/requests/{request}', [BrowseWorkRequestController::class, 'show'])->name('work_requests.show');

Route::get('/test', fn () => view('Job_Taker.job_taker-pesanSon'));

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

// =======================
// INVOICE
// =======================
Route::get('/generate-invoice/{transaction}', [InvoiceController::class, 'generateInvoice'])->name('generate.invoice');

// =======================
// WORKER REGISTRATION FLOW
// =======================
Route::get('/joinworker', fn () => redirect()->route('worker.register.step1'));

Route::prefix('joinWorker')->name('worker.register.')->group(function () {
    Route::get('/join', [WorkerRegistrationController::class, 'createStep1'])->name('step1');
    Route::post('/join', [WorkerRegistrationController::class, 'store1'])->name('store1');

    Route::get('/join2', [WorkerRegistrationController::class, 'createStep2'])->name('step2');
    Route::post('/join2', [WorkerRegistrationController::class, 'store2'])->name('store2');

    Route::get('/join3', [WorkerRegistrationController::class, 'createStep3'])->name('step3');
    Route::post('/join3', [WorkerRegistrationController::class, 'finalizeRegistration'])->name('finalize');

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
Route::resource('requesttt', RequestController::class);
