<?php

namespace App\Http\Controllers;

use App\Models\Transaction; // Assuming your Transaction model is in App\Models
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // Import the PDF facade
use Carbon\Carbon; // Import Carbon for date formatting
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File; // Import the File facade for reading image content

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
        // Eager load necessary relationships if they are not already loaded
        $transaction->load('request', 'requester', 'worker');

        // --- Start of changes for Base64 image embedding ---

        // Define the path to your logo image in the public directory.
        // Make sure this path is correct relative to your 'public' folder.
        $logoPath = public_path('Image/Logo/Logo Kerjain - LightBackground.png');

        // Check if the logo file exists to prevent errors
        if (File::exists($logoPath)) {
            // Get the image content
            $logoData = File::get($logoPath);
            // Determine the image MIME type (e.g., 'png', 'jpeg')
            $logoMimeType = File::mimeType($logoPath);

            // Construct the Base64 data URI for the image
            $base64Logo = 'data:' . $logoMimeType . ';base64,' . base64_encode($logoData);
        } else {
            // Handle case where logo is not found, e.g., set a placeholder or null
            $base64Logo = null;
            // Removed: \Log::error('Invoice logo not found at: ' . $logoPath);
        }

        // --- End of changes for Base64 image embedding ---

        // Prepare data to pass to the invoice Blade view
        $data = [
            'transaction' => $transaction,
            'start_work' => $transaction->start_work ? Carbon::parse($transaction->start_work) : null,
            'finish_work' => $transaction->finish_work ? Carbon::parse($transaction->finish_work) : null,
            'base64Logo' => $base64Logo, // Pass the Base64 encoded logo to the view
        ];

        // Generate the PDF from the Blade view.
        // Explicitly set paper to A4 and orientation to 'portrait'.
        $pdf = Pdf::loadView('invoices.invoice_template', $data)
            ->setPaper('A4', 'portrait');

        // Define the filename for the downloaded PDF.
        $filename = 'invoice_' . ($transaction->order_number ?? 'N_A') . '.pdf';
        $user = Auth::user();
        activity()
            ->inLog('Document')
            ->on($transaction)
            ->causedBy($user)
            ->log("{$user->first_name} telah mengunduh invoice untuk transaksi #{$transaction->order_number}.");
        // Return the PDF as a download.
        return $pdf->download($filename);
    }
}
