@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-semibold">Transaksi (Kasir View)</h2>
        {{-- no direct "create" button here; kasir uses the dedicated checkout flow --}}
        {{-- <a href="{{ route('kasir.dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Kembali ke Kasir</a> --}}
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($transaksi as $t)
            <div class="bg-white rounded shadow p-4 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-lg font-semibold text-gray-800">Trx #{{ $t->transaksi_id }}</h3>
                        <span class="text-sm text-gray-500">{{ optional($t->tanggal)->format('Y-m-d H:i') }}</span>
                    </div>
                    <p class="text-xl font-bold text-indigo-700 mb-2">Rp {{ number_format($t->total, 0, ',', '.') }}</p>
                    
                    <div class="text-sm text-gray-600 mb-1">
                        Pelanggan: <span class="font-medium">{{ optional($t->pelanggan)->nama ?? 'Umum' }}</span>
                    </div>
                    <div class="text-sm text-gray-600 mb-3">
                        Metode Bayar: <span class="font-medium">{{ ucfirst($t->metode_bayar) }}</span>
                    </div>

                    @if($t->midtrans_transaction_id)
                        <div class="mt-3 text-xs text-gray-500">Midtrans ID: <span class="font-mono">{{ $t->midtrans_transaction_id }}</span></div>
                    @endif
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $t->status === 'selesai' ? 'bg-green-100 text-green-700' : ($t->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                            {{ ucfirst($t->status) }}
                        </span>
                    </div>

                    <div class="flex items-center space-x-2">
                        @if($t->status === 'pending' && $t->metode_bayar !== 'tunai')
                            <a href="{{ route('midtrans.checkout', $t->transaksi_id) }}" class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 transition">Bayar</a>
                        @endif
                        
                        {{-- View Invoice Button --}}
                        <a href="{{ route('transaksi.invoice', $t->transaksi_id) }}" target="_blank" class="px-3 py-1 bg-indigo-500 text-white rounded text-sm hover:bg-indigo-600 transition">Invoice</a>

                        <form action="{{ route('transaksi.destroy', $t->transaksi_id) }}" method="POST" data-confirm="Hapus transaksi ini?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600 transition">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center text-gray-500 p-6 bg-white rounded shadow">Belum ada transaksi.</div>
        @endforelse
    </div>
    <div class="mt-6">
        {{ $transaksi->links() }}
    </div>
</div>
@endsection
