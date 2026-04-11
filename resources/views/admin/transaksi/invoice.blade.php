@extends('layouts.app')

@section('content')
<!-- Container Utama: MEWAH di Layar, RAMPING di Printer -->
<div id="receipt-container" class="max-w-sm mx-auto bg-white p-6 shadow-xl rounded-xl border border-gray-100 mt-10 print:mt-0 print:shadow-none print:border-none print:p-0">
    
    <!-- Bagian Header: Mewah dengan Logo -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center space-x-3">
            <div class="p-2 bg-blue-50 rounded-xl print:p-0">
                <img src="{{ asset('image/warungin_logo.png') }}" class="w-12 h-12 rounded-lg shadow-sm print:shadow-none print:grayscale">
            </div>
            <div>
                <div class="text-xl font-extrabold text-blue-900 tracking-tight uppercase print:text-black">WARUNGIN</div>
                <div class="text-[10px] text-gray-500 font-medium uppercase tracking-widest print:text-black">Official Invoice</div>
            </div>
        </div>
        <div class="text-right">
            <div class="text-[10px] text-gray-400 font-mono">{{ optional($transaksi->tanggal)->format('d M Y') }}</div>
            <div class="text-[10px] text-gray-400 font-mono">{{ optional($transaksi->tanggal)->format('H:i:s') }}</div>
        </div>
    </div>

    <!-- Divider Garis Modern -->
    <div class="h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent mb-6 print:bg-none print:border-b print:border-dashed print:border-black"></div>

    <!-- Info Transaksi -->
    <div class="grid grid-cols-2 gap-4 mb-6 text-xs">
        <div>
            <div class="text-gray-400 mb-1">ID Transaksi</div>
            <div class="font-bold text-gray-800 print:text-black">#{{ $transaksi->transaksi_id }}</div>
        </div>
        <div class="text-right">
            <div class="text-gray-400 mb-1">Kasir</div>
            <div class="font-bold text-gray-800 print:text-black">{{ auth()->user()->nama ?? 'System' }}</div>
        </div>
    </div>

    <!-- Item List -->
    <div class="space-y-3 mb-6">
        <div class="text-[10px] uppercase font-bold text-blue-600 tracking-widest mb-2 print:text-black print:mb-1">Rincian Belanja</div>
        @if(isset($items) && $items->count())
            @foreach($items as $it)
                <div class="flex justify-between items-start text-sm">
                    <div class="flex-1 pr-4">
                        <div class="font-bold text-gray-800 leading-tight print:text-black">{{ $it->nama_produk ?? 'Item' }}</div>
                        <div class="text-[10px] text-gray-500 print:text-black">{{ $it->jumlah }} x Rp {{ number_format($it->harga_satuan, 0, ',', '.') }}</div>
                    </div>
                    <div class="font-bold text-gray-900 print:text-black">Rp {{ number_format($it->subtotal, 0, ',', '.') }}</div>
                </div>
            @endforeach
        @endif
    </div>

    <!-- Divider Items -->
    <div class="border-b border-dashed border-gray-200 mb-6 print:border-black"></div>

    <!-- Ringkasan Pembayaran -->
    <div class="space-y-2 mb-8">
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
            <div class="flex justify-between text-xs">
                <span class="text-gray-500">Promo: <span class="font-bold text-green-600 print:text-black">{{ $appliedPromo->name }}</span></span>
                <span class="font-bold text-red-500">-{{ number_format($transaksi->diskon, 0, ',', '.') }}</span>
            </div>
        @elseif($transaksi->diskon > 0)
            <div class="flex justify-between text-xs">
                <span class="text-gray-500">Total Diskon</span>
                <span class="font-bold text-red-500">-{{ number_format($transaksi->diskon, 0, ',', '.') }}</span>
            </div>
        @endif

        <div class="flex justify-between items-center py-2 bg-gray-50 rounded-lg px-3 print:bg-none print:px-0 print:border-t print:border-black">
            <span class="text-sm font-bold text-gray-700 print:text-black uppercase">Grand Total</span>
            <span class="text-xl font-black text-blue-700 print:text-black print:text-lg">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</span>
        </div>

        <div class="flex justify-between text-xs px-3 print:px-0">
            <span class="text-gray-500 uppercase print:text-black">Bayar ({{ $transaksi->metode_bayar }})</span>
            <span class="font-bold text-gray-800 print:text-black">Rp {{ number_format($transaksi->nominal_bayar ?? $transaksi->total, 0, ',', '.') }}</span>
        </div>

        @if($transaksi->kembalian > 0)
            <div class="flex justify-between text-xs px-3 print:px-0">
                <span class="text-gray-500 print:text-black">Uang Kembali</span>
                <span class="font-bold text-blue-600 print:text-black">Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</span>
            </div>
        @endif
    </div>

    <!-- Footer: Mewah & Professional -->
    <div class="text-center">
        <div class="inline-block px-4 py-1 bg-gray-900 text-white text-[10px] font-bold uppercase tracking-widest rounded-full mb-3 print:bg-none print:text-black print:border-black print:border print:rounded-none">-- Terima Kasih --</div>
        <div class="text-[9px] text-gray-400 leading-relaxed italic print:text-black">Semoga hari anda menyenangkan! <br> Barang yang telah dibeli tidak dapat ditukar atau dikembalikan.</div>
    </div>

    <!-- Panel Tombol (HANYA LAYAR) -->
    <div class="print:hidden mt-10 grid grid-cols-2 gap-3">
        <button onclick="window.print()" class="flex items-center justify-center py-3 bg-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-200 hover:bg-blue-700 transition transform hover:scale-[1.02]">
            <i class="ri-printer-line mr-2"></i> Cetak Struk
        </button>
        <a href="{{ Auth::user()->role === 'kasir' ? route('pos.index') : route('transaksi.index') }}" class="flex items-center justify-center py-3 bg-gray-100 text-gray-700 rounded-xl font-bold border border-gray-200 hover:bg-gray-200 transition">
            Kembali
        </a>
    </div>
</div>

<style>
    /* DESAIN LAYAR TETAP MEWAH (Tailwind sudah handle) */

    @media print {
        /* CSS KHUSUS PRINTER THERMAL 58MM - AMAN & RAPI */
        
        /* 1. Reset Global */
        body { background: white !important; margin: 0 !important; padding: 0 !important; }
        
        /* 2. Sembunyikan elemen dashboard */
        header, nav, aside, footer, .print\:hidden,
        body > div:not([id="receipt-container"]),
        .min-h-screen { 
            display: none !important; 
        }

        /* 3. Paksa Container ke format Pita 58mm */
        #receipt-container {
            width: 58mm !important;
            max-width: 58mm !important;
            min-width: 58mm !important;
            margin: 0 !important;
            padding: 3mm !important;
            display: block !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            border: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            visibility: visible !important;
            font-family: 'Courier New', Courier, monospace !important; /* Font Monospace untuk Kasir */
        }

        /* 4. Pastikan semua anak elemen muncul */
        #receipt-container * { 
            visibility: visible !important; 
            color: black !important; 
            text-shadow: none !important;
            box-shadow: none !important;
        }

        /* 5. Grayscale Logo agar tidak Hitam Blok */
        img { filter: grayscale(1) !important; opacity: 0.8 !important; }

        @page {
            size: 58mm auto; /* Biarkan panjangnya mengikuti isi */
            margin: 0;
        }
    }
</style>

<script>
    // Trigger print otomatis jika diinginkan
    window.onload = function() {
        // window.print();
    };
</script>
@endsection
