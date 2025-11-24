@extends('layouts.app')

@section('title', 'Dashboard Kasir')

@section('content')
<div class="flex-1 p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto space-y-8">

        <!-- Welcome card -->
        <div class="relative overflow-hidden bg-gradient-to-r from-green-600 to-emerald-600 rounded-2xl shadow-lg p-8 text-white">
            <div class="absolute top-0 right-0 w-48 h-48 bg-white opacity-10 rounded-full -mr-12 -mt-12 blur-3xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Selamat Datang, {{ auth()->user()->nama ?? auth()->user()->username }} 👋</h2>
                    <p class="mt-2 text-green-100 text-lg">Gunakan kasir untuk memproses transaksi dengan cepat.</p>
                    <div class="mt-6 inline-flex items-center bg-white/10 px-4 py-2 rounded-lg backdrop-blur-sm border border-white/20 text-sm font-medium">
                        <span>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
                    </div>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('pos.index') }}" class="bg-white text-green-700 px-4 py-2 rounded-lg font-medium">Buka Kasir</a>
                    <a href="{{ route('transaksi_detail.index') }}" class="bg-white/20 text-white px-4 py-2 rounded-lg">Riwayat</a>
                </div>
            </div>
        </div>

        <!-- Summary cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $today = \Carbon\Carbon::now()->toDateString();
                $transToday = \App\Models\Transaksi::whereDate('tanggal', $today)->count();
                $totalToday = \App\Models\Transaksi::whereDate('tanggal', $today)->sum('total');
                $produkCount = \App\Models\Produk::count();
                $pendingPayments = \App\Models\Pembayaran::where('jumlah', 0)->count();
            @endphp

            <div class="bg-white p-4 rounded-xl shadow-sm">
                <p class="text-sm text-gray-500">Transaksi Hari Ini</p>
                <p class="text-2xl font-bold text-gray-800">{{ $transToday }}</p>
            </div>

            <div class="bg-white p-4 rounded-xl shadow-sm">
                <p class="text-sm text-gray-500">Total Penjualan</p>
                <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalToday,0,',','.') }}</p>
            </div>

            <div class="bg-white p-4 rounded-xl shadow-sm">
                <p class="text-sm text-gray-500">Produk Terdaftar</p>
                <p class="text-2xl font-bold text-gray-800">{{ $produkCount }}</p>
            </div>

            <div class="bg-white p-4 rounded-xl shadow-sm">
                <p class="text-sm text-gray-500">Pembayaran Pending</p>
                <p class="text-2xl font-bold text-gray-800">{{ $pendingPayments }}</p>
            </div>
        </div>

        <!-- Recent activity table -->
        <div class="bg-white p-4 rounded-xl shadow-sm">
            <h2 class="font-semibold mb-3">Aktivitas Terbaru</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                        <tr>
                            <th class="px-4 py-2">Waktu</th>
                            <th class="px-4 py-2">Jenis</th>
                            <th class="px-4 py-2">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(\App\Models\Transaksi::latest()->limit(6)->get() as $t)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ optional($t->tanggal)->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-2">Transaksi #{{ $t->transaksi_id }}</td>
                            <td class="px-4 py-2">Rp {{ number_format($t->total,0,',','.') }} — {{ ucfirst($t->status) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection