<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Produk;
use App\Models\Pelanggan;
use App\Models\TransaksiDetail; // Added this import
use Carbon\Carbon;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        // Get current month and year
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        // Calculate summary data
        $monthlyRevenue = Transaksi::whereYear('tanggal', $year)->whereMonth('tanggal', $month)->sum('total');
        $monthlyCount = Transaksi::whereYear('tanggal', $year)->whereMonth('tanggal', $month)->count();
        $produkCount = Produk::count();
        $customerCount = Pelanggan::count();

        // Fetch monthly summary for the last 6 months including average transaction value
        $monthlySummary = Transaksi::selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as period, SUM(total) as revenue, COUNT(*) as cnt, AVG(total) as avg_transaction_value")
                                    ->groupBy('period')
                                    ->orderBy('period', 'desc')
                                    ->limit(6)
                                    ->get();

        // Fetch Top Products (last 30 days by quantity sold)
        $topProducts = Transaksi::join('transaksi_detail', 'transaksi.transaksi_id', '=', 'transaksi_detail.transaksi_id')
                                ->join('produk', 'transaksi_detail.produk_id', '=', 'produk.produk_id')
                                ->where('transaksi.tanggal', '>=', Carbon::now()->subDays(30))
                                ->selectRaw('produk.nama_produk as product_name, SUM(transaksi_detail.jumlah) as total_quantity_sold')
                                ->groupBy('produk.nama_produk')
                                ->orderByDesc('total_quantity_sold')
                                ->limit(5) // Top 5 products
                                ->get();

        return view('owner.dashboard', compact(
            'month', 
            'year', 
            'monthlyRevenue', 
            'monthlyCount', 
            'produkCount', 
            'customerCount', 
            'monthlySummary',
            'topProducts' // Added new variable
        ));
    }
}
