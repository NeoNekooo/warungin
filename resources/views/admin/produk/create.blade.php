@extends('layouts.app')

@section('content')
    <main class="flex-1 p-6">
        <div class="space-y-6">

            <div class="bg-white p-6 rounded-xl shadow-md flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-800">Tambah Produk</h2>
            </div>

            <div class="bg-white shadow-lg rounded-xl p-6">

                <form action="{{ route('produk.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @csrf

                    <div class="flex flex-col">
                        <label for="kode_barcode" class="text-gray-700 font-medium mb-1">Barcode</label>
                        <input 
                            type="text" 
                            name="kode_barcode" 
                            id="kode_barcode"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="Masukkan Kode Barcode"
                            value="{{ old('kode_barcode') }}"
                        >
                        @error('kode_barcode')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="nama_produk" class="text-gray-700 font-medium mb-1">Nama Produk</label>
                        <input 
                            type="text" 
                            name="nama_produk" 
                            id="nama_produk"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="Masukkan Nama Produk"
                            value="{{ old('nama_produk') }}"
                        >
                        @error('nama_produk')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="kategori_id" class="text-gray-700 font-medium mb-1">Kategori</label>
                        <select
                            name="kategori_id" 
                            id="kategori_id"
                            class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        >
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoris as $kategori)
                                <option 
                                    value="{{ $kategori->id }}"
                                    {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}
                                >
                                    {{ $kategori->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('kategori_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="harga_beli" class="text-gray-700 font-medium mb-1">Harga Beli</label>
                        <input 
                            type="number" 
                            name="harga_beli" 
                            id="harga_beli"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="Contoh : 12000"
                            value="{{ old('harga_beli') }}"
                        >
                        @error('harga_beli')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="harga_jual" class="text-gray-700 font-medium mb-1">Harga Jual</label>
                        <input 
                            type="number" 
                            name="harga_jual" 
                            id="harga_jual"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="Contoh : 15000"
                            value="{{ old('harga_jual') }}"
                        >
                        @error('harga_jual')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="stok" class="text-gray-700 font-medium mb-1">Stok</label>
                        <input 
                            type="number" 
                            name="stok" 
                            id="stok"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="0"
                            value="{{ old('stok') }}"
                        >
                        @error('stok')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="satuan" class="text-gray-700 font-medium mb-1">Satuan</label>
                        <input 
                            type="text" 
                            name="satuan" 
                            id="satuan"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="Sachet/ml/Pcs/Strip/Bungkus dll"
                            value="{{ old('satuan') }}"
                        >
                        @error('satuan')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col">
                        <label for="deskripsi" class="text-gray-700 font-medium mb-1">Deskripsi</label>
                        <input 
                            type="text" 
                            name="deskripsi" 
                            id="deskripsi"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                            placeholder="(Opsional)"
                            value="{{ old('deskripsi') }}"
                        >
                        @error('deskripsi')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2 flex justify-end mt-2">
                        <a href="{{ route('produk.index') }}" class="bg-red-600 hover:bg-red-700 text-white py-2 px-5 rounded-lg font-semibold transition duration-200 mr-2">
                            Batal
                        </a>
                        <button 
                            type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-5 rounded-lg font-semibold transition duration-200">
                            Simpan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </main>
@endsection
