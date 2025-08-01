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
    
    Auth\RegisteredUserController,
    Auth\SocialController,
    Auth\AuthenticatedSessionController,
    PusherController,
    ChatController,
    HomeController, 
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
use App\Http\Controllers\ProfileController;




Route::get('/profile', function () {
    return view('job-requester.profile');
})->middleware('auth')->name('profile');
Route::get('/profile/{id}', [ProfileController::class, 'show'])->name('profile.show');
Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
Route::post('/profile/update-photo', [ProfileController::class, 'updatePhoto'])->name('profile.update.photo');




Route::post('/profile/upload-worker', [ProfileController::class, 'uploadWorkerPhoto'])->name('profile.upload.worker');




Route::get('/', fn() => view('landing'))->name('landing');




Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('register', [RegisteredUserController::class, 'store']);
Route::post('/send-otp', [RegisteredUserController::class, 'sendOtp'])->name('send.otp');
Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('login', [AuthenticatedSessionController::class, 'store']);
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');


Route::get('/admin/pilihan', function () {
    
    if (FacadesAuth::check() && FacadesAuth::user()->role === 'admin') {
        return view('admin.pilihan');
    }
    return redirect()->route('landing'); 
})->name('admin.pilihan')->middleware('auth'); 



Route::get('/auth-google-redirect', [SocialController::class, 'google_redirect'])->name('auth-google-redirect');
Route::get('/auth-google-callback', [SocialController::class, 'google_callback'])->name('auth-google-callback');




Route::middleware('auth')->group(function () {
    
    
    
    
    Route::get('/switch-to-job-requester', [HomeController::class, 'switchToRequesterRole'])->name('switch.to.requester');
    Route::get('/switch-to-job-taker', [HomeController::class, 'switchToTakerRole'])->name('switch.to.taker');

    
    
    
    
    Route::get('/job-req/beranda', [HomeController::class, 'jobRequesterHome'])->name('job-req.home');
    Route::get('/job-req/tawarkan-kerja', fn() => view('job-requester.post-work'));
    Route::post('/job-req/tawarkan-kerja', [RequestController::class, 'add']);
    Route::post('/postwork', [RequestController::class, 'add']);
    Route::get('/job-req/riwayat', [TransactionController::class, 'index'])->name('orders.index');
    Route::get('/job-req/pesan', fn() => view('job-requester.chat'))->name('jobrequester.chat');
    Route::get('/job-req/on-going-work-request/{transactionId}', [TransactionController::class, 'showOngoing'])->name('request.ongoing');

    
    Route::get('/request/{request:slug}', fn(WorkRequest $request) => view('request', ['workRequest' => $request]));
    Route::get('/edit/{request:slug}', fn(WorkRequest $request) => view('edit', ['workRequest' => $request]));

    
    
    
    

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

    

    Route::get('/requests/{request}', [BrowseWorkRequestController::class, 'show'])->name('work_requests.show');

    
    
    
    Route::post('/transaction/{id}/cancel', [TransactionController::class, 'cancel'])->name('transaction.cancel');
    Route::post('/transaction/{transaction}/mark-complete', [TransactionController::class, 'markComplete'])->name('transaction.markComplete');
    Route::post('/user/submit-report/{transaction}', [TransactionController::class, 'storeReport'])->name('user.submitReport');

    
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store'); 
    Route::get('/transaction-details/{id}', [TransactionController::class, 'getTransactionDetails'])->name('transaction.details');

    
    
    
    Route::get('/hubungi/{requestId}', [ChatController::class, 'startChat'])->name('chat.start');
    Route::post('/tawar/{requestId}', [ChatController::class, 'startOffer'])->name('chat.offer');

    
    
    
    Route::post('/requests/{request}/hire/{worker}', [RequestController::class, 'hireWorker'])->name('requests.hire');
    Route::post('/requests/{request}/accept', [RequestController::class, 'acceptRequest'])->name('requests.accept');

    
    
    
    Route::get('/job-req/top-up', [TopUpController::class, 'index'])->name('top-up.job-req');

    
    Route::post('/topup', [TopUpController::class, 'createInvoice'])->name('topup.create');
    Route::get('/job-req/wallet', [BalanceController::class, 'index'])->name('balance.job-req');
    Route::get('/wallet/balance', [BalanceController::class, 'getCurrentBalance'])->name('balance.get');
    Route::get('/topup/status/{external_id}', [TopUpController::class, 'checkStatus'])->name('topup.status');

    
    
    
    Route::get('/generate-invoice/{transaction}', [InvoiceController::class, 'generateInvoice'])->name('generate.invoice');

    
    
    
    Route::get('/joinworker', fn() => redirect()->route('worker.register.step1'));

    

    Route::middleware(['auth', PreventReRegistration::class])->group(function () {
        Route::post('/ktp/ocr', [workerRegistrationController::class, 'ocrKtpAjax'])->name('ktp.ocr.ajax');

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
    });

    
    
    
    
    
    
    
    
    Route::post('/request/validate', [RequestController::class, 'validateRequest'])->name('request.validate');
});


Route::get('switch-language/{locale}', [LocalizationController::class, 'switch'])->name('language.switch');
Route::post('/webhooks/xendit', [WebhookController::class, 'handleXendit'])->name('webhooks.xendit');




Route::resource('request', RequestController::class);
Route::post('/request/validate', [RequestController::class, 'validateRequest'])->name('request.validate');
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/pilihan', function () { 
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

    
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::patch('/reports/{report}', [ReportController::class, 'update'])->name('reports.update');

    
    Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{id}/show', [AdminTransactionController::class, 'show'])->name('transactions.show');
});
Route::get('/admin/activity-log', function () {
    
    $activities = Activity::orderBy('id', 'desc')->take(50)->get(); 
    return view('admin-test-iwan.activity-log', compact('activities'));
})->name('admin.activity');































































