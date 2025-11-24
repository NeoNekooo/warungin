@extends('layouts.app')

@section('content')
            <main class="flex-1 p-6">
            <div class="space-y-6">
                
                <!-- Kartu Sambutan -->
                <div class="bg-white p-6 rounded-xl shadow-md flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">Manajemen Akun</h2>
                    </div>
                    @role('admin')
                    <a href="{{ route('manajemen_akun.create') }}" class="bg-blue-500 hover:bg-blue-600 p-2 text-white rounded-lg transition">Tambah Akun</a>
                    @endrole
                </div>

            <div class="overflow-x-auto rounded-lg shadow-lg bg-white p-4">
        <table class="w-full border-collapse text-left">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="px-4 py-3 border-b">No</th>
                    <th class="px-4 py-3 border-b">Username</th>
                    <th class="px-4 py-3 border-b">Nama</th>
                    <th class="px-4 py-3 border-b">Email</th>
                    <th class="px-4 py-3 border-b">Password</th>
                    <th class="px-4 py-3 border-b">Role</th>
                    <th class="px-4 py-3 border-b">No.hp</th>
                    <th class="px-4 py-3 border-b">Status</th>
                    <th class="px-4 py-3 border-b text-center">Aksi</th>
                </tr>
            </thead>

            <tbody class="text-gray-700">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 border-b">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3 border-b">{{ $user->username}}</td>
                    <td class="px-4 py-3 border-b">{{ $user->name }}</td>
                    <td class="px-4 py-3 border-b">{{ $user->email }}</td>
                    <td class="px-4 py-3 border-b">*****</td>
                    <td class="px-4 py-3 border-b">{{ $user->role}}</td>
                    <td class="px-4 py-3 border-b">{{ $user->no_hp}}</td>
                    <td class="px-4 py-3 border-b">{{ $user->status}}</td>
                    <td class="px-4 py-3 border-b text-center">
                        <div class="space-x-2">
                            <form action="" method="post">
                                @method('DELETE')
                                @csrf
                            <a href="" class="px-3 py-1 text-sm bg-blue-500 text-white rounded-md hover:bg-blue-600">Edit</a>
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

            <div class="mt-4">
                {{ $users->links() }}
            </div>

@endsection