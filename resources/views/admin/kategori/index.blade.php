@extends('layouts.app')

@section('title', 'Daftar Kategori')

@section('content')
<div class="space-y-8 relative"> {{-- Perluas spacing --}}

    {{-- Notifikasi Toast (Tetap sama, karena sudah baik) --}}
    <div id="toast-container" class="fixed bottom-5 right-5 z-[100] flex flex-col gap-2"></div>
    
    {{-- Header Dashboard/Judul Halaman yang Lebih Dinamis --}}
    <div class="bg-white p-6 rounded-2xl shadow-xl border border-gray-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-5">
        
        <div class="space-y-1">
            <h2 class="flex items-center text-3xl font-extrabold text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.0" stroke="currentColor" class="size-7 text-indigo-500 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                </svg>
                Daftar Kategori
            </h2>
            <div class="flex flex-wrap items-center text-sm text-gray-500">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                    <i class="ri-folder-open-line mr-1.5 text-sm"></i> Manajemen Data
                </span>
                <span class="mx-3 hidden sm:inline-block">•</span>
                <p class="text-sm mt-1 sm:mt-0 text-gray-600">Kelompokkan produk agar lebih terorganisir.</p>
            </div>
        </div>

        <div class="w-full md:w-auto flex justify-end">
            @role('admin')
            <button onclick="openModal('modalAdd')" class="group bg-indigo-600 text-white px-6 py-3 rounded-xl transition-all shadow-md hover:bg-indigo-700 hover:shadow-lg flex items-center font-semibold text-base w-full md:w-auto">
                <i class="ri-add-line mr-2 text-xl group-hover:rotate-90 transition-transform"></i> 
                Tambah Kategori Baru
            </button>
            @endrole
        </div>
    </div>
    
    <div class="flex flex-col gap-6">

        {{-- Konten Utama: Daftar Kategori --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            
            {{-- Bagian Desktop (Tabel) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-indigo-600 text-white/90 text-sm">
                            <th class="px-6 py-4 font-bold w-16">No</th>
                            <th class="px-6 py-4 font-bold">Nama Kategori</th>
                            <th class="px-6 py-4 font-bold">Deskripsi</th>
                            <th class="px-6 py-4 font-bold text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                        @forelse($kategoris as $kategori)
                        <tr class="hover:bg-blue-50/50 transition duration-200">
                            <td class="px-6 py-4 text-gray-500 font-medium">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3 flex-shrink-0">
                                        <i class="ri-price-tag-3-fill text-lg"></i>
                                    </div>
                                    <span class="text-base font-semibold text-gray-800">{{ $kategori->nama_kategori }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $kategori->deskripsi ?? 'Tidak ada deskripsi' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <button onclick="openEditModal('{{ $kategori->kategori_id }}', '{{ $kategori->nama_kategori }}', '{{ $kategori->deskripsi }}')" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-full transition" title="Edit">
                                        <i class="ri-pencil-line text-lg"></i>
                                    </button>
                                    
                                    <button onclick="confirmDelete('{{ route('kategori.destroy', $kategori->kategori_id) }}', '{{ $kategori->nama_kategori }}')" class="p-2 text-red-600 hover:bg-red-50 rounded-full transition" title="Hapus">
                                        <i class="ri-delete-bin-line text-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-100 p-5 rounded-full mb-4">
                                        <i class="ri-inbox-line text-5xl text-gray-300"></i>
                                    </div>
                                    <p class="font-semibold text-gray-600 text-lg">Belum ada data kategori.</p>
                                    <p class="text-sm mt-1 text-gray-500">Silakan tambah kategori baru untuk ditampilkan di sini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Bagian Mobile (Card List) --}}
            <div class="md:hidden p-4 space-y-4">
                <h3 class="text-lg font-bold text-gray-700 px-2 pt-2">Daftar Kategori</h3>
                @forelse($kategoris as $kategori)
                <div class="bg-white border border-gray-200 p-4 rounded-xl shadow-sm space-y-3">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="ri-price-tag-3-fill text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">No. {{ $loop->iteration }}</p>
                                <p class="text-base font-semibold text-gray-800">{{ $kategori->nama_kategori }}</p>
                            </div>
                        </div>
                        <div class="flex gap-1 flex-shrink-0">
                             <button onclick="openEditModal('{{ $kategori->kategori_id }}', '{{ $kategori->nama_kategori }}', '{{ $kategori->deskripsi }}')" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-full transition" title="Edit">
                                <i class="ri-pencil-line text-lg"></i>
                            </button>
                            
                            <button onclick="confirmDelete('{{ route('kategori.destroy', $kategori->kategori_id) }}', '{{ $kategori->nama_kategori }}')" class="p-2 text-red-600 hover:bg-red-50 rounded-full transition" title="Hapus">
                                <i class="ri-delete-bin-line text-lg"></i>
                            </button>
                        </div>
                    </div>
                    <div class="border-t border-dashed pt-2">
                        <p class="text-xs font-medium text-gray-500 mb-1">Deskripsi:</p>
                        <p class="text-sm text-gray-600">{{ $kategori->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                    </div>
                </div>
                @empty
                <div class="py-10 text-center text-gray-400">
                    <div class="flex flex-col items-center justify-center">
                        <div class="bg-gray-100 p-5 rounded-full mb-4">
                            <i class="ri-inbox-line text-5xl text-gray-300"></i>
                        </div>
                        <p class="font-semibold text-gray-600 text-lg">Belum ada data kategori.</p>
                        <p class="text-sm mt-1 text-gray-500">Silakan tambah kategori baru untuk ditampilkan di sini.</p>
                    </div>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($kategoris->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-center md:justify-end">
                {{ $kategoris->links('vendor.pagination.tailwind') }} {{-- Pastikan Anda punya template pagination tailwind --}}
            </div>
            @endif

        </div>

    </div>

</div> 

{{-- Modals (Modal Tambah, Edit, Hapus - Tetap sama, karena sudah cukup modern dengan animasi) --}}
{{-- Saya akan sertakan kembali modal-modal Anda untuk memastikan kode tetap utuh --}}

{{-- Modal Tambah --}}
<div id="modalAdd" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="backdropAdd" onclick="closeModal('modalAdd')"></div>
    
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div id="panelAdd" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg opacity-0 scale-95 border border-gray-100">
                
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="ri-add-circle-fill mr-2 text-xl text-indigo-200"></i> Tambah Kategori
                    </h3>
                    <button onclick="closeModal('modalAdd')" class="text-indigo-200 hover:text-white hover:bg-indigo-600/50 rounded-full p-1 transition focus:outline-none">
                        <i class="ri-close-line text-2xl"></i>
                    </button>
                </div>

                <form action="{{ route('kategori.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-6 py-6 space-y-5">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Kategori</label>
                            <input type="text" name="nama_kategori" id="add_nama" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" required placeholder="Contoh: Minuman">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                            <textarea name="deskripsi" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" rows="3" placeholder="Deskripsi singkat kategori..."></textarea>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                        <button type="submit" class="w-full sm:w-auto bg-indigo-600 text-white px-5 py-2.5 rounded-xl hover:bg-indigo-700 font-medium shadow-sm flex items-center justify-center">
                            <i class="ri-save-line mr-2"></i> Simpan
                        </button>
                        <button type="button" onclick="closeModal('modalAdd')" class="w-full sm:w-auto bg-white text-gray-700 px-5 py-2.5 rounded-xl border border-gray-300 hover:bg-gray-50 font-medium">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modalEdit" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="backdropEdit" onclick="closeModal('modalEdit')"></div>
    
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div id="panelEdit" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg opacity-0 scale-95 border border-gray-100">
                
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="ri-edit-2-fill mr-2 text-xl text-indigo-200"></i> Edit Kategori
                    </h3>
                    <button onclick="closeModal('modalEdit')" class="text-indigo-200 hover:text-white hover:bg-indigo-600/50 rounded-full p-1 transition focus:outline-none">
                        <i class="ri-close-line text-2xl"></i>
                    </button>
                </div>

                <form id="formEdit" action="" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="bg-white px-6 py-6 space-y-5">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Kategori</label>
                            <input type="text" name="nama_kategori" id="edit_nama" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                            <textarea name="deskripsi" id="edit_deskripsi" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" rows="3"></textarea>
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

{{-- Modal Hapus --}}
<div id="modalDelete" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="backdropDelete" onclick="closeModal('modalDelete')"></div>
    
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div id="panelDelete" class="relative transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md opacity-0 scale-95 p-6 border border-gray-100">
                
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 mb-4 animate-pulse">
                    <i class="ri-alarm-warning-fill text-3xl text-red-600"></i>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Kategori?</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Anda akan menghapus kategori <span id="deleteTargetName" class="font-bold text-gray-800 bg-red-50 px-2 py-0.5 rounded-lg border border-red-200"></span>. 
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

{{-- Skrip JavaScript (Tetap sama, karena sudah baik) --}}
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
        if(id === 'modalEdit') setTimeout(() => document.getElementById('edit_nama').focus(), 200);
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
        document.getElementById('edit_deskripsi').value = deskripsi === 'null' ? '' : deskripsi; // Menangani null dari Blade
        
        const form = document.getElementById('formEdit');
        // Pastikan route update Anda menggunakan ID
        form.action = "{{ url('kategori') }}/" + id; 
        
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
        
        // Menambahkan animasi bounce pada ikon
        toast.className = `${bgClass} text-white px-6 py-4 rounded-xl shadow-lg flex items-center gap-3 transform transition-all duration-300 translate-x-10 opacity-0`;
        toast.innerHTML = `<i class="${icon} text-xl animate-pulse"></i><span class="font-medium">${message}</span>`;
        
        container.appendChild(toast);
        requestAnimationFrame(() => toast.classList.remove('translate-x-10', 'opacity-0'));
        
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-4');
            setTimeout(() => toast.remove(), 300);
        }, 4000); // Durasi tampil lebih lama
    }

    @if(session('success')) showToast("{{ session('success') }}"); @endif
</script>
@endsection