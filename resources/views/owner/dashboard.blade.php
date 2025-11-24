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
                        <span>{{ \Carbon\Carbon::now()->isoFormat('MMMM YYYY') }}</span>
                    </div>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('report.index') }}" class="bg-white text-blue-700 px-4 py-2 rounded-lg font-medium">Lihat Laporan</a>
                    <a href="{{ route('transaksi.index') }}" class="bg-white/20 text-white px-4 py-2 rounded-lg">Daftar Transaksi</a>
                </div>
            </div>
        </div>

        <!-- Summary cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $month = \Carbon\Carbon::now()->month;
                $year = \Carbon\Carbon::now()->year;
                $monthlyRevenue = \App\Models\Transaksi::whereYear('tanggal', $year)->whereMonth('tanggal', $month)->sum('total');
                $monthlyCount = \App\Models\Transaksi::whereYear('tanggal', $year)->whereMonth('tanggal', $month)->count();
                $produkCount = \App\Models\Produk::count();
                $customerCount = \App\Models\Pelanggan::count();
            @endphp

            <div class="bg-white p-4 rounded-xl shadow-sm">
                <p class="text-sm text-gray-500">Pendapatan Bulanan</p>
                <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($monthlyRevenue,0,',','.') }}</p>
            </div>

            <div class="bg-white p-4 rounded-xl shadow-sm">
                <p class="text-sm text-gray-500">Transaksi Bulanan</p>
                <p class="text-2xl font-bold text-gray-800">{{ $monthlyCount }}</p>
            </div>

            <div class="bg-white p-4 rounded-xl shadow-sm">
                <p class="text-sm text-gray-500">Total Produk</p>
                <p class="text-2xl font-bold text-gray-800">{{ $produkCount }}</p>
            </div>

            <div class="bg-white p-4 rounded-xl shadow-sm">
                <p class="text-sm text-gray-500">Pelanggan Terdaftar</p>
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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(\App\Models\Transaksi::selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as period, SUM(total) as revenue, COUNT(*) as cnt")->groupBy('period')->orderBy('period','desc')->limit(6)->get() as $r)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $r->period }}</td>
                            <td class="px-4 py-2">Rp {{ number_format($r->revenue,0,',','.') }}</td>
                            <td class="px-4 py-2">{{ $r->cnt }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
