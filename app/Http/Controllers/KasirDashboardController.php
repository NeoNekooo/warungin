<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Produk;
use App\Models\Pembayaran;
use Carbon\Carbon;

class KasirDashboardController extends Controller
{
    public function index()
    {
        // Get today's date
        $today = Carbon::now()->toDateString();

        // Calculate summary data
        $transToday = Transaksi::whereDate('tanggal', $today)->count();
        $totalToday = Transaksi::whereDate('tanggal', $today)->sum('total');
        $produkCount = Produk::count();
        $pendingPayments = Pembayaran::where('jumlah', 0)->count();

        // Fetch recent activity (latest 6 transactions)
        $recentActivity = Transaksi::latest()->limit(6)->get();

        return view('kasir.dashboard', compact(
            'today',
            'transToday',
            'totalToday',
            'produkCount',
            'pendingPayments',
            'recentActivity'
        ));
    }
}
