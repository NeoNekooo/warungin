@extends('layouts.app')

@section('content')
    <main class="flex-1 p-6">
            <div class="space-y-6">
                
                <!-- Kartu Sambutan -->
                <div class="bg-white p-6 rounded-xl shadow-md flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Tambah Akun</h2>
                    </div>
                </div>

                <div class="bg-white shadow-lg rounded-xl p-6">

    <form action="{{ route('manajemen_akun.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf

        <!-- Username -->
        <div class="flex flex-col">
            <label for="username" class="text-gray-700 font-medium mb-1">Username</label>
            <input 
                type="text" 
                name="username" 
                id="username"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                placeholder="Masukkan nama username"
            >
            @error('username')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Nama -->
        <div class="flex flex-col">
            <label for="nama" class="text-gray-700 font-medium mb-1">Nama</label>
            <input 
                type="text" 
                name="nama" 
                id="nama"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                placeholder="Masukkan Nama"
            >
            @error('nama')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email  -->
        <div class="flex flex-col">
            <label for="email" class="text-gray-700 font-medium mb-1">Email</label>
            <input 
                type="text" 
                name="email" 
                id="email"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                placeholder="Masukkan email"
            >
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col">
            <label for="role" class="text-gray-700 font-medium mb-1">Role</label>
            <select name="role" id="role"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
            >
                <option value="">-- Pilih Role --</option>
                <option value="admin">Admin</option>
                <option value="owner">Owner</option>
                <option value="kasir">Kasir</option>
            </select>
            @error('role')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email  -->
        <div class="flex flex-col">
            <label for="no_hp" class="text-gray-700 font-medium mb-1">No.Hp</label>
            <input 
                type="text" 
                name="no_hp" 
                id="no_hp"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                placeholder="Masukkan no.hp"
            >
            @error('no_hp')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email  -->
        <div class="flex flex-col">
            <label for="password" class="text-gray-700 font-medium mb-1">Password</label>
            <input 
                type="password" 
                name="password" 
                id="password"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                placeholder="Masukkan Password"
            >
            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tombol Submit -->
        <div class="md:col-span-2 flex justify-end mt-2">
            <a href="{{ route('manajemen_akun.index') }}" class="bg-red-600 hover:bg-red-700 text-white py-2 px-5 rounded-lg font-semibold transition duration-200 mr-2">Batal</a>
            <button 
                type="submit" 
                class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-5 rounded-lg font-semibold transition duration-200">
                Simpan
            </button>
        </div>
    </form>
</div>

@endsection