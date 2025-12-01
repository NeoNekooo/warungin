@extends('layouts.app')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <h2 class="text-3xl font-extrabold text-gray-800 mb-6 border-b pb-2">Ringkasan Laporan Penjualan</h2>

    {{-- 1. Kartu Ringkasan (Summary Cards) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        
        {{-- Total Transaksi --}}
        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-indigo-500">
            <div class="text-sm font-semibold text-gray-500 flex items-center gap-2">
                <i class="ri-file-list-3-line text-lg text-indigo-400"></i>
                Total Transaksi
            </div>
            <div class="text-4xl font-extrabold text-gray-900 mt-2 tracking-tight">
                {{ number_format($totalTransactions ?? 0, 0, ',', '.') }}
            </div>
            <p class="text-xs text-gray-400 mt-1">Transaksi yang difilter</p>
        </div>

        {{-- Total Pendapatan --}}
        <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-green-500">
            <div class="text-sm font-semibold text-gray-500 flex items-center gap-2">
                <i class="ri-money-dollar-circle-line text-lg text-green-400"></i>
                Total Pendapatan
            </div>
            <div class="text-4xl font-extrabold text-green-700 mt-2 tracking-tight">
                Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}
            </div>
            <p class="text-xs text-gray-400 mt-1">Pendapatan kotor</p>
        </div>

        {{-- Area Filter dan Export --}}
        <div class="p-6 bg-white rounded-xl shadow-lg border border-gray-100 flex flex-col justify-between">
            <h4 class="text-base font-bold text-gray-700 mb-4 flex items-center gap-2">
                <i class="ri-settings-4-line"></i> Opsi Laporan
            </h4>
            
            <form method="GET" action="{{ route('reports.index') }}" class="space-y-3">
                <div class="flex flex-col">
                    <label for="period" class="text-xs font-semibold text-gray-500 mb-1">Filter Periode:</label>
                    <select name="period" id="period" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua Bulan & Tahun</option>
                        @foreach($periods as $p)
                            <option value="{{ $p->key }}" {{ (isset($period) && $period == $p->key) ? 'selected' : '' }}>{{ $p->label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition shadow-md">
                    <i class="ri-search-line mr-1"></i> Terapkan Filter
                </button>
            </form>
            
            <hr class="my-4 border-gray-100">

            <a href="{{ route('reports.pdf', array_merge(request()->query(), ['period' => $period ?? ''])) }}" class="w-full px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition shadow-md flex items-center justify-center gap-2">
                <i class="ri-file-pdf-line"></i> Eksport ke PDF
            </a>
        </div>
    </div>

    {{-- 2. Daftar Laporan Detail --}}
    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
        <h3 class="text-xl font-extrabold text-gray-800 mb-6 border-b pb-3">Detail Data Per Periode</h3>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
                <thead class="text-xs text-gray-600 uppercase bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 font-extrabold">Bulan & Tahun</th>
                        <th class="px-4 py-3 font-extrabold text-center">Jumlah Transaksi</th>
                        <th class="px-4 py-3 font-extrabold text-right">Pendapatan Perbulan</th>
                        <th class="px-4 py-3 font-extrabold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($rows as $row)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-4 py-4 whitespace-nowrap font-semibold text-gray-800">{{ $row->period }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-gray-600 text-center">{{ number_format($row->count, 0, ',', '.') }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-green-700 font-extrabold text-right">Rp {{ number_format($row->total, 0, ',', '.') }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                {{-- Tombol Detail atau Aksi Lain --}}
                                <a href="#" class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold">Lihat Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="px-4 py-12 text-center text-lg text-gray-500 italic" colspan="4">Tidak ada data laporan yang ditemukan untuk periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection