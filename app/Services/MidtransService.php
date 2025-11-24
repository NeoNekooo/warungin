<?php

namespace App\Services;

use Midtrans\Snap;
use Midtrans\Notification;

class MidtransService
{
    public function __construct()
    {
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = filter_var(config('services.midtrans.is_production'), FILTER_VALIDATE_BOOLEAN);
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
    }

    /**
     * Create a Snap token for a transaction.
     * Expects array with keys: order_id, gross_amount, customer (optional)
     */
    public function createSnapToken(array $transaction): string
    {
        $params = [
            'transaction_details' => [
                'order_id' => $transaction['order_id'],
                'gross_amount' => $transaction['gross_amount'],
            ],
        ];

        if (!empty($transaction['customer'])) {
            $params['customer_details'] = $transaction['customer'];
        }

        return Snap::getSnapToken($params);
    }

    /**
     * Parse incoming Midtrans notification and return the Notification instance
     */
    public function parseNotification(): Notification
    {
        return new Notification();
    }

    /**
     * Verify Midtrans notification signature_key.
     * Midtrans signature_key is HMAC SHA512 of order_id|status_code|gross_amount|server_key
     * Returns true if valid, false otherwise.
     */
    public function verifySignature(array $notificationData): bool
    {
        $serverKey = config('services.midtrans.server_key');
        if (empty($serverKey)) {
            return false;
        }

        $orderId = $notificationData['order_id'] ?? null;
        $statusCode = $notificationData['status_code'] ?? null;
        $grossAmount = $notificationData['gross_amount'] ?? null;
        $signatureKey = $notificationData['signature_key'] ?? null;

        if (!$orderId || !$statusCode || !$grossAmount || !$signatureKey) {
            return false;
        }

        $input = $orderId . $statusCode . $grossAmount . $serverKey;
        $expected = hash('sha512', $input);

        return hash_equals($expected, $signatureKey);
    }
}
