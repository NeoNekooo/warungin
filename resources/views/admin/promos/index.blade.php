@extends('layouts.app')

@section('content')
<div class="space-y-6 relative min-h-screen">

    <!-- TOAST NOTIFICATION CONTAINER -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-[100] flex flex-col gap-2"></div>

    <!-- HEADER -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Promo</h2>
            <div class="flex items-center text-sm text-gray-500 mt-2 gap-3">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-100">
                    <i class="ri-price-tag-3-line mr-1.5"></i> Promo & Diskon
                </span>
                <span class="hidden sm:inline text-gray-300">•</span>
                <span class="text-gray-500">Kelola promo yang muncul di POS</span>
            </div>
        </div>

        <div>
            <button onclick="openModal('modalAddPromo')" class="w-full md:w-auto bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center font-medium text-sm">
                <i class="ri-add-line mr-2 text-lg"></i> Tambah Promo
            </button>
        </div>
    </div>

    <!-- LIST PROMOS -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($promos as $promo)
        <div class="group bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition p-4">
            <div class="flex items-start justify-between">
                <div>
                    <div class="font-semibold text-gray-800">{{ $promo->name ?? $promo->title ?? ('Promo #'.$promo->id) }}</div>
                    <div class="text-xs text-gray-500">{{ $promo->code ?? '-' }}</div>
                    <div class="text-sm text-yellow-700 font-semibold mt-2">
                        @if($promo->percent)
                            {{ $promo->percent }}% off
                        @elseif($promo->discount)
                            Rp {{ number_format($promo->discount,0,',','.') }}
                        @else
                            -
                        @endif
                    </div>
                    @if($promo->description)
                        <div class="text-xs text-gray-500 mt-2 line-clamp-3">{{ $promo->description }}</div>
                    @endif
                </div>
                <div class="text-right text-xs text-gray-400">
                    {{ $promo->starts_at ? $promo->starts_at->format('Y-m-d') : '-' }}
                    <div class="text-gray-300">—</div>
                    {{ $promo->ends_at ? $promo->ends_at->format('Y-m-d') : '-' }}
                </div>
            </div>

            <div class="mt-4 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <button onclick='openEditPromo(@json($promo))' class="px-3 py-1 bg-white border border-gray-100 text-gray-700 rounded hover:bg-gray-50">Edit</button>
                    <button onclick='openDeletePromo({{ $promo->id }}, "{{ addslashes($promo->name ?? $promo->title ?? 'Promo') }}")' class="px-3 py-1 bg-red-50 text-red-700 rounded hover:bg-red-100">Hapus</button>
                </div>
                <div class="text-sm">
                    @if($promo->active)
                        <span class="px-2 py-0.5 text-green-700 bg-green-50 rounded">Aktif</span>
                    @else
                        <span class="px-2 py-0.5 text-gray-600 bg-gray-50 rounded">Nonaktif</span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $promos->links() }}</div>

</div>

<!-- MODAL: ADD PROMO -->
<div id="modalAddPromo" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('modalAddPromo')"></div>
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-100">
            <div class="bg-gradient-to-r from-yellow-600 to-yellow-700 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center"><i class="ri-add-circle-fill mr-2 text-xl text-yellow-200"></i> Tambah Promo</h3>
                <button onclick="closeModal('modalAddPromo')" class="text-yellow-200 hover:text-white rounded-full p-1"><i class="ri-close-line text-2xl"></i></button>
            </div>

            <form action="{{ route('promos.store') }}" method="POST">
                @csrf
                <div class="bg-white px-6 py-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <input type="text" name="name" placeholder="Nama promo" class="border p-2 rounded">
                        <input type="text" name="code" placeholder="Kode promo (opsional)" class="border p-2 rounded">
                        <textarea name="description" rows="3" placeholder="Deskripsi" class="border p-2 rounded md:col-span-2"></textarea>
                        <input type="number" step="0.01" name="discount" placeholder="Diskon (nominal)" class="border p-2 rounded">
                        <input type="number" name="percent" placeholder="Diskon (%)" class="border p-2 rounded">
                        <label class="flex items-center"><input type="checkbox" name="active" value="1" checked class="mr-2"> Aktif</label>
                        <input type="date" name="starts_at" class="border p-2 rounded">
                        <input type="date" name="ends_at" class="border p-2 rounded">
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                    <button type="submit" class="px-5 py-2.5 bg-yellow-600 text-white rounded-xl">Simpan</button>
                    <button type="button" onclick="closeModal('modalAddPromo')" class="px-5 py-2.5 bg-white border rounded-xl">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: EDIT PROMO -->
<div id="modalEditPromo" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal('modalEditPromo')"></div>
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-100">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-700 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center"><i class="ri-edit-box-line mr-2 text-xl text-indigo-200"></i> Edit Promo</h3>
                <button onclick="closeModal('modalEditPromo')" class="text-indigo-200 hover:text-white rounded-full p-1"><i class="ri-close-line text-2xl"></i></button>
            </div>

            <form id="formEditPromo" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="bg-white px-6 py-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <input id="edit_name" type="text" name="name" placeholder="Nama promo" class="border p-2 rounded">
                        <input id="edit_code" type="text" name="code" placeholder="Kode promo (opsional)" class="border p-2 rounded">
                        <textarea id="edit_description" name="description" rows="3" placeholder="Deskripsi" class="border p-2 rounded md:col-span-2"></textarea>
                        <input id="edit_discount" type="number" step="0.01" name="discount" placeholder="Diskon (nominal)" class="border p-2 rounded">
                        <input id="edit_percent" type="number" name="percent" placeholder="Diskon (%)" class="border p-2 rounded">
                        <label class="flex items-center"><input id="edit_active" type="checkbox" name="active" value="1" class="mr-2"> Aktif</label>
                        <input id="edit_starts_at" type="date" name="starts_at" class="border p-2 rounded">
                        <input id="edit_ends_at" type="date" name="ends_at" class="border p-2 rounded">
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl">Update</button>
                    <button type="button" onclick="closeModal('modalEditPromo')" class="px-5 py-2.5 bg-white border rounded-xl">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: DELETE PROMO CONFIRM -->
<div id="modalDeletePromo" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/40" onclick="closeModal('modalDeletePromo')"></div>
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
            <div class="px-6 py-6">
                <h3 class="text-lg font-bold">Hapus Promo</h3>
                <p class="text-sm text-gray-600 mt-2">Apakah Anda yakin ingin menghapus promo <span id="deletePromoName" class="font-semibold"></span>?</p>
                <div class="mt-4 flex justify-end gap-3">
                    <form id="formDeletePromo" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Hapus</button>
                    </form>
                    <button onclick="closeModal('modalDeletePromo')" class="px-4 py-2 bg-white border rounded">Batal</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPTS: modal helpers and promo handlers -->
<script>
    function openModal(id){ document.getElementById(id).classList.remove('hidden'); }
    function closeModal(id){ document.getElementById(id).classList.add('hidden'); }

    function openEditPromo(data){
        const url = "{{ route('promos.update', ':id') }}".replace(':id', data.id);
        document.getElementById('formEditPromo').action = url;
        document.getElementById('edit_name').value = data.name ?? data.title ?? '';
        document.getElementById('edit_code').value = data.code ?? '';
        document.getElementById('edit_description').value = data.description ?? '';
        document.getElementById('edit_discount').value = data.discount ?? '';
        document.getElementById('edit_percent').value = data.percent ?? '';
        document.getElementById('edit_active').checked = (data.active == 1 || data.active === true || data.active === '1');
        document.getElementById('edit_starts_at').value = data.starts_at ? data.starts_at.split(' ')[0] : '';
        document.getElementById('edit_ends_at').value = data.ends_at ? data.ends_at.split(' ')[0] : '';
        openModal('modalEditPromo');
    }

    function openDeletePromo(id, name){
        const url = "{{ route('promos.destroy', ':id') }}".replace(':id', id);
        document.getElementById('formDeletePromo').action = url;
        document.getElementById('deletePromoName').textContent = name;
        openModal('modalDeletePromo');
    }

    // Small toast helper
    function showToast(message, type='success'){
        const container = document.getElementById('toast-container');
        const t = document.createElement('div');
        t.className = (type==='success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white') + ' px-6 py-3 rounded-xl shadow-md';
        t.textContent = message;
        container.appendChild(t);
        setTimeout(()=> { t.remove(); }, 3000);
    }

    @if(session('success'))
        showToast("{{ session('success') }}");
    @endif
</script>

@endsection
