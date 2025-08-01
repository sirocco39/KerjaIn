<?php

namespace App\Http\Controllers;

use App\Models\Transaction; 
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; 
use Carbon\Carbon; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File; 

class InvoiceController extends Controller
{
    /**
     * Generates and downloads an invoice PDF for a given transaction.
     *
     * @param  \App\Models\Transaction  $transaction The transaction model (via route model binding).
     * @return \Illuminate\Http\Response
     */
    public function generateInvoice(Transaction $transaction)
    {
        
        $transaction->load('request', 'requester', 'worker');

        

        
        
        $logoPath = public_path('Image/Logo/Logo Kerjain - LightBackground.png');

        
        if (File::exists($logoPath)) {
            
            $logoData = File::get($logoPath);
            
            $logoMimeType = File::mimeType($logoPath);

            
            $base64Logo = 'data:' . $logoMimeType . ';base64,' . base64_encode($logoData);
        } else {
            
            $base64Logo = null;
            
        }

        
        $data = [
            'transaction' => $transaction,
            'start_work' => $transaction->start_work ? Carbon::parse($transaction->start_work) : null,
            'finish_work' => $transaction->finish_work ? Carbon::parse($transaction->finish_work) : null,
            'base64Logo' => $base64Logo, 
        ];

        
        
        $pdf = Pdf::loadView('invoices.invoice_template', $data)
            ->setPaper('A4', 'portrait');

        
        $filename = 'invoice_' . ($transaction->order_number ?? 'N_A') . '.pdf';
        $user = Auth::user();
        activity()
            ->inLog('Document')
            ->on($transaction)
            ->causedBy($user)
            ->log("{$user->first_name} telah mengunduh invoice untuk transaksi #{$transaction->order_number} pada " . Carbon::now('Asia/Jakarta')->format('d M Y, H:i:s') . ".");
        
        return $pdf->download($filename);
    }
}
