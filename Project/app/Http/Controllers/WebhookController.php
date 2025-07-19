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
        if (isset($payload['status']) && $payload['status'] === 'PAID') {
            // 3. Cari Pesanan di Database Anda
            $order = TopUpOrder::where('external_id', $payload['external_id'])
                ->where('status', 'pending') // Pastikan hanya proses yang masih pending
                ->first();

            if ($order) {
                // 4. Lakukan Update Database dalam Transaksi Atomik
                DB::transaction(function () use ($order, $payload) {
                    // a. Ubah status pesanan
                    $order->update(['status' => 'paid']);

                    // b. Tambah saldo pengguna
                    $order->user->increment('balance', $order->amount);

                    // c. Catat di riwayat wallet
                    WalletTransaction::create([
                        'user_id' => $order->user_id,
                        'amount' => $order->amount,
                        'type' => 'debit',
                        'description' => 'Top up via Xendit (' . $payload['payment_channel'] . ')',
                    ]);
                });
            }
        }

        // 5. Kirim respon OK ke Xendit
        return response()->json(['message' => 'Webhook processed']);
    }
}
