<style>
    /* Custom Scrollbar untuk Navigasi */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #c0c0c0;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #a0a0a0;
    }

    /* Transisi untuk Transform dan Margin - Penting untuk animasi yang halus */
    /* Pastikan kelas transition-all ada di elemen utama yang bergerak (sidebar & content) */
    .transition-all {
        transition-property: all;
        transition-duration: 300ms;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>

{{-- 
    Keterangan Alpine.js:
    - `open`: Status buka/tutup sidebar.
    - `isDesktop`: Status apakah lebar layar saat ini >= 1024px.
    - `setupResizeWatcher()`: Mengatur ulang `open` hanya saat beralih antara mobile dan desktop. 
      (DIUBAH: Di desktop, `open` disetel true secara default, tetapi user BISA menutupnya melalui toggle).
--}}
<div x-data="{ 
    open: window.innerWidth >= 1024, // Buka default di desktop (lg:), tutup di mobile
    isDesktop: window.innerWidth >= 1024,
    toggle() { this.open = !this.open },
    setupResizeWatcher() {
        window.addEventListener('resize', () => {
            const newIsDesktop = window.innerWidth >= 1024;
            
            if (newIsDesktop !== this.isDesktop) {
                this.isDesktop = newIsDesktop;
                // Saat beralih ke desktop, pastikan terbuka.
                if (this.isDesktop) {
                    this.open = true; 
                } else {
                    // Saat beralih ke mobile, pastikan tertutup (overlay).
                    this.open = false; 
                }
            }
        });
    }
}" x-init="setupResizeWatcher">

    {{-- ======================================================================= --}}
    {{-- A. SIDEBAR UTAMA (Fixed Position) --}}
    {{-- ======================================================================= --}}
    <div x-cloak x-ref="sidebar"
        :class="{ 
            /* Tutup: geser ke kiri di semua ukuran layar */
            'transform -translate-x-full': !open,
            /* Buka: geser ke posisi 0 */
            'transform translate-x-0': open
        }"
        class="w-64 bg-white shadow-2xl flex flex-col fixed inset-y-0 left-0 z-40 lg:z-20 border-r border-gray-200 transition-all">

        {{-- Logo dan Nama Aplikasi --}}
        <div class="flex items-center p-6 border-b border-gray-100 bg-indigo-50">
            <img src="{{ asset('image/warungin_logo.png') }}" class="w-10 h-10 rounded-xl mr-3 shadow-md border border-indigo-100">
            <h1 class="text-xl font-extrabold text-indigo-700 tracking-wider">WARUNGIN</h1>
        </div>

        {{-- Helper Active Function --}}
        @php
            if (! function_exists('isActive')) {
                function isActive($route) {
                    return request()->routeIs($route)
                        ? 'bg-indigo-50 text-indigo-700 font-bold shadow-sm ring-2 ring-indigo-200'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-indigo-800';
                }
            }
        @endphp

        <nav class="flex-1 p-4 space-y-1 overflow-y-auto custom-scrollbar">

            {{-- Dashboard --}}
            @if (Auth::user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 ease-in-out transform hover:scale-[1.01] {{ isActive('admin.dashboard') }}"
                    @click="!isDesktop && toggle()">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                        <path d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z" />
                        <path d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z" />
                    </svg>
                    <span class="ml-3">Dashboard Admin</span>
                </a>
            @elseif (Auth::user()->role === 'owner')
                <a href="{{ route('owner.dashboard') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 ease-in-out transform hover:scale-[1.01] {{ isActive('owner.dashboard') }}"
                    @click="!isDesktop && toggle()">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                        <path d="M11.47 3.841a.75.75 0 0 1 1.06 0l8.69 8.69a.75.75 0 1 0 1.06-1.061l-8.689-8.69a2.25 2.25 0 0 0-3.182 0l-8.69 8.69a.75.75 0 1 0 1.061 1.06l8.69-8.689Z" />
                        <path d="m12 5.432 8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 0 1-.75-.75v-4.5a.75.75 0 0 0-.75-.75h-3a.75.75 0 0 0-.75.75V21a.75.75 0 0 1-.75.75H5.625a1.875 1.875 0 0 1-1.875-1.875v-6.198a2.29 2.29 0 0 0 .091-.086L12 5.432Z" />
                    </svg>
                    <span class="ml-3">Dashboard Owner</span>
                </a>
            @elseif (Auth::user()->role === 'kasir')
                <a href="{{ route('kasir.dashboard') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 ease-in-out transform hover:scale-[1.01]
                    {{ request()->routeIs('kasir.dashboard')
                        ? 'bg-green-100 text-green-700 font-bold shadow-sm ring-2 ring-green-200'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-green-800' }}"
                    @click="!isDesktop && toggle()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-2.25 4.5H19.5a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H4.5A2.25 2.25 0 0 0 2.25 6.75v10.5a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                    <span class="ml-3">Dashboard Kasir</span>
                </a>
            @endif

            {{-- Admin Menu --}}
            @if (Auth::user()->role === 'admin')

            {{-- Divider: Data Master --}}
            <div class="text-xs uppercase font-extrabold text-indigo-400 opacity-80 mt-6 mb-1 tracking-wider pt-2 border-t border-gray-100">
                Data Master
            </div>

            {{-- Produk --}}
            <a href="{{ route('produk.index') }}"
                class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 ease-in-out transform hover:scale-[1.01] {{ isActive('produk.index') }}"
                @click="!isDesktop && toggle()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                </svg>
                <span class="ml-3">Produk</span>
            </a>

            {{-- Kategori --}}
            <a href="{{ route('kategori.index') }}"
                class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 ease-in-out transform hover:scale-[1.01] {{ isActive('kategori.index') }}"
                @click="!isDesktop && toggle()">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                <path fill-rule="evenodd" d="M5.25 2.25a3 3 0 0 0-3 3v4.318a3 3 0 0 0 .879 2.121l9.58 9.581c.92.92 2.39 1.186 3.548.428a18.849 18.849 0 0 0 5.441-5.44c.758-1.16.492-2.629-.428-3.548l-9.58-9.581a3 3 0 0 0-2.122-.879H5.25ZM6.375 7.5a1.125 1.125 0 1 0 0-2.25 1.125 1.125 0 0 0 0 2.25Z" clip-rule="evenodd" />
                </svg>
                <span class="ml-3">Kategori</span>
            </a>

            {{-- Pelanggan --}}
            <a href="{{ route('pelanggan.index') }}"
                class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 ease-in-out transform hover:scale-[1.01] {{ isActive('pelanggan.index') }}"
                @click="!isDesktop && toggle()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
                <span class="ml-3">Pelanggan</span>
            </a>

            {{-- Stok Log --}}
            <a href="{{ route('stok_log.index') }}"
                class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 ease-in-out transform hover:scale-[1.01] {{ isActive('stok_log.index') }}"
                @click="!isDesktop && toggle()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                </svg>
                <span class="ml-3">Stok Log</span>
            </a>


            {{-- Divider: Menu Transaksi --}}
            <div class="text-xs uppercase font-extrabold text-indigo-400 opacity-80 mt-6 mb-1 tracking-wider pt-2 border-t border-gray-100">
                Menu Transaksi
            </div>

            {{-- Transaksi --}}
            <a href="{{ route('transaksi.index') }}" class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 ease-in-out transform hover:scale-[1.01] {{ isActive('transaksi.index') }}"
                @click="!isDesktop && toggle()">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                <path fill-rule="evenodd" d="M3 3a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V3zm2 5h14v10H5V8zm2-3h10v1H7V5zm0 12h10v1H7v-1z" clip-rule="evenodd" />
                </svg>
                <span class="ml-3">Transaksi</span>
            </a>

            {{-- Pembayaran --}}
            <a href="{{ route('pembayaran.index') }}" class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 ease-in-out transform hover:scale-[1.01] {{ isActive('pembayaran.index') }}"
                @click="!isDesktop && toggle()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                </svg>
                <span class="ml-3">Pembayaran</span>
            </a>

            {{-- Transaksi Detail --}}
            <a href="{{ route('transaksi_detail.index') }}" class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 ease-in-out transform hover:scale-[1.01] {{ isActive('transaksi_detail.index') }}"
                @click="!isDesktop && toggle()">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                <path fill-rule="evenodd" d="M3 3a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V3zm2 5h14v10H5V8zm2-3h10v1H7V5zm0 12h10v1H7v-1z" clip-rule="evenodd" />
                </svg>
                <span class="ml-3">Transaksi Detail</span>
            </a>


            {{-- Divider: Pengaturan --}}
            <div class="text-xs uppercase font-extrabold text-indigo-400 opacity-80 mt-6 mb-1 tracking-wider pt-2 border-t border-gray-100">
                Pengaturan
            </div>

            {{-- Manajemen Akun --}}
            <a href="{{ route('manajemen_akun.index') }}"
                class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 ease-in-out transform hover:scale-[1.01] {{ isActive('manajemen_akun.index') }}"
                @click="!isDesktop && toggle()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
                <span class="ml-3">Manajemen Akun</span>
            </a>
            @endif

            {{-- Owner Menu --}}
            @if (Auth::user()->role === 'owner')
                <div class="text-xs uppercase font-extrabold text-indigo-400 opacity-80 mt-6 mb-1 tracking-wider pt-2 border-t border-gray-100">
                    Data Master (Owner)
                </div>
                {{-- Produk --}}
                <a href="{{ route('produk.index') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 ease-in-out transform hover:scale-[1.01] {{ isActive('produk.index') }}"
                    @click="!isDesktop && toggle()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>
                    <span class="ml-3">Produk</span>
                </a>
                {{-- Kategori --}}
                <a href="{{ route('kategori.index') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 ease-in-out transform hover:scale-[1.01] {{ isActive('kategori.index') }}"
                    @click="!isDesktop && toggle()">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                    <path fill-rule="evenodd" d="M5.25 2.25a3 3 0 0 0-3 3v4.318a3 3 0 0 0 .879 2.121l9.58 9.581c.92.92 2.39 1.186 3.548.428a18.849 18.849 0 0 0 5.441-5.44c.758-1.16.492-2.629-.428-3.548l-9.58-9.581a3 3 0 0 0-2.122-.879H5.25ZM6.375 7.5a1.125 1.125 0 1 0 0-2.25 1.125 1.125 0 0 0 0 2.25Z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-3">Kategori</span>
                </a>
                {{-- Pelanggan --}}
                <a href="{{ route('pelanggan.index') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 ease-in-out transform hover:scale-[1.01] {{ isActive('pelanggan.index') }}"
                    @click="!isDesktop && toggle()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                    <span class="ml-3">Pelanggan</span>
                </a>
                {{-- Stok Log --}}
                <a href="{{ route('stok_log.index') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 ease-in-out transform hover:scale-[1.01] {{ isActive('stok_log.index') }}"
                    @click="!isDesktop && toggle()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                    </svg>
                    <span class="ml-3">Stok Log</span>
                </a>
                <div class="text-xs uppercase font-extrabold text-indigo-400 opacity-80 mt-6 mb-1 tracking-wider pt-2 border-t border-gray-100">
                    Menu Transaksi (Owner)
                </div>
                {{-- Transaksi --}}
                <a href="{{ route('transaksi.index') }}" class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 ease-in-out transform hover:scale-[1.01] {{ isActive('transaksi.index') }}"
                    @click="!isDesktop && toggle()">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V3zm2 5h14v10H5V8zm2-3h10v1H7V5zm0 12h10v1H7v-1z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-3">Transaksi</span>
                </a>
                {{-- Pembayaran --}}
                <a href="{{ route('pembayaran.index') }}" class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 ease-in-out transform hover:scale-[1.01] {{ isActive('pembayaran.index') }}"
                    @click="!isDesktop && toggle()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                    </svg>
                    <span class="ml-3">Pembayaran</span>
                </a>
                {{-- Promo --}}
                <a href="{{ route('promos.index') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 ease-in-out transform hover:scale-[1.01] {{ isActive('promos.index') }}"
                    @click="!isDesktop && toggle()">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                    <path fill-rule="evenodd" d="M1.5 6.375c0-1.036.84-1.875 1.875-1.875h17.25c1.035 0 1.875.84 1.875 1.875v3.026a.75.75 0 0 1-.375.65 2.249 2.249 0 0 0 0 3.898.75.75 0 0 1 .375.65v3.026c0 1.035-.84 1.875-1.875 1.875H3.375A1.875 1.875 0 0 1 1.5 17.625v-3.026a.75.75 0 0 1 .374-.65 2.249 2.249 0 0 0 0-3.898.75.75 0 0 1-.374-.65V6.375Zm15-1.125a.75.75 0 0 1 .75.75v.75a.75.75 0 0 1-1.5 0V6a.75.75 0 0 1 .75-.75Zm.75 4.5a.75.75 0 0 0-1.5 0v.75a.75.75 0 0 0 1.5 0v-.75Zm-.75 3a.75.75 0 0 1 .75.75v.75a.75.75 0 0 1-1.5 0v-.75a.75.75 0 0 1 .75-.75Zm.75 4.5a.75.75 0 0 0-1.5 0V18a.75.75 0 0 0 1.5 0v-.75ZM6 12a.75.75 0 0 1 .75-.75H12a.75.75 0 0 1 0 1.5H6.75A.75.75 0 0 1 6 12Zm.75 2.25a.75.75 0 0 0 0 1.5h3a.75.75 0 0 0 0-1.5h-3Z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-3">Promo</span>
                </a>
                
                {{-- Laporan & Analitik --}}
                <a href="{{ route('reports.index') }}" 
                   class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 ease-in-out transform hover:scale-[1.01] {{ isActive('reports.index') }}"
                   @click="!isDesktop && toggle()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25m0-1.5H5.25M6 4.5h.75A2.25 2.25 0 0 1 9 6.75V16.5M6 4.5V3m0 3.75v3m0 3.75v3m0-11.25H9.75M12 4.5h.75A2.25 2.25 0 0 1 15 6.75V16.5M12 4.5v3m0 3.75v3m0 3.75v3m0-11.25H15.75" />
                    </svg>
                    <span class="ml-3">Laporan & Analitik</span>
                </a>
            @endif

            {{-- Kasir Menu --}}
            @if (Auth::user()->role === 'kasir')
                <div class="text-xs uppercase font-extrabold text-indigo-400 opacity-80 mt-6 mb-1 tracking-wider pt-2 border-t border-gray-100">
                    Operasional Kasir
                </div>
                
                {{-- Transaksi (POS) --}}
                <a href="{{ route('pos.index') }}" class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 ease-in-out transform hover:scale-[1.01] {{ isActive('pos.index') }}"
                    @click="!isDesktop && toggle()">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                    <path d="M4.5 3.75a3 3 0 0 0-3 3v.75h21v-.75a3 3 0 0 0-3-3h-15Z" />
                    <path fill-rule="evenodd" d="M22.5 9.75h-21v7.5a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3v-7.5Zm-18 3.75a.75.75 0 0 1 .75-.75h6a.75.75 0 0 1 0 1.5h-6a.75.75 0 0 1-.75-.75Zm.75 2.25a.75.75 0 0 0 0 1.5h3a.75.75 0 0 0 0-1.5h-3Z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-3">Transaksi (POS)</span>
                </a>

                {{-- Produk --}}
                <a href="{{ route('produk.index') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 ease-in-out transform hover:scale-[1.01] {{ isActive('produk.index') }}"
                    @click="!isDesktop && toggle()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>
                    <span class="ml-3">Produk</span>
                </a>

                {{-- Pelanggan --}}
                <a href="{{ route('pelanggan.index') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 ease-in-out transform hover:scale-[1.01] {{ isActive('pelanggan.index') }}"
                    @click="!isDesktop && toggle()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                    <span class="ml-3">Pelanggan</span>
                </a>

                {{-- Transaksi Detail --}}
                <a href="{{ route('transaksi_detail.index') }}" class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 ease-in-out transform hover:scale-[1.01] {{ isActive('transaksi_detail.index') }}"
                    @click="!isDesktop && toggle()">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V3zm2 5h14v10H5V8zm2-3h10v1H7V5zm0 12h10v1H7v-1z" clip-rule="evenodd" />
                    </svg>
                    <span class="ml-3">Transaksi Detail</span>
                </a>
                {{-- Pembayaran --}}
                <a href="{{ route('pembayaran.index') }}" class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 ease-in-out transform hover:scale-[1.01] {{ isActive('pembayaran.index') }}"
                    @click="!isDesktop && toggle()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                    </svg>
                    <span class="ml-3">Pembayaran</span>
                </a>
                {{-- Stok Log --}}
                <a href="{{ route('stok_log.index') }}"
                    class="flex items-center px-4 py-2.5 rounded-lg transition duration-150 ease-in-out transform hover:scale-[1.01] {{ isActive('stok_log.index') }}"
                    @click="!isDesktop && toggle()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                    </svg>
                    <span class="ml-3">Stok Log</span>
                </a>
            @endif


        </nav>

        {{-- Bottom Section (Laporan dan Logout) --}}
        <div class="p-4 border-t border-gray-200 bg-gray-50">

            {{-- Laporan: Admin --}}
            @if (Auth::user()->role === 'admin')
            <a href="{{ route('reports.index') }}" 
               class="flex items-center px-4 py-2.5 mb-3 rounded-lg text-indigo-700 font-semibold bg-indigo-100 hover:bg-indigo-200 transition duration-150 ease-in-out"
               @click="!isDesktop && toggle()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 text-indigo-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25m0-1.5H5.25M6 4.5h.75A2.25 2.25 0 0 1 9 6.75V16.5M6 4.5V3m0 3.75v3m0 3.75v3m0-11.25H9.75M12 4.5h.75A2.25 2.25 0 0 1 15 6.75V16.5M12 4.5v3m0 3.75v3m0 3.75v3m0-11.25H15.75" />
                </svg>
                <span class="ml-3">Laporan & Analitik</span>
            </a>
            @endif

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center px-4 py-2.5 w-full rounded-lg border border-red-400 text-red-700 
                            bg-red-100 hover:bg-red-200 transition duration-150 font-bold shadow-md"
                    @click="!isDesktop && toggle()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H6" />
                    </svg>
                    <span class="ml-3">Logout</span>
                </button>
            </form>
        </div>

    </div>

    {{-- Backdrop (Hanya muncul di mobile saat sidebar terbuka) --}}
    <div x-show="open && !isDesktop"
        @click="toggle()"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-30 bg-gray-900 bg-opacity-50 lg:hidden">
    </div>

    {{-- ======================================================================= --}}
    {{-- B. CONTENT UTAMA (Didorong/Push Content) --}}
    {{-- ======================================================================= --}}
    <div x-cloak x-transition class="flex flex-col min-h-screen bg-gray-100 transition-all"
        :class="{ 
            /* Di desktop (lg:), geser konten 64 unit ke kanan jika 'open' true */
            'lg:ml-64': open,
            'lg:ml-0': !open 
        }">
        
        {{-- Header untuk Toggle Button (Penting) --}}
        <header class="h-16 w-full shadow-md bg-white flex items-center justify-between p-4 sticky top-0 z-10">
            <div class="flex items-center">
                {{-- Tombol Buka/Tutup Sidebar --}}
                <button type="button" 
                        @click="toggle()" 
                        class="p-2 mr-4 text-gray-500 rounded-lg hover:text-indigo-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <span x-show="!open">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </span>
                    <span x-show="open">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </span>
                </button>
                <span class="text-lg font-bold text-blue-600">Hallo,</span> <span class="text-lg font-bold text-black">  {{ auth()->user()->nama}}</span>
            </div>

            {{-- Info User --}}
            <div class="hidden sm:block">
                    <div id="realtime-clock" class="text-3xl font-extrabold text-indigo-700 tracking-wider font-mono">00:00:00</div>
                    <p class="text-xs text-gray-500 mt-0.5">{{ \Carbon\Carbon::now()->isoFormat('ddd, D MMM') }}</p>
            </div>
        </header>

        {{-- Konten Utama Halaman --}}
        <main class="flex-1 p-6 overflow-y-visible">
            @yield('content')
        </main>

        {{-- Footer (Opsional) --}}
        <footer class="p-4 border-t border-gray-200 text-center text-sm text-gray-500">
            &copy; 2025 WARUNGIN. All rights reserved.
        </footer>
    </div>
</div>
<script>
    function updateClock() {
        const now = new Date();
        let hours = now.getHours();
        let minutes = now.getMinutes();
        let seconds = now.getSeconds();

        // Tambahkan nol di depan
        hours = hours < 10 ? '0' + hours : hours;
        minutes = minutes < 10 ? '0' + minutes : minutes;
        seconds = seconds < 10 ? '0' + seconds : seconds;

        const timeString = hours + ':' + minutes + ':' + seconds;

        // Perbarui elemen dengan ID 'realtime-clock'
        document.getElementById('realtime-clock').innerHTML = timeString;
    }

    // Panggil fungsi saat halaman dimuat
    updateClock();

    // Atur agar fungsi dipanggil setiap 1 detik
    setInterval(updateClock, 1000); 
</script>