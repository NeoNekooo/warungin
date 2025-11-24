<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout</title>
</head>
<body>
    <h1>Checkout - Transaksi #{{ $transaksi->transaksi_id ?? 'N/A' }}</h1>
    <p>Total: {{ number_format($transaksi->total, 2) }}</p>

    <button id="pay-button">Bayar dengan Midtrans</button>

    @php
        $isProduction = config('services.midtrans.is_production');
        $clientKey = config('services.midtrans.client_key');
        $snapUrl = $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js';
    @endphp

    <script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
    <script>
        const snapToken = "{{ $snapToken }}";
        document.getElementById('pay-button').addEventListener('click', function () {
            window.snap.pay(snapToken, {
                onSuccess: function(result){
                    if (typeof showToast === 'function') showToast('Pembayaran sukses', 'success');
                    else console.log('Pembayaran sukses');
                    console.log(result);
                    // optionally redirect or call server to mark transaksi
                },
                onPending: function(result){
                    if (typeof showToast === 'function') showToast('Pembayaran pending', 'info');
                    else console.log('Pembayaran pending');
                    console.log(result);
                },
                onError: function(result){
                    if (typeof showToast === 'function') showToast('Pembayaran error', 'error');
                    else console.log('Pembayaran error');
                    console.log(result);
                },
                onClose: function(){
                    if (typeof showToast === 'function') showToast('Anda menutup popup pembayaran tanpa menyelesaikan pembayaran', 'error');
                    else console.log('Popup pembayaran ditutup');
                }
            });
        });
    </script>
</body>
</html>
