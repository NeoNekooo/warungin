@extends('layouts.app')

@section('title', 'Manajemen Promo')

@section('content')
<div class="space-y-8 relative"> {{-- Perluas spacing --}}

    {{-- Notifikasi Toast --}}
    <div id="toast-container" class="fixed bottom-5 right-5 z-[100] flex flex-col gap-2"></div>

    {{-- Header Dashboard/Judul Halaman (WARNA YELLOW TETAP) --}}
    <div class="bg-white p-6 rounded-2xl shadow-xl border border-gray-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-5">
        
        <div class="space-y-1">
            <h2 class="flex items-center text-3xl font-extrabold text-gray-900">
                <i class="ri-coupon-3-fill text-3xl text-yellow-500 mr-2"></i>
                Manajemen Promo
            </h2>
            <div class="flex flex-wrap items-center text-sm text-gray-500">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700 border border-yellow-200">
                    <i class="ri-price-tag-3-line mr-1.5 text-sm"></i> Promo & Diskon
                </span>
                <span class="mx-3 hidden sm:inline-block">•</span>
                <p class="text-sm mt-1 sm:mt-0 text-gray-600">Kelola daftar promo dan kupon diskon Anda.</p>
            </div>
        </div>

        <div class="w-full md:w-auto flex justify-end">
            <button onclick="openModal('modalAddPromo')" class="group bg-yellow-600 text-white px-6 py-3 rounded-xl transition-all shadow-md hover:bg-yellow-700 hover:shadow-lg flex items-center font-semibold text-base w-full md:w-auto">
                <i class="ri-add-line mr-2 text-xl group-hover:rotate-90 transition-transform"></i> 
                Tambah Promo Baru
            </button>
        </div>
    </div>
    
    <div class="flex flex-col gap-6">

        {{-- Konten Utama: Daftar Promo (Menggunakan format Card Grid yang lebih cocok untuk promo) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($promos as $promo)
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden hover:shadow-2xl transition-shadow duration-300">
                
                {{-- Card Header --}}
                <div class="p-5 border-b border-gray-100 bg-yellow-50/50">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-medium text-gray-500">
                                <i class="ri-hashtag text-sm mr-1"></i> {{ $promo->code ?? 'TANPA KODE' }}
                            </p>
                            <h3 class="text-xl font-bold text-gray-800 mt-1 line-clamp-2">
                                {{ $promo->name ?? $promo->title ?? ('Promo #'.$promo->id) }}
                            </h3>
                        </div>
                        <div class="flex-shrink-0">
                             @if($promo->active)
                                <span class="px-3 py-1 text-green-700 bg-green-100 rounded-full text-xs font-semibold border border-green-200">Aktif</span>
                            @else
                                <span class="px-3 py-1 text-gray-600 bg-gray-100 rounded-full text-xs font-semibold border border-gray-200">Nonaktif</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="p-5 space-y-3">
                    
                    {{-- Detail Diskon --}}
                    <div class="flex items-center gap-4">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-700 flex-shrink-0">
                            <i class="ri-money-dollar-circle-fill text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Nilai Diskon</p>
                            <p class="text-2xl font-extrabold text-yellow-600">
                                @if($promo->percent)
                                    {{ $promo->percent }}%
                                @elseif($promo->discount)
                                    Rp {{ number_format($promo->discount,0,',','.') }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    @if($promo->description)
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-1">Deskripsi:</p>
                            <p class="text-sm text-gray-600 line-clamp-3">{{ $promo->description }}</p>
                        </div>
                    @endif

                    {{-- Tanggal Berlaku --}}
                    <div class="text-xs text-gray-500 pt-2 border-t border-dashed">
                        <i class="ri-calendar-line mr-1"></i> Berlaku:
                        <span class="font-medium text-gray-700">
                            {{ $promo->starts_at ? $promo->starts_at->format('d M y') : 'Selamanya' }}
                        </span> 
                        <span class="text-gray-400">hingga</span> 
                        <span class="font-medium text-gray-700">
                            {{ $promo->ends_at ? $promo->ends_at->format('d M y') : 'Tidak Terbatas' }}
                        </span>
                    </div>

                </div>

                {{-- Card Footer (Aksi) --}}
                <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                    <button 
                        onclick='openEditPromo(@json($promo))' 
                        class="p-2 text-indigo-600 hover:bg-indigo-100 rounded-full transition" 
                        title="Edit Promo">
                        <i class="ri-pencil-line text-lg"></i>
                    </button>
                    
                    <button 
                        onclick='confirmDeletePromo("{{ route('promos.destroy', $promo->id) }}", "{{ addslashes($promo->name ?? $promo->title ?? 'Promo') }}")' 
                        class="p-2 text-red-600 hover:bg-red-100 rounded-full transition" 
                        title="Hapus Promo">
                        <i class="ri-delete-bin-line text-lg"></i>
                    </button>
                </div>

            </div>
            @empty
            <div class="col-span-full py-16 text-center text-gray-400 bg-white rounded-2xl shadow-xl border border-gray-100">
                <div class="flex flex-col items-center justify-center">
                    <div class="bg-gray-100 p-5 rounded-full mb-4">
                        <i class="ri-inbox-line text-5xl text-gray-300"></i>
                    </div>
                    <p class="font-semibold text-gray-600 text-lg">Tidak ada data promo yang tersedia.</p>
                    <p class="text-sm mt-1 text-gray-500">Silakan tambah promo baru untuk ditampilkan di sini.</p>
                </div>
            </div>
            @endforelse
        </div>
        
        {{-- Pagination --}}
        @if(isset($promos) && method_exists($promos, 'hasPages') && $promos->hasPages())
        <div class="px-6 py-4 bg-white rounded-2xl border border-gray-100 flex justify-center md:justify-end">
            {{ $promos->links('vendor.pagination.tailwind') }} 
        </div>
        @endif

    </div>

</div> 

{{--------------------------------------------------}}
{{-- MODAL TAMBAH PROMO (STORE) --}}
{{--------------------------------------------------}}
<div id="modalAddPromo" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="backdropAddPromo" onclick="closeModal('modalAddPromo')"></div>
    
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div id="panelAddPromo" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl opacity-0 scale-95 border border-gray-100">
                
                {{-- Header Modal (WARNA YELLOW) --}}
                <div class="bg-gradient-to-r from-yellow-600 to-yellow-700 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="ri-add-circle-fill mr-2 text-xl text-yellow-200"></i> Tambah Promo
                    </h3>
                    <button onclick="closeModal('modalAddPromo')" class="text-yellow-200 hover:text-white hover:bg-yellow-600/50 rounded-full p-1 transition focus:outline-none">
                        <i class="ri-close-line text-2xl"></i>
                    </button>
                </div>

                <form action="{{ route('promos.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-6 py-6 space-y-5 max-h-[60vh] overflow-y-auto">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            {{-- Nama Promo --}}
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Promo</label>
                                <input type="text" name="name" id="add_name" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all" required placeholder="Contoh: Diskon Akhir Bulan">
                            </div>
                            
                            {{-- Kode Promo --}}
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Kode Promo (Opsional)</label>
                                <input type="text" name="code" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all" placeholder="Contoh: SAVE20">
                            </div>
                            
                            {{-- Deskripsi --}}
                            <div class="md:col-span-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                                <textarea name="description" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all" rows="3" placeholder="Deskripsi singkat promo..."></textarea>
                            </div>

                            {{-- Diskon Nominal --}}
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Diskon (Nominal Rupiah)</label>
                                <input type="number" step="0.01" name="discount" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all" placeholder="Contoh: 10000">
                            </div>

                            {{-- Diskon Persen --}}
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Diskon (Persen %)</label>
                                <input type="number" name="percent" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all" placeholder="Contoh: 15">
                            </div>
                            
                            {{-- Tanggal Mulai --}}
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Mulai Berlaku</label>
                                <input type="date" name="starts_at" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all">
                            </div>

                            {{-- Tanggal Berakhir --}}
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Berakhir</label>
                                <input type="date" name="ends_at" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all">
                            </div>
                            
                            {{-- Status Aktif --}}
                            <div class="md:col-span-2">
                                <label class="flex items-center text-gray-700 text-sm font-bold">
                                    <input type="checkbox" name="active" value="1" checked class="h-4 w-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500 mr-2"> 
                                    Promo Aktif (Langsung berlaku saat ini)
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                        <button type="submit" class="w-full sm:w-auto bg-yellow-600 text-white px-5 py-2.5 rounded-xl hover:bg-yellow-700 font-medium shadow-sm flex items-center justify-center">
                            <i class="ri-save-line mr-2"></i> Simpan Promo
                        </button>
                        <button type="button" onclick="closeModal('modalAddPromo')" class="w-full sm:w-auto bg-white text-gray-700 px-5 py-2.5 rounded-xl border border-gray-300 hover:bg-gray-50 font-medium">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{--------------------------------------------------}}
{{-- MODAL EDIT PROMO (UPDATE) --}}
{{--------------------------------------------------}}
<div id="modalEditPromo" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="backdropEditPromo" onclick="closeModal('modalEditPromo')"></div>
    
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div id="panelEditPromo" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl opacity-0 scale-95 border border-gray-100">
                
                {{-- Header Modal (WARNA YELLOW) --}}
                <div class="bg-gradient-to-r from-yellow-600 to-yellow-700 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="ri-edit-2-fill mr-2 text-xl text-yellow-200"></i> Edit Promo
                    </h3>
                    <button onclick="closeModal('modalEditPromo')" class="text-yellow-200 hover:text-white hover:bg-yellow-600/50 rounded-full p-1 transition focus:outline-none">
                        <i class="ri-close-line text-2xl"></i>
                    </button>
                </div>

                <form id="formEditPromo" action="" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="bg-white px-6 py-6 space-y-5 max-h-[60vh] overflow-y-auto">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                            {{-- Nama Promo --}}
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Promo</label>
                                <input id="edit_name" type="text" name="name" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all" required>
                            </div>
                            
                            {{-- Kode Promo --}}
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Kode Promo (Opsional)</label>
                                <input id="edit_code" type="text" name="code" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all">
                            </div>
                            
                            {{-- Deskripsi --}}
                            <div class="md:col-span-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                                <textarea id="edit_description" name="description" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all" rows="3"></textarea>
                            </div>

                            {{-- Diskon Nominal --}}
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Diskon (Nominal Rupiah)</label>
                                <input id="edit_discount" type="number" step="0.01" name="discount" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all">
                            </div>

                            {{-- Diskon Persen --}}
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Diskon (Persen %)</label>
                                <input id="edit_percent" type="number" name="percent" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all">
                            </div>
                            
                            {{-- Tanggal Mulai --}}
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Mulai Berlaku</label>
                                <input id="edit_starts_at" type="date" name="starts_at" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all">
                            </div>

                            {{-- Tanggal Berakhir --}}
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal Berakhir</label>
                                <input id="edit_ends_at" type="date" name="ends_at" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-all">
                            </div>
                            
                            {{-- Status Aktif --}}
                            <div class="md:col-span-2">
                                <label class="flex items-center text-gray-700 text-sm font-bold">
                                    <input id="edit_active" type="checkbox" name="active" value="1" class="h-4 w-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500 mr-2"> 
                                    Promo Aktif (Langsung berlaku saat ini)
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                        <button type="submit" class="w-full sm:w-auto bg-yellow-600 text-white px-5 py-2.5 rounded-xl hover:bg-yellow-700 font-medium shadow-sm flex items-center justify-center">
                            <i class="ri-save-line mr-2"></i> Update Promo
                        </button>
                        <button type="button" onclick="closeModal('modalEditPromo')" class="w-full sm:w-auto bg-white text-gray-700 px-5 py-2.5 rounded-xl border border-gray-300 hover:bg-gray-50 font-medium">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{--------------------------------------------------}}
{{-- MODAL HAPUS PROMO (DELETE) --}}
{{--------------------------------------------------}}
<div id="modalDeletePromo" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="backdropDeletePromo" onclick="closeModal('modalDeletePromo')"></div>
    
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div id="panelDeletePromo" class="relative transform overflow-hidden rounded-2xl bg-white text-center shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md opacity-0 scale-95 p-6 border border-gray-100">
                
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100 mb-4 animate-pulse">
                    <i class="ri-alarm-warning-fill text-3xl text-red-600"></i>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Promo?</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Anda akan menghapus promo <span id="deletePromoName" class="font-bold text-gray-800 bg-red-50 px-2 py-0.5 rounded-lg border border-red-200"></span>. 
                    <br>Tindakan ini tidak dapat dibatalkan.
                </p>

                <form id="formDeletePromo" action="" method="POST" class="flex justify-center gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="closeModal('modalDeletePromo')" class="w-full bg-white border border-gray-300 text-gray-700 px-5 py-2.5 rounded-xl hover:bg-gray-50 font-medium transition">Batal</button>
                    <button type="submit" class="w-full bg-red-600 text-white px-5 py-2.5 rounded-xl hover:bg-red-700 font-medium shadow-lg shadow-red-200 transition">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Skrip JavaScript --}}
<script>
    // --- ANIMASI MODAL UMUM (DARI TEMPLATE KATEGORI/PELANGGAN) ---
    function openModal(id) {
        const modal = document.getElementById(id);
        const backdrop = modal.querySelector('div[id^="backdrop"]');
        const panel = modal.querySelector('div[id^="panel"]');
        
        modal.classList.remove('hidden');
        
        requestAnimationFrame(() => {
            if(backdrop) backdrop.classList.remove('opacity-0');
            if(panel) {
                panel.classList.remove('opacity-0', 'scale-95');
                panel.classList.add('opacity-100', 'scale-100');
            }
        });

        if(id === 'modalAddPromo') setTimeout(() => document.getElementById('add_name').focus(), 200);
        if(id === 'modalEditPromo') setTimeout(() => document.getElementById('edit_name').focus(), 200);
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        const backdrop = modal.querySelector('div[id^="backdrop"]');
        const panel = modal.querySelector('div[id^="panel"]');

        if(backdrop) backdrop.classList.add('opacity-0');
        if(panel) {
            panel.classList.remove('opacity-100', 'scale-100');
            panel.classList.add('opacity-0', 'scale-95');
        }

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // --- LOGIC EDIT PROMO --
    function openEditPromo(data){
        const url = "{{ url('promos') }}/" + data.id; // Menggunakan URL helper
        document.getElementById('formEditPromo').action = url;
        
        document.getElementById('edit_name').value = data.name ?? data.title ?? '';
        document.getElementById('edit_code').value = data.code ?? '';
        document.getElementById('edit_description').value = data.description ?? '';
        document.getElementById('edit_discount').value = data.discount ?? '';
        document.getElementById('edit_percent').value = data.percent ?? '';
        document.getElementById('edit_active').checked = (data.active == 1 || data.active === true || data.active === '1');
        
        // Mengubah format tanggal untuk input type="date"
        document.getElementById('edit_starts_at').value = data.starts_at ? data.starts_at.split(' ')[0] : '';
        document.getElementById('edit_ends_at').value = data.ends_at ? data.ends_at.split(' ')[0] : '';
        
        openModal('modalEditPromo');
    }

    // --- LOGIC HAPUS PROMO ---
    function confirmDeletePromo(url, name){
        document.getElementById('formDeletePromo').action = url;
        document.getElementById('deletePromoName').textContent = name;
        openModal('modalDeletePromo');
    }

    // --- TOAST NOTIFICATION (Disamakan dengan Kategori/Pelanggan) ---
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