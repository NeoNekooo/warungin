@extends('layouts.app')

@section('content')
@php
    $isProduction = config('services.midtrans.is_production');
    $clientKey = config('services.midtrans.client_key');
    $snapUrl = $isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js';
@endphp

<div class="flex h-[calc(100vh-theme(spacing.16))] bg-gray-100 overflow-hidden font-sans">
    
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <div class="bg-white border-b px-6 py-4 flex items-center justify-between shrink-0 z-10 shadow-sm">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Warungin POS</h1>
                <p class="text-xs text-gray-500">Kasir: {{ auth()->user()->name ?? 'Admin' }}</p>
            </div>
            
            <div class="relative w-full max-w-lg mx-4">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input id="search" type="text" placeholder="Cari Produk / Scan Barcode (F2)" 
                    class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow bg-gray-50 focus:bg-white" autofocus>
                <button id="search-clear" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 hidden">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.dashboard') }}" class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100 transition" title="Dashboard">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </a>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-6 scroll-smooth bg-gray-100" id="main-scroll">
            <div class="grid grid-cols-12 gap-6">
                
                <div class="col-span-12 xl:col-span-3 lg:col-span-4 order-2 lg:order-1">
                    @php $promos = isset($promos) ? $promos : collect(); @endphp
                    @if($promos->isNotEmpty())
                        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 h-full">
                            <div class="space-y-4">
                                <h3 class="font-bold text-gray-700 flex items-center gap-2 pb-2 border-b border-gray-100">
                                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                    Promo Tersedia
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-3 max-h-[500px] overflow-y-auto pr-1">
                                    @foreach($promos as $promo)
                                        @php
                                            $pDiscount = $promo->discount ?? ($promo->diskon ?? null);
                                            $pPercent = $promo->percent ?? ($promo->percentage ?? null);
                                        @endphp
                                        <div class="promo-card group relative bg-orange-50/50 border border-dashed border-orange-300 rounded-lg p-4 cursor-pointer hover:bg-orange-50 hover:border-orange-500 transition-all shadow-sm"
                                             data-promo-id="{{ $promo->id }}" 
                                             data-name="{{ $promo->name ?? 'Promo' }}" 
                                             data-discount="{{ $pDiscount }}" 
                                             data-percent="{{ $pPercent }}">
                                            
                                            <div class="absolute -left-2 top-1/2 -mt-2 w-4 h-4 bg-white rounded-full border-r border-orange-200"></div>
                                            <div class="absolute -right-2 top-1/2 -mt-2 w-4 h-4 bg-white rounded-full border-l border-orange-200"></div>

                                            <div class="flex justify-between items-start pl-2">
                                                <div>
                                                    <h4 class="font-bold text-gray-800 text-sm group-hover:text-orange-600">{{ $promo->name }}</h4>
                                                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $promo->description }}</p>
                                                </div>
                                                <div class="bg-orange-100 text-orange-700 text-xs font-bold px-2 py-1 rounded">
                                                    @if($pPercent) {{ $pPercent }}% @else Rp{{ number_format($pDiscount/1000) }}k @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-span-12 xl:col-span-9 lg:col-span-8 order-1 lg:order-2">
                    <div id="loading-skeleton" class="hidden grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
                         @for($i=0; $i<8; $i++)
                            <div class="bg-white rounded-xl p-4 shadow-sm animate-pulse h-64">
                                <div class="bg-gray-200 h-32 w-full rounded-lg mb-4"></div>
                                <div class="bg-gray-200 h-4 w-3/4 rounded mb-2"></div>
                                <div class="bg-gray-200 h-4 w-1/2 rounded"></div>
                            </div>
                         @endfor
                    </div>

                    <div id="product-list" class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
                        </div>
                    
                    <div id="empty-state" class="hidden text-center py-20">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-200 mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Produk tidak ditemukan</h3>
                        <p class="text-gray-500">Coba kata kunci lain.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <aside class="w-96 bg-white border-l shadow-xl flex flex-col shrink-0 z-20 h-full">
        <div class="p-5 border-b flex justify-between items-center bg-gray-50">
            <div>
                <h2 class="font-bold text-lg text-gray-800">Keranjang</h2>
                <span class="text-xs text-gray-500 font-mono">ID: #{{ date('ymdHi') }}</span>
            </div>
            <button id="clear-cart" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded transition" title="Kosongkan">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-1" id="cart-items">
            </div>

        <div class="p-5 bg-gray-50 border-t space-y-3 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
            
            <div id="promo-badge" class="hidden flex justify-between items-center bg-green-100 border border-green-200 text-green-800 px-3 py-2 rounded text-sm">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span id="applied-promo-name">Promo</span>
                </span>
                <button id="clear-promo" class="text-xs hover:underline text-green-700 font-bold">Hapus</button>
            </div>

            <div class="space-y-1 text-sm text-gray-600">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span id="subtotal" class="font-mono">Rp 0</span>
                </div>
                <div class="flex justify-between text-green-600">
                    <span>Diskon</span>
                    <span id="diskon" class="font-mono">- Rp 0</span>
                </div>
                <div class="flex justify-between">
                    <span>Pajak (PPN)</span> 
                    <span id="pajak" class="font-mono">Rp 0</span>
                </div>
            </div>

            <div class="border-t border-dashed border-gray-300 pt-3">
                <div class="flex justify-between items-end">
                    <span class="font-bold text-gray-800">Total</span>
                    <span id="total" class="text-2xl font-extrabold text-indigo-700 font-mono">Rp 0</span>
                </div>
            </div>

            <div class="mt-4">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Metode Bayar</label>
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="pay_method" value="tunai" class="peer sr-only" checked>
                        <div class="text-center py-2 border rounded peer-checked:bg-indigo-50 peer-checked:border-indigo-500 peer-checked:text-indigo-700 hover:bg-gray-50 transition text-sm font-medium">
                            💵 Tunai
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="pay_method" value="qris" class="peer sr-only">
                        <div class="text-center py-2 border rounded peer-checked:bg-indigo-50 peer-checked:border-indigo-500 peer-checked:text-indigo-700 hover:bg-gray-50 transition text-sm font-medium">
                            📱 QRIS
                        </div>
                    </label>
                </div>
                
                <button id="btn-bayar" class="w-full bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold py-3.5 px-4 rounded-lg shadow-lg hover:shadow-xl transition-all flex justify-center items-center gap-2 group disabled:opacity-50 disabled:cursor-not-allowed">
                    <span>Proses Pembayaran</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </button>
            </div>
        </div>
    </aside>
</div>

<div id="toast" class="fixed bottom-10 left-1/2 transform -translate-x-1/2 z-50 hidden transition-all duration-300 pointer-events-none">
    <div id="toast-body" class="bg-gray-800 text-white px-6 py-3 rounded-full shadow-2xl flex items-center gap-3">
        <span id="toast-icon"></span>
        <span id="toast-msg" class="font-medium text-sm"></span>
    </div>
</div>

<script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    
    // --- STATE ---
    const state = {
        products: [],
        cart: [],
        selectedPromo: null,
        loading: false
    };

    // --- DOM ELEMENTS ---
    const els = {
        productList: document.getElementById('product-list'),
        loadingInfo: document.getElementById('loading-skeleton'),
        emptyState: document.getElementById('empty-state'),
        cartItems: document.getElementById('cart-items'),
        subtotal: document.getElementById('subtotal'),
        diskon: document.getElementById('diskon'),
        pajak: document.getElementById('pajak'),
        total: document.getElementById('total'),
        btnBayar: document.getElementById('btn-bayar'),
        searchInput: document.getElementById('search'),
        searchClear: document.getElementById('search-clear'),
        toast: document.getElementById('toast'),
        toastBody: document.getElementById('toast-body'),
        toastMsg: document.getElementById('toast-msg'),
        promoBadge: document.getElementById('promo-badge'),
        appliedPromoName: document.getElementById('applied-promo-name'),
        payRadios: document.getElementsByName('pay_method')
    };

    // --- HELPERS ---
    const formatRp = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n);
    
    const showToast = (message, type = 'success') => {
        if(!els.toast) return;
        els.toastMsg.textContent = message;
        if(type === 'success') {
            els.toastBody.className = 'bg-gray-800 text-white px-6 py-3 rounded-full shadow-2xl flex items-center gap-3 border border-green-500';
            els.toastBody.innerHTML = `<svg class="w-5 h-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> ${message}`;
        } else {
            els.toastBody.className = 'bg-gray-800 text-white px-6 py-3 rounded-full shadow-2xl flex items-center gap-3 border border-red-500';
            els.toastBody.innerHTML = `<svg class="w-5 h-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> ${message}`;
        }
        els.toast.classList.remove('hidden');
        // Auto hide
        setTimeout(() => els.toast.classList.add('hidden'), 3000);
    };

    // --- LOAD & RENDER PRODUCTS ---
    async function loadProducts(q = '') {
        state.loading = true;
        els.productList.innerHTML = '';
        els.productList.classList.add('hidden');
        els.loadingInfo.classList.remove('hidden');
        els.emptyState.classList.add('hidden');

        // Toggle clear button
        if(q.length > 0) els.searchClear.classList.remove('hidden');
        else els.searchClear.classList.add('hidden');

        try {
            const res = await fetch(`{{ route('pos.search') }}?q=${encodeURIComponent(q)}`);
            if(!res.ok) throw new Error('Network error');
            state.products = await res.json();
            renderProducts();
        } catch (error) {
            console.error(error);
            showToast('Gagal memuat produk', 'error');
        } finally {
            state.loading = false;
            els.loadingInfo.classList.add('hidden');
            if(state.products.length === 0) els.emptyState.classList.remove('hidden');
            else els.productList.classList.remove('hidden');
        }
    }

    function renderProducts() {
        els.productList.innerHTML = state.products.map(p => {
            const img = p.gambar_url || 'https://via.placeholder.com/150?text=No+Image';
            // Perhatikan: Kita pakai data-id, BUKAN onclick di HTML string ini
            return `
            <div class="product-card group bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col h-full cursor-pointer relative" 
                 data-id="${p.produk_id}">
                <div class="h-32 bg-gray-100 relative overflow-hidden">
                    <img src="${img}" alt="${p.nama_produk}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-2 right-2 bg-white/90 backdrop-blur text-xs font-bold px-2 py-1 rounded-md shadow-sm">
                        Stok: ${p.stok}
                    </div>
                </div>
                <div class="p-4 flex flex-col flex-1">
                    <h3 class="font-bold text-gray-800 text-sm mb-1 leading-tight line-clamp-2 pointer-events-none">${p.nama_produk}</h3>
                    <div class="mt-auto flex items-center justify-between pt-2 pointer-events-none">
                        <span class="text-indigo-600 font-extrabold text-base">${formatRp(p.harga_jual)}</span>
                        <div class="bg-indigo-50 text-indigo-600 p-1.5 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
            `;
        }).join('');
    }

    // --- ADD ITEM (EVENT DELEGATION) ---
    // Ini solusi agar "Add Item" selalu jalan walaupun elemen baru dibuat
    els.productList.addEventListener('click', (e) => {
        const card = e.target.closest('.product-card');
        if (card) {
            const id = card.dataset.id;
            const product = state.products.find(p => p.produk_id == id);
            if(product) addToCart(product);
        }
    });

    function addToCart(p, qty = 1) {
        if(p.stok <= 0) {
            showToast('Stok habis!', 'error');
            return;
        }

        const existing = state.cart.find(c => c.produk_id == p.produk_id);
        if (existing) {
            if(existing.jumlah >= p.stok) {
                showToast('Stok maksimal tercapai', 'error');
                return;
            }
            existing.jumlah += qty;
        } else {
            state.cart.push({
                produk_id: p.produk_id,
                nama: p.nama_produk,
                harga: p.harga_jual,
                jumlah: qty,
                stok_max: p.stok
            });
        }
        renderCart();
    }

    // --- CART RENDER & ACTIONS ---
    function renderCart() {
        if(state.cart.length === 0) {
            els.cartItems.innerHTML = `
                <div class="h-full flex flex-col items-center justify-center text-gray-400 opacity-60">
                    <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span class="text-sm">Keranjang Kosong</span>
                </div>`;
            els.btnBayar.disabled = true;
            els.btnBayar.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            els.cartItems.innerHTML = state.cart.map((c, i) => `
                <div class="flex items-center justify-between p-3 bg-white border border-gray-100 rounded-lg shadow-sm mb-2 group hover:border-indigo-200 transition">
                    <div class="flex-1 min-w-0 pr-3">
                        <div class="font-semibold text-gray-800 text-sm truncate">${c.nama}</div>
                        <div class="text-xs text-gray-500">${formatRp(c.harga)}</div>
                    </div>
                    <div class="flex items-center bg-gray-100 rounded-lg p-1 gap-1">
                        <button data-action="dec" data-index="${i}" class="cart-action w-6 h-6 flex justify-center items-center bg-white rounded shadow text-gray-600 hover:text-red-500 font-bold text-xs">-</button>
                        <span class="w-8 text-center text-sm font-bold text-gray-700">${c.jumlah}</span>
                        <button data-action="inc" data-index="${i}" class="cart-action w-6 h-6 flex justify-center items-center bg-white rounded shadow text-gray-600 hover:text-indigo-600 font-bold text-xs">+</button>
                    </div>
                </div>
            `).join('');
            els.btnBayar.disabled = false;
            els.btnBayar.classList.remove('opacity-50', 'cursor-not-allowed');
        }
        calculateTotals();
    }

    // Event Delegation untuk tombol Cart (+ / -)
    els.cartItems.addEventListener('click', (e) => {
        const btn = e.target.closest('.cart-action');
        if(!btn) return;

        const index = parseInt(btn.dataset.index);
        const action = btn.dataset.action;
        const item = state.cart[index];

        if(action === 'inc') {
            if(item.jumlah < item.stok_max) item.jumlah++;
            else showToast('Stok maksimal', 'error');
        } else if(action === 'dec') {
            if(item.jumlah > 1) item.jumlah--;
            else state.cart.splice(index, 1); // Hapus item
        }
        renderCart();
    });

    function calculateTotals() {
        let subtotal = state.cart.reduce((sum, item) => sum + (item.harga * item.jumlah), 0);
        let discount = 0;

        if (state.selectedPromo) {
            const p = state.selectedPromo;
            if (p.percent) discount = Math.round((p.percent / 100) * subtotal);
            else if (p.discount) discount = Number(p.discount);
            
            if(discount > subtotal) discount = subtotal;
        }

        const taxable = subtotal - discount;
        const tax = 0; // Set logic PPN di sini jika perlu
        const total = taxable + tax;

        els.subtotal.textContent = formatRp(subtotal);
        els.diskon.textContent = discount > 0 ? `- ${formatRp(discount)}` : formatRp(0);
        els.pajak.textContent = formatRp(tax);
        els.total.textContent = formatRp(total);

        // Promo Badge Update
        if(state.selectedPromo) {
            els.promoBadge.classList.remove('hidden');
            els.promoBadge.classList.add('flex');
            els.appliedPromoName.textContent = state.selectedPromo.name;
        } else {
            els.promoBadge.classList.add('hidden');
            els.promoBadge.classList.remove('flex');
        }
    }

    // --- SEARCH & CLEAR ---
    let searchTimeout;
    els.searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => loadProducts(e.target.value), 400);
    });

    els.searchClear.addEventListener('click', () => {
        els.searchInput.value = '';
        els.searchInput.focus();
        loadProducts('');
    });

    document.getElementById('clear-cart').addEventListener('click', () => {
        if(state.cart.length > 0) {
            // Use global promise-based confirm dialog
            confirmDialog('Kosongkan keranjang?').then(ok => {
                if(!ok) return;
                state.cart = [];
                state.selectedPromo = null;
                renderCart();
                document.querySelectorAll('.promo-card').forEach(c => c.classList.remove('ring-2', 'ring-orange-500', 'bg-orange-50'));
                showToast('Keranjang dikosongkan');
            });
        }
    });

    // --- PROMO SELECTION ---
    document.querySelectorAll('.promo-card').forEach(card => {
        card.addEventListener('click', function() {
            const id = this.dataset.promoId;
            if (state.selectedPromo && state.selectedPromo.id == id) {
                // Unselect
                state.selectedPromo = null;
                this.classList.remove('ring-2', 'ring-orange-500', 'bg-orange-50');
                showToast('Promo dihapus');
            } else {
                // Select
                document.querySelectorAll('.promo-card').forEach(c => c.classList.remove('ring-2', 'ring-orange-500', 'bg-orange-50'));
                this.classList.add('ring-2', 'ring-orange-500', 'bg-orange-50');
                state.selectedPromo = {
                    id: id,
                    name: this.dataset.name,
                    discount: this.dataset.discount,
                    percent: this.dataset.percent
                };
                showToast('Promo dipakai');
            }
            calculateTotals();
        });
    });

    document.getElementById('clear-promo').addEventListener('click', () => {
        state.selectedPromo = null;
        document.querySelectorAll('.promo-card').forEach(c => c.classList.remove('ring-2', 'ring-orange-500', 'bg-orange-50'));
        calculateTotals();
    });

    // --- PAYMENT LOGIC ---
    let originalText = '';
    els.btnBayar.addEventListener('click', async () => {
        if(state.cart.length === 0) return;

        // Ambil metode bayar
        let selectedMethod = 'tunai';
        for(const radio of els.payRadios) {
            if(radio.checked) selectedMethod = radio.value;
        }

        const payload = {
            items: state.cart,
            metode_bayar: selectedMethod,
            promo_id: state.selectedPromo ? state.selectedPromo.id : null,
            total_bayar: 0 // Backend calculation is safer
        };

        originalText = els.btnBayar.innerHTML;
        els.btnBayar.disabled = true;
        els.btnBayar.innerHTML = `<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memproses...`;

        try {
            const res = await fetch('{{ route('pos.pay') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            if (!data.success) throw new Error(data.message || 'Transaksi Gagal');

            if (selectedMethod === 'tunai') {
                showToast('Pembayaran Tunai Berhasil!');
                if(data.invoice_url) window.open(data.invoice_url, '_blank');
                resetPos();
            } else {
                // MIDTRANS HANDLING
                if (typeof window.snap === 'undefined') {
                    throw new Error('Midtrans Snap JS tidak terload. Periksa koneksi atau Client Key.');
                }
                
                window.snap.pay(data.snap_token, {
                    onSuccess: function(result){
                        showToast('Pembayaran Lunas!');
                        if(data.invoice_url) window.open(data.invoice_url, '_blank');
                        resetPos();
                    },
                    onPending: function(result){
                        showToast('Menunggu Pembayaran...', 'info');
                        if(data.invoice_url) window.open(data.invoice_url, '_blank');
                        resetPos();
                    },
                    onError: function(result){
                        showToast('Pembayaran Gagal/Dibatalkan', 'error');
                    },
                    onClose: function(){
                        els.btnBayar.disabled = false;
                        els.btnBayar.innerHTML = originalText;
                    }
                });
            }
        } catch (error) {
            console.error(error);
            showToast(error.message, 'error');
            els.btnBayar.disabled = false;
            els.btnBayar.innerHTML = originalText;
        }
    });

    function resetPos() {
        state.cart = [];
        state.selectedPromo = null;
        renderCart();
        loadProducts(); // Refresh stok
        document.querySelectorAll('.promo-card').forEach(c => c.classList.remove('ring-2', 'ring-orange-500', 'bg-orange-50'));
        els.btnBayar.disabled = false;
        els.btnBayar.innerHTML = originalText;
    }

    // Keyboard F2 Shortcut
    document.addEventListener('keydown', (e) => {
        if(e.key === 'F2') {
            e.preventDefault();
            els.searchInput.focus();
        }
    });

    // Initial Load
    loadProducts();
});
</script>
@endsection