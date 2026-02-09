@extends('layouts.app')

@section('title', 'Dashboard Owner')

@section('content')
<div class="flex-1 p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto space-y-8">

        <!-- Welcome card -->
        <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-sky-600 rounded-2xl shadow-lg p-8 text-white">
            <div class="absolute top-0 right-0 w-48 h-48 bg-white opacity-10 rounded-full -mr-12 -mt-12 blur-3xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Halo, Pemilik Toko {{ auth()->user()->nama ?? auth()->user()->username }}</h2>
                    <p class="mt-2 text-blue-100 text-lg">Ringkasan performa toko dan laporan bulanan.</p>
                    <div class="mt-6 inline-flex items-center bg-white/10 px-4 py-2 rounded-lg backdrop-blur-sm border border-white/20 text-sm font-medium">
                        <span>{{ \Carbon\Carbon::createFromDate($year, $month)->isoFormat('MMMM YYYY') }}</span>
                    </div>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('reports.index') }}" class="bg-white text-blue-700 px-4 py-2 rounded-lg font-medium">Lihat Laporan</a>
                    <a href="{{ route('transaksi.index') }}" class="bg-white/20 text-white px-4 py-2 rounded-lg">Daftar Transaksi</a>
                </div>
            </div>
        </div>

        <!-- Summary cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-4 rounded-xl shadow-sm">
                <div class="flex items-center text-sm text-gray-500">
                    <i class="ri-wallet-line mr-2 text-xl text-blue-500"></i> Pendapatan Bulanan
                </div>
                <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($monthlyRevenue,0,',','.') }}</p>
            </div>

            <div class="bg-white p-4 rounded-xl shadow-sm">
                <div class="flex items-center text-sm text-gray-500">
                    <i class="ri-exchange-dollar-line mr-2 text-xl text-green-500"></i> Transaksi Bulanan
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ $monthlyCount }}</p>
            </div>

            <div class="bg-white p-4 rounded-xl shadow-sm">
                <div class="flex items-center text-sm text-gray-500">
                    <i class="ri-box-3-line mr-2 text-xl text-yellow-500"></i> Total Produk
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ $produkCount }}</p>
            </div>

            <div class="bg-white p-4 rounded-xl shadow-sm">
                <div class="flex items-center text-sm text-gray-500">
                    <i class="ri-user-line mr-2 text-xl text-purple-500"></i> Pelanggan Terdaftar
                </div>
                <p class="text-2xl font-bold text-gray-800">{{ $customerCount }}</p>
            </div>
        </div>

        <!-- Monthly table preview -->
        <div class="bg-white p-4 rounded-xl shadow-sm">
            <h2 class="font-semibold mb-3">Ringkasan Bulanan</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                        <tr>
                            <th class="px-4 py-2">Bulan</th>
                            <th class="px-4 py-2">Pendapatan</th>
                            <th class="px-4 py-2">Transaksi</th>
                            <th class="px-4 py-2">Rata-rata Transaksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlySummary as $r)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $r->period }}</td>
                            <td class="px-4 py-2">Rp {{ number_format($r->revenue,0,',','.') }}</td>
                            <td class="px-4 py-2">{{ $r->cnt }}</td>
                            <td class="px-4 py-2">Rp {{ number_format($r->avg_transaction_value,0,',','.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Products Section -->
        <div class="bg-white p-4 rounded-xl shadow-sm">
            <h2 class="font-semibold mb-3">Produk Terlaris (30 Hari Terakhir)</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                        <tr>
                            <th class="px-4 py-2">Produk</th>
                            <th class="px-4 py-2">Terjual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $product)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $product->product_name }}</td>
                            <td class="px-4 py-2">{{ $product->total_quantity_sold }} Unit</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-4 py-2 text-center text-gray-500">Tidak ada produk terjual dalam 30 hari terakhir.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
