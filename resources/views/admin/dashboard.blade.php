@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="flex-1 p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto space-y-8">

        {{-- 1. Kartu Sambutan (Modern Gradient) --}}
        <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 to-blue-700 rounded-2xl shadow-xl p-8 text-white">
            
            {{-- Hiasan Background --}}
            <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full -mr-16 -mt-16 blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-indigo-400 opacity-20 rounded-full -ml-16 -mb-16 blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight">Selamat Datang, {{ auth()->user()->name ?? 'Admin' }}! 👋</h2>
                    <p class="mt-2 text-indigo-100 text-lg">Ringkasan aktivitas dan status kunci Warungin hari ini.</p>
                    
                    {{-- Tanggal Hari Ini --}}
                    <div class="mt-6 inline-flex items-center bg-white/10 px-4 py-2 rounded-xl backdrop-blur-sm border border-white/20 text-sm font-medium">
                        <i class="ri-calendar-event-line mr-2 text-lg"></i>
                        <span>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
                    </div>
                </div>
                
                {{-- Logo Dekoratif --}}
                <div class="bg-white/20 p-4 rounded-2xl backdrop-blur-md shadow-inner hidden md:block border border-white/10">
                    {{-- Ganti dengan icon yang lebih relevan jika logo tidak tersedia --}}
                    <i class="ri-store-2-fill text-6xl text-white/90 drop-shadow-md"></i>
                </div>
            </div>
        </div>
        
        {{-- 2. Grid Ringkasan (Key Metrics - 4 Kolom) --}}
        <h2 class="text-xl font-bold text-gray-800 border-b pb-2">Ringkasan Keuangan Harian</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $summaries = [
                    [
                        'title' => 'Total Penjualan', 
                        'icon' => 'ri-shopping-bag-3-fill', 
                        'value' => 'Rp 1.250.000', // Data real: $penjualan_hari_ini
                        'color' => 'text-indigo-600',
                        'bg' => 'bg-indigo-50',
                        'trend' => '+12% dari kemarin',
                        'trend_color' => 'text-green-500' // Tambahkan warna tren
                    ],
                    [
                        'title' => 'Total Pengeluaran', 
                        'icon' => 'ri-wallet-3-fill', 
                        'value' => 'Rp 500.000', // Data real: $pengeluaran_hari_ini
                        'color' => 'text-red-600',
                        'bg' => 'bg-red-50',
                        'trend' => 'Stabil',
                        'trend_color' => 'text-gray-500'
                    ],
                    [
                        'title' => 'Total Transaksi', 
                        'icon' => 'ri-file-list-3-fill', 
                        'value' => '15', // Data real: $total_transaksi_hari_ini
                        'color' => 'text-purple-600',
                        'bg' => 'bg-purple-50',
                        'trend' => '5 Pembatalan',
                        'trend_color' => 'text-orange-500'
                    ],
                    [
                        'title' => 'Keuntungan Bersih', 
                        'icon' => 'ri-line-chart-fill', 
                        'value' => 'Rp 750.000', // Data real: $keuntungan_bersih
                        'color' => 'text-green-600',
                        'bg' => 'bg-green-50',
                        'trend' => 'Target tercapai',
                        'trend_color' => 'text-green-500'
                    ],
                ];
            @endphp

            @foreach ($summaries as $item)
                @php
                    // Logika sederhana untuk icon trend
                    $trendIcon = '';
                    if (str_contains($item['trend'], '+')) {
                        $trendIcon = 'ri-arrow-up-line';
                    } elseif (str_contains($item['trend'], 'Stabil')) {
                        $trendIcon = 'ri-line-line';
                    } elseif (str_contains($item['trend'], 'Pembatalan')) {
                        $trendIcon = 'ri-error-warning-line';
                    } else {
                         $trendIcon = 'ri-check-line';
                    }
                @endphp
                <div class="group bg-white rounded-2xl p-6 shadow-md border border-gray-100 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ $item['title'] }}</p>
                            <h3 class="text-3xl font-extrabold text-gray-900 mt-1 tracking-tight">{{ $item['value'] }}</h3>
                        </div>
                        {{-- Icon Container --}}
                        <div class="{{ $item['bg'] }} {{ $item['color'] }} p-3 rounded-xl group-hover:scale-110 transition-transform duration-300 flex items-center justify-center w-14 h-14 shadow-lg ring-1 ring-gray-100">
                            <i class="{{ $item['icon'] }} text-2xl"></i>
                        </div>
                    </div>
                    
                    {{-- Footer Kecil di Card --}}
                    <div class="flex items-center text-xs pt-3 mt-2 {{ $item['trend_color'] }} border-t border-gray-100">
                        <i class="{{ $trendIcon }} mr-1.5 text-sm"></i>
                        <span class="font-semibold">{{ $item['trend'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 3. Area Data Detail (Grafik & Tabel) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Grafik Penjualan (2/3 Lebar) --}}
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-md border border-gray-100 min-h-[380px] flex flex-col">
                <div class="flex justify-between items-center mb-6 border-b pb-3">
                    <h3 class="font-extrabold text-gray-800 text-lg flex items-center"><i class="ri-line-chart-fill mr-2 text-indigo-500"></i> Statistik Penjualan 7 Hari Terakhir</h3>
                    <button class="text-sm text-indigo-600 hover:text-indigo-800 font-semibold">Lihat Detail</button>
                </div>
                
                {{-- Placeholder Chart --}}
                <div class="flex-1 flex flex-col items-center justify-center bg-gray-50 rounded-xl border border-dashed border-gray-200 p-8">
                    <div class="bg-white p-4 rounded-full mb-3 shadow-md">
                        <i class="ri-bar-chart-grouped-line text-4xl text-blue-400"></i>
                    </div>
                    <p class="text-md font-extrabold text-gray-600">Area Grafik (Chart.js/ApexCharts)</p>
                    <p class="text-xs text-gray-400 mt-1 italic">Tampilkan tren pendapatan vs pengeluaran.</p>
                </div>
            </div>

            {{-- Produk Terlaris (1/3 Lebar) --}}
            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100 min-h-[380px] flex flex-col">
                <div class="flex justify-between items-center mb-6 border-b pb-3">
                    <h3 class="font-extrabold text-gray-800 text-lg flex items-center"><i class="ri-trophy-line mr-2 text-yellow-500"></i> Produk Terlaris</h3>
                </div>

                {{-- Placeholder List Produk --}}
                <ul class="space-y-3 flex-1 overflow-y-auto">
                    {{-- Looping Produk Terlaris --}}
                    @for($i = 1; $i <= 5; $i++)
                        <li class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100 hover:bg-indigo-50 transition">
                            <div class="flex items-center">
                                <span class="font-extrabold text-xl mr-3 text-indigo-500">#{{ $i }}</span>
                                <div>
                                    <p class="font-semibold text-gray-800">Nama Produk {{ $i }}</p>
                                    <p class="text-xs text-gray-500">Kategori • Rp20.000</p>
                                </div>
                            </div>
                            <span class="text-sm font-bold text-green-600">{{ rand(50, 200) }} Pcs</span>
                        </li>
                    @endfor
                </ul>
                <div class="mt-4 text-center">
                    <a href="#" class="text-sm font-semibold text-indigo-600 hover:underline">Lihat Semua Produk</a>
                </div>
            </div>
        </div>
        
        {{-- 4. Data Operasional: Aktivitas Transaksi Terbaru & Peringatan Stok --}}
        <h2 class="text-xl font-bold text-gray-800 border-b pb-2 pt-4">Operasional & Logistik</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Transaksi Terbaru (Log Operasional) --}}
            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
                <h3 class="font-extrabold text-gray-800 text-lg mb-6 flex items-center border-b pb-3"><i class="ri-history-line mr-2 text-indigo-500"></i> 10 Transaksi Terbaru</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kasir</th>
                                <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @for($i=0; $i<5; $i++)
                            <tr>
                                <td class="px-3 py-3 whitespace-nowrap font-mono text-gray-900">#{{ 1000 + $i }}</td>
                                <td class="px-3 py-3 whitespace-nowrap text-gray-600">{{ \Carbon\Carbon::now()->subMinutes(rand(1, 60))->format('H:i') }}</td>
                                <td class="px-3 py-3 whitespace-nowrap text-gray-600">Kasir {{ rand(1, 3) }}</td>
                                <td class="px-3 py-3 whitespace-nowrap text-right font-bold text-indigo-600">Rp{{ number_format(rand(10000, 250000), 0, ',', '.') }}</td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-center">
                    <a href="#" class="text-sm font-semibold text-indigo-600 hover:underline">Lihat Semua Transaksi</a>
                </div>
            </div>

            {{-- Peringatan Inventaris/Stok --}}
            <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
                <h3 class="font-extrabold text-gray-800 text-lg mb-6 flex items-center border-b pb-3"><i class="ri-alert-line mr-2 text-orange-500"></i> Peringatan Stok Rendah</h3>
                <ul class="space-y-3 flex-1 overflow-y-auto">
                     @for($i = 1; $i <= 4; $i++)
                        <li class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-200 hover:bg-orange-100 transition">
                            <div class="flex items-center">
                                <i class="ri-box-3-line text-2xl mr-3 text-orange-500"></i>
                                <div>
                                    <p class="font-semibold text-gray-800">Nama Produk Kurang {{ $i }}</p>
                                    <p class="text-xs text-gray-500">Stok kritis: 
                                        <span class="font-bold text-red-600">{{ rand(1, 10) }} pcs</span>
                                    </p>
                                </div>
                            </div>
                            <a href="#" class="text-xs font-bold text-indigo-600 hover:underline">Isi Stok</a>
                        </li>
                    @endfor
                    <li class="text-center py-4 text-gray-500 italic text-sm">
                        <i class="ri-check-double-line mr-1"></i> Tidak ada stok yang habis.
                    </li>
                </ul>
                <div class="mt-4 text-center">
                    <a href="#" class="text-sm font-semibold text-indigo-600 hover:underline">Lihat Semua Inventaris</a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection