<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TopUpOrder;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handleXendit(Request $request)
    {
        // 1. Verifikasi Token Webhook
        $xenditWebhookToken = config('services.xendit.webhook_secret');
        if ($request->header('x-callback-token') !== $xenditWebhookToken) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $request->all();
        Log::info('Xendit Webhook Received:', $payload);
        // 2. Proses hanya jika pembayaran sukses ('PAID')
        $order = TopUpOrder::where('external_id', $payload['external_id'])
            ->where('status', 'pending') // Pastikan hanya proses yang masih pending
            ->first();

        // 3. Jika tidak ada order yang pending, hentikan proses. Ini mencegah error.
        if (!$order) {
            return response()->json(['message' => 'No pending order found or already processed.']);
        }

        $status = $payload['status'] ?? null;
        $user = $order->user;

        // 4. Proses berdasarkan status
        if ($status === 'PAID') {
            DB::transaction(function () use ($order, $payload, $user) {
                // a. Ubah status pesanan
                $order->update(['status' => 'paid']);

                // b. Tambah saldo pengguna
                $user->increment('balance', $order->amount);

                // c. Catat di riwayat wallet
                WalletTransaction::create([
                    'user_id' => $order->user_id,
                    'amount' => $order->amount,
                    'type' => 'debit',
                    'description' => 'Top up via Xendit (' . $payload['payment_channel'] . ')',
                ]);

                // d. Logging (dipindah ke dalam transaction agar konsisten)
                activity()
                    ->inLog('Finance')
                    ->on($order)
                    ->causedBy($user)
                    ->withProperties(['amount' => $order->amount, 'channel' => $payload['payment_channel'], 'xendit_invoice_id' => $payload['id']])
                    ->log("Top up untuk {$user->first_name} dengan invoice #{$payload['id']} sebesar Rp" . number_format($order->amount, 0, ',', '.') . " berhasil dikonfirmasi melalui {$payload['payment_channel']}.");
            });
        } elseif ($status === 'EXPIRED') {
            // a. Ubah status pesanan menjadi 'expired'
            $order->update(['status' => 'expired']);

            // b. Buat log aktivitas
            activity()
                ->inLog('Finance')
                ->on($order)
                ->causedBy($user)
                ->withProperties(['xendit_invoice_id' => $payload['id']])
                ->log("Invoice top up #{$payload['id']} untuk pengguna {$user->first_name} sebesar Rp" . number_format($order->amount, 0, ',', '.') . " telah kedaluwarsa.");
        }

        // 5. Kirim respon OK ke Xendit
        return response()->json(['message' => 'Webhook processed']);
    }
}
