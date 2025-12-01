@extends('layouts.app')

@section('title', 'Daftar Pelanggan')

@section('content')
<div x-data="{ 
    openAdd: false, 
    openEdit: false, 
    openDelete: false,
    pelanggan: {}, // Digunakan untuk modal Edit
    deleteTarget: {}, // Digunakan untuk modal Delete
    
    setDeleteTarget(data) {
        this.deleteTarget = { id: data.id, nama_pelanggan: data.nama_pelanggan }; 
        this.openDelete = true;
        // Panggil fungsi global untuk menampilkan modal delete
        confirmDeleteGlobal(this.getDeleteUrl(), this.deleteTarget.nama_pelanggan); 
    },
    
    getDeleteUrl() {
        return this.deleteTarget.id ? `/pelanggan/${this.deleteTarget.id}` : '#';
    },

    setEditPelanggan(data) {
        this.pelanggan = data;
        this.openEdit = true;
        // Panggil fungsi global untuk menampilkan modal edit
        openEditModalGlobal(data);
    }
}" x-cloak class="space-y-8 relative"> 

    {{-- Notifikasi Toast --}}
    <div id="toast-container" class="fixed bottom-5 right-5 z-[100] flex flex-col gap-2"></div>

    {{-- Header Dashboard/Judul Halaman (DISAMAKAN DENGAN KATEGORI) --}}
    <div class="bg-white p-6 rounded-2xl shadow-xl border border-gray-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-5">
        
        <div class="space-y-1">
            <h2 class="flex items-center text-3xl font-extrabold text-gray-900">
                <i class="ri-team-line text-3xl text-indigo-500 mr-2"></i>
                Daftar Pelanggan
            </h2>
            <div class="flex flex-wrap items-center text-sm text-gray-500">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                    <i class="ri-user-star-line mr-1.5 text-sm"></i> Manajemen Member
                </span>
                <span class="mx-3 hidden sm:inline-block">•</span>
                <p class="text-sm mt-1 sm:mt-0 text-gray-600">Kelola data dan level member pelanggan.</p>
            </div>
        </div>

        <div class="w-full md:w-auto flex justify-end">
            <button @click="openModal('modalAddPelanggan')" class="group bg-indigo-600 text-white px-6 py-3 rounded-xl transition-all shadow-md hover:bg-indigo-700 hover:shadow-lg flex items-center font-semibold text-base w-full md:w-auto">
                <i class="ri-add-line mr-2 text-xl group-hover:rotate-90 transition-transform"></i> 
                Tambah Pelanggan Baru
            </button>
        </div>
    </div>
    
    <div class="flex flex-col gap-6">

        {{-- Konten Utama: Daftar Pelanggan --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            
            {{-- Bagian Desktop (Tabel) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-indigo-600 text-white/90 text-sm">
                            <th class="px-6 py-4 font-bold w-16">No</th>
                            <th class="px-6 py-4 font-bold">Nama Pelanggan</th>
                            <th class="px-6 py-4 font-bold">No HP / Email</th>
                            <th class="px-6 py-4 font-bold hidden lg:table-cell">Alamat</th>
                            <th class="px-6 py-4 font-bold text-center w-24">Level</th>
                            <th class="px-6 py-4 font-bold text-center w-24">Poin</th>
                            <th class="px-6 py-4 font-bold text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                        @forelse ($pelanggans as $pelanggan)
                        <tr class="hover:bg-blue-50/50 transition duration-200">
                            <td class="px-6 py-4 text-gray-500 font-medium">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3 flex-shrink-0">
                                        <i class="ri-user-3-fill text-lg"></i>
                                    </div>
                                    <span class="text-base font-semibold text-gray-800">{{ $pelanggan->nama_pelanggan }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                <p class="font-medium">{{ $pelanggan->no_hp }}</p>
                                <p class="text-xs text-gray-500 hidden xl:block">{{ $pelanggan->email }}</p>
                            </td>
                            <td class="px-6 py-4 text-gray-600 hidden lg:table-cell">
                                {{ $pelanggan->alamat ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $level_color = [
                                        'Gold' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                        'Silver' => 'bg-gray-200 text-gray-800 border-gray-300',
                                        'Regular' => 'bg-blue-100 text-blue-800 border-blue-300',
                                    ];
                                @endphp
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full border {{ $level_color[$pelanggan->member_level] ?? 'bg-indigo-100 text-indigo-800 border-indigo-300' }}">
                                    {{ $pelanggan->member_level }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-base text-green-700">
                                {{ number_format($pelanggan->poin, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <button 
                                        @click="setEditPelanggan({{ json_encode($pelanggan) }})"
                                        class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-full transition" 
                                        title="Edit">
                                        <i class="ri-pencil-line text-lg"></i>
                                    </button>
                                    
                                    <button 
                                        @click="setDeleteTarget({{ json_encode(['id' => $pelanggan->id, 'nama_pelanggan' => $pelanggan->nama_pelanggan]) }})" 
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-full transition" 
                                        title="Hapus">
                                        <i class="ri-delete-bin-line text-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-100 p-5 rounded-full mb-4">
                                        <i class="ri-inbox-line text-5xl text-gray-300"></i>
                                    </div>
                                    <p class="font-semibold text-gray-600 text-lg">Tidak ada data pelanggan yang tersedia.</p>
                                    <p class="text-sm mt-1 text-gray-500">Silakan tambah pelanggan baru untuk ditampilkan di sini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Bagian Mobile (Card List) --}}
            <div class="md:hidden p-4 space-y-4">
                <h3 class="text-lg font-bold text-gray-700 px-2 pt-2">Daftar Pelanggan</h3>
                @forelse($pelanggans as $pelanggan)
                <div class="bg-white border border-gray-200 p-4 rounded-xl shadow-sm space-y-3">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="ri-user-3-fill text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">No. {{ $loop->iteration }}</p>
                                <p class="text-base font-semibold text-gray-800">{{ $pelanggan->nama_pelanggan }}</p>
                            </div>
                        </div>
                        <div class="flex gap-1 flex-shrink-0">
                            <button 
                                @click="setEditPelanggan({{ json_encode($pelanggan) }})"
                                class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-full transition" 
                                title="Edit">
                                <i class="ri-pencil-line text-lg"></i>
                            </button>
                            <button 
                                @click="setDeleteTarget({{ json_encode(['id' => $pelanggan->id, 'nama_pelanggan' => $pelanggan->nama_pelanggan]) }})" 
                                class="p-2 text-red-600 hover:bg-red-50 rounded-full transition" 
                                title="Hapus">
                                <i class="ri-delete-bin-line text-lg"></i>
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 border-t border-dashed pt-2">
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">Level Member:</p>
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full border {{ $level_color[$pelanggan->member_level] ?? 'bg-indigo-100 text-indigo-800 border-indigo-300' }}">
                                {{ $pelanggan->member_level }}
                            </span>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-medium text-gray-500 mb-1">Poin:</p>
                            <p class="text-lg font-extrabold text-green-700 leading-none">{{ number_format($pelanggan->poin, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-10 text-center text-gray-400">
                    <div class="flex flex-col items-center justify-center">
                        <div class="bg-gray-100 p-5 rounded-full mb-4">
                            <i class="ri-inbox-line text-5xl text-gray-300"></i>
                        </div>
                        <p class="font-semibold text-gray-600 text-lg">Tidak ada data pelanggan yang tersedia.</p>
                        <p class="text-sm mt-1 text-gray-500">Silakan tambah pelanggan baru untuk ditampilkan di sini.</p>
                    </div>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            {{-- Asumsi Anda menggunakan pagination, tambahkan ini jika diperlukan --}}
            @if(isset($pelanggans) && method_exists($pelanggans, 'hasPages') && $pelanggans->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-center md:justify-end">
                {{ $pelanggans->links('vendor.pagination.tailwind') }} 
            </div>
            @endif

        </div>

    </div>

    {{--------------------------------------------------}}
    {{-- MODAL TAMBAH PELANGGAN (STORE) --}}
    {{-- Menggunakan ID baru: modalAddPelanggan --}}
    {{--------------------------------------------------}}
    <div id="modalAddPelanggan" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="backdropAddPelanggan" onclick="closeModal('modalAddPelanggan')"></div>
        
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div id="panelAddPelanggan" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg opacity-0 scale-95 border border-gray-100">
                    
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <i class="ri-user-add-line mr-2 text-xl text-indigo-200"></i> Tambah Pelanggan Baru
                        </h3>
                        <button onclick="closeModal('modalAddPelanggan')" class="text-indigo-200 hover:text-white hover:bg-indigo-600/50 rounded-full p-1 transition focus:outline-none">
                            <i class="ri-close-line text-2xl"></i>
                        </button>
                    </div>

                    <form action="{{ route('pelanggan.store') }}" method="POST">
                        @csrf
                        <div class="bg-white px-6 py-6 space-y-5 max-h-[500px] overflow-y-auto">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Pelanggan</label>
                                <input type="text" name="nama_pelanggan" id="add_nama_pelanggan" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">No HP</label>
                                <input type="text" name="no_hp" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Alamat</label>
                                <textarea name="alamat" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" rows="2"></textarea>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                                <input type="email" name="email" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Member Level</label>
                                <select name="member_level" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                                    <option value="Regular">Regular</option>
                                    <option value="Silver">Silver</option>
                                    <option value="Gold">Gold</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                            <button type="submit" class="w-full sm:w-auto bg-indigo-600 text-white px-5 py-2.5 rounded-xl hover:bg-indigo-700 font-medium shadow-sm flex items-center justify-center">
                                <i class="ri-save-line mr-2"></i> Simpan
                            </button>
                            <button type="button" onclick="closeModal('modalAddPelanggan')" class="w-full sm:w-auto bg-white text-gray-700 px-5 py-2.5 rounded-xl border border-gray-300 hover:bg-gray-50 font-medium">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{--------------------------------------------------}}
    {{-- MODAL EDIT PELANGGAN (UPDATE) --}}
    {{-- Menggunakan ID baru: modalEditPelanggan --}}
    {{--------------------------------------------------}}
    <div id="modalEditPelanggan" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="backdropEditPelanggan" onclick="closeModal('modalEditPelanggan')"></div>
        
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div id="panelEditPelanggan" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg opacity-0 scale-95 border border-gray-100">
                    
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <i class="ri-edit-2-fill mr-2 text-xl text-indigo-200"></i> Edit Pelanggan: <span x-text="pelanggan.nama_pelanggan" class="ml-1 text-indigo-100"></span>
                        </h3>
                        <button onclick="closeModal('modalEditPelanggan')" class="text-indigo-200 hover:text-white hover:bg-indigo-600/50 rounded-full p-1 transition focus:outline-none">
                            <i class="ri-close-line text-2xl"></i>
                        </button>
                    </div>

                    <form id="formEditPelanggan" :action="`/pelanggan/${pelanggan.id}`" method="POST">
                        @csrf
                        @method('PUT') 
                        
                        <div class="bg-white px-6 py-6 space-y-5 max-h-[500px] overflow-y-auto">
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Pelanggan</label>
                                <input type="text" name="nama_pelanggan" x-model="pelanggan.nama_pelanggan" id="edit_nama_pelanggan" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">No HP</label>
                                <input type="text" name="no_hp" x-model="pelanggan.no_hp" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Alamat</label>
                                <textarea name="alamat" x-model="pelanggan.alamat" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" rows="2"></textarea>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                                <input type="email" name="email" x-model="pelanggan.email" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Member Level</label>
                                <select name="member_level" x-model="pelanggan.member_level" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                                    <option value="Regular">Regular</option>
                                    <option value="Silver">Silver</option>
                                    <option value="Gold">Gold</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Poin</label>
                                <input type="number" name="poin" x-model="pelanggan.poin" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            </div>

                        </div>
                        
                        <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                            <button type="submit" class="w-full sm:w-auto bg-indigo-600 text-white px-5 py-2.5 rounded-xl hover:bg-indigo-700 font-medium shadow-sm flex items-center justify-center">
                                <i class="ri-save-line mr-2"></i> Update
                            </button>
                            <button type="button" onclick="closeModal('modalEditPelanggan')" class="w-full sm:w-auto bg-white text-gray-700 px-5 py-2.5 rounded-xl border border-gray-300 hover:bg-gray-50 font-medium">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{--------------------------------------------------}}
    {{-- MODAL HAPUS PELANGGAN (DELETE) --}}
    {{-- Menggunakan ID baru: modalDeletePelanggan --}}
    {{--------------------------------------------------}}
    <div id="modalDeletePelanggan" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="backdropDeletePelanggan" onclick="closeModal('modalDeletePelanggan')"></div>
        
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div id="panelDeletePelanggan" class="relative transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md opacity-0 scale-95 p-6 border border-gray-100">
                    
                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 mb-4 animate-pulse">
                        <i class="ri-alarm-warning-fill text-3xl text-red-600"></i>
                    </div>

                    <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Pelanggan?</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        Anda akan menghapus pelanggan <span id="deleteTargetNamePelanggan" class="font-bold text-gray-800 bg-red-50 px-2 py-0.5 rounded-lg border border-red-200"></span>. 
                        <br>Tindakan ini tidak dapat dibatalkan.
                    </p>

                    <form id="formDeletePelanggan" action="" method="POST" class="flex justify-center gap-3">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="closeModal('modalDeletePelanggan')" class="w-full bg-white border border-gray-300 text-gray-700 px-5 py-2.5 rounded-xl hover:bg-gray-50 font-medium transition">Batal</button>
                        <button type="submit" class="w-full bg-red-600 text-white px-5 py-2.5 rounded-xl hover:bg-red-700 font-medium shadow-lg shadow-red-200 transition">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


</div> 

{{-- Skrip JavaScript (Disamakan dengan Kategori, Disesuaikan untuk Pelanggan) --}}
<script>
    // --- ANIMASI MODAL UMUM (DIPINDAHKAN KE LUAR BLADE JIKA MUNGKIN, TAPI DITARUH DI SINI UNTUK KELENGKAPAN) ---
    function openModal(id) {
        const modal = document.getElementById(id);
        const backdrop = modal.querySelector('div[id^="backdrop"]');
        const panel = modal.querySelector('div[id^="panel"]');
        
        modal.classList.remove('hidden');
        
        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        });

        if(id === 'modalAddPelanggan') setTimeout(() => document.getElementById('add_nama_pelanggan').focus(), 200);
        if(id === 'modalEditPelanggan') setTimeout(() => document.getElementById('edit_nama_pelanggan').focus(), 200);
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        const backdrop = modal.querySelector('div[id^="backdrop"]');
        const panel = modal.querySelector('div[id^="panel"]');

        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // --- LOGIC EDIT PELANGGAN GLOBAL (DIPANGGIL DARI ALPINE) ---
    function openEditModalGlobal(pelanggan) {
        // PERBAIKAN: Menggunakan ID modal yang benar
        const form = document.getElementById('formEditPelanggan');
        
        // Mengisi nilai input modal EDIT
        document.getElementById('edit_nama_pelanggan').value = pelanggan.nama_pelanggan;
        // ... (Alpine handles x-model for others) ...

        // Mengatur action form (Ini penting karena x-data sudah mengurus :action)
        // form.action = `/pelanggan/${pelanggan.id}`; 
        
        openModal('modalEditPelanggan'); // Memanggil modal edit pelanggan
    }

    // --- LOGIC HAPUS PELANGGAN GLOBAL (DIPANGGIL DARI ALPINE) ---
    function confirmDeleteGlobal(url, name) {
        // PERBAIKAN: Menggunakan ID modal dan elemen yang benar
        document.getElementById('formDeletePelanggan').action = url;
        document.getElementById('deleteTargetNamePelanggan').textContent = name;
        openModal('modalDeletePelanggan'); // Memanggil modal delete pelanggan
    }

    // --- TOAST NOTIFICATION (Disamakan dengan Kategori) ---
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        
        let bgClass = type === 'success' ? 'bg-green-600' : 'bg-red-600';
        let icon = type === 'success' ? 'ri-check-double-line' : 'ri-error-warning-line';
        
        toast.className = `${bgClass} text-white px-6 py-4 rounded-xl shadow-lg flex items-center gap-3 transform transition-all duration-300 translate-x-10 opacity-0`;
        toast.innerHTML = `<i class="${icon} text-xl animate-pulse"></i><span class="font-medium">${message}</span>`;
        
        container.appendChild(toast);
        requestAnimationFrame(() => toast.classList.remove('translate-x-10', 'opacity-0'));
        
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-4');
            setTimeout(() => toast.remove(), 300);
        }, 4000); 
    }

    @if(session('success')) showToast("{{ session('success') }}"); @endif
</script>
@endsection