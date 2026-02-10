<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Pembayaran;
use App\Http\Controllers\TransaksiController;

class MidtransController extends Controller
{
    protected MidtransService $midtrans;
    protected TransaksiController $transaksiController;

    public function __construct(MidtransService $midtrans, TransaksiController $transaksiController)
    {
        $this->midtrans = $midtrans;
        $this->transaksiController = $transaksiController;
        // Require auth and specific roles for all actions except notification (which is public webhook)
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user) return abort(403);
            $allowed = ['admin', 'kasir', 'owner'];
            if (!in_array($user->role, $allowed)) return abort(403);
            return $next($request);
        })->except(['notification']);
    }

    // Show a simple checkout page with Snap integration
    public function checkout(Transaksi $transaksi)
    {
        $orderId = 'order-' . $transaksi->transaksi_id . '-' . time();

        $snapToken = $this->midtrans->createSnapToken([
            'order_id' => $orderId,
            'gross_amount' => (float) $transaksi->total,
            'customer' => [
                'first_name' => $transaksi->pelanggan_id ? optional($transaksi->pelanggan)->nama : 'Guest',
                'email' => optional($transaksi->pelanggan)->email,
            ],
        ]);

        return view('checkout', compact('transaksi', 'snapToken', 'orderId'));
    }

    // Midtrans server-to-server notification endpoint
    public function notification(Request $request)
    {
        try {
            $notification = $this->midtrans->parseNotification();

            $transactionStatus = $notification->transaction_status ?? null;
            $orderId = $notification->order_id ?? null;

            // Persist full notification for debugging/audit
            $raw = $notification ? (array) $notification : $request->all();

            // Verify signature_key if present
            $notifArray = $raw;
            if (is_object($notification)) {
                $notifArray = (array) $notification;
            }

            if (isset($notifArray['signature_key'])) {
                $valid = $this->midtrans->verifySignature($notifArray);
                if (!$valid) {
                    Log::warning('Midtrans notification signature mismatch', ['order_id' => $orderId, 'payload' => $notifArray]);
                    return response('Invalid signature', 403);
                }
            }

            // Parse transaksi_id from order_id (format: order-{id}-{ts})
            if ($orderId && preg_match('/order-(\d+)-/', $orderId, $matches)) {
                $transaksiId = $matches[1];
                $transaksi = Transaksi::where('transaksi_id', $transaksiId)->first();
                if ($transaksi) {
                    // Map Midtrans statuses to local payment_status
                    $statusMap = [
                        'capture' => 'selesai',
                        'settlement' => 'selesai',
                        'pending' => 'pending',
                        'deny' => 'batal',
                        'expire' => 'batal',
                        'cancel' => 'batal',
                    ];

                    $newStatus = $statusMap[$transactionStatus] ?? 'pending';

                    $transaksi->status = $newStatus;
                    $transaksi->midtrans_transaction_id = $notification->transaction_id ?? null;
                    $transaksi->payment_status = $transactionStatus ?? $transaksi->payment_status;
                    $transaksi->midtrans_raw = $raw;
                    $transaksi->save();

                        // Try to update pembayaran record if exists (match by referensi order_id or transaksi_id)
                        try {
                            $pay = null;
                            if ($orderId) {
                                $pay = Pembayaran::where('referensi', $orderId)->first();
                            }
                            if (!$pay) {
                                $pay = Pembayaran::where('transaksi_id', $transaksi->transaksi_id)->first();
                            }
                            if ($pay) {
                                // Update reference and amount when available
                                $pay->referensi = $notification->transaction_id ?? $pay->referensi;
                                if (isset($notification->gross_amount)) {
                                    $pay->jumlah = (float) $notification->gross_amount;
                                }
                                // set metode to midtrans
                                $pay->metode = 'midtrans';
                                $pay->save();
                            }
                        } catch (\Throwable $e) {
                            Log::warning('Failed to reconcile pembayaran for order: ' . $orderId, ['error' => $e->getMessage()]);
                        }
                        // If transaction is now completed, generate invoice HTML for record
                        if ($newStatus === 'selesai') {
                            $this->transaksiController->generateInvoiceHtml($transaksi);
                        }
                }
            } else {
                Log::warning('Midtrans notification received with invalid order_id', ['order_id' => $orderId, 'payload' => $raw]);
            }

            return response('OK', 200);
        } catch (\Throwable $e) {
            Log::error('Midtrans notification handling failed: ' . $e->getMessage(), ['exception' => $e]);
            return response('ERROR', 500);
        }
    }
}
