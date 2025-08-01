<?php

namespace App\Http\Controllers;

use App\Models\Request as WorkRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function switchToRequesterRole(): RedirectResponse
    {
        $requester = Auth::user();
        $firstName = $requester->first_name ?? 'Pengguna';
        return redirect()->route('job-req.home')->with('custom_blue_alert', __('alerts.beralih_ke_requester', ['nama' => $firstName]));
    }
    public function switchToTakerRole(): RedirectResponse
    {
        $worker = Auth::user();
        $firstName = $worker->first_name ?? 'Pengguna';

        return redirect()->route('job-taker.home')->with('custom_blue_alert', __('alerts.beralih_ke_taker', ['nama' => $firstName]));
    }

    public function jobRequesterHome(): View
    {
        $requesterId = Auth::id();
        $fiveLatestRequests = WorkRequest::where('requester_id', $requesterId)
            ->whereNull('deleted_at')
            ->where(function ($query) {
                $query->where('status', 'open')
                    ->orWhere(function ($subQuery) {

                        $subQuery->where('status', 'closed')
                            ->whereHas('transaction');
                    });
            })
            ->latest()
            ->with('transaction')
            ->get();

        return view('job-requester.home', compact('fiveLatestRequests'));
    }

    public function jobTakerHome(): View
    {
        $workerId = Auth::id();
        $fiveLatestTransaction = Transaction::where('worker_id', $workerId)
            ->whereNull('deleted_at')
            ->latest()
            ->with('requester', 'request')
            ->get();

        return view('job-taker.home', compact('fiveLatestTransaction'));
    }
}
