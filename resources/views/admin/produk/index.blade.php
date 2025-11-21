@extends('layouts.app')

@section('content')
            <main class="flex-1 p-6">
            <div class="space-y-6">
                
                <!-- Kartu Sambutan -->
                <div class="bg-white p-6 rounded-xl shadow-md flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Daftar Produk</h2>
                    </div>
                    <div>
                    <a href="{{ route('produk.create') }}" class="bg-blue-500 hover:bg-blue-600 p-2 text-white rounded-lg transition">Tambah Produk</a>
                    <a href="" class="bg-green-500 hover:bg-green-600 p-2 text-white rounded-lg transition">Scan Barcode</a>
                    </div>
                </div>

            <div class="overflow-x-auto rounded-lg shadow-lg bg-white p-4">
        <table class="w-full border-collapse text-left">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="px-4 py-3 border-b">No</th>
                    <th class="px-4 py-3 border-b">Barcode</th>
                    <th class="px-4 py-3 border-b">Nama Produk</th>
                    <th class="px-4 py-3 border-b">Kategori</th>
                    <th class="px-4 py-3 border-b">Harga Beli</th>
                    <th class="px-4 py-3 border-b">Harga Jual</th>
                    <th class="px-4 py-3 border-b">Stok</th>
                    <th class="px-4 py-3 border-b">Satuan</th>
                    <th class="px-4 py-3 border-b">Deskripsi</th>
                    <th class="px-4 py-3 border-b">Status</th>
                    <th class="px-4 py-3 border-b text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="text-gray-700">
                @forelse($produks as $produk)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 border-b">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3 border-b">{{ $produk->kode_barcode}}</td>
                    <td class="px-4 py-3 border-b">{{ $produk->nama_produk}}</td>
                    <td class="px-4 py-3 border-b">{{ $produk->kategori->nama_kategori ?? '-'}}</td>
                    <td class="px-4 py-3 border-b">Rp {{ number_format($produk->harga_beli, 0, ',', '.')}}</td>
                    <td class="px-4 py-3 border-b">Rp {{ number_format($produk->harga_jual, 0, ',', '.')}}</td>
                    <td class="px-4 py-3 border-b">{{ $produk->stok}}</td>
                    <td class="px-4 py-3 border-b">{{ $produk->satuan}}</td>
                    <td class="px-4 py-3 border-b">{{ $produk->deskripsi}}</td>
                    <td class="px-4 py-3 border-b">{{ $produk->status}}</td>
                    <td class="px-4 py-3 border-b text-center">
                        <div class="space-x-2">
                            <form action="{{ route('produk.destroy', $produk->id) }}" method="post">
                                @method('DELETE')
                                @csrf
                            <a href="{{ route('produk.edit', $produk->id)}}" class="px-3 py-1 text-sm bg-blue-500 text-white rounded-md hover:bg-blue-600">Edit</a>
                            <button class="px-3 py-1 text-sm bg-red-500 text-white rounded-md hover:bg-red-600">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
         </div>

@endsection