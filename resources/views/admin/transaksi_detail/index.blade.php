@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="bg-white p-6 rounded-xl shadow-sm">
        <h2 class="text-xl font-bold mb-2">Transaksi Detail</h2>
        <p class="text-sm text-gray-500 mb-4">Daftar item per transaksi</p>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Transaksi ID</th>
                        <th class="px-4 py-3">Produk</th>
                        <th class="px-4 py-3">Barcode</th>
                        <th class="px-4 py-3">Jumlah</th>
                        <th class="px-4 py-3">Harga Satuan</th>
                        <th class="px-4 py-3">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($details as $d)
                        <tr class="border-b">
                            <td class="px-4 py-3">{{ $d->detail_id }}</td>
                            <td class="px-4 py-3">{{ $d->tanggal ?? $d->created_at }}</td>
                            <td class="px-4 py-3">{{ $d->transaksi_id }}</td>
                            <td class="px-4 py-3">{{ $d->nama_produk }}</td>
                            <td class="px-4 py-3">{{ $d->kode_barcode ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $d->jumlah }}</td>
                            <td class="px-4 py-3">{{ number_format($d->harga_satuan,0,',','.') }}</td>
                            <td class="px-4 py-3">{{ number_format($d->subtotal,0,',','.') }}</td>
                        </tr>
                    @empty
                        <tr><td class="px-4 py-6 text-center" colspan="8">Belum ada detail transaksi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $details->links() }}</div>
    </div>
</div>
@endsection
