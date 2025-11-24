<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use PDF;

class ReportController extends Controller
{
    public function __construct()
    {
        // Reports index accessible to admin/kasir/owner; exporting restricted to admin/owner
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user) return abort(403);
            $allowed = ['admin', 'kasir', 'owner'];
            if (!in_array($user->role, $allowed)) return abort(403);
            return $next($request);
        });

        $this->middleware(function ($request, $next) {
            // exportPdf should be admin or owner only
            $action = $request->route()->getActionMethod();
            if ($action === 'exportPdf') {
                $user = auth()->user();
                if (!$user || !in_array($user->role, ['admin','owner'])) return abort(403);
            }
            return $next($request);
        });
    }
    public function index(Request $request)
    {
        $period = $request->query('period'); // expected format YYYY-MM or empty for all

        // Build base query
        $base = DB::table('transaksi');

        if ($period) {
            // parse YYYY-MM
            if (preg_match('/^(\d{4})-(\d{1,2})$/', $period, $m)) {
                $year = (int) $m[1];
                $month = (int) $m[2];
                $base->whereYear('tanggal', $year)->whereMonth('tanggal', $month);
            }
        }

        // Totals (count & revenue) for selected period or all
        $totals = (clone $base)->selectRaw('COUNT(*) as total_count, COALESCE(SUM(total),0) as total_revenue')->first();

        $totalTransactions = $totals->total_count ?? 0;
        $totalRevenue = $totals->total_revenue ?? 0;

        // Period list - show available year-month combinations using DISTINCT on DATE_FORMAT
        $periods = DB::table('transaksi')
            ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as period_key")
            ->distinct()
            ->orderByRaw("DATE_FORMAT(tanggal, '%Y-%m') desc")
            ->get()
            ->map(function($r){
                // parse year/month from period_key (format YYYY-MM)
                [$y, $m] = explode('-', $r->period_key);
                $label = date('F Y', mktime(0,0,0,(int)$m,1,(int)$y));
                return (object)['key' => $r->period_key, 'label' => $label, 'period_key' => $r->period_key];
            });

        // Rows grouped by month (for the table)
        // Rows grouped by period_key (YYYY-MM) - only select the formatted period + aggregates
        $rowsQuery = DB::table('transaksi')
            ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as period_key, COUNT(*) as count, COALESCE(SUM(total),0) as total")
            ->groupByRaw("DATE_FORMAT(tanggal, '%Y-%m')")
            ->orderByRaw("DATE_FORMAT(tanggal, '%Y-%m') desc");

        if ($period) {
            if (preg_match('/^(\d{4})-(\d{2})$/', $period, $m)) {
                $rowsQuery->whereYear('tanggal', (int)$m[1])->whereMonth('tanggal', (int)$m[2]);
            }
        }

        $rows = $rowsQuery->get()->map(function($r){
            // r->period_key is YYYY-MM
            [$y, $m] = explode('-', $r->period_key);
            $periodLabel = date('F Y', mktime(0,0,0,(int)$m,1,(int)$y));
            return (object)[
                'period' => $periodLabel,
                'count' => $r->count,
                'total' => $r->total,
                'year' => (int)$y,
                'month' => (int)$m,
                'period_key' => $r->period_key,
            ];
        });

        return view('admin.reports.index', compact('rows','periods','totalTransactions','totalRevenue','period'));
    }

    public function exportPdf(Request $request)
    {
        $rows = DB::table('transaksi')
            ->selectRaw("DATE(tanggal) as tanggal, COUNT(*) as count, SUM(total) as total")
            ->groupBy(DB::raw('DATE(tanggal)'))
            ->orderBy('tanggal', 'desc')
            ->limit(365)
            ->get();

        $data = compact('rows');

        // If barryvdh/laravel-dompdf is installed, use it. Otherwise return the HTML view for printing.
        if (class_exists('\\Barryvdh\\DomPDF\\Facade\\Pdf') || class_exists('PDF') || class_exists('Dompdf\\Dompdf')) {
            try {
                $pdf = null;
                if (class_exists('PDF')) {
                    $pdf = PDF::loadView('admin.reports.pdf', $data);
                } elseif (class_exists('Barryvdh\\DomPDF\\Facade\\Pdf')) {
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.pdf', $data);
                }
                if ($pdf) {
                    return $pdf->download('laporan-harian.pdf');
                }
            } catch (\Throwable $e) {
                // fallback to HTML below
            }
        }

        // Fallback: render HTML view and return as response so user can print from browser
        $html = view('admin.reports.pdf', $data)->render();
        return response($html);
    }
}
