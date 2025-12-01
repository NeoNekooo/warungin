@extends('layouts.app')

@section('title', 'Laporan Detail Transaksi')

@section('content')
<div class="space-y-8 relative min-h-screen">

    {{-- Notifikasi Toast (jika ada) --}}
    <div id="toast-container" class="fixed bottom-5 right-5 z-[100] flex flex-col gap-2"></div>

    {{-- Header Dashboard/Judul Halaman (Diubah agar konsisten) --}}
    <div class="bg-white p-6 rounded-2xl shadow-xl border border-gray-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-5">
        
        <div class="space-y-1">
            <h2 class="flex items-center text-3xl font-extrabold text-gray-900">
                <i class="ri-file-list-3-fill text-3xl text-indigo-500 mr-2"></i>
                Laporan Detail Transaksi
            </h2>
            <div class="flex flex-wrap items-center text-sm text-gray-500">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                    <i class="ri-line-chart-line mr-1.5 text-sm"></i> Data Penjualan Rinci
                </span>
                <span class="mx-3 hidden sm:inline-block">•</span>
                <p class="text-sm mt-1 sm:mt-0 text-gray-600">Daftar lengkap item yang dibeli dalam setiap transaksi.</p>
            </div>
        </div>

        {{-- Tempat untuk tombol Aksi (misalnya: Export) --}}
        <div class="w-full md:w-auto flex justify-end">
            <button class="group bg-indigo-600 text-white px-6 py-3 rounded-xl transition-all shadow-md hover:bg-indigo-700 hover:shadow-lg flex items-center font-semibold text-base w-full md:w-auto">
                <i class="ri-file-download-line mr-2 text-xl"></i> Export Laporan (CSV/Excel)
            </button>
        </div>
    </div>

    {{-- Konten Utama: Tabel Detail Transaksi --}}
    <div class="overflow-x-auto rounded-2xl shadow-xl bg-white ring-1 ring-gray-200">
        <table class="min-w-full border-collapse text-left text-sm">
            <thead>
                {{-- Header Tabel dengan warna Indigo --}}
                <tr class="bg-indigo-600 text-white">
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider rounded-tl-2xl w-10">#</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider text-center">Tanggal</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider text-center">ID Transaksi</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider">Produk</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider text-center">Barcode</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider text-right">Qty</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider text-right">Harga Satuan</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider text-right rounded-tr-2xl">Subtotal</th>
                </tr>
            </thead>

            <tbody class="text-gray-700 divide-y divide-gray-100">
                @forelse($details as $d)
                <tr class="hover:bg-indigo-50 transition duration-150 ease-in-out">
                    {{-- # --}}
                    <td class="px-5 py-3.5 text-sm text-gray-600">{{ $loop->iteration }}</td>

                    {{-- Tanggal --}}
                    <td class="px-5 py-3.5 text-xs text-gray-500 text-center font-medium whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($d->tanggal ?? $d->created_at)->isoFormat('D MMM YYYY') }}<br>
                        <span class="text-gray-400">{{ \Carbon\Carbon::parse($d->tanggal ?? $d->created_at)->isoFormat('HH:mm:ss') }}</span>
                    </td>

                    {{-- Transaksi ID --}}
                    <td class="px-5 py-3.5 text-center font-mono font-semibold text-indigo-700">
                        <span class="inline-block px-3 py-0.5 bg-indigo-100 rounded-full text-xs border border-indigo-300">
                            {{ $d->transaksi_id }}
                        </span>
                    </td>
                    
                    {{-- Nama Produk --}}
                    <td class="px-5 py-3.5 font-semibold text-gray-900">
                         <div class="flex items-center">
                            <i class="ri-shopping-cart-2-fill text-indigo-400 mr-2"></i>
                            {{ $d->nama_produk }}
                        </div>
                    </td>

                    {{-- Barcode --}}
                    <td class="px-5 py-3.5 text-center">
                        @if($d->kode_barcode)
                            <span class="px-3 py-1 text-xs bg-gray-100 text-gray-600 font-mono rounded-lg border border-gray-300 shadow-sm">
                                {{ $d->kode_barcode }}
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs bg-gray-50 text-gray-400 rounded-lg font-medium">
                                N/A
                            </span>
                        @endif
                    </td>

                    {{-- Jumlah (Qty) --}}
                    <td class="px-5 py-3.5 text-right text-gray-800 font-bold">
                        {{ number_format($d->jumlah, 0, ',', '.') }}
                    </td>

                    {{-- Harga Satuan --}}
                    <td class="px-5 py-3.5 text-right font-medium text-gray-700 font-mono">
                        Rp {{ number_format($d->harga_satuan,0,',','.') }}
                    </td>

                    {{-- Subtotal --}}
                    <td class="px-5 py-3.5 text-right font-extrabold text-indigo-700 text-base font-mono">
                        Rp {{ number_format($d->subtotal,0,',','.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center text-gray-400 italic bg-white">
                        <div class="flex flex-col items-center justify-center">
                            <div class="bg-gray-100 p-5 rounded-full mb-3">
                                <i class="ri-file-text-line text-5xl text-gray-300"></i>
                            </div>
                            <p class="text-xl font-bold text-gray-600">Tidak Ada Data Detail Transaksi</p>
                            <p class="text-sm mt-1 text-gray-500">Belum ada item penjualan yang tercatat dalam detail transaksi.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if(isset($details) && method_exists($details, 'hasPages') && $details->hasPages())
    <div class="px-6 py-4 bg-white rounded-2xl border border-gray-100 flex justify-center md:justify-end">
        {{ $details->links('vendor.pagination.tailwind') }}
    </div>
    @endif
</div>
@endsection