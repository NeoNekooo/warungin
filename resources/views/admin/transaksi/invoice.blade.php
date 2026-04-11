<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran #{{ $transaksi->transaksi_id }}</title>
    <style>
        /* CSS KHUSUS PRINTER THERMAL 58MM */
        @page {
            size: 58mm auto;
            margin: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            width: 58mm;
            margin: 0;
            padding: 5px;
            font-size: 11px;
            color: black;
            background-color: white;
            line-height: 1.2;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .header {
            margin-bottom: 10px;
        }

        .store-name {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .divider {
            border-top: 1px dashed black;
            margin: 5px 0;
        }

        .flex {
            display: flex;
            justify-content: space-between;
        }

        .items {
            margin: 10px 0;
        }

        .item-row {
            margin-bottom: 5px;
        }

        .totals {
            margin-top: 10px;
        }

        .total-row {
            margin-bottom: 2px;
        }

        .grand-total {
            font-size: 14px;
            border-top: 1px dashed black;
            padding-top: 5px;
            margin-top: 5px;
        }

        .footer {
            margin-top: 15px;
            font-size: 10px;
        }

        /* Navigasi layar saja */
        .no-print {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            padding: 10px;
            background: rgba(255,255,255,0.9);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .btn {
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-family: sans-serif;
            font-size: 12px;
            cursor: pointer;
        }

        .btn-print { background: #000; color: #fff; border: none; }
        .btn-back { background: #eee; color: #333; border: 1px solid #ccc; }

        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; }
        }
    </style>
</head>
<body>

    <div class="header text-center">
        <div class="store-name">WARUNGIN</div>
        <div>Eceran & Grosir Modern</div>
    </div>

    <div class="divider"></div>

    <div class="info">
        <div class="flex">
            <span>No: {{ $transaksi->transaksi_id }}</span>
            <span>{{ optional($transaksi->tanggal)->format('d/m/y H:i') }}</span>
        </div>
        <div class="flex">
            <span>Kasir: {{ auth()->user()->nama ?? 'System' }}</span>
        </div>
    </div>

    <div class="divider"></div>

    <div class="items">
        @if(isset($items) && $items->count())
            @foreach($items as $it)
                <div class="item-row">
                    <div class="font-bold">{{ $it->nama_produk ?? 'Item' }}</div>
                    <div class="flex">
                        <span>{{ $it->jumlah }} x {{ number_format($it->harga_satuan, 0, ',', '.') }}</span>
                        <span>{{ number_format($it->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="divider"></div>

    <div class="totals">
        @php
            $appliedPromo = null;
            $raw = $transaksi->midtrans_raw ?? null;
            if(is_string($raw)){
                try { $raw = json_decode($raw, true); } catch(\Throwable $e) { $raw = null; }
            }
            $appliedId = (is_array($raw) && isset($raw['applied_promo_id'])) ? $raw['applied_promo_id'] : null;
            if($appliedId){
                try{ $appliedPromo = \App\Models\Promo::find($appliedId); } catch(\Throwable $e){ $appliedPromo = null; }
            }
        @endphp

        @if($appliedPromo)
            <div class="total-row flex">
                <span>Promo ({{ $appliedPromo->code }})</span>
                <span>-{{ number_format($transaksi->diskon, 0, ',', '.') }}</span>
            </div>
        @elseif($transaksi->diskon > 0)
            <div class="total-row flex">
                <span>Diskon</span>
                <span>-{{ number_format($transaksi->diskon, 0, ',', '.') }}</span>
            </div>
        @endif

        <div class="total-row flex grand-total font-bold">
            <span>TOTAL</span>
            <span>Rp {{ number_format($transaksi->total, 0, ',', '.') }}</span>
        </div>
        
        <div class="total-row flex">
            <span>Bayar ({{ $transaksi->metode_bayar }})</span>
            <span>{{ number_format($transaksi->nominal_bayar ?? $transaksi->total, 0, ',', '.') }}</span>
        </div>
        
        @if($transaksi->kembalian > 0)
            <div class="total-row flex">
                <span>Kembali</span>
                <span>{{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
            </div>
        @endif
    </div>

    <div class="divider"></div>

    <div class="footer text-center">
        <div class="font-bold uppercase">-- Terima Kasih --</div>
        <div>Barang yang sudah dibeli <br> tidak dapat ditukar</div>
    </div>

    <!-- Interface Non-Cetak -->
    <div class="no-print">
        <button onclick="window.print()" class="btn btn-print">Cetak Struk</button>
        <a href="{{ Auth::user()->role === 'kasir' ? route('pos.index') : route('transaksi.index') }}" class="btn btn-back">Kembali</a>
    </div>

    <script>
        // Auto print delay
        window.onload = function() {
            setTimeout(() => {
                // window.print();
            }, 500);
        };
    </script>
</body>
</html>
