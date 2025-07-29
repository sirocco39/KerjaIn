<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\TopUpOrder; // Pastikan model ini sudah dibuat
use App\Models\User;
use Xendit\Configuration;
use Xendit\Invoice;
use Xendit\Invoice\Invoice as InvoiceInvoice;
use Xendit\Invoice\InvoiceApi;

class TopUpController extends Controller
{
    public function __construct()
    {
        // Middleware untuk memastikan pengguna sudah login
        Configuration::setXenditKey(env('XENDIT_SECRET_KEY'));
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $viewPath = '';

        // Cek apakah URL yang diakses mengandung 'job-requester'
        if ($request->is('job-req/*')) {
            $viewPath = 'job-requester.top-up';

            // Cek apakah URL yang diakses mengandung 'job-taker'
        } elseif ($request->is('job-taker/*')) {
            $viewPath = 'job-taker.top-up';

            // Jika tidak keduanya, arahkan ke halaman lain atau tampilkan error
        } else {
            // Changed to custom alert
            return redirect()->route('landing')->with('custom_error_alert', __('alerts.halaman_tidak_ditemukan'));
        }

        return view($viewPath, compact('user'));
    }
    public function createInvoice(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'amount' => 'required|numeric|min:10000',
        ]);

        // 2. Buat Catatan Pesanan di Database Anda
        $order = TopUpOrder::create([
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'external_id' => 'TOPUP-' . Str::uuid(), // Buat ID unik
            'status' => 'pending',
        ]);

        // 3. Siapkan Parameter untuk Xendit
        $params = [
            'external_id' => $order->external_id,
            'payer_email' => Auth::user()->email,
            'description' => 'Top Up Saldo Kerjain sebesar Rp' . number_format($request->amount),
            'amount' => $request->amount,
            'success_redirect_url' => route('balance.job-req', ['order_id' => $order->external_id]),
        ];

        try {
            // 4. Kirim Permintaan ke Xendit
            $apiInstance = new InvoiceApi();
            $invoice = $apiInstance->createInvoice($params);

            // 5. Update Pesanan dengan Info dari Xendit
            $order->update([
                'xendit_invoice_id' => $invoice['id'],
                'invoice_url' => $invoice['invoice_url'],
            ]);
            $user = Auth::user();
            activity()
                ->inLog('Finance') // Kelompokkan ke log 'Finance'
                ->on($order) // Targetnya adalah order yang baru dibuat
                ->causedBy(Auth::user()) // Pelakunya adalah user yang login
                ->withProperties(['amount' => $request->amount])
                ->log("Pengguna {$user->first_name} telah membuat invoice top up sebesar Rp" . number_format($request->amount));

            // 6. Arahkan Pengguna ke Halaman Pembayaran
            return redirect($invoice['invoice_url']);
        } catch (\Exception $e) {
            // Changed to custom alert
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
