<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\StokLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Hitung Total Produk
        $totalProduk = Produk::count();

        // 2. Hitung Total Kategori
        $totalKategori = Kategori::count();

        // 3. Hitung Total Stok (Semua produk dijumlahkan)
        $totalStok = Produk::sum('stok');

        // 4. Produk Stok Menipis (Stok <= 5)
        $produkMenipis = Produk::where('stok', '<=', 5)->count();

        // 5. Produk Terbaru (Ambil 5 data terakhir untuk list di dashboard)
        $produkTerbaru = Produk::with('kategori')->latest()->take(5)->get();

        // --- DATA DUMMY (Karena belum ada tabel Transaksi) ---
        // Nanti bisa diganti dengan real data: Transaksi::whereDate('created_at', today())->count();
        $transaksiHariIni = 0; 
        $pendapatanHariIni = 0;

        return view('dashboard', compact(
            'totalProduk', 
            'totalKategori', 
            'totalStok', 
            'produkMenipis',
            'produkTerbaru',
            'transaksiHariIni',
            'pendapatanHariIni'
        ));
    }
}