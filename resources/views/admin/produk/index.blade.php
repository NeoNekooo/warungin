@extends('layouts.app')

@section('title', 'Daftar Produk')

@section('content')
<div class="space-y-8 relative min-h-screen"> {{-- Spacing lebih besar --}}
    
    <div id="toast-container" class="fixed bottom-5 right-5 z-[100] flex flex-col gap-2"></div>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-md mb-4 relative">
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

    <div class="bg-white p-6 rounded-2xl shadow-xl border border-gray-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-5">
        
        <div class="space-y-1">
            <h2 class="flex items-center text-3xl font-extrabold text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.0" stroke="currentColor" class="size-7 text-blue-500 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                </svg>
                Daftar Produk
            </h2>
            <div class="flex flex-wrap items-center text-sm text-gray-500">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                    <i class="ri-folder-open-line mr-1.5 text-sm"></i> Manajemen Data
                </span>
                <span class="mx-3 hidden sm:inline-block">•</span>
                <span class="flex items-center text-green-700 bg-green-50 px-3 py-1 rounded-full border border-green-200 text-xs font-semibold mt-1 sm:mt-0">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse mr-1.5"></span>
                    Total: {{ $produks->total() }} Produk
                </span>
            </div>
        </div>

        <div class="w-full md:w-auto flex justify-end">
            @hasanyrole(['admin','owner'])
            <button onclick="openModal('modalAdd')" class="group bg-blue-600 text-white px-6 py-3 rounded-xl transition-all shadow-md hover:bg-blue-700 hover:shadow-lg flex items-center font-semibold text-base w-full md:w-auto">
                <i class="ri-add-line mr-2 text-xl group-hover:rotate-90 transition-transform"></i> 
                Tambah Produk Baru
            </button>
            @endhasanyrole
        </div>
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
        <form action="{{ route('produk.index') }}" method="GET" class="relative w-full sm:w-96">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="ri-search-line text-gray-400 text-lg"></i>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" 
                class="block w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl leading-5 bg-white placeholder-gray-400 focus:outline-none focus:placeholder-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out shadow-sm" 
                placeholder="Cari nama produk atau scan barcode...">
        </form>
        {{-- Tombol Filter/Reset bisa ditambahkan di sini --}}
    </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

    @forelse($produks as $produk)
    <div class="group bg-white rounded-xl border border-gray-200 shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col sm:flex-row lg:flex-col">
        
        <!-- Gambar Produk -->
        <div class="relative bg-gray-50 w-full sm:w-1/3 lg:w-full aspect-square flex items-center justify-center overflow-hidden border-b lg:border-b-2 sm:border-b-0 sm:border-r border-gray-100">
            @if($produk->stok <= 5 && $produk->stok > 0)
                <div class="absolute top-0 right-0 bg-red-600 text-white rounded-bl-xl px-3 py-1.5 shadow-lg text-xs font-bold z-10 flex items-center animate-pulse tracking-wider">
                    <i class="ri-fire-fill mr-1"></i> SISA {{ $produk->stok }}
                </div>
            @elseif($produk->stok == 0)
                <div class="absolute top-0 right-0 bg-gray-700 text-white rounded-bl-xl px-3 py-1.5 shadow-lg text-xs font-bold z-10 flex items-center tracking-wider">
                    <i class="ri-close-circle-fill mr-1"></i> HABIS
                </div>
            @endif

            @if($produk->status == 'nonaktif')
                <div class="absolute top-2 left-2 bg-gray-800 text-white rounded-full px-2.5 py-1 shadow-md text-xs font-semibold z-10 opacity-90 tracking-wide">
                    Nonaktif
                </div>
            @endif

            @if($produk->gambar_url)
                <img src="{{ asset('storage/' . $produk->gambar_url) }}" alt="{{ $produk->nama_produk }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500 ease-in-out">
            @else
                <div class="flex flex-col items-center text-gray-300">
                    <i class="ri-image-off-line text-6xl mb-2 text-gray-400"></i>
                    <span class="text-sm font-medium text-gray-500">Gambar Tidak Tersedia</span>
                </div>
            @endif
        </div>

        <!-- Info Produk -->
        <div class="p-4 flex-1 flex flex-col justify-between">
            <div class="flex justify-between items-start mb-2">
                <span class="text-xs font-bold tracking-wider uppercase text-indigo-700 bg-indigo-50 px-3 py-1 rounded-full truncate max-w-[70%] shadow-sm">
                    {{ $produk->kategori->nama_kategori ?? 'UMUM' }}
                </span>
                <div class="flex items-center text-gray-500 text-xs font-medium mt-0.5" title="Stok Saat Ini">
                    <i class="ri-inbox-fill mr-1 text-base"></i> Stok: 
                    <span class="ml-1 font-extrabold {{ $produk->stok <= 5 ? 'text-red-600' : 'text-gray-700' }}">{{ $produk->stok }}</span>
                </div>
            </div>

            <h3 class="font-bold text-gray-900 text-lg leading-tight mb-3 line-clamp-2" title="{{ $produk->nama_produk }}">
                {{ $produk->nama_produk }}
            </h3>
            
            <div class="pt-2 border-t border-dashed border-gray-200 mt-auto">
                <p class="text-xs text-gray-500 mb-1 font-medium">Harga Jual / Satuan ({{ $produk->satuan ?? 'Pcs' }})</p>
                <div class="flex justify-between items-end">
                    <p class="text-xl sm:text-2xl text-blue-700 font-extrabold leading-tight">
                        Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}
                    </p>
                    <span class="bg-gray-100 text-gray-600 text-[11px] font-mono px-2 py-0.5 rounded-lg border border-gray-200 shadow-inner" title="Kode Barcode">
                        {{ $produk->kode_barcode ?? 'N/A' }}
                    </span>
                </div>
            </div>

            @hasanyrole(['admin','owner'])
            <div class="mt-3 grid grid-cols-2 divide-x divide-gray-200 border-t border-gray-200 bg-gray-50/50">
                <button onclick='openEditModal(@json($produk))' class="py-2 text-center text-sm font-semibold text-gray-700 hover:text-indigo-700 hover:bg-indigo-50 transition-colors flex items-center justify-center w-full focus:outline-none">
                    <i class="ri-pencil-line mr-1.5"></i> Edit
                </button>
                
                <form action="{{ route('produk.destroy', $produk->produk_id) }}" method="POST" class="delete-form w-full" data-produk-name="{{ $produk->nama_produk }}">
                    @csrf
                    @method('DELETE')
                    <button type="button" data-action="open-delete-modal" class="w-full py-2 text-center text-sm font-semibold text-gray-700 hover:text-red-700 hover:bg-red-50 transition-colors flex items-center justify-center focus:outline-none">
                        <i class="ri-delete-bin-line mr-1.5"></i> Hapus
                    </button>
                </form>
            </div>
            @endhasanyrole
        </div>
    </div>
    @empty
        <div class="col-span-full py-16 flex flex-col items-center justify-center text-center bg-white rounded-xl border border-dashed border-gray-300 shadow-lg">
            <!-- Empty state -->
        </div>
    @endforelse

</div>


    <div class="mt-8 flex justify-center">
        {{ $produks->links() }}
    </div>

</div> 

<div id="modalAdd" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="backdropAdd" onclick="closeModal('modalAdd')"></div>
    
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div id="panelAdd" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl opacity-0 scale-95 border border-gray-100">
                
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="ri-add-circle-fill mr-2 text-xl text-blue-200"></i> Tambah Produk Baru
                    </h3>
                    <button onclick="closeModal('modalAdd')" class="text-blue-200 hover:text-white hover:bg-blue-600/50 rounded-full p-1 transition focus:outline-none">
                        <i class="ri-close-line text-2xl"></i>
                    </button>
                </div>

                <form action="{{ route('produk.store') }}" method="POST" class="h-auto max-h-[70vh] overflow-y-auto" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="bg-white px-6 py-6 space-y-6">
                        <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                            <label class="block text-blue-800 text-sm font-bold mb-2">Barcode / Kode Produk</label>
                            <div class="flex gap-2 items-center">
                                <input type="text" id="add_kode_barcode" name="kode_barcode" class="flex-1 px-4 py-2.5 border border-blue-200 rounded-xl font-mono focus:ring-blue-500 focus:border-blue-500" placeholder="Scan kode..." autocomplete="off">
                                <button type="button" id="btn_generate_barcode" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-blue-200 bg-white hover:bg-blue-50 text-sm font-semibold text-blue-700">
                                    <i class="ri-refresh-line"></i> Generate
                                </button>
                            </div>
                            <div id="add_barcode_preview" class="mt-2 hidden text-center">
                                <svg id="add_barcode_svg"></svg>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="col-span-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Produk</label>
                                <input type="text" name="nama_produk" class="form-input w-full px-4 py-2.5 rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                                <select name="kategori_id" class="form-select w-full px-4 py-2.5 rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($kategoris as $kategori)
                                        <option value="{{ $kategori->kategori_id }}">{{ $kategori->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Satuan</label>
                                <select name="satuan" class="form-select w-full px-4 py-2.5 rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="pcs">Pcs</option>
                                    <option value="pack">Pack</option>
                                    <option value="kardus">Kardus</option>
                                    <option value="kg">Kg</option>
                                    <option value="liter">Liter</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Harga Beli</label>
                                <input type="number" name="harga_beli" class="form-input w-full px-4 py-2.5 rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2 text-blue-600">Harga Jual</label>
                                <input type="number" name="harga_jual" class="form-input w-full px-4 py-2.5 rounded-xl border-blue-200 bg-blue-50/50 font-bold text-gray-800 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Stok</label>
                                <input type="number" name="stok" class="form-input w-full px-4 py-2.5 rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500" value="0">
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                                <select name="status" class="form-select w-full px-4 py-2.5 rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
                                </select>
                            </div>

                            <div class="col-span-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Gambar</label>
                                <input type="file" name="gambar" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" onchange="previewImage(event, 'add_preview_img', 'add_preview_div')">
                                <div id="add_preview_div" class="hidden mt-2 h-32 w-32 rounded-xl border border-gray-200 p-1 bg-white shadow-sm">
                                    <img id="add_preview_img" src="#" class="h-full w-full rounded-lg object-cover">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-all shadow-md">
                            <i class="ri-save-line mr-2"></i> Simpan
                        </button>
                        <button type="button" onclick="closeModal('modalAdd')" class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-white text-gray-700 font-medium rounded-xl border border-gray-300 hover:bg-gray-50">
                            Batal
                        </button>
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
            <div id="panelEdit" class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl opacity-0 scale-95 border border-gray-100">
                
                <div class="bg-gradient-to-r from-indigo-600 to-purple-700 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="ri-edit-box-line mr-2 text-xl text-indigo-200"></i> Edit Produk
                    </h3>
                    <button onclick="closeModal('modalEdit')" class="text-indigo-200 hover:text-white hover:bg-indigo-600/50 rounded-full p-1 transition focus:outline-none">
                        <i class="ri-close-line text-2xl"></i>
                    </button>
                </div>

                <form id="formEdit" action="" method="POST" class="h-auto max-h-[70vh] overflow-y-auto" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="bg-white px-6 py-6 space-y-6">
                        <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100">
                            <label class="block text-indigo-800 text-sm font-bold mb-2">Barcode / Kode Produk</label>
                            <div class="flex gap-2 items-center">
                                <input type="text" name="kode_barcode" id="edit_kode_barcode" class="flex-1 px-4 py-2.5 border border-indigo-200 rounded-xl font-mono focus:ring-indigo-500 focus:border-indigo-500">
                                <button type="button" id="btn_generate_edit_barcode" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-indigo-200 bg-white hover:bg-indigo-50 text-sm font-semibold text-indigo-700">
                                    <i class="ri-refresh-line"></i> Generate
                                </button>
                            </div>
                            <div id="edit_barcode_preview" class="mt-2 hidden text-center">
                                <svg id="edit_barcode_svg"></svg>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="col-span-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Produk</label>
                                <input type="text" name="nama_produk" id="edit_nama_produk" class="form-input w-full px-4 py-2.5 rounded-xl border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" required>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                                <select name="kategori_id" id="edit_kategori_id" class="form-select w-full px-4 py-2.5 rounded-xl border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($kategoris as $kategori)
                                        <option value="{{ $kategori->kategori_id }}">{{ $kategori->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Satuan</label>
                                <select name="satuan" id="edit_satuan" class="form-select w-full px-4 py-2.5 rounded-xl border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="pcs">Pcs</option>
                                    <option value="pack">Pack</option>
                                    <option value="kardus">Kardus</option>
                                    <option value="kg">Kg</option>
                                    <option value="liter">Liter</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Harga Beli</label>
                                <input type="number" name="harga_beli" id="edit_harga_beli" class="form-input w-full px-4 py-2.5 rounded-xl border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" required>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2 text-indigo-600">Harga Jual</label>
                                <input type="number" name="harga_jual" id="edit_harga_jual" class="form-input w-full px-4 py-2.5 rounded-xl border-indigo-200 bg-indigo-50/50 font-bold text-gray-800 focus:ring-indigo-500 focus:border-indigo-500" required>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Stok</label>
                                <input type="number" name="stok" id="edit_stok" class="form-input w-full px-4 py-2.5 rounded-xl border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                                <select name="status" id="edit_status" class="form-select w-full px-4 py-2.5 rounded-xl border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
                                </select>
                            </div>

                            <div class="col-span-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Ganti Gambar (Opsional)</label>
                                <input type="file" name="gambar" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" onchange="previewImage(event, 'edit_image_preview_img', 'edit_image_preview_container')">
                                
                                <div id="edit_image_preview_container" class="hidden mt-3 h-32 w-32 rounded-xl border border-gray-200 p-1 bg-white shadow-sm">
                                    <img id="edit_image_preview_img" src="#" class="h-full w-full rounded-lg object-cover">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition-all shadow-md">
                            <i class="ri-save-line mr-2"></i> Update
                        </button>
                        <button type="button" onclick="closeModal('modalEdit')" class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-white text-gray-700 font-medium rounded-xl border border-gray-300 hover:bg-gray-50">
                            Batal
                        </button>
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
                
                <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Produk?</h3>
                <p class="text-sm text-gray-500 mb-6">
                    Anda akan menghapus produk <span id="deleteProductName" class="font-bold text-gray-800 bg-red-50 px-2 py-0.5 rounded-lg border border-red-200">Nama Produk</span>. 
                    <br>Tindakan ini tidak dapat dibatalkan.
                </p>

                <div class="flex justify-center gap-3">
                    <button type="button" onclick="closeModal('modalDelete')" class="w-full bg-white border border-gray-300 text-gray-700 px-5 py-2.5 rounded-xl hover:bg-gray-50 font-medium transition">Batal</button>
                    <button id="deleteConfirmBtn" type="button" class="w-full bg-red-600 text-white px-5 py-2.5 rounded-xl hover:bg-red-700 font-medium shadow-lg shadow-red-200 transition">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
    // --- FUNGSI UMUM MODAL ---
    function openModal(id) {
        const modal = document.getElementById(id);
        const backdrop = modal.querySelector('div[id^="backdrop"]');
        const panel = modal.querySelector('div[id^="panel"]');
        
        modal.classList.remove('hidden');
        
        // Animasi Masuk (Scale up & Fade in)
        requestAnimationFrame(() => {
            if(backdrop) backdrop.classList.remove('opacity-0');
            if(panel) {
                panel.classList.remove('opacity-0', 'scale-95');
                panel.classList.add('opacity-100', 'scale-100');
            }
        });

        // Auto focus
        if(id === 'modalAdd') setTimeout(() => document.querySelector('#modalAdd input[name="nama_produk"]').focus(), 200);
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
            if(modal) modal.classList.add('hidden');
        }, 300);
    }

    // --- TOAST NOTIFICATION ---
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

    // Tampilkan toast jika ada session success
    @if(session('success')) showToast("{{ session('success') }}"); @endif
    
    // --- BARCODE GENERATOR ---
    function generateBarcode(code, svgId, previewDivId) {
        if (code && code.length > 5) {
            try {
                JsBarcode(`#${svgId}`, code, {
                    displayValue: true,
                    text: code,
                    fontSize: 14,
                    margin: 0,
                    width: 1.5,
                    height: 50
                });
                document.getElementById(previewDivId).classList.remove('hidden');
            } catch (e) {
                console.error("Failed to generate barcode:", e);
                document.getElementById(previewDivId).classList.add('hidden');
            }
        } else {
            document.getElementById(previewDivId).classList.add('hidden');
        }
    }

    function generateRandomBarcode() {
        // Generate kode 13 digit acak
        return Math.floor(100000000000 + Math.random() * 900000000000).toString();
    }

    // Logic untuk Modal Tambah Barcode
    document.getElementById('btn_generate_barcode').addEventListener('click', function() {
        const code = generateRandomBarcode();
        document.getElementById('add_kode_barcode').value = code;
        generateBarcode(code, 'add_barcode_svg', 'add_barcode_preview');
    });

    document.getElementById('add_kode_barcode').addEventListener('input', function() {
        generateBarcode(this.value, 'add_barcode_svg', 'add_barcode_preview');
    });

    // Logic untuk Modal Edit Barcode
    document.getElementById('btn_generate_edit_barcode').addEventListener('click', function() {
        const code = generateRandomBarcode();
        document.getElementById('edit_kode_barcode').value = code;
        generateBarcode(code, 'edit_barcode_svg', 'edit_barcode_preview');
    });

    document.getElementById('edit_kode_barcode').addEventListener('input', function() {
        generateBarcode(this.value, 'edit_barcode_svg', 'edit_barcode_preview');
    });

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
        } else {
            // Jika file dihapus/dibatalkan, sembunyikan preview
            previewDiv.classList.add('hidden');
            previewImg.src = '';
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

        // Preview Gambar Lama
        const previewDiv = document.getElementById('edit_image_preview_container');
        const previewImg = document.getElementById('edit_image_preview_img');
        if (data.gambar_url) {
            previewImg.src = "{{ asset('storage') }}/" + data.gambar_url;
            previewDiv.classList.remove('hidden');
        } else {
            previewDiv.classList.add('hidden');
            previewImg.src = '';
        }

        // Preview Barcode
        generateBarcode(data.kode_barcode, 'edit_barcode_svg', 'edit_barcode_preview');

        openModal('modalEdit');
    }

    // --- DELETE CONFIRMATION MODAL HANDLERS ---
    let _pendingDeleteForm = null;

    // Delegate clicks for buttons that open the delete modal
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-action="open-delete-modal"]');
        if (!btn) return;
        e.preventDefault();

        const form = btn.closest('form.delete-form');
        if (!form) return;

        _pendingDeleteForm = form;
        const name = form.dataset.produkName || 'Produk';
        const el = document.getElementById('deleteProductName');
        if (el) el.textContent = name;

        openModal('modalDelete');
    });

    // Confirm deletion -> submit the stored form
    const deleteConfirmBtn = document.getElementById('deleteConfirmBtn');
    if (deleteConfirmBtn) {
        deleteConfirmBtn.addEventListener('click', function () {
            if (!_pendingDeleteForm) return closeModal('modalDelete');
            _pendingDeleteForm.submit();
        });
    }

</script>
@endsection