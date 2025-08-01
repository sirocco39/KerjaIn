<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\TopUpOrder; 
use App\Models\User;
use Xendit\Configuration;
use Xendit\Invoice;
use Xendit\Invoice\Invoice as InvoiceInvoice;
use Xendit\Invoice\InvoiceApi;

class TopUpController extends Controller
{
    public function __construct()
    {
        
        Configuration::setXenditKey(env('XENDIT_SECRET_KEY'));
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $viewPath = '';

        
        if ($request->is('job-req/*')) {
            $viewPath = 'job-requester.top-up';

            
        } elseif ($request->is('job-taker/*')) {
            $viewPath = 'job-taker.top-up';

            
        } else {
            
            return redirect()->route('landing')->with('custom_error_alert', __('alerts.halaman_tidak_ditemukan'));
        }

        return view($viewPath, compact('user'));
    }
    public function createInvoice(Request $request)
    {
        
        $request->validate([
            'amount' => 'required|numeric|min:10000',
        ]);

        
        $order = TopUpOrder::create([
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'external_id' => 'TOPUP-' . Str::uuid(), 
            'status' => 'pending',
        ]);

        
        $params = [
            'external_id' => $order->external_id,
            'payer_email' => Auth::user()->email,
            'description' => 'Top Up Saldo Kerjain sebesar Rp' . number_format($request->amount),
            'amount' => $request->amount,
            'success_redirect_url' => route('balance.job-req', ['order_id' => $order->external_id]),
        ];

        try {
            
            $apiInstance = new InvoiceApi();
            $invoice = $apiInstance->createInvoice($params);

            
            $order->update([
                'xendit_invoice_id' => $invoice['id'],
                'invoice_url' => $invoice['invoice_url'],
            ]);
            $user = Auth::user();
            activity()
                ->inLog('Finance') 
                ->on($order) 
                ->causedBy(Auth::user()) 
                ->withProperties(['amount' => $request->amount])
                ->log("Pengguna {$user->first_name} telah membuat invoice top up sebesar Rp" . number_format($request->amount));

            
            return redirect($invoice['invoice_url']);
        } catch (\Exception $e) {
            
            return back()->with('custom_error_alert', __('alerts.gagal_membuat_invoice', ['error' => $e->getMessage()]));
        }
    }
    public function checkStatus($external_id)
    {

        $order = \App\Models\TopUpOrder::where('external_id', $external_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return response()->json(['status' => $order->status]);
    }
}
