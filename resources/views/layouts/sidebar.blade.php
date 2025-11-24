{{-- 1. Sidebar Fixed --}}
<div class="w-64 bg-white shadow-2xl flex flex-col fixed inset-y-0 left-0 z-20 border-r border-gray-200">

    {{-- Logo --}}
    <div class="flex items-center p-6 border-b border-gray-100">
        <img src="{{ asset('image/warungin_logo.png') }}" class="w-10 h-10 rounded-full mr-3 shadow">
        <h1 class="text-xl font-bold text-gray-800 tracking-wider">WARUNGIN</h1>
    </div>

    {{-- Helper Active --}}
    @php
        if (! function_exists('isActive')) {
            function isActive($route) {
                return request()->routeIs($route)
                    ? 'bg-blue-100 text-blue-700 font-semibold shadow-sm'
                    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900';
            }
        }
    @endphp

    <nav class="flex-1 p-4 space-y-3 overflow-y-auto">

        {{-- Dashboard --}}
        @if (Auth::user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center px-4 py-2 rounded-lg transition
                {{ request()->routeIs('admin.dashboard') 
                    ? 'bg-blue-100 text-blue-700 font-semibold shadow-sm' 
                    : 'text-gray-700 hover:bg-gray-100' }}">
                <i class="fas fa-home mr-3 text-blue-600"></i> Dashboard Admin
            </a>
        @else
            <a href="{{ route('kasir.dashboard') }}"
                class="flex items-center px-4 py-2 rounded-lg transition
                {{ request()->routeIs('kasir.dashboard') 
                    ? 'bg-green-100 text-green-700 font-semibold shadow-sm' 
                    : 'text-gray-700 hover:bg-gray-100' }}">
                <i class="fas fa-cash-register mr-3 text-green-600"></i> Dashboard Kasir
            </a>
        @endif



        {{-- ================= ADMIN ONLY ================= --}}
        @if (Auth::user()->role === 'admin')

            {{-- Divider --}}
            <div class="text-xs uppercase font-bold text-gray-400 mt-6 mb-1 tracking-wider">
                Data Master
            </div>

            {{-- Produk --}}
            <a href="{{ route('produk.index') }}"
                class="flex items-center px-4 py-2 rounded-lg transition {{ isActive('produk.index') }}">
                <i class="fas fa-box-open mr-3 w-4"></i> Produk
            </a>

            {{-- Kategori --}}
            <a href="{{ route('kategori.index') }}"
                class="flex items-center px-4 py-2 rounded-lg transition {{ isActive('kategori.index') }}">
                <i class="fas fa-tags mr-3 w-4"></i> Kategori
            </a>

            {{-- Pelanggan --}}
            <a href="{{ route('pelanggan.index') }}"
                class="flex items-center px-4 py-2 rounded-lg transition {{ isActive('pelanggan.index') }}">
                <i class="fas fa-users mr-3 w-4"></i> Pelanggan
            </a>

            {{-- Promo --}}
            <a href="{{ route('promos.index') }}"
                class="flex items-center px-4 py-2 rounded-lg transition {{ isActive('promos.index') }}">
                <i class="fas fa-percent mr-3 w-4"></i> Promo
            </a>

            {{-- Stok Log --}}
            <a href="{{ route('stok_log.index') }}"
                class="flex items-center px-4 py-2 rounded-lg transition {{ isActive('stok_log.index') }}">
                <i class="fas fa-list-alt mr-3 w-4"></i> Stok Log
            </a>


            {{-- Divider --}}
            <div class="text-xs uppercase font-bold text-gray-400 mt-6 mb-1 tracking-wider">
                Menu Transaksi
            </div>

            <a href="{{ route('pos.index') }}" class="flex items-center px-4 py-2 rounded-lg transition {{ isActive('pos.index') }}">
                <i class="fas fa-receipt mr-3 w-4"></i> Transaksi
            </a>
            <a href="{{ route('pembayaran.index') }}" class="flex items-center px-4 py-2 rounded-lg transition {{ isActive('pembayaran.index') }}">
                <i class="fas fa-wallet mr-3 w-4"></i> Pembayaran
            </a>
            <a href="{{ route('transaksi_detail.index') }}" class="flex items-center px-4 py-2 rounded-lg transition {{ isActive('transaksi_detail.index') }}">
                <i class="fas fa-info-circle mr-3 w-4"></i> Transaksi Detail
            </a>


            {{-- Divider --}}
            <div class="text-xs uppercase font-bold text-gray-400 mt-6 mb-1 tracking-wider">
                Pengaturan
            </div>

            <a href="{{ route('manajemen_akun.index') }}"
                class="flex items-center px-4 py-2 rounded-lg transition {{ isActive('manajemen_akun.index') }}">
                <i class="fas fa-user-cog mr-3 w-4"></i> Kelola Akun
            </a>
        @endif



        {{-- ================= KASIR / OWNER ================= --}}
        @hasanyrole(['kasir','owner'])
            <div class="text-xs uppercase font-bold text-gray-400 mt-6 mb-1 tracking-wider">
                Operasional Kasir
            </div>
            <a href="{{ route('pos.index') }}" class="flex items-center px-4 py-2 rounded-lg transition {{ isActive('pos.index') }}">
                <i class="fas fa-cash-register mr-3 w-4"></i> Transaksi
            </a>

            <a href="{{ route('produk.index') }}" class="flex items-center px-4 py-2 rounded-lg transition {{ isActive('produk.index') }}">
                <i class="fas fa-box-open mr-3 w-4"></i> Produk
            </a>

            <a href="{{ route('transaksi_detail.index') }}" class="flex items-center px-4 py-2 rounded-lg transition {{ isActive('transaksi_detail.index') }}">
                <i class="fas fa-info-circle mr-3 w-4"></i> Transaksi Detail
            </a>

            <a href="{{ route('pelanggan.index') }}" class="flex items-center px-4 py-2 rounded-lg transition {{ isActive('pelanggan.index') }}">
                <i class="fas fa-users mr-3 w-4"></i> Pelanggan
            </a>

        @endhasanyrole

    </nav>

    {{-- Bottom Section --}}
    <div class="p-4 border-t border-gray-200 bg-white">

        {{-- Laporan: Admin / Kasir / Owner --}}
        @hasanyrole(['admin','kasir','owner'])
        <a href="{{ route('reports.index') }}" class="flex items-center px-4 py-2 mb-2 rounded-lg text-gray-700 hover:bg-gray-100">
            <i class="fas fa-chart-line mr-3 w-4"></i> Laporan & Analitik
        </a>
        @endhasanyrole

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="flex items-center px-4 py-2 w-full rounded-lg border border-red-300 text-red-600 
                        hover:bg-red-50 hover:text-red-700 transition font-semibold">
                <i class="fas fa-sign-out-alt mr-3 w-4"></i> Logout
            </button>
        </form>
    </div>

</div>

{{-- Main Content --}}
<div class="ml-64 flex flex-col min-h-screen bg-gray-100">
    <main class="flex-1 p-6 overflow-y-visible">
        @yield('content')
    </main>
</div>
