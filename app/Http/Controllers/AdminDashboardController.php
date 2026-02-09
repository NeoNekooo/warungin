<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Produk;
use App\Models\User;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $currentDate = Carbon::now();

        // 1. Summary Metrics
        $totalRevenue = Transaksi::sum('total');
        $totalTransactions = Transaksi::count();
        $totalUsers = User::count();
        $totalProducts = Produk::count();
        $totalExpenses = 0; // Placeholder
        $netProfit = $totalRevenue - $totalExpenses;

        // 2. Best-selling Products (last 30 days)
        $topProducts = Transaksi::join('transaksi_detail', 'transaksi.transaksi_id', '=', 'transaksi_detail.transaksi_id')
                        ->join('produk', 'transaksi_detail.produk_id', '=', 'produk.produk_id')
                        ->where('transaksi.tanggal', '>=', Carbon::now()->subDays(30))
                        ->selectRaw('produk.nama_produk as product_name, SUM(transaksi_detail.jumlah) as total_quantity_sold')
                        ->groupBy('produk.nama_produk')
                        ->orderByDesc('total_quantity_sold')
                        ->limit(5)
                        ->get();

        // 3. Recent Transactions
        $recentTransactions = Transaksi::with('kasir')->latest()->limit(10)->get();

        // 4. Low Stock Alerts
        $lowStockProducts = Produk::where('stok', '<=', 10)->orderBy('stok')->limit(5)->get();

        // 5. Monthly Sales Overview (last 6 months)
        $monthlySalesOverview = Transaksi::selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as period, SUM(total) as revenue, COUNT(*) as cnt, AVG(total) as avg_transaction_value")
                                    ->groupBy('period')
                                    ->orderBy('period', 'desc')
                                    ->limit(6)
                                    ->get();

        // 6. Sales Trend for the last 7 days (Chart Logic)
        $salesTrend7Days = Transaksi::selectRaw('DATE(tanggal) as date, SUM(total) as daily_revenue')
                                    ->where('tanggal', '>=', Carbon::now()->subDays(7))
                                    ->groupBy('date')
                                    ->orderBy('date', 'asc')
                                    ->get()
                                    ->mapWithKeys(function ($item) {
                                        return [$item['date'] => $item['daily_revenue']];
                                    });

        $labels = [];
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($date)->isoFormat('ddd, D MMM');
            $data[] = $salesTrend7Days->get($date, 0);
        }

        $salesChartData = [
            'labels' => $labels,
            'data' => $data,
        ];

        return view('admin.dashboard', compact(
            'currentDate', 'totalRevenue', 'totalTransactions', 'totalUsers', 
            'totalProducts', 'totalExpenses', 'netProfit', 'topProducts', 
            'recentTransactions', 'lowStockProducts', 'monthlySalesOverview', 'salesChartData'
        ));
    }
}