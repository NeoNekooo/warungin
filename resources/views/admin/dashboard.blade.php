@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
    <div class="flex-1 p-6 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto space-y-8">

            {{-- 1. Kartu Sambutan --}}
            <div
                class="relative overflow-hidden bg-gradient-to-r from-indigo-600 to-blue-700 rounded-2xl shadow-xl p-8 text-white">
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full -mr-16 -mt-16 blur-3xl pointer-events-none">
                </div>
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-3xl font-extrabold tracking-tight">Selamat Datang,
                            {{ auth()->user()->name ?? 'Admin' }}! 👋</h2>
                        <p class="mt-2 text-indigo-100 text-lg">Ringkasan aktivitas Warungin hari ini.</p>
                        <div
                            class="mt-6 inline-flex items-center bg-white/10 px-4 py-2 rounded-xl backdrop-blur-sm border border-white/20 text-sm font-medium">
                            <i class="ri-calendar-event-line mr-2 text-lg"></i>
                            <span>{{ $currentDate->isoFormat('dddd, D MMMM Y') }}</span>
                        </div>
                    </div>
                    <div class="bg-white/20 p-4 rounded-2xl backdrop-blur-md hidden md:block border border-white/10">
                        <i class="ri-store-2-fill text-6xl text-white/90 drop-shadow-md"></i>
                    </div>
                </div>
            </div>

            {{-- 2. Grid Key Metrics --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Total Pendapatan --}}
                <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-100 hover:shadow-lg transition-shadow">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Pendapatan</p>
                            <h3 class="text-2xl font-bold text-gray-900 mt-1">Rp
                                {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                        </div>
                        <div class="bg-indigo-50 text-indigo-600 p-3 rounded-xl"><i
                                class="ri-money-dollar-circle-line text-2xl"></i></div>
                    </div>
                </div>
                {{-- Total Transaksi --}}
                <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-100 hover:shadow-lg transition-shadow">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Transaksi</p>
                            <h3 class="text-2xl font-bold text-gray-900 mt-1">
                                {{ number_format($totalTransactions, 0, ',', '.') }}</h3>
                        </div>
                        <div class="bg-blue-50 text-blue-600 p-3 rounded-xl"><i class="ri-bill-line text-2xl"></i></div>
                    </div>
                </div>
                {{-- Total Pengguna --}}
                <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-100 hover:shadow-lg transition-shadow">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Pengguna</p>
                            <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalUsers, 0, ',', '.') }}
                            </h3>
                        </div>
                        <div class="bg-green-50 text-green-600 p-3 rounded-xl"><i class="ri-group-line text-2xl"></i></div>
                    </div>
                </div>
                {{-- Total Produk --}}
                <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-100 hover:shadow-lg transition-shadow">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Produk</p>
                            <h3 class="text-2xl font-bold text-gray-900 mt-1">
                                {{ number_format($totalProducts, 0, ',', '.') }}</h3>
                        </div>
                        <div class="bg-yellow-50 text-yellow-600 p-3 rounded-xl"><i class="ri-box-3-line text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Area Grafik & Produk Terlaris --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Diagram Penjualan --}}
                <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-md border border-gray-100">
                    <div class="flex justify-between items-center mb-6 border-b pb-3">
                        <h3 class="font-extrabold text-gray-800 text-lg flex items-center">
                            <i class="ri-line-chart-fill mr-2 text-indigo-500"></i> Tren Penjualan 7 Hari Terakhir
                        </h3>
                    </div>

                    <div class="relative h-72 w-full">
                        <canvas id="salesChart"></canvas>
                    </div>



                    <div class="mt-8">
                        <h4 class="text-sm font-bold text-gray-700 mb-4 italic">Detail Ringkasan Bulanan:</h4>
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
                                    @foreach ($monthlySalesOverview as $r)
                                        <tr class="border-t">
                                            <td class="px-4 py-2">{{ $r->period }}</td>
                                            <td class="px-4 py-2 font-bold text-indigo-600">Rp
                                                {{ number_format($r->revenue, 0, ',', '.') }}</td>
                                            <td class="px-4 py-2">{{ $r->cnt }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Produk Terlaris --}}
                <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100 flex flex-col">
                    <h3 class="font-extrabold text-gray-800 text-lg mb-6 flex items-center border-b pb-3">
                        <i class="ri-trophy-line mr-2 text-yellow-500"></i> Top 5 Produk (30 Hari)
                    </h3>
                    <ul class="space-y-4 flex-1">
                        @forelse($topProducts as $product)
                            <li class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
                                <div class="flex items-center">
                                    <span class="font-black text-lg mr-3 text-indigo-300">#{{ $loop->iteration }}</span>
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $product->product_name }}</p>
                                        <p class="text-xs text-green-600 font-bold">{{ $product->total_quantity_sold }}
                                            Terjual</p>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="text-center py-10 text-gray-400 italic">Belum ada data.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- 4. Operasional: Transaksi & Stok --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
                    <h3 class="font-extrabold text-gray-800 text-lg mb-4 flex items-center border-b pb-3"><i
                            class="ri-history-line mr-2 text-indigo-500"></i> 10 Transaksi Terbaru</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                                <tr>
                                    <th class="px-3 py-2 text-left">ID</th>
                                    <th class="px-3 py-2 text-left">Kasir</th>
                                    <th class="px-3 py-2 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($recentTransactions as $transaction)
                                    <tr>
                                        <td class="px-3 py-3 font-mono">#{{ $transaction->transaksi_id }}</td>
                                        <td class="px-3 py-3">{{ $transaction->kasir->nama ?? 'N/A' }}</td>
                                        <td class="px-3 py-3 text-right font-bold text-indigo-600">
                                            Rp{{ number_format($transaction->total, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100">
                    <h3 class="font-extrabold text-gray-800 text-lg mb-4 flex items-center border-b pb-3"><i
                            class="ri-alert-line mr-2 text-orange-500"></i> Stok Rendah</h3>
                    <div class="space-y-3">
                        @forelse($lowStockProducts as $product)
                            <div
                                class="flex justify-between items-center p-3 bg-orange-50 border border-orange-100 rounded-xl">
                                <span class="font-medium text-gray-700">{{ $product->nama_produk }}</span>
                                <span
                                    class="bg-red-500 text-white text-xs px-2 py-1 rounded-lg font-bold">{{ $product->stok }}
                                    Pcs</span>
                            </div>
                        @empty
                            <p class="text-center py-10 text-gray-400">Stok aman terkendali.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($salesChartData['labels']),
                datasets: [{
                    label: 'Pendapatan Harian',
                    data: @json($salesChartData['data']),
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#4f46e5'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection
