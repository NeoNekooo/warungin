@extends('layouts.app')

@section('title', 'Manajemen Akun Pengguna')

@section('content')
<div class="space-y-8 relative min-h-screen">

    {{-- Notifikasi Toast --}}
    {{-- Tempat untuk menampung pesan sukses/gagal --}}
    <div id="toast-container" class="fixed bottom-5 right-5 z-[100] flex flex-col gap-2">
        {{-- Contoh notifikasi sukses: --}}
        {{-- @if(session('success'))
            <div class="bg-green-500 text-white p-4 rounded-lg shadow-xl">
                {{ session('success') }}
            </div>
        @endif --}}
    </div>

    {{-- Header Dashboard/Judul Halaman (Warna Biru) --}}
    <div class="bg-white p-6 rounded-2xl shadow-xl border border-gray-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-5">
        
        <div class="space-y-1">
            <h2 class="flex items-center text-3xl font-extrabold text-gray-900">
                <i class="ri-user-settings-fill text-3xl text-blue-500 mr-2"></i>
                Manajemen Akun
            </h2>
            <div class="flex flex-wrap items-center text-sm text-gray-500">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                    <i class="ri-team-line mr-1.5 text-sm"></i> Daftar Pengguna
                </span>
                <span class="mx-3 hidden sm:inline-block">•</span>
                <p class="text-sm mt-1 sm:mt-0 text-gray-600">Kelola daftar akun, hak akses, dan status pengguna sistem.</p>
            </div>
        </div>

        {{-- Tombol Tambah Akun (Hanya untuk Admin) --}}
        @role('admin')
        <div class="w-full md:w-auto flex justify-end">
            <button onclick="openModal('modalAdd')" class="group bg-blue-600 text-white px-6 py-3 rounded-xl transition-all shadow-md hover:bg-blue-700 hover:shadow-lg flex items-center font-semibold text-base w-full md:w-auto">
                <i class="ri-user-add-line mr-2 text-xl group-hover:rotate-6 transition-transform"></i> 
                Tambah Akun
            </button>
        </div>
        @endrole
    </div>

    {{-- Tabel Daftar Akun --}}
    <div class="overflow-x-auto rounded-2xl shadow-xl bg-white ring-1 ring-gray-200">
        <table class="min-w-full border-collapse text-left text-sm">
            <thead>
                {{-- Header Tabel dengan warna Biru --}}
                <tr class="bg-blue-600 text-white">
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider rounded-tl-2xl w-10">No</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider">Nama Lengkap</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider">Username</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider">Email</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider text-center">Role</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider">No. HP</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider text-center">Status</th>
                    <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider text-center rounded-tr-2xl">Aksi</th>
                </tr>
            </thead>

            <tbody class="text-gray-700 divide-y divide-gray-100">
                @forelse($users as $user)
                <tr class="hover:bg-blue-50 transition duration-150 ease-in-out">
                    <td class="px-5 py-3.5 text-sm text-gray-600">{{ $loop->iteration }}</td>
                    
                    {{-- Nama Lengkap --}}
                    <td class="px-5 py-3.5 font-medium text-sm text-gray-800">{{ $user->nama }}</td>

                    {{-- Username --}}
                    <td class="px-5 py-3.5 font-bold text-sm text-gray-800">
                        <i class="ri-user-3-fill text-blue-400 mr-1.5"></i>
                        {{ $user->username}}
                    </td>
                    
                    {{-- Email --}}
                    <td class="px-5 py-3.5 text-sm text-gray-600">{{ $user->email }}</td>
                    
                    {{-- Role --}}
                    <td class="px-5 py-3.5 text-center">
                        @php
                            $role = strtolower($user->role);
                            $class = '';
                            if ($role === 'admin') {
                                $class = 'bg-red-100 text-red-700 border-red-300';
                            } elseif ($role === 'kasir') {
                                $class = 'bg-green-100 text-green-700 border-green-300';
                            } else {
                                $class = 'bg-yellow-100 text-yellow-700 border-yellow-300';
                            }
                        @endphp
                        <span class="px-3 py-1 text-xs font-bold rounded-full border {{ $class }} whitespace-nowrap">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    
                    {{-- No. HP --}}
                    <td class="px-5 py-3.5 text-sm text-gray-600 font-mono">{{ $user->no_hp ?? '-'}}</td>
                    
                    {{-- Status --}}
                    <td class="px-5 py-3.5 text-center">
                         @if(strtolower($user->status) == 'aktif')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 ring-1 ring-green-300">Aktif</span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 ring-1 ring-gray-300">Non-aktif</span>
                        @endif
                    </td>
                    
                    {{-- Aksi --}}
                    <td class="px-5 py-3.5 text-center">
                        <div class="flex justify-center space-x-2">
                            {{-- Tombol Edit --}}
                            <button onclick="openModal('modalEdit')" class="p-2 text-blue-600 hover:bg-blue-100 rounded-full transition" title="Edit Akun">
                                <i class="ri-edit-2-line text-lg"></i>
                            </button>
                            
                            {{-- Tombol Hapus --}}
                            <button onclick="openModal('modalDelete')" class="p-2 text-red-600 hover:bg-red-100 rounded-full transition" title="Hapus Akun">
                                <i class="ri-delete-bin-6-line text-lg"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center text-gray-400 italic bg-white">
                        <div class="flex flex-col items-center justify-center">
                            <div class="bg-gray-100 p-5 rounded-full mb-3">
                                <i class="ri-user-unfollow-line text-5xl text-gray-300"></i>
                            </div>
                            <p class="text-xl font-bold text-gray-600">Data Akun Kosong</p>
                            <p class="text-sm mt-1 text-gray-500">Silakan tambahkan akun baru untuk mulai mengelola pengguna.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if(isset($users) && method_exists($users, 'hasPages') && $users->hasPages())
    <div class="px-6 py-4 bg-white rounded-2xl border border-gray-100 flex justify-center md:justify-end">
        {{ $users->links('vendor.pagination.tailwind') }}
    </div>
    @endif
</div>

{{-- MODAL TAMBAH AKUN --}}
<div id="modalAdd" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-xl shadow-2xl w-full max-w-lg transform transition-all duration-300 scale-95 opacity-0" id="modalAddContent">
        <h3 class="text-xl font-bold text-blue-600 mb-4 border-b pb-2 flex items-center">
            <i class="ri-user-add-line mr-2"></i> Tambah Akun Baru
        </h3>
        <form action="{{ route('manajemen_akun.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="nama_add" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" id="nama_add" name="nama" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            </div>
             <div>
                <label for="username_add" class="block text-sm font-medium text-gray-700">Username <span class="text-red-500">*</span></label>
                <input type="text" id="username_add" name="username" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            </div>
            <div>
                <label for="email_add" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                <input type="email" id="email_add" name="email" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            </div>
            <div>
                <label for="password_add" class="block text-sm font-medium text-gray-700">Password <span class="text-red-500">*</span></label>
                <input type="password" id="password_add" name="password" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            </div>
            <div>
                <label for="role_add" class="block text-sm font-medium text-gray-700">Role <span class="text-red-500">*</span></label>
                <select id="role_add" name="role" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    <option value="">Pilih Role</option>
                    <option value="admin">Admin</option>
                    <option value="kasir">Kasir</option>
                    {{-- Tambahkan role lain jika ada --}}
                </select>
            </div>
             <div>
                <label for="no_hp_add" class="block text-sm font-medium text-gray-700">No. HP</label>
                <input type="text" id="no_hp_add" name="no_hp" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label for="status_add" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                <select id="status_add" name="status" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeModal('modalAdd')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Simpan Akun</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT AKUN --}}
<div id="modalEdit" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-xl shadow-2xl w-full max-w-lg transform transition-all duration-300 scale-95 opacity-0" id="modalEditContent">
        <h3 class="text-xl font-bold text-blue-600 mb-4 border-b pb-2 flex items-center">
            <i class="ri-user-follow-line mr-2"></i> Edit Akun Pengguna
        </h3>
        <form id="editForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT') {{-- Gunakan PUT untuk update --}}
            
            <input type="hidden" id="user_id_edit" name="user_id">
            
            <div>
                <label for="nama_edit" class="block text-sm font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" id="nama_edit" name="nama" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            </div>
             <div>
                <label for="username_edit" class="block text-sm font-medium text-gray-700">Username <span class="text-red-500">*</span></label>
                <input type="text" id="username_edit" name="username" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            </div>
            <div>
                <label for="email_edit" class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                <input type="email" id="email_edit" name="email" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            </div>
            <div>
                <label for="password_edit" class="block text-sm font-medium text-gray-700">Password Baru (Kosongkan jika tidak diganti)</label>
                <input type="password" id="password_edit" name="password" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <p class="mt-1 text-xs text-gray-500 italic">Isi kolom ini hanya jika Anda ingin mengubah password pengguna.</p>
            </div>
            <div>
                <label for="role_edit" class="block text-sm font-medium text-gray-700">Role <span class="text-red-500">*</span></label>
                <select id="role_edit" name="role" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    <option value="admin">Admin</option>
                    <option value="kasir">Kasir</option>
                </select>
            </div>
             <div>
                <label for="no_hp_edit" class="block text-sm font-medium text-gray-700">No. HP</label>
                <input type="text" id="no_hp_edit" name="no_hp" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label for="status_edit" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                <select id="status_edit" name="status" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeModal('modalEdit')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL HAPUS AKUN --}}
<div id="modalDelete" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-xl shadow-2xl w-full max-w-sm transform transition-all duration-300 scale-95 opacity-0" id="modalDeleteContent">
        <h3 class="text-xl font-bold text-red-600 mb-4 border-b pb-2 flex items-center">
            <i class="ri-alert-line mr-2"></i> Konfirmasi Hapus Akun
        </h3>
        <p class="text-gray-700 mb-4">
            Anda yakin ingin menghapus akun **<span id="deleteUsername" class="font-bold"></span>**? 
            <br>Tindakan ini tidak dapat dibatalkan.
        </p>
        <form id="deleteForm" method="POST" class="flex justify-end space-x-3">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeModal('modalDelete')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">Hapus Akun</button>
        </form>
    </div>
</div>

{{-- Script JS untuk Modal (Sama dengan script kategori) --}}
<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        const modalContent = document.getElementById(id + 'Content');
        
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 50);
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        const modalContent = document.getElementById(id + 'Content');
        
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Contoh fungsi untuk mengisi data modal edit sebelum dibuka (Anda perlu menyesuaikan ini)
    document.querySelectorAll('tr').forEach(row => {
        row.querySelector('.flex.justify-center button:nth-child(1)')?.addEventListener('click', function() {
            // Asumsi: Ambil data dari atribut data pada baris tabel (misalnya, data-id, data-nama, dll.)
            // Ini hanyalah placeholder. Anda harus menyesuaikan cara pengambilan data di proyek Anda.
            const userId = 1; // Ganti dengan logika pengambilan ID
            const userName = 'Contoh Admin'; // Ganti dengan logika pengambilan nama
            const userEmail = 'admin@example.com'; // Ganti dengan logika pengambilan email
            const userRole = 'admin'; // Ganti dengan logika pengambilan role
            const userStatus = 'aktif'; // Ganti dengan logika pengambilan status
            const userNoHp = '08123456789'; // Ganti dengan logika pengambilan no_hp
            const userUsername = 'contoh_admin'; // Ganti dengan logika pengambilan username


            document.getElementById('user_id_edit').value = userId;
            document.getElementById('nama_edit').value = userName;
            document.getElementById('username_edit').value = userUsername;
            document.getElementById('email_edit').value = userEmail;
            document.getElementById('role_edit').value = userRole;
            document.getElementById('status_edit').value = userStatus;
            document.getElementById('no_hp_edit').value = userNoHp;

            // Update action URL for the form
            document.getElementById('editForm').action = '/manajemen_akun/' + userId; 
            
            openModal('modalEdit');
        });

        // Contoh fungsi untuk mengisi data modal delete sebelum dibuka
        row.querySelector('.flex.justify-center button:nth-child(2)')?.addEventListener('click', function() {
            const userId = 1; // Ganti dengan logika pengambilan ID
            const userName = 'Contoh Admin'; // Ganti dengan logika pengambilan nama/username

            document.getElementById('deleteUsername').textContent = userName;
            // Update action URL for the form
            document.getElementById('deleteForm').action = '/manajemen_akun/' + userId; 

            openModal('modalDelete');
        });
    });

</script>
@endsection