@extends('layouts.app')

@section('title', 'Riwayat Stok Produk')

@section('content')
<div class="space-y-8 relative min-h-screen">

    {{-- Notifikasi Toast (jika ada) --}}
    <div id="toast-container" class="fixed bottom-5 right-5 z-[100] flex flex-col gap-2"></div>

    {{-- Header Dashboard/Judul Halaman (Diubah agar konsisten) --}}
    <div class="bg-white p-6 rounded-2xl shadow-xl border border-gray-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-5">
        
        <div class="space-y-1">
            <h2 class="flex items-center text-3xl font-extrabold text-gray-900">
                <i class="ri-history-line text-3xl text-indigo-500 mr-2"></i>
                Stok Log
            </h2>
            <div class="flex flex-wrap items-center text-sm text-gray-500">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                    <i class="ri-inbox-fill mr-1.5 text-sm"></i> Riwayat Inventori
                </span>
                <span class="mx-3 hidden sm:inline-block">•</span>
                <p class="text-sm mt-1 sm:mt-0 text-gray-600">Mencatat semua perubahan stok produk (masuk dan keluar).</p>
            </div>
        </div>

        {{-- Tidak ada tombol tambah karena log biasanya otomatis --}}
        {{-- Jika Anda ingin tombol, letakkan di sini --}}
        {{-- <div>
            <button class="bg-indigo-600 text-white ...">Tambah Log Manual</button>
        </div> --}}
    </div>

    {{-- Tabel Daftar Stok Log --}}
    <div class="overflow-x-auto rounded-2xl shadow-xl bg-white ring-1 ring-gray-200">
        <table class="min-w-full border-collapse text-left">
            <thead>
                {{-- Header Tabel dengan warna Indigo --}}
                <tr class="bg-indigo-600 text-white">
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider rounded-tl-2xl w-10">No</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider">Produk</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider">Tanggal</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider text-center">Tipe</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider text-right">Jumlah</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider">Sumber</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider max-w-xs">Keterangan</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider rounded-tr-2xl">User</th>
                </tr>
            </thead>

            <tbody class="text-gray-700 divide-y divide-gray-100">
                @forelse($stokLogs as $log)
                <tr class="hover:bg-indigo-50 transition duration-150 ease-in-out">
                    <td class="px-5 py-3.5 text-sm text-gray-600">{{ $loop->iteration }}</td>
                    
                    {{-- Nama Produk --}}
                    <td class="px-5 py-3.5 font-medium text-sm text-gray-800">
                        <div class="flex items-center">
                             <i class="ri-box-3-fill text-indigo-400 mr-2"></i>
                            {{ $log->produk->nama_produk ?? 'Produk dihapus' }}
                        </div>
                    </td>
                    
                    {{-- Tanggal --}}
                    <td class="px-5 py-3.5 text-xs text-gray-500 font-medium">
                        {{ \Carbon\Carbon::parse($log->tanggal)->isoFormat('D MMM YYYY, HH:mm') }}
                    </td>
                    
                    {{-- Tipe Log --}}
                    <td class="px-5 py-3.5 text-center capitalize">
                        @if($log->tipe === 'masuk')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 ring-1 ring-green-300">
                                <i class="ri-add-box-line mr-1"></i> MASUK
                            </span>
                        @elseif($log->tipe === 'keluar')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 ring-1 ring-red-300">
                                <i class="ri-subtract-line mr-1"></i> KELUAR
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                {{ strtoupper($log->tipe) }}
                            </span>
                        @endif
                    </td>
                    
                    {{-- Jumlah --}}
                    <td class="px-5 py-3.5 text-right font-extrabold text-base {{ $log->tipe === 'masuk' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $log->tipe === 'keluar' ? '-' : '+' }}{{ number_format($log->jumlah, 0, ',', '.') }}
                    </td>
                    
                    {{-- Sumber --}}
                    <td class="px-5 py-3.5 text-sm text-gray-600 font-semibold">{{ $log->sumber }}</td>
                    
                    {{-- Keterangan --}}
                    <td class="px-5 py-3.5 text-sm text-gray-500 max-w-xs truncate" title="{{ $log->keterangan }}">
                        {{ $log->keterangan }}
                    </td>
                    
                    {{-- User --}}
                    <td class="px-5 py-3.5 text-sm text-gray-600">
                        <div class="flex items-center">
                            <i class="ri-user-2-fill text-gray-400 mr-1.5"></i>
                            {{ $log->user->name ?? 'Sistem' }}
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="px-5 py-12 text-center text-gray-400 italic" colspan="8">
                        <div class="flex flex-col items-center justify-center">
                            <div class="bg-gray-100 p-5 rounded-full mb-3">
                                <i class="ri-file-search-line text-5xl text-gray-300"></i>
                            </div>
                            <p class="text-xl font-bold text-gray-600">Data Stok Log Kosong</p>
                            <p class="text-sm mt-1 text-gray-500">Belum ada riwayat perubahan stok yang tercatat.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if(isset($stokLogs) && method_exists($stokLogs, 'hasPages') && $stokLogs->hasPages())
    <div class="px-6 py-4 bg-white rounded-2xl border border-gray-100 flex justify-center md:justify-end">
        {{ $stokLogs->links() }} 
    </div>
    @endif
    
</div>
@endsection