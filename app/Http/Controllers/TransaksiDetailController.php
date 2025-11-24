<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Transaksi;

class TransaksiDetailController extends Controller
{
    public function __construct()
    {
        // Allow admin, kasir and owner to view transaksi detail
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user) return abort(403);
            if (!in_array($user->role, ['admin','kasir','owner'])) return abort(403);
            return $next($request);
        });
    }
     public function index()
    {
        // join transaksi_detail with produk and transaksi header
        $details = DB::table('transaksi_detail as td')
            ->join('produk as p', 'td.produk_id', '=', 'p.produk_id')
            ->join('transaksi as t', 'td.transaksi_id', '=', 't.transaksi_id')
            ->select('td.*', 'p.nama_produk', 'p.kode_barcode', 't.tanggal', 't.kasir_id')
            ->orderBy('td.created_at', 'desc')
            ->paginate(30);

        return view('admin.transaksi_detail.index', compact('details'));
    }
}
