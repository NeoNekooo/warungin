@extends('layouts.app')

@section('content')
<div id="receipt-container" class="max-w-sm mx-auto bg-white p-4 shadow print:shadow-none print:p-0" style="font-family: 'Courier New', Courier, monospace;">
    <!-- Bagian Header: Nama Toko & Logo -->
    <div class="text-center mb-4">
        <img src="{{ asset('image/warungin_logo.png') }}" class="w-16 h-16 rounded-full mx-auto mb-2 shadow-sm border border-gray-100">
        <div class="text-xl font-bold uppercase tracking-tighter text-blue-800">WARUNGIN</div>
        <div class="text-[10px] text-gray-500 italic">Eceran & Grosir Modern</div>
    </div>

    <!-- Garis Pemisah Klasik -->
    <div class="border-b border-dashed border-gray-400 mb-3"></div>

    <!-- Info Transaksi -->
    <div class="text-[11px] mb-3 leading-tight">
        <div class="flex justify-between">
            <span>No: {{ $transaksi->transaksi_id }}</span>
            <span>{{ optional($transaksi->tanggal)->format('d/m/y H:i') }}</span>
        </div>
        <div class="flex justify-between">
            <span>Kasir: {{ auth()->user()->nama ?? 'System' }}</span>
            <span>Ref: {{ $transaksi->midtrans_transaction_id ? substr($transaksi->midtrans_transaction_id, 0, 8) : '-' }}</span>
        </div>
    </div>

    <div class="border-b border-dashed border-gray-400 mb-3"></div>

    <!-- Item List -->
    <div class="text-[12px] space-y-2">
        @if(isset($items) && $items->count())
            @foreach($items as $it)
                <div>
                    <div class="font-medium text-gray-800 leading-tight">{{ $it->nama_produk ?? 'Item' }}</div>
                    <div class="flex justify-between text-[11px] text-gray-600">
                        <span>{{ $it->jumlah }} x Rp {{ number_format($it->harga_satuan, 0, ',', '.') }}</span>
                        <span>Rp {{ number_format($it->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="border-b border-dashed border-gray-400 my-3"></div>

    <!-- Perhitungan Total -->
    <div class="text-[12px] space-y-1">
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
            <div class="flex justify-between">
                <span>Promo ({{ $appliedPromo->code }})</span>
                <span>-{{ number_format($transaksi->diskon, 0, ',', '.') }}</span>
            </div>
        @elseif($transaksi->diskon > 0)
            <div class="flex justify-between">
                <span>Potongan / Diskon</span>
                <span>-{{ number_format($transaksi->diskon, 0, ',', '.') }}</span>
            </div>
        @endif

        <div class="flex justify-between font-bold text-lg pt-1 border-t border-gray-100">
            <span>TOTAL</span>
            <span>Rp {{ number_format($transaksi->total, 0, ',', '.') }}</span>
        </div>
        
        <div class="flex justify-between text-[11px] pt-1">
            <span class="uppercase">Bayar ({{ $transaksi->metode_bayar }})</span>
            <span>Rp {{ number_format($transaksi->nominal_bayar ?? $transaksi->total, 0, ',', '.') }}</span>
        </div>
        
        @if($transaksi->kembalian > 0)
            <div class="flex justify-between text-[11px]">
                <span>Kembalian</span>
                <span>Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
            </div>
        @endif
    </div>

    <div class="border-b border-dashed border-gray-400 my-4"></div>

    <!-- Footer -->
    <div class="text-center space-y-1 mb-8">
        <div class="text-[11px] font-bold uppercase tracking-widest">-- Terima Kasih --</div>
        <div class="text-[9px] text-gray-500">Barang yang sudah dibeli <br> tidak dapat ditukar/dikembalikan</div>
    </div>

    <!-- Tombol Aksi (Hidden on Print) -->
    <div class="print:hidden mt-6 flex gap-2 justify-center">
        <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm shadow-md hover:bg-blue-700 transition">
            <i class="ri-printer-line mr-1"></i> Cetak Struk
        </button>
        <a href="{{ Auth::user()->role === 'kasir' ? route('pos.index') : route('transaksi.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm border hover:bg-gray-200 transition">
            Kembali
        </a>
    </div>
</div>

<style>
    @media print {
        /* Reset Background & Container */
        body { background: white !important; padding: 0 !important; margin: 0 !important; }
        
        /* Pastikan hanya receipt-container yang muncul */
        body > div:not(#receipt-container), 
        header, 
        nav, 
        aside, 
        footer:not(#receipt-container footer) {
            display: none !important;
        }

        #receipt-container {
            width: 58mm !important;
            max-width: 58mm !important;
            margin: 0 !important;
            padding: 5mm !important; /* Margin kecil di dalam kertas */
            box-shadow: none !important;
            visibility: visible !important;
            display: block !important;
            position: absolute;
            left: 0;
            top: 0;
        }

        /* Tampilkan semua anak dari container */
        #receipt-container * {
            visibility: visible !important;
        }

        /* Sembunyikan tombol */
        .print\:hidden { display: none !important; }

        @page {
            size: 58mm auto;
            margin: 0;
        }
    }

    /* Styling for screen (Preview) */
    #receipt-container {
        border: 1px solid #eee;
        border-radius: 8px;
    }
</style>

<script>
    // Auto trigger print window
    window.onload = function() {
        setTimeout(() => {
            // Uncomment line below if you want auto-print dialog
            // window.print();
        }, 500);
    };
</script>
@endsection
