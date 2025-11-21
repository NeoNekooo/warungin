@extends('layouts.app')

@section('content')
    <main class="flex-1 p-6">
            <div class="space-y-6">
                
                <!-- Kartu Sambutan -->
                <div class="bg-white p-6 rounded-xl shadow-md flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Tambah Pelanggan</h2>
                    </div>
                </div>

                <div class="bg-white shadow-lg rounded-xl p-6">

    <form action="{{ route('pelanggan.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf

        <!-- Nama Pelanggan -->
        <div class="flex flex-col">
            <label for="nama_kategori" class="text-gray-700 font-medium mb-1">Nama Pelanggan</label>
            <input 
                type="text" 
                name="nama_pelanggan" 
                id="nama_pelanggan"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                placeholder="Masukkan nama Pelanggan"
            >
            @error('nama_pelanggan')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- No Hp -->
        <div class="flex flex-col">
            <label for="deskripsi" class="text-gray-700 font-medium mb-1">No.hp</label>
            <input 
                type="tel" 
                name="no_hp" 
                id="no_hp"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                placeholder="Masukkan No.hp"
            >
            @error('no_hp')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div class="flex flex-col">
            <label for="email" class="text-gray-700 font-medium mb-1">Email</label>
            <input 
                type="email" 
                name="email" 
                id="email"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                placeholder="Masukkan Email"
            >
            @error('no_hp')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Alamat -->
        <div class="flex flex-col">
            <label for="email" class="text-gray-700 font-medium mb-1">Alamat</label>
            <textarea 
            style="resize: none;"
            name="alamat" 
            id="alamat" 
            placeholder="(Opsional)"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
            ></textarea>
            @error('no_hp')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>


        <!-- Tombol Submit -->
        <div class="md:col-span-2 flex justify-end mt-2">
            <a href="{{ route('pelanggan.index') }}" class="bg-red-600 hover:bg-red-700 text-white py-2 px-5 rounded-lg font-semibold transition duration-200 mr-2">Batal</a>
            <button 
                type="submit" 
                class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-5 rounded-lg font-semibold transition duration-200">
                Simpan
            </button>
        </div>
    </form>
</div>

@endsection