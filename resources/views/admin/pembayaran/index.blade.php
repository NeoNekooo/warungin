@extends('layouts.app')

@section('title', 'Daftar Pembayaran')

@section('content')
<div class="space-y-8 relative min-h-screen">

    {{-- Notifikasi Toast (jika ada) --}}
    <div id="toast-container" class="fixed bottom-5 right-5 z-[100] flex flex-col gap-2"></div>

    {{-- Header Dashboard/Judul Halaman (Diubah agar konsisten) --}}
    <div class="bg-white p-6 rounded-2xl shadow-xl border border-gray-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-5">
        
        <div class="space-y-1">
            <h2 class="flex items-center text-3xl font-extrabold text-gray-900">
                <i class="ri-wallet-3-fill text-3xl text-indigo-500 mr-2"></i>
                Daftar Pembayaran
            </h2>
            <div class="flex flex-wrap items-center text-sm text-gray-500">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                    <i class="ri-bank-card-line mr-1.5 text-sm"></i> Semua Metode Pembayaran
                </span>
                <span class="mx-3 hidden sm:inline-block">•</span>
                <p class="text-sm mt-1 sm:mt-0 text-gray-600">Semua pembayaran *split* atau *gateway* yang tercatat.</p>
            </div>
        </div>

        {{-- Tempat untuk tombol Aksi (misalnya: Filter/Export) --}}
        <div class="w-full md:w-auto flex justify-end">
            <button class="group bg-white border border-indigo-300 text-indigo-600 px-6 py-3 rounded-xl transition-all shadow-sm hover:bg-indigo-50 flex items-center font-semibold text-base w-full md:w-auto">
                <i class="ri-filter-3-line mr-2 text-xl"></i> Filter Pembayaran
            </button>
        </div>
    </div>

    {{-- Konten Utama: Tabel Daftar Pembayaran --}}
    <div class="overflow-x-auto rounded-2xl shadow-xl bg-white ring-1 ring-gray-200">
        <table class="min-w-full border-collapse text-left text-sm">
            <thead>
                {{-- Header Tabel dengan warna Indigo --}}
                <tr class="bg-indigo-600 text-white">
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider rounded-tl-2xl w-10">ID Bayar</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider text-center">ID Transaksi</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider">Metode Pembayaran</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider text-right">Jumlah Dibayar</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider">Referensi / Bank</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider text-center">Tanggal Transaksi</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider text-center rounded-tr-2xl">Aksi</th>
                </tr>
            </thead>

            <tbody class="text-gray-700 divide-y divide-gray-100">
                @forelse($pembayarans ?? [] as $pay)
                <tr class="hover:bg-indigo-50 transition duration-150 ease-in-out">
                    {{-- ID Pembayaran --}}
                    <td class="px-5 py-3.5 text-sm text-gray-500 font-mono">#{{ $pay->pembayaran_id }}</td>
                    
                    {{-- Transaksi ID --}}
                    <td class="px-5 py-3.5 text-center font-mono font-medium text-gray-700">
                        {{ $pay->transaksi_id }}
                    </td>
                    
                    {{-- Metode --}}
                    <td class="px-5 py-3.5">
                        @php
                            $metode = strtolower($pay->metode);
                            $class = '';
                            $icon = '';
                            if ($metode === 'tunai') {
                                $class = 'bg-green-100 text-green-700 border-green-300';
                                $icon = 'ri-money-line';
                            } elseif ($metode === 'qris') {
                                $class = 'bg-blue-100 text-blue-700 border-blue-300';
                                $icon = 'ri-qr-code-line';
                            } else {
                                $class = 'bg-purple-100 text-purple-700 border-purple-300';
                                $icon = 'ri-bank-card-line';
                            }
                        @endphp
                        <span class="px-3 py-1 text-xs font-bold rounded-full border {{ $class }} whitespace-nowrap">
                            <i class="{{ $icon }} mr-1"></i> {{ ucfirst($pay->metode) }}
                        </span>
                    </td>

                    {{-- Jumlah --}}
                    <td class="px-5 py-3.5 text-right font-extrabold text-indigo-700 text-base font-mono">
                        Rp {{ number_format($pay->jumlah,0,',','.') }}
                    </td>

                    {{-- Referensi --}}
                    <td class="px-5 py-3.5 text-gray-600 font-mono text-xs">
                        <span class="inline-block max-w-[150px] truncate" title="{{ $pay->referensi ?? 'Tidak ada referensi' }}">
                            {{ $pay->referensi ?? '-' }}
                        </span>
                    </td>
                    
                    {{-- Tanggal --}}
                    <td class="px-5 py-3.5 text-gray-600 text-center text-xs font-medium whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($pay->created_at)->isoFormat('D MMM YYYY') }}<br>
                        <span class="text-gray-400">{{ \Carbon\Carbon::parse($pay->created_at)->isoFormat('HH:mm:ss') }}</span>
                    </td>

                    {{-- Aksi --}}
                    <td class="px-5 py-3.5 text-center">
                        <a href="{{ route('pembayaran.show', $pay->pembayaran_id) }}"
                           class="inline-flex items-center px-3 py-2 bg-indigo-100 text-indigo-700 font-bold text-xs rounded-xl hover:bg-indigo-200 transition duration-150 shadow-sm">
                            <i class="ri-eye-line mr-1"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-gray-400 italic bg-white">
                        <div class="flex flex-col items-center justify-center">
                            <div class="bg-gray-100 p-5 rounded-full mb-3">
                                <i class="ri-money-dollar-circle-line text-5xl text-gray-300"></i>
                            </div>
                            <p class="text-xl font-bold text-gray-600">Tidak Ada Data Pembayaran</p>
                            <p class="text-sm mt-1 text-gray-500">Belum ada transaksi pembayaran yang tercatat.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection