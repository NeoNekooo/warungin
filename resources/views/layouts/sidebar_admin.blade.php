<div class="w-64 bg-white shadow-xl flex flex-col flex-shrink-0 h-screen sticky top-0">
    
    <div class="flex items-center p-6 border-b border-gray-100">
        {{-- Pastikan path logo benar --}}
        <img src="{{ asset('image/warungin_logo.png') }}" alt="Logo" class="w-10 h-10 rounded-full object-cover mr-3">
        <h1 class="text-xl font-semibold text-gray-800">WARUNGIN</h1>
    </div>

    <nav class="flex-1 p-4 space-y-2">
        
        <a href="#"
            {{-- Menggunakan route() Laravel jika tersedia --}}
            class="flex items-center px-4 py-2 text-gray-800 rounded-lg bg-blue-100 font-semibold transition duration-200">
            <i class="fas fa-home mr-3 text-blue-600"></i> 
            Dashboard
        </a>

        <a href="" 
            class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition duration-200">
            <i class="fas fa-receipt mr-3"></i> 
            Transaksi
        </a>
        
        <a href="" 
            class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">
            <i class="fas fa-boxes mr-3"></i> 
            Stock Barang
        </a>
    </nav>
    
    <div class="border-t border-gray-100 space-y-2 py-2">
        
        <a href="" 
           class="flex items-center mx-4 px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition duration-200">
            <i class="fas fa-chart-line mr-3"></i> 
            Laporan
        </a>

        {{-- 
        <form method="POST" action="{{ route('kasir.logout') }}" class="mx-4">
            @csrf
            <button type="submit" 
                class="flex items-center px-4 py-2 text-red-500 hover:bg-red-50 rounded-lg w-full text-left transition duration-200">
                <i class="fas fa-sign-out-alt mr-3"></i> 
                Logout
            </button>
        </form>
        --}}
    </div>
</div>