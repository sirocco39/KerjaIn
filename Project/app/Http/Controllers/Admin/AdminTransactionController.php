<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Request as ServiceRequest;
use App\Models\Payment;
use App\Models\Transaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminTransactionController extends Controller
{
    /**
     * Display a simplified and general financial overview focusing on company profit and user balances.
     */
    public function index()
    {
        
        $totalCompanyProfitFromServiceFee = ServiceRequest::where('status', 'closed')->sum('service_fee');

        
        $totalUserBalance = User::sum('balance');

        
        $totalLockedBalance = User::sum('locked_balance');

        
        $Requests = ServiceRequest::with(['requester', 'transaction.worker'])
            ->withTrashed()
            ->orderByDesc('created_at')
            ->paginate(10);

        
        $breadcrumbs = [
            'mainPageTitle' => 'Admin',
            'currentPageTitle' => 'Penghasilan',
        ];

        return view('admin.transactions.index', compact(
            'totalCompanyProfitFromServiceFee',
            'totalUserBalance',
            'totalLockedBalance',
            'Requests',
            'breadcrumbs' 
        ));
    }
}
