@extends('layouts.app')

@section('content')
<div class="max-w-sm mx-auto bg-white p-6 shadow print:p-0 print:shadow-none" style="font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-3">
            <img src="{{ asset('image/warungin_logo.png') }}" class="w-14 h-14 rounded-full shadow-sm">
            <div>
                <div class="text-lg font-bold">WARUNGIN</div>
                <div class="text-xs text-gray-500">Invoice</div>
            </div>
        </div>
        <div class="text-right text-xs text-gray-500">{{ optional($transaksi->tanggal)->format('d M Y H:i:s') }}</div>
    </div>

    <div class="border-t pt-3">
        <div class="flex justify-between text-sm py-1"><div class="text-gray-600">Kode Pembayaran</div><div class="font-medium">{{ $transaksi->midtrans_transaction_id ?? $transaksi->transaksi_id }}</div></div>
        <div class="flex justify-between text-sm py-1"><div class="text-gray-600">Merchant</div><div class="font-medium">Warungin.com</div></div>
        <div class="flex justify-between text-sm py-1"><div class="text-gray-600">Referensi</div><div class="font-medium">{{ $transaksi->midtrans_transaction_id ?? '-' }}</div></div>
    </div>

    <div class="my-4 text-sm font-semibold">-- ITEM --</div>

    <div class="text-sm">
        @if(isset($items) && $items->count())
            @foreach($items as $it)
                <div class="flex justify-between py-1 border-b border-dashed border-gray-100 pb-1">
                    <div class="text-gray-700">{{ $it->nama_produk ?? ($it->produk_id ? 'Produk #'.$it->produk_id : 'Item') }}</div>
                    <div class="text-gray-700">Rp {{ number_format($it->subtotal,0,',','.') }}</div>
                </div>
            @endforeach
        @else
            <div class="flex justify-between py-1 border-b border-dashed border-gray-100 pb-1"><div>Contoh Produk A</div><div>Rp 12,000</div></div>
            <div class="flex justify-between py-1 border-b border-dashed border-gray-100 pb-1"><div>Contoh Produk B</div><div>Rp 3,000</div></div>
            <div class="flex justify-between py-1 border-b border-dashed border-gray-100 pb-1"><div>Contoh Produk C</div><div>Rp 20,000</div></div>
        @endif
    </div>

    <div class="mt-4">
        @php
            $appliedPromo = null;
            // midtrans_raw may be array or json
            $raw = $transaksi->midtrans_raw ?? null;
            if(is_string($raw)){
                try { $raw = json_decode($raw, true); } catch(\Throwable $e) { $raw = null; }
            }
            $appliedId = null;
            if(is_array($raw) && isset($raw['applied_promo_id'])) $appliedId = $raw['applied_promo_id'];
            if($appliedId){
                try{ $appliedPromo = \App\Models\Promo::find($appliedId); } catch(\Throwable $e){ $appliedPromo = null; }
            }
        @endphp

        @if($appliedPromo)
            <div class="flex justify-between py-1"><div class="text-gray-600">Promo</div><div class="font-medium">{{ $appliedPromo->name }} ({{ $appliedPromo->code ?? '-' }})</div></div>
        @endif

        <div class="flex justify-between py-1"><div class="text-gray-600">Diskon</div><div class="font-medium">Rp {{ $transaksi->diskon ? number_format($transaksi->diskon,0,',','.') : '0' }}</div></div>
        <div class="flex justify-between py-1"><div class="text-gray-600">Bayar</div><div class="font-medium">Rp {{ number_format($transaksi->nominal_bayar ?? $transaksi->total,0,',','.') }}</div></div>
        <div class="flex justify-between py-1 text-xl font-bold"><div>Total</div><div>Rp {{ number_format($transaksi->total,0,',','.') }}</div></div>
        <div class="flex justify-between py-1"><div class="text-gray-600">Kembalian</div><div class="font-medium">Rp {{ number_format($transaksi->kembalian ?? 0,0,',','.') }}</div></div>
    </div>

    <div class="text-center mt-6 text-sm font-semibold">-- TERIMA KASIH --</div>

    <div class="mt-4 flex items-center justify-between">
        <div class="text-xs text-gray-500">Simpan atau cetak bukti ini.</div>
        <div>
            <button id="print-button" onclick="window.print()" class="px-3 py-1 bg-gray-800 text-white rounded text-sm mr-2">Print</button>
            <a href="{{ route('pos.index') }}" class="px-3 py-1 border rounded text-sm">Kembali</a>
        </div>
    </div>
</div>

<script>
    // Auto-print when invoice is opened (small delay to allow rendering)
    window.addEventListener('DOMContentLoaded', function(){
        setTimeout(function(){
            // trigger print and close window for popups (if opened via window.open)
            try { window.print(); } catch(e) {}
        }, 250);
    });
</script>

<style>
    @media print {
        /* Hide everything by default, then show the invoice container AND its children */
        body * { visibility: hidden !important; }
        .print\:p-0, .print\:shadow-none { visibility: visible !important; }
        /* Ensure all descendant elements of the invoice container are visible in print */
        .print\:p-0 *, .print\:shadow-none * { visibility: visible !important; }
        .print\:p-0 { padding: 0 !important; }
        .print\:shadow-none { box-shadow: none !important; }
        .max-w-sm { width: 100% !important; margin: 0 auto; }
    }
</style>

@endsection
