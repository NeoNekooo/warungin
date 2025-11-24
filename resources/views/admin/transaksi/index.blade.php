@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-semibold">Transaksi (Kasir View)</h2>
        {{-- no direct "create" button here; kasir uses the dedicated checkout flow --}}
        <a href="{{ route('kasir.dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Kembali ke Kasir</a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($transaksi as $t)
            <div class="bg-white rounded shadow p-4 flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-semibold">Trx #{{ $t->transaksi_id }}</h3>
                            <p class="text-sm text-gray-500">{{ optional($t->tanggal)->format('Y-m-d H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-sm text-gray-500">{{ $t->metode_bayar }}</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($t->total, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-500">Pelanggan: {{ optional($t->pelanggan)->nama ?? 'Umum' }}</p>
                    </div>

                    @if($t->midtrans_transaction_id)
                        <div class="mt-3 text-xs text-gray-500">Midtrans ID: {{ $t->midtrans_transaction_id }}</div>
                    @endif
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <div>
                        <span class="inline-block px-2 py-1 text-xs rounded {{ $t->status === 'selesai' ? 'bg-green-100 text-green-700' : ($t->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">{{ ucfirst($t->status) }}</span>
                    </div>

                    <div class="flex items-center space-x-2">
                        @if($t->status === 'pending' && $t->metode_bayar !== 'tunai')
                            <a href="{{ route('midtrans.checkout', $t->transaksi_id) }}" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Bayar</a>
                        @endif

                        <form action="{{ route('transaksi.destroy', $t->transaksi_id) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded text-sm">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center text-gray-500 p-6 bg-white rounded shadow">Belum ada transaksi.</div>
        @endforelse
    </div>
</div>
@endsection
<x-app-layout>
            <main class="flex-1 p-6">
            <div class="space-y-6">
                
                <!-- Kartu Sambutan -->
                <div class="bg-white p-6 rounded-xl shadow-md flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Transaksi</h2>
                        <p class="text-gray-500">Ringkasan Penjualan & Pengeluaran Hari Ini</p>
                    </div>
                    {{-- Logo kecil di pojok kartu --}}
                    <img src="{{ asset('image/warungin_logo.png') }}" alt="Logo" class="w-12 h-12 opacity-50">
                </div>
            </x-app-layout>