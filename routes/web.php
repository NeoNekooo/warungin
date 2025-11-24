<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\StokLogController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\TransaksiDetailController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\ManajemenAkunController;
use App\Http\Controllers\PromoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth', 'role:admin')->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard'); 
    })->name('admin.dashboard');

    Route::resource('transaksi', TransaksiController::class);
    Route::resource('pembayaran', PembayaranController::class);
    Route::resource('stok_log', StokLogController::class);
    Route::resource('kategori', KategoriController::class);
    Route::resource('transaksi_detail', TransaksiDetailController::class);
    Route::resource('produk', ProdukController::class);
    Route::resource('pelanggan', PelangganController::class);
    Route::resource('manajemen_akun', ManajemenAkunController::class);
    // Promo management
    Route::resource('promos', PromoController::class);

    // Midtrans checkout for a transaksi (admin may trigger a payment checkout)
    Route::get('/midtrans/checkout/{transaksi}', [MidtransController::class, 'checkout'])->name('midtrans.checkout');
    // Invoice view for transaksi (admin)
    Route::get('/transaksi/{transaksi}/invoice', [TransaksiController::class, 'invoice'])->name('transaksi.invoice');


});



Route::middleware('auth', 'role:kasir')->group(function () {
    Route::get('/kasir/dashboard', function () {
        return view('kasir.dashboard'); 
    })->name('kasir.dashboard');
});

// POS routes - accessible to any authenticated user
use App\Http\Controllers\PosController;
Route::middleware('auth')->group(function () {
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('/pos/search', [PosController::class, 'search'])->name('pos.search');
    Route::post('/pos/pay', [PosController::class, 'pay'])->name('pos.pay');
});

require __DIR__.'/auth.php';

// Public endpoint for Midtrans server notifications
Route::post('/midtrans/notification', [MidtransController::class, 'notification'])->name('midtrans.notification');
