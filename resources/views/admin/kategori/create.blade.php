@extends('layouts.app')

@section('content')
    <main class="flex-1 p-6">
            <div class="space-y-6">
                
                <!-- Kartu Sambutan -->
                <div class="bg-white p-6 rounded-xl shadow-md flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Buat Kategori</h2>
                    </div>
                </div>

                <div class="bg-white shadow-lg rounded-xl p-6">

    <form action="{{ route('kategori.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf

        <!-- Nama Kategori -->
        <div class="flex flex-col">
            <label for="nama_kategori" class="text-gray-700 font-medium mb-1">Nama Kategori</label>
            <input 
                type="text" 
                name="nama_kategori" 
                id="nama_kategori"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                placeholder="Masukkan nama kategori"
            >
            @error('nama_kategori')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Deskripsi -->
        <div class="flex flex-col">
            <label for="deskripsi" class="text-gray-700 font-medium mb-1">Deskripsi</label>
            <input 
                type="text" 
                name="deskripsi" 
                id="deskripsi"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                placeholder="Masukkan deskripsi"
            >
            @error('deskripsi')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tombol Submit -->
        <div class="md:col-span-2 flex justify-end mt-2">
            <a href="{{ route('kategori.index') }}" class="bg-red-600 hover:bg-red-700 text-white py-2 px-5 rounded-lg font-semibold transition duration-200 mr-2">Batal</a>
            <button 
                type="submit" 
                class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-5 rounded-lg font-semibold transition duration-200">
                Simpan
            </button>
        </div>
    </form>
</div>

@endsection