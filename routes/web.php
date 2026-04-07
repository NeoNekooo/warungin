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
use App\Http\Controllers\PosController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\KasirDashboardController;
use App\Http\Controllers\AdminDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'kasir') {
            return redirect()->route('kasir.dashboard');
        } elseif ($user->role === 'owner') {
            return redirect()->route('owner.dashboard');
        }
        // Fallback for other roles or if role is not set
        return redirect()->route('profile.edit'); // Or a generic dashboard
    }
    return view('auth.login');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin & Owner Group
    Route::middleware('role:admin|owner')->group(function () {
        // Reports accessible to Admin and Owner
        Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pdf', [App\Http\Controllers\ReportController::class, 'exportPdf'])->name('reports.pdf');
        // Promo management accessible to Admin and Owner
        Route::resource('promos', PromoController::class);
        // Transaksi (Header) accessible to Admin and Owner
        Route::resource('transaksi', TransaksiController::class);
        // Kategori accessible to Admin and Owner
        Route::resource('kategori', KategoriController::class);
    });

    // Admin & Owner & Kasir Group
    Route::middleware('role:admin|owner|kasir')->group(function () {
        // Produk management accessible to Admin, Owner, and Kasir
        Route::resource('produk', ProdukController::class)->except(['show']);
        // Generate a new unique barcode string for product creation (AJAX)
        Route::get('/produk/generate-barcode', [ProdukController::class, 'generateBarcode'])->name('produk.generateBarcode');
        // Pelanggan accessible to Admin, Owner, and Kasir
        Route::resource('pelanggan', PelangganController::class);
        // Export Transaksi Detail (CSV)
        Route::get('/transaksi_detail/export', [TransaksiDetailController::class, 'export'])->name('transaksi_detail.export');
        // Transaksi Detail accessible to Admin, Owner, and Kasir
        Route::resource('transaksi_detail', TransaksiDetailController::class);
        // Stok Log accessible to Admin, Owner, and Kasir
        Route::resource('stok_log', StokLogController::class);
        // Pembayaran accessible to Admin, Owner, and Kasir (index/show only due to controller logic)
        Route::resource('pembayaran', PembayaranController::class)->only(['index', 'show']);
        // Invoice view for transaksi (admin, owner, kasir)
        Route::get('/transaksi/{transaksi}/invoice', [TransaksiController::class, 'invoice'])->name('transaksi.invoice');
        // Absensi user (admin, owner, kasir)
        // Di routes/web.php
        Route::group(['prefix' => 'absensi', 'as' => 'absensi.'], function () {
            // Route Utama (Halaman Scan)
            Route::get('/', [AbsensiController::class, 'index'])->name('index');
            Route::post('/store', [AbsensiController::class, 'store'])->name('store');

            // QR Code (only kasir should access)
            Route::get('/my-qr', function() {
                return view('absensi.my-qr');
            })->middleware('role:kasir')->name('myQr');

            // Laporan & Fitur AJAX
            Route::get('/laporan', [AbsensiController::class, 'laporan'])->name('laporan');
            Route::get('/export', [AbsensiController::class, 'exportExcel'])->name('export');
            Route::get('/latest-data', [AbsensiController::class, 'getLatestAbsensi'])->name('latest');

            // Proses Scan & Logic
            Route::post('/proses-scan', [AbsensiController::class, 'prosesScan'])->name('prosesScan');
            Route::post('/simpan', [AbsensiController::class, 'simpanAbsensi'])->name('simpan');

            // --- FITUR BARU: Input Manual & Jadwal ---
            // Simpan Absensi Manual (Izin/Sakit)
            Route::post('/store-manual', [AbsensiController::class, 'storeManual'])->name('storeManual');
            
            // Simpan Jadwal (Bisa ditaruh di Controller baru atau tetap di AbsensiController)
            Route::post('/jadwal-store', [AbsensiController::class, 'storeJadwal'])->name('jadwalStore');
        });
    });

    // Admin Specific Routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::post('/pembayaran/{pembayaran}/reconcile', [PembayaranController::class, 'reconcile'])->name('pembayaran.reconcile');
        Route::resource('manajemen_akun', ManajemenAkunController::class);
        // Generate a new unique barcode string for product creation (AJAX) // Moved to Admin & Owner | Kasir Group
        // Route::get('/produk/generate-barcode', [ProdukController::class, 'generateBarcode'])->name('produk.generateBarcode');
        // Midtrans checkout for a transaksi (admin may trigger a payment checkout)
        Route::get('/midtrans/checkout/{transaksi}', [MidtransController::class, 'checkout'])->name('midtrans.checkout');
    });

    // Kasir Specific Routes
    Route::middleware('role:kasir')->group(function () {
        Route::get('/kasir/dashboard', [KasirDashboardController::class, 'index'])->name('kasir.dashboard');

        // POS routes - accessible to Kasir
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::get('/pos/search', [PosController::class, 'search'])->name('pos.search');
        Route::post('/pos/pay', [PosController::class, 'pay'])->name('pos.pay');
    });

    // Owner specific dashboard
    Route::middleware('role:owner')->group(function () {
        Route::get('/owner/dashboard', [OwnerDashboardController::class, 'index'])->name('owner.dashboard');
    });
});

require __DIR__.'/auth.php';

// Public endpoint for Midtrans server notifications
Route::post('/midtrans/notification', [MidtransController::class, 'notification'])->name('midtrans.notification');
