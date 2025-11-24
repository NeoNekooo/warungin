@extends('layouts.app')

@section('title', 'Daftar Produk')

@section('content')
<div class="space-y-6 relative min-h-screen">
    
    <!-- TOAST NOTIFICATION CONTAINER -->
    <div id="toast-container" class="fixed bottom-5 right-5 z-[100] flex flex-col gap-2"></div>

    <!-- BLOK ERROR VALIDASI -->
    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm mb-4 relative">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="ri-error-warning-fill text-red-500 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Gagal Menyimpan Data</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ================= 1. HEADER CARD ================= -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4">
        
        <!-- Bagian Kiri: Judul & Info -->
        <div class="w-full md:w-auto">
            <h2 class="text-2xl font-bold text-gray-800">Daftar Produk</h2>
            <div class="flex flex-wrap items-center text-sm text-gray-500 mt-2 gap-3">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                    <i class="ri-folder-open-line mr-1.5"></i> Manajemen Data
                </span>
                <span class="hidden sm:inline text-gray-300">•</span>
                <span class="flex items-center text-green-600 bg-green-50 px-2 py-0.5 rounded-full border border-green-100 text-xs">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse mr-1.5"></span>
                    Scanner Ready
                </span>
                <span class="hidden sm:inline text-gray-300">•</span>
                <span>Kelola {{ $produks->total() }} produk kamu disini</span>
            </div>
        </div>

        <!-- Bagian Kanan: Tombol Tambah -->
        <div class="w-full md:w-auto">
            @hasanyrole(['admin','owner'])
            <button onclick="openModal('modalAdd')" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center font-medium text-sm">
                <i class="ri-add-line mr-2 text-lg"></i> Tambah Produk
            </button>
            @endhasanyrole
        </div>
    </div>

    <!-- ================= 2. SEARCH BAR ================= -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
        <form action="{{ route('produk.index') }}" method="GET" class="relative w-full sm:w-96">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="ri-search-line text-gray-400 text-lg"></i>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" 
                class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl leading-5 bg-white placeholder-gray-400 focus:outline-none focus:placeholder-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out shadow-sm" 
                placeholder="Cari nama produk atau scan barcode...">
        </form>
    </div>

    <!-- ================= 3. GRID CARD PRODUK ================= -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6 gap-4">
        
        @forelse($produks as $produk)
        <!-- CARD ITEM START -->
        <div class="group bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col overflow-hidden relative">
            
            <!-- Gambar (Kotak Full) -->
            <div class="relative bg-gray-50 aspect-square w-full flex items-center justify-center overflow-hidden border-b border-gray-100">
                <!-- Badge Stok -->
                @if($produk->stok <= 5)
                <div class="absolute top-2 right-2 bg-white text-red-500 rounded px-1.5 py-0.5 shadow-sm text-[10px] font-bold border border-red-100 z-10 flex items-center">
                    <i class="ri-alarm-warning-fill mr-1"></i> Sisa {{ $produk->stok }}
                </div>
                @endif

                <!-- Badge Status -->
                @if($produk->status == 'nonaktif')
                <div class="absolute top-2 left-2 bg-gray-800 text-white rounded px-1.5 py-0.5 shadow-sm text-[10px] font-bold z-10 opacity-75">
                    Nonaktif
                </div>
                @endif

                @if($produk->gambar_url)
                    <img src="{{ asset('storage/' . $produk->gambar_url) }}" alt="{{ $produk->nama_produk }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                @else
                    <div class="flex flex-col items-center text-gray-300">
                        <i class="ri-image-2-line text-3xl mb-1"></i>
                        <span class="text-[10px] font-medium text-gray-400">No Image</span>
                    </div>
                @endif
            </div>

            <!-- Konten -->
            <div class="p-3 flex flex-col flex-1">
                <!-- Kategori & Stok -->
                <div class="flex justify-between items-center mb-1.5">
                    <span class="text-[10px] font-bold tracking-wider uppercase text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100 truncate max-w-[60%]">
                        {{ $produk->kategori->nama_kategori ?? 'UMUM' }}
                    </span>
                    <div class="flex items-center text-gray-500 text-[10px]" title="Stok Saat Ini">
                        <i class="ri-box-3-line mr-1"></i> {{ $produk->stok }}
                    </div>
                </div>

                <!-- Nama Produk -->
                <h3 class="font-bold text-gray-800 text-sm leading-tight mb-1 line-clamp-2 min-h-[2.5rem]" title="{{ $produk->nama_produk }}">
                    {{ $produk->nama_produk }}
                </h3>
                
                <!-- Harga & Barcode -->
                <div class="mt-auto pt-2 border-t border-dashed border-gray-100">
                    <p class="text-[10px] text-gray-400 mb-0.5">Harga Jual</p>
                    <div class="flex justify-between items-end">
                        <p class="text-blue-600 font-bold text-base leading-none">
                            {{ number_format($produk->harga_jual, 0, ',', '.') }}
                        </p>
                        <span class="bg-gray-50 text-gray-500 text-[9px] font-mono px-1 py-0.5 rounded border border-gray-200">
                            {{ $produk->kode_barcode ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            @hasanyrole(['admin','owner'])
            <div class="grid grid-cols-2 divide-x divide-gray-100 border-t border-gray-100 bg-gray-50">
                <button onclick='openEditModal(@json($produk))' class="py-2 text-center text-xs font-medium text-gray-600 hover:text-blue-600 hover:bg-white transition-colors flex items-center justify-center w-full">
                    <i class="ri-pencil-line mr-1"></i> Edit
                </button>
                
                <form action="{{ route('produk.destroy', $produk->produk_id) }}" method="POST" data-confirm="Hapus produk ini?" class="w-full">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full py-2 text-center text-xs font-medium text-gray-600 hover:text-red-600 hover:bg-white transition-colors flex items-center justify-center">
                        <i class="ri-delete-bin-line mr-1"></i> Hapus
                    </button>
                </form>
            </div>
            @else
            <div class="grid grid-cols-2 divide-x divide-gray-100 border-t border-gray-100 bg-gray-50">
                <div class="py-2 text-center text-xs font-medium text-gray-400 flex items-center justify-center">&nbsp;</div>
                <div class="py-2 text-center text-xs font-medium text-gray-400 flex items-center justify-center">&nbsp;</div>
            </div>
            @endhasanyrole

        </div>
        <!-- CARD ITEM END -->

        @empty
        <!-- Empty State -->
        <div class="col-span-full py-12 flex flex-col items-center justify-center text-center">
            <div class="bg-gray-100 p-4 rounded-full mb-3">
                <i class="ri-search-eye-line text-4xl text-gray-300"></i>
            </div>
            <h3 class="text-base font-bold text-gray-800">Produk tidak ditemukan</h3>
            <p class="text-gray-500 text-sm mt-1">Coba cari dengan kata kunci lain.</p>
            @if(request('search'))
                <a href="{{ route('produk.index') }}" class="mt-3 text-blue-600 hover:underline text-sm font-medium">Reset Pencarian</a>
            @endif
        </div>
        @endforelse

    </div>

    <!-- ================= PAGINATION ================= -->
    <div class="mt-6">
        {{ $produks->links() }}
    </div>

</div>

<!-- ================= MODAL TAMBAH PRODUK ================= -->
<div id="modalAdd" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal('modalAdd')"></div>
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-100">
            
            <!-- Header Modal -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center">
                    <i class="ri-add-circle-fill mr-2 text-xl text-blue-200"></i> Tambah Produk Baru
                </h3>
                <button onclick="closeModal('modalAdd')" class="text-blue-200 hover:text-white hover:bg-blue-600/50 rounded-full p-1 transition focus:outline-none">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>

            <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Isi Form Tambah -->
                <div class="bg-white px-6 py-6 space-y-6">
                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                        <label class="block text-blue-800 text-sm font-bold mb-2">Barcode / Kode Produk</label>
                        <input type="text" name="kode_barcode" class="w-full px-4 py-2.5 border border-blue-200 rounded-lg font-mono focus:ring-blue-500 focus:border-blue-500" placeholder="Scan kode..." autocomplete="off">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="col-span-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Produk</label>
                            <input type="text" name="nama_produk" class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                            <select name="kategori_id" class="form-select w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($kategoris as $kategori)
                                    <option value="{{ $kategori->kategori_id }}">{{ $kategori->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Satuan</label>
                            <select name="satuan" class="form-select w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                <option value="pcs">Pcs</option>
                                <option value="pack">Pack</option>
                                <option value="kardus">Kardus</option>
                                <option value="kg">Kg</option>
                                <option value="liter">Liter</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Harga Beli</label>
                            <input type="number" name="harga_beli" class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2 text-blue-600">Harga Jual</label>
                            <input type="number" name="harga_jual" class="form-input w-full rounded-lg border-blue-200 bg-blue-50/50 font-bold text-gray-800 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Stok</label>
                            <input type="number" name="stok" class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500" value="0">
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                            <select name="status" class="form-select w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Gambar</label>
                            <input type="file" name="gambar" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" onchange="previewImage(event, 'add_preview_img', 'add_preview_div')">
                            <div id="add_preview_div" class="hidden mt-2 h-32 w-32 rounded-lg border border-gray-200 p-1 bg-white">
                                <img id="add_preview_img" src="#" class="h-full w-full rounded-md object-cover">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all shadow-sm hover:shadow">
                        <i class="ri-save-line mr-2"></i> Simpan
                    </button>
                    <button type="button" onclick="closeModal('modalAdd')" class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-white text-gray-700 font-medium rounded-xl border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 transition-all">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= 2. MODAL EDIT PRODUK ================= -->
<div id="modalEdit" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal('modalEdit')"></div>
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-100">
            
            <!-- Header Modal Edit -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-700 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center">
                    <i class="ri-edit-box-line mr-2 text-xl text-indigo-200"></i> Edit Produk
                </h3>
                <button onclick="closeModal('modalEdit')" class="text-indigo-200 hover:text-white hover:bg-indigo-600/50 rounded-full p-1 transition focus:outline-none">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>

            <!-- FORM UPDATE -->
            <form id="formEdit" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Isi Form Edit -->
                <div class="bg-white px-6 py-6 space-y-6">
                    <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100">
                        <label class="block text-indigo-800 text-sm font-bold mb-2">Barcode / Kode Produk</label>
                        <input type="text" name="kode_barcode" id="edit_kode_barcode" class="w-full px-4 py-2.5 border border-indigo-200 rounded-lg font-mono focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="col-span-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Nama Produk</label>
                            <input type="text" name="nama_produk" id="edit_nama_produk" class="form-input w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>

                        <!-- Kategori -->
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                            <select name="kategori_id" id="edit_kategori_id" class="form-select w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($kategoris as $kategori)
                                    <option value="{{ $kategori->kategori_id }}">{{ $kategori->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Satuan -->
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Satuan</label>
                            <select name="satuan" id="edit_satuan" class="form-select w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="pcs">Pcs</option>
                                <option value="pack">Pack</option>
                                <option value="kardus">Kardus</option>
                                <option value="kg">Kg</option>
                                <option value="liter">Liter</option>
                            </select>
                        </div>

                        <!-- Harga Beli -->
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Harga Beli</label>
                            <input type="number" name="harga_beli" id="edit_harga_beli" class="form-input w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>

                        <!-- Harga Jual -->
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2 text-indigo-600">Harga Jual</label>
                            <input type="number" name="harga_jual" id="edit_harga_jual" class="form-input w-full rounded-lg border-indigo-200 bg-indigo-50/50 font-bold text-gray-800 focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>

                        <!-- Stok -->
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Stok</label>
                            <input type="number" name="stok" id="edit_stok" class="form-input w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                            <select name="status" id="edit_status" class="form-select w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>

                        <!-- Upload Gambar -->
                        <div class="col-span-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Ganti Gambar (Opsional)</label>
                            <input type="file" name="gambar" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" onchange="previewImage(event, 'edit_image_preview_img', 'edit_image_preview_container')">
                            
                            <!-- Preview Container -->
                            <div id="edit_image_preview_container" class="hidden mt-3 h-32 w-32 rounded-lg border border-gray-200 p-1 bg-white shadow-sm">
                                <img id="edit_image_preview_img" src="#" class="h-full w-full rounded-md object-cover">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-sm hover:shadow">
                        <i class="ri-save-line mr-2"></i> Update
                    </button>
                    <button type="button" onclick="closeModal('modalEdit')" class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-white text-gray-700 font-medium rounded-xl border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 transition-all">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script JS -->
<script>
    // --- FUNGSI UMUM MODAL ---
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if(modal) {
            modal.classList.remove('hidden');
            const panel = modal.querySelector('.transform');
            if(panel) {
                panel.classList.remove('opacity-0', 'scale-95');
                panel.classList.add('opacity-100', 'scale-100');
            }
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if(modal) {
            const panel = modal.querySelector('.transform');
            if(panel) {
                panel.classList.add('opacity-0', 'scale-95');
                panel.classList.remove('opacity-100', 'scale-100');
            }
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }
    }

    // --- FUNGSI EDIT MODAL (Mengisi Data ke Form) ---
    function openEditModal(data) {
        let id = data.produk_id;
        let url = "{{ route('produk.update', ':id') }}";
        url = url.replace(':id', id);
        
        document.getElementById('formEdit').action = url;

        // Isi Input
        document.getElementById('edit_kode_barcode').value = data.kode_barcode || '';
        document.getElementById('edit_nama_produk').value = data.nama_produk;
        document.getElementById('edit_harga_beli').value = data.harga_beli;
        document.getElementById('edit_harga_jual').value = data.harga_jual;
        document.getElementById('edit_stok').value = data.stok;
        document.getElementById('edit_satuan').value = data.satuan;
        document.getElementById('edit_kategori_id').value = data.kategori_id;
        document.getElementById('edit_status').value = data.status;

        // Preview Gambar
        const previewDiv = document.getElementById('edit_image_preview_container');
        const previewImg = document.getElementById('edit_image_preview_img');
        if (data.gambar_url) {
            previewImg.src = "{{ asset('storage') }}/" + data.gambar_url;
            previewDiv.classList.remove('hidden');
        } else {
            previewDiv.classList.add('hidden');
        }

        openModal('modalEdit');
    }

    // --- PREVIEW IMAGE ---
    function previewImage(event, imgId, containerId) {
        const input = event.target;
        const previewDiv = document.getElementById(containerId);
        const previewImg = document.getElementById(imgId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewDiv.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // --- SCANNER LOGIC ---
    let barcodeBuffer = "";
    let lastKeyTime = Date.now();
    document.addEventListener('keydown', function(e) {
        if (e.target.tagName === 'INPUT' && e.target.type !== 'text') return;
        
        const currentTime = Date.now();
        if (currentTime - lastKeyTime > 50) barcodeBuffer = "";
        lastKeyTime = currentTime;

        if (e.key === "Enter" && barcodeBuffer.length > 3) {
            // Jika ingin otomatis buka modal tambah saat scan di halaman utama:
            if(document.getElementById('modalAdd').classList.contains('hidden') && document.getElementById('modalEdit').classList.contains('hidden')) {
               // Optional: Logic untuk auto search atau auto open add modal
            }
            barcodeBuffer = "";
        } else if (e.key.length === 1) {
            barcodeBuffer += e.key;
        }
    });

    // --- TOAST NOTIFICATION ---
    @if(session('success'))
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = "bg-green-600 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3 transform transition-all duration-300 translate-x-10 opacity-0";
        toast.innerHTML = `<i class="ri-check-double-line text-xl"></i><span>{{ session('success') }}</span>`;
        container.appendChild(toast);
        requestAnimationFrame(() => { toast.classList.remove('translate-x-10', 'opacity-0'); });
        setTimeout(() => { toast.classList.add('opacity-0', 'translate-y-4'); setTimeout(() => toast.remove(), 300); }, 3000);
    @endif
</script>
@endsection