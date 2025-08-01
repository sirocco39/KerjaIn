<?php

namespace App\Http\Controllers;

use App\Models\Request as JobRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class JobTakerRequestController extends Controller
{
    public function acceptRequest($id)
    {
        
        $jobRequest = JobRequest::findOrFail($id);
        
        $transaction = JobRequest::hireAndFinalize($jobRequest, Auth::user()); 

        
        if ($transaction instanceof Transaction) {
            return response()->json([
                'success'      => true,
                'message'      => __('alerts.job_accepted_success_redirect'),
                'redirect_url' => route('job-taker.accepted-work-request', $transaction->id)
            ]);
        } else {
            
            
            return back()->with('custom_error_alert', __('alerts.terjadi_kesalahan'));
        }
    }
}
