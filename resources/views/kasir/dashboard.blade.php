<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir - WARUNGIN</title>
    
    {{-- PASTIKAN LINK INI MENGGUNAKAN .png, SESUAI PERBAIKAN SEBELUMNYA --}}
    <link rel="icon" type="image/png" href="{{ asset('image/warungin_logo.png') }}">

    {{-- Tailwind CSS & App Scripts --}}
    @vite(['resources/css/app.css', 'resources/js/app.js']) 

    {{-- Tambahkan Font Awesome untuk semua ikon (search, moon, receipt, dll) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans antialiased">

<div class="flex h-screen overflow-hidden">
    
    <!-- Bagian 1: Sidebar (Kiri) -->
    <div class="w-64 bg-white shadow-xl flex flex-col flex-shrink-0">
        <!-- Logo dan Nama Warungin -->
        <div class="flex items-center p-6 border-b border-gray-100">
            <img src="{{ asset('image/warungin_logo.png') }}" alt="Logo" class="w-10 h-10 rounded-full object-cover mr-3">
            <h1 class="text-xl font-semibold text-gray-800">WARUNGIN</h1>
        </div>

        <!-- Navigasi -->
        <nav class="flex-1 p-4 space-y-2">
            
            <!-- Link Dashboard (Aktif) -->
            <a href="#"
               class="flex items-center px-4 py-2 text-gray-800 rounded-lg bg-blue-100 font-semibold transition duration-200">
                <i class="fas fa-home mr-3 text-blue-600"></i> 
                Dashboard
            </a>

            <!-- Link Transaksi -->
            <a href="#" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition duration-200">
                <i class="fas fa-receipt mr-3"></i> 
                Transaksi
            </a>
            
            <!-- Link Stock Barang -->
            <a href="#" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-boxes mr-3"></i> 
                Stock Barang
            </a>
            <!-- Link Stock Barang -->
            <a href="#" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                <i class="fas fa-boxes mr-3"></i> 
                Riwayat
            </a>
            
            
           
            
            
            <!-- Form Logout (Diletakkan di Sidebar untuk akses mudah) -->
            <div class="border-t border-gray-100 dark:border-gray-700 mt-auto space-y-2 py-2">
    
    <a href="#" class="flex items-center w-full mx-4 px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition duration-200">
        <i class="fas fa-chart-line mr-3"></i> 
        Laporan
    </a>

    <form method="POST" action="{{ route('kasir.logout') }}" class="mx-4">
        @csrf
        <button type="submit" 
            class="flex items-center px-4 py-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg w-full text-left transition duration-200">
            <i class="fas fa-sign-out-alt mr-3"></i> 
            Logout
        </button>
    </form>
</div>
        </nav>
    </div>

    <!-- Bagian 2 & 3: Main Content Area -->
    <div class="flex-1 flex flex-col overflow-y-auto overflow-x-hidden">
        
        <!-- Header/Search Bar -->
        <header class="flex items-center justify-between p-4 bg-white border-b border-gray-100 shadow-sm">
            
            <!-- Search Bar -->
            <div class="relative w-96">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-search text-gray-400"></i> 
                </span>
                <input class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-200 focus:outline-none focus:ring-1 focus:ring-blue-500" 
                       type="text" 
                       placeholder="Search...">
            </div>

            <!-- Right Side Icons -->
            <div class="flex items-center space-x-4">
                <button class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-moon text-xl"></i> 
                </button>
                
                <button class="px-3 py-1 bg-gray-100 rounded-lg text-gray-700 font-semibold hover:bg-gray-200 transition border border-gray-300">
                    Star
                </button>

                {{-- Avatar User, menampilkan nama user yang login --}}
                <div class="flex items-center space-x-2">
                     <span class="text-sm font-medium text-gray-700 hidden sm:inline">{{ Auth::user()->name }}</span>
                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-lg">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Konten Utama Dashboard -->
        <main class="flex-1 p-6">
            <div class="space-y-6">
                
                <!-- Kartu Sambutan -->
                <div class="bg-white p-6 rounded-xl shadow-md flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Selamat Datang Di Dashboard Warungin</h2>
                        <p class="text-gray-500">Ringkasan Penjualan & Pengeluaran Hari Ini</p>
                    </div>
                    {{-- Logo kecil di pojok kartu --}}
                    <img src="{{ asset('image/warungin_logo.png') }}" alt="Logo" class="w-12 h-12 opacity-50">
                </div>

                <!-- Bagian Ringkasan 4 Kolom -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @php
                        $summaries = [
                            ['title' => 'Total Penjualan', 'icon' => 'fas fa-shopping-cart', 'value' => 'Rp 1.250.000'],
                            ['title' => 'Total Pengeluaran', 'icon' => 'fas fa-money-bill-alt', 'value' => 'Rp 500.000'],
                            ['title' => 'Transaksi Hari Ini', 'icon' => 'fas fa-receipt', 'value' => '15 Transaksi'],
                            ['title' => 'Keuntungan Bersih', 'icon' => 'fas fa-chart-line', 'value' => 'Rp 750.000'],
                        ];
                    @endphp

                    @foreach ($summaries as $item)
                        <div class="bg-white p-5 rounded-xl shadow-md border border-gray-100 flex flex-col justify-between">
                            <i class="{{ $item['icon'] }} text-3xl text-blue-500 mb-3"></i>
                            <p class="text-sm font-medium text-gray-500">{{ $item['title'] }}</p>
                            <h3 class="text-xl font-bold text-gray-800">{{ $item['value'] }}</h3>
                        </div>
                    @endforeach
                </div>

                <!-- Bagian Transaksi Terbaru (Tabel) -->
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Transaksi Terbaru</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TANGGAL</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NAMA BARANG</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">JUMLAH</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                {{-- Data contoh --}}
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">14 Okt 2025</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Kopi Gudey</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">10</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">Rp 12.500</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">15 Okt 2025</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Mie Goreng</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">3</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">Rp 12.000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

</body>
</html>
