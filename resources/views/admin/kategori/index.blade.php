@extends('layouts.app')

@section('title', 'Daftar Kategori')

@section('content')
<div class="space-y-6 relative">

    <div id="toast-container" class="fixed bottom-5 right-5 z-[100] flex flex-col gap-2"></div>
    
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4 relative overflow-hidden">
        
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-10 -mt-10 opacity-50 pointer-events-none"></div>
        
        <div class="z-10">
            <h2 class="text-2xl font-bold text-gray-800">Daftar Kategori</h2>
            <div class="flex items-center text-sm text-gray-500 mt-1">
                <span class="flex items-center text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">
                    <i class="ri-folder-info-line mr-1"></i>
                    Manajemen Data
                </span>
                <span class="mx-2">•</span>
                <span>Kelompokkan produk agar lebih rapi</span>
            </div>
        </div>

        <div class="flex gap-3 z-10">
            <button onclick="openModal('modalAdd')" class="group bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl transition-all shadow-md hover:shadow-lg flex items-center font-medium">
                <i class="ri-add-line mr-2 text-lg group-hover:rotate-90 transition-transform"></i> 
                Tambah Kategori
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-4 font-semibold w-16">No</th>
                        <th class="px-6 py-4 font-semibold">Nama Kategori</th>
                        <th class="px-6 py-4 font-semibold">Deskripsi</th>
                        <th class="px-6 py-4 font-semibold text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm divide-y divide-gray-50">
                    @forelse($kategoris as $kategori)
                    <tr class="hover:bg-blue-50/30 transition duration-200">
                        <td class="px-6 py-4 text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-medium text-gray-800">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center mr-3">
                                    <i class="ri-price-tag-3-fill"></i>
                                </div>
                                <span class="text-base font-semibold">{{ $kategori->nama_kategori }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            {{ $kategori->deskripsi ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-2">
                                <button onclick="openEditModal('{{ $kategori->kategori_id }}', '{{ $kategori->nama_kategori }}', '{{ $kategori->deskripsi }}')" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                    <i class="ri-pencil-line text-lg"></i>
                                </button>
                                
                                <button onclick="confirmDelete('{{ route('kategori.destroy', $kategori->kategori_id) }}', '{{ $kategori->nama_kategori }}')" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                    <i class="ri-delete-bin-line text-lg"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-gray-50 p-4 rounded-full mb-3">
                                    <i class="ri-folder-open-line text-4xl text-gray-300"></i>
                                </div>
                                <p class="font-medium text-gray-500">Belum ada data kategori.</p>
                                <p class="text-xs mt-1 text-gray-400">Silakan tambah kategori baru untuk memulai.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div> 
<div id="modalAdd" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="backdropAdd" onclick="closeModal('modalAdd')"></div>
    
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div id="panelAdd" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg opacity-0 scale-95 border border-gray-100">
                
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="ri-add-circle-fill mr-2 text-xl text-blue-200"></i> Tambah Kategori
                    </h3>
                    <button onclick="closeModal('modalAdd')" class="text-blue-200 hover:text-white hover:bg-blue-600/50 rounded-full p-1 transition focus:outline-none">
                        <i class="ri-close-line text-2xl"></i>
                    </button>
                </div>

                <form action="{{ route('kategori.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-6 py-6 space-y-5">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Kategori</label>
                            <input type="text" name="nama_kategori" id="add_nama" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" required placeholder="Contoh: Minuman">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                            <textarea name="deskripsi" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" rows="3" placeholder="Deskripsi singkat kategori..."></textarea>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                        <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white px-5 py-2.5 rounded-xl hover:bg-blue-700 font-medium shadow-sm flex items-center justify-center">
                            <i class="ri-save-line mr-2"></i> Simpan
                        </button>
                        <button type="button" onclick="closeModal('modalAdd')" class="w-full sm:w-auto bg-white text-gray-700 px-5 py-2.5 rounded-xl border border-gray-300 hover:bg-gray-50 font-medium">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="modalEdit" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="backdropEdit" onclick="closeModal('modalEdit')"></div>
    
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div id="panelEdit" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg opacity-0 scale-95 border border-gray-100">
                
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="ri-edit-2-fill mr-2 text-xl text-blue-200"></i> Edit Kategori
                    </h3>
                    <button onclick="closeModal('modalEdit')" class="text-blue-200 hover:text-white hover:bg-blue-600/50 rounded-full p-1 transition focus:outline-none">
                        <i class="ri-close-line text-2xl"></i>
                    </button>
                </div>

                <form id="formEdit" action="" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="bg-white px-6 py-6 space-y-5">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Kategori</label>
                            <input type="text" name="nama_kategori" id="edit_nama" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                            <textarea name="deskripsi" id="edit_deskripsi" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                        <button type="submit" class="w-full sm:w-auto bg-indigo-600 text-white px-5 py-2.5 rounded-xl hover:bg-indigo-700 font-medium shadow-sm flex items-center justify-center">
                            <i class="ri-save-line mr-2"></i> Update
                        </button>
                        <button type="button" onclick="closeModal('modalEdit')" class="w-full sm:w-auto bg-white text-gray-700 px-5 py-2.5 rounded-xl border border-gray-300 hover:bg-gray-50 font-medium">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="modalDelete" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="backdropDelete" onclick="closeModal('modalDelete')"></div>
    
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div id="panelDelete" class="relative transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md opacity-0 scale-95 p-6 border border-gray-100">
                
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 mb-4 animate-bounce">
                    <i class="ri-alarm-warning-fill text-3xl text-red-600"></i>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Kategori?</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Anda akan menghapus kategori <span id="deleteTargetName" class="font-bold text-gray-800 bg-gray-100 px-2 py-0.5 rounded"></span>. 
                    <br>Tindakan ini tidak dapat dibatalkan.
                </p>

                <form id="formDelete" action="" method="POST" class="flex justify-center gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="closeModal('modalDelete')" class="w-full bg-white border border-gray-300 text-gray-700 px-5 py-2.5 rounded-xl hover:bg-gray-50 font-medium transition">Batal</button>
                    <button type="submit" class="w-full bg-red-600 text-white px-5 py-2.5 rounded-xl hover:bg-red-700 font-medium shadow-lg shadow-red-200 transition">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // --- ANIMASI MODAL UMUM ---
    function openModal(id) {
        const modal = document.getElementById(id);
        const backdrop = modal.querySelector('div[id^="backdrop"]');
        const panel = modal.querySelector('div[id^="panel"]');
        
        modal.classList.remove('hidden');
        
        // Animasi Masuk (Scale up & Fade in)
        requestAnimationFrame(() => {
            backdrop.classList.remove('opacity-0');
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        });

        // Auto focus jika modal tambah
        if(id === 'modalAdd') setTimeout(() => document.getElementById('add_nama').focus(), 200);
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        const backdrop = modal.querySelector('div[id^="backdrop"]');
        const panel = modal.querySelector('div[id^="panel"]');

        // Animasi Keluar
        backdrop.classList.add('opacity-0');
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // --- LOGIC EDIT KATEGORI ---
    function openEditModal(id, nama, deskripsi) {
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_deskripsi').value = deskripsi;
        
        const form = document.getElementById('formEdit');
        form.action = "{{ url('kategori') }}/" + id; // Sesuaikan jika perlu
        
        openModal('modalEdit');
    }

    // --- LOGIC HAPUS KATEGORI ---
    function confirmDelete(url, name) {
        document.getElementById('formDelete').action = url;
        document.getElementById('deleteTargetName').textContent = name;
        openModal('modalDelete');
    }

    // --- TOAST NOTIFICATION ---
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        
        let bgClass = type === 'success' ? 'bg-green-600' : 'bg-red-600';
        let icon = type === 'success' ? 'ri-check-double-line' : 'ri-error-warning-line';
        
        toast.className = `${bgClass} text-white px-6 py-4 rounded-xl shadow-lg flex items-center gap-3 transform transition-all duration-300 translate-x-10 opacity-0`;
        toast.innerHTML = `<i class="${icon} text-xl"></i><span class="font-medium">${message}</span>`;
        
        container.appendChild(toast);
        requestAnimationFrame(() => toast.classList.remove('translate-x-10', 'opacity-0'));
        
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-4');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    @if(session('success')) showToast("{{ session('success') }}"); @endif
</script>
@endsection