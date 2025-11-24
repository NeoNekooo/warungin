@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="bg-white p-6 rounded-xl shadow-sm">
        <h2 class="text-xl font-bold mb-2">Daftar Pembayaran</h2>
        <p class="text-sm text-gray-500 mb-4">Semua pembayaran yang tercatat (split payments / gateway)</p>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Transaksi ID</th>
                        <th class="px-4 py-3">Metode</th>
                        <th class="px-4 py-3">Jumlah</th>
                        <th class="px-4 py-3">Referensi</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($pembayarans ?? [] as $pay)
                        <tr class="border-b">
                            <td class="px-4 py-3">{{ $pay->pembayaran_id }}</td>
                            <td class="px-4 py-3">{{ $pay->transaksi_id }}</td>
                            <td class="px-4 py-3">{{ $pay->metode }}</td>
                            <td class="px-4 py-3">{{ number_format($pay->jumlah,0,',','.') }}</td>
                            <td class="px-4 py-3">{{ $pay->referensi ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $pay->created_at }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('pembayaran.show', $pay->pembayaran_id) }}" class="text-indigo-600 hover:underline">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="px-4 py-6 text-center" colspan="7">Belum ada pembayaran</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
            <main class="flex-1 p-6">
            <div class="space-y-6">
                
                <!-- Kartu Sambutan -->
                <div class="bg-white p-6 rounded-xl shadow-md flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Pembayaran</h2>
                    </div>
                    {{-- Logo kecil di pojok kartu --}}
                    <img src="{{ asset('image/warungin_logo.png') }}" alt="Logo" class="w-12 h-12 opacity-50">
                </div>
@endsection