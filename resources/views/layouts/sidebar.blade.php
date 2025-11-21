{{-- 1. Sidebar: FIXED, full-height, di sebelah kiri --}}
    <div class="w-64 bg-white shadow-xl flex flex-col fixed h-screen left-0 top-0 z-20">
        
        {{-- Logo/Header --}}
        <div class="flex items-center p-6 border-b border-gray-100 flex-shrink-0">
            <img src="{{ asset('image/warungin_logo.png') }}" alt="Logo" class="w-10 h-10 rounded-full object-cover mr-3 shadow-sm">
            <h1 class="text-xl font-bold text-gray-800 tracking-wider">WARUNGIN</h1>
        </div>

        {{-- Navigasi Utama: Flex-1 agar mengisi sisa ruang, overflow-y-auto agar menu bisa discroll --}}
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
            
            {{-- Bagian 1: Dashboard (Conditional by Role) --}}
            @if (Auth::user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center px-4 py-2 text-gray-800 rounded-lg bg-blue-100 font-semibold transition duration-200 hover:bg-blue-200">
                    <i class="fas fa-home mr-3 text-blue-600"></i> 
                    Dashboard Admin
                </a>
            @else
                 <a href="{{ route('kasir.dashboard') }}"
                   class="flex items-center px-4 py-2 text-gray-800 rounded-lg bg-green-100 font-semibold transition duration-200 hover:bg-green-200">
                    <i class="fas fa-cash-register mr-3 text-green-600"></i> 
                    Dashboard Kasir
                </a>
            @endif

            {{-- --- Menu Khusus ADMIN (Data Master & Transaksi Detail) --- --}}
            @if (Auth::user()->role === 'admin')
                
                {{-- Data Master Header --}}
                <div class="font-semibold text-xs uppercase text-gray-400 mt-6 pt-4 border-t border-gray-200">Data Master</div>
                <div class="space-y-1">
                    <a href="{{route('produk.index')}}" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-box-open mr-3 w-4"></i> Produk
                    </a>
                    <a href="{{route('kategori.index')}}" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-tags mr-3 w-4"></i> Kategori
                    </a>
                    <a href="{{route('pelanggan.index')}}" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-users mr-3 w-4"></i> Pelanggan
                    </a>
                    <a href="{{route('stok_log.index')}}" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-list-alt mr-3 w-4"></i> Stok Log
                    </a>
                </div>

                {{-- Menu Transaksi Header --}}
                <div class="font-semibold text-xs uppercase text-gray-400 mt-6 pt-4 border-t border-gray-200">Menu Transaksi</div>
                <div class="space-y-1">
                    <a href="" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition duration-200">
                        <i class="fas fa-receipt mr-3 w-4"></i> Transaksi
                    </a>
                    <a href="" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-wallet mr-3 w-4"></i> Pembayaran
                    </a>
                    <a href="" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-info-circle mr-3 w-4"></i> Transaksi Detail
                    </a>
                </div>
                
                {{-- Pengaturan Header --}}
                <div class="font-semibold text-xs uppercase text-gray-400 mt-6 pt-4 border-t border-gray-200">Pengaturan</div>
                <div class="space-y-1">
                     <a href="{{ route('manajemen_akun.index') }}" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-user-cog mr-3 w-4"></i> Kelola Akun
                    </a>
                </div>
            @endif

            {{-- --- Menu Khusus KASIR (Operasional) --- --}}
            @if (Auth::user()->role === 'kasir')
                {{-- Operasional Kasir Header --}}
                <div class="font-semibold text-xs uppercase text-gray-400 mt-6 pt-4 border-t border-gray-200">Operasional Kasir</div>
                <div class="space-y-1">
                    <a href="" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition duration-200">
                        <i class="fas fa-cash-register mr-3 w-4"></i> Transaksi Baru
                    </a>
                    <a href="" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition duration-200">
                        <i class="fas fa-history mr-3 w-4"></i> Riwayat Transaksi
                    </a>
                    <a href="" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-search mr-3 w-4"></i> Lihat Produk
                    </a>
                    <a href="" class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-address-book mr-3 w-4"></i> Lihat Pelanggan
                    </a>
                </div>
            @endif
        </nav>
        
        {{-- Logout dan Laporan (Ditempatkan di bawah, di luar nav yang bisa di-scroll) --}}
        <div class="p-4 border-t border-gray-200 bg-white flex-shrink-0">
            @if (Auth::user()->role === 'admin')
                <a href="" 
                    class="flex items-center px-4 py-2 mb-2 text-gray-600 hover:bg-gray-100 rounded-lg transition duration-200">
                    <i class="fas fa-chart-line mr-3 w-4"></i> Laporan & Analitik
                </a>
            @endif

            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" 
                    class="flex items-center px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg w-full text-left transition duration-200 font-semibold border border-red-200 hover:border-red-300">
                    <i class="fas fa-sign-out-alt mr-3 w-4"></i> Logout
                </button>
            </form>
        </div>
    </div>

    {{-- 2. Main Content: Diberi margin kiri agar tidak tertutup sidebar yang fixed --}}
    <div class="ml-64 flex flex-col min-h-screen">
        <main class="flex-1 bg-gray-100 p-6 overflow-y-auto">
            @yield('content')
        </main>
    </div>