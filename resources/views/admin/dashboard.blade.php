@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="flex-1 p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto space-y-8">

        <!-- 1. Kartu Sambutan (Modern Gradient) -->
        <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-lg p-8 text-white relative">
            <!-- Hiasan Background (Blob) -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full -mr-16 -mt-16 blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-blue-400 opacity-20 rounded-full -ml-16 -mb-16 blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight">Selamat Datang, Admin! 👋</h2>
                    <p class="mt-2 text-blue-100 text-lg">Berikut adalah ringkasan aktivitas Warungin hari ini.</p>
                    
                    <!-- Tanggal Hari Ini -->
                    <div class="mt-6 inline-flex items-center bg-white/10 px-4 py-2 rounded-lg backdrop-blur-sm border border-white/20 text-sm font-medium">
                        <i class="ri-calendar-event-line mr-2 text-lg"></i>
                        <span>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
                    </div>
                </div>
                
                <!-- Logo Dekoratif (Glassmorphism) -->
                <div class="bg-white/20 p-4 rounded-2xl backdrop-blur-md shadow-inner hidden md:block border border-white/10">
                        <img src="{{ asset('image/warungin_logo.png') }}" alt="Logo" class="w-16 h-16 object-contain drop-shadow-md">
                </div>
            </div>
        </div>

        <!-- 2. Grid Ringkasan (4 Kolom) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $summaries = [
                    [
                        'title' => 'Total Penjualan', 
                        'icon' => 'ri-shopping-bag-3-fill', 
                        'value' => 'Rp 1.250.000',
                        'color' => 'text-blue-600',
                        'bg' => 'bg-blue-50',
                        'border' => 'border-blue-100',
                        'trend' => '+12% dari kemarin'
                    ],
                    [
                        'title' => 'Total Pengeluaran', 
                        'icon' => 'ri-wallet-3-fill', 
                        'value' => 'Rp 500.000',
                        'color' => 'text-red-600',
                        'bg' => 'bg-red-50',
                        'border' => 'border-red-100',
                        'trend' => 'Stabil'
                    ],
                    [
                        'title' => 'Transaksi Hari Ini', 
                        'icon' => 'ri-file-list-3-fill', 
                        'value' => '15',
                        'color' => 'text-purple-600',
                        'bg' => 'bg-purple-50',
                        'border' => 'border-purple-100',
                        'trend' => '5 Menunggu'
                    ],
                    [
                        'title' => 'Keuntungan Bersih', 
                        'icon' => 'ri-line-chart-fill', 
                        'value' => 'Rp 750.000',
                        'color' => 'text-green-600',
                        'bg' => 'bg-green-50',
                        'border' => 'border-green-100',
                        'trend' => 'Sangat Baik'
                    ],
                ];
            @endphp

            @foreach ($summaries as $item)
                <div class="group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-1 transition-all duration-300">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ $item['title'] }}</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $item['value'] }}</h3>
                        </div>
                        <!-- Icon Container -->
                        <div class="{{ $item['bg'] }} {{ $item['color'] }} {{ $item['border'] }} border p-3 rounded-xl group-hover:scale-110 transition-transform duration-300 flex items-center justify-center w-12 h-12 shadow-sm">
                            <i class="{{ $item['icon'] }} text-xl"></i>
                        </div>
                    </div>
                    
                    <!-- Footer Kecil di Card -->
                    <div class="flex items-center text-xs text-gray-400 border-t border-gray-50 pt-3 mt-1">
                        <i class="ri-time-line mr-1.5"></i>
                        <span>{{ $item['trend'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- 3. Placeholder Grafik & Produk (Layout Grid 2:1) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Grafik Area (Placeholder) -->
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 min-h-[300px] flex flex-col">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-gray-800 text-lg">Statistik Penjualan</h3>
                    <button class="text-sm text-blue-600 hover:underline">Lihat Detail</button>
                </div>
                
                <!-- Placeholder Chart -->
                <div class="flex-1 flex flex-col items-center justify-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
                    <div class="bg-white p-4 rounded-full mb-3 shadow-sm">
                        <i class="ri-bar-chart-grouped-line text-4xl text-blue-300"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Grafik Penjualan Mingguan</p>
                    <p class="text-xs text-gray-400 mt-1">(Nanti aja sama Fauzi)</p>
                </div>
            </div>

            <!-- Produk Terlaris (Placeholder) -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 min-h-[300px] flex flex-col">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-gray-800 text-lg">Produk Terlaris</h3>
                </div>

                <!-- Placeholder List -->
                <div class="flex-1 flex flex-col items-center justify-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
                    <div class="bg-white p-4 rounded-full mb-3 shadow-sm">
                        <i class="ri-trophy-line text-4xl text-yellow-400"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Top Produk Bulan Ini</p>
                    <p class="text-xs text-gray-400 mt-1">(Data belum tersedia)</p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection