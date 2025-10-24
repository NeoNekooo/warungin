<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KasirAuthController;
use App\Http\Middleware\EnsureUserIsKasir; 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/Route::prefix('kasir')->group(function () {
    // ... Rute Login dan Register lainnya ...

    // PASTIKAN BARIS LOGOUT INI SUDAH BENAR:
    Route::post('/logout', [KasirAuthController::class, 'logout'])
        ->name('kasir.logout'); // <-- NAMA INI HILANG ATAU SALAH
});

Route::get('/', function () {
    return view('kasir.login'); // Rute halaman utama
});

// Rute dashboard (dibuat oleh Breeze)
// Pastikan rute dashboard yang baru Anda edit tadi ada.
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// routes/web.php

// RUTE FORM LOGIN SEDERHANA UNTUK KASIR
Route::get('/kasir-login', function () {
    return view('kasir.login');
})->name('kasir.login.form');

// RUTE UNTUK PROSES LOGIN (Sementaran, nanti kita arahkan ke Controller)
Route::post('/kasir-login', function () {
    // Logika otentikasi akan ditambahkan di sini
    // Untuk saat ini, kita kembalikan ke halaman form
    return redirect()->back()->withInput();
})->name('kasir.login.submit');

// ... rute lain di bawah ini ...
// ... Rute login sederhana yang sudah ada ...
Route::get('/kasir-login', function () {
    return view('kasir.login');
})->name('kasir.login.form');

Route::post('/kasir-login', function () {
    // Akan diganti dengan Controller
    return redirect()->back()->withInput();
})->name('kasir.login.submit');
// ...

// RUTE FORM REGISTER SEDERHANA UNTUK KASIR BARU
Route::get('/kasir-register', function () {
    return view('kasir.register');
})->name('kasir.register.form');

Route::post('/kasir-register', function () {
    // Akan diganti dengan Controller
    return redirect()->back()->withInput();
})->name('kasir.register.submit');  
// MENGIMPOR SEMUA RUTE LOGIN/REGISTER DARI BREEZE
// RUTE FORM LOGIN SEDERHANA UNTUK KASIR
Route::get('/kasir-login', function () {
    return view('kasir.login');
})->name('kasir.login.form');

// UBAH DARI FUNCTION MENJADI CONTROLLER
Route::post('/kasir-login', [KasirAuthController::class, 'login'])->name('kasir.login.submit');


// RUTE FORM REGISTER SEDERHANA UNTUK KASIR BARU
Route::get('/kasir-register', function () {
    return view('kasir.register');
})->name('kasir.register.form');

// UBAH DARI FUNCTION MENJADI CONTROLLER
Route::post('/kasir-register', [KasirAuthController::class, 'register'])->name('kasir.register.submit');
// ... (rute-rute lainnya) ...

Route::get('/kasir/dashboard', function () {
    return view('kasir.dashboard');
})->middleware(['auth', 'kasir'])->name('kasir.dashboard'); // Gunakan middleware 'auth' & 'kasir'

// routes/web.php

// ... Pastikan alias 'kasir' sudah terdaftar di bootstrap/app.php sebelum menggunakan ini!

// Rute Dashboard Kasir
Route::get('/kasir/dashboard', function () {
    return view('kasir.dashboard');
})->middleware(['auth', EnsureUserIsKasir::class])->name('kasir.dashboard'); 
// Keterangan: Jika alias 'kasir' masih bermasalah, gunakan langsung classnya: 'auth', EnsureUserIsKasir::class

require __DIR__.'/auth.php';
