<?php

namespace App\Http\Controllers;

use App\Models\Request as WorkRequest; // Alias to avoid conflict with Illuminate\Http\Request
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Use FacadesAuth for consistency with your web.php
use Illuminate\Http\RedirectResponse; // Import RedirectResponse for type hinting
use Illuminate\View\View; // Import View for type hinting

class HomeController extends Controller
{
    /**
     * Handles switching to the Job Requester role and redirects to the home page.
     */
    public function switchToRequesterRole(): RedirectResponse
    {
        $requester = Auth::user();
        // Ensure first_name exists, fallback to 'Pengguna'
        $firstName = $requester->first_name ?? 'Pengguna';

        // You might add logic here to explicitly change the user's role in the database
        // if your application requires a persistent role change beyond just navigation.
        // For example:
        // $requester->update(['role' => 'job_requester']);

        // Redirect to the job requester home page with the flash message
        return redirect()->route('job-req.home')->with('custom_blue_alert', "Anda berhasil beralih ke peran Job Requester, {$firstName}!");
    }

    /**
     * Handles switching to the Job Taker role and redirects to the home page.
     */
    public function switchToTakerRole(): RedirectResponse
    {
        $worker = Auth::user();
        // Ensure first_name exists, fallback to 'Pengguna'
        $firstName = $worker->first_name ?? 'Pengguna';

        // You might add logic here to explicitly change the user's role in the database
        // if your application requires a persistent role change beyond just navigation.
        // For example:
        // $worker->update(['role' => 'job_taker']);

        // Redirect to the job taker home page with the flash message
        return redirect()->route('job-taker.home')->with('custom_blue_alert', "Anda berhasil beralih ke peran Job Taker, {$firstName}!");
    }

    /**
     * Show the job requester dashboard with latest requests.
     * This method is now the *target* of the redirect from switchToRequesterRole.
     */
    public function jobRequesterHome(): View
    {
        $requesterId = Auth::id();
        // No need to get $requester or $firstName here for the alert, as it comes from the redirect.
        // However, you might need $requester for other view data.
        $fiveLatestRequests = WorkRequest::where('requester_id', $requesterId)
            ->whereNull('deleted_at')
            ->latest()
            ->with('transaction')
            ->get();

        // No need to set the 'custom_blue_alert' here anymore, as it comes from the redirect
        return view('job-requester.home', compact('fiveLatestRequests'));
    }

    /**
     * Show the job taker dashboard with latest transactions.
     * This method is now the *target* of the redirect from switchToTakerRole.
     */
    public function jobTakerHome(): View
    {
        $workerId = Auth::id();
        // No need to get $worker or $firstName here for the alert, as it comes from the redirect.
        // However, you might need $worker for other view data.
        $fiveLatestTransaction = Transaction::where('worker_id', $workerId)
            ->whereNull('deleted_at')
            ->latest()
            ->with('requester', 'request')
            ->get();

        // No need to set the 'custom_blue_alert' here anymore, as it comes from the redirect
        return view('job-taker.home', compact('fiveLatestTransaction'));
    }
}
