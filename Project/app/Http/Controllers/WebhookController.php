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
        
        $xenditWebhookToken = config('services.xendit.webhook_secret');
        if ($request->header('x-callback-token') !== $xenditWebhookToken) {
            return response()->json(['message' => __('alerts.topup_unauthorized_webhook')], 401);
        }

        $payload = $request->all();
        Log::info('Xendit Webhook Received:', $payload);
        
        $order = TopUpOrder::where('external_id', $payload['external_id'])
            ->where('status', 'pending') 
            ->first();

        
        if (!$order) {
            return response()->json(['message' => __('alerts.topup_no_pending_order_webhook')]);
        }

        $status = $payload['status'] ?? null;
        $user = $order->user;

        
        if ($status === 'PAID') {
            DB::transaction(function () use ($order, $payload, $user) {
                
                $order->update(['status' => 'paid']);

                
                $user->increment('balance', $order->amount);

                
                WalletTransaction::create([
                    'user_id' => $order->user_id,
                    'amount' => $order->amount,
                    'type' => 'debit',
                    'description_en' => 'Top up via Xendit (' . $payload['payment_channel'] . ')',
                    'description_id' => 'Isi saldo melalui Xendit (' . $payload['payment_channel'] . ')',
                ]);

                
                activity()
                    ->inLog('Finance')
                    ->on($order)
                    ->causedBy($user)
                    ->withProperties(['amount' => $order->amount, 'channel' => $payload['payment_channel'], 'xendit_invoice_id' => $payload['id']])
                    ->log("Top up untuk {$user->first_name} dengan invoice #{$payload['id']} sebesar Rp" . number_format($order->amount, 0, ',', '.') . " berhasil dikonfirmasi melalui {$payload['payment_channel']}.");
            });
        } elseif ($status === 'EXPIRED') {
            
            $order->update(['status' => 'expired']);

            
            activity()
                ->inLog('Finance')
                ->on($order)
                ->causedBy($user)
                ->withProperties(['xendit_invoice_id' => $payload['id']])
                ->log("Invoice top up #{$payload['id']} untuk pengguna {$user->first_name} sebesar Rp" . number_format($order->amount, 0, ',', '.') . " telah kedaluwarsa.");
        }

        
        return response()->json(['message' => __('alerts.topup_webhook_processed_success')]);
    }
}
