<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembayaran;

class PembayaranController extends Controller
{
    public function __construct()
    {
        // index/show accessible by admin and kasir; reconcile only admin
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user) return abort(403);
            $allowed = ['admin', 'owner', 'kasir'];
            if (!in_array($user->role, $allowed)) return abort(403);
            return $next($request);
        })->only(['index','show']);

        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user || $user->role !== 'admin') return abort(403);
            return $next($request);
        })->only(['reconcile']);
    }
    public function index(Request $request)
    {
        $query = Pembayaran::with('transaksi');

        // Filter berdasarkan Metode
        if ($request->filled('metode')) {
            $query->where('metode', $request->metode);
        }

        // Filter berdasarkan Rentang Tanggal
        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        // Filter berdasarkan Nominal Jumlah
        if ($request->filled('min_amount')) {
            $query->where('jumlah', '>=', $request->min_amount);
        }
        if ($request->filled('max_amount')) {
            $query->where('jumlah', '<=', $request->max_amount);
        }

        // Filter berdasarkan ID Transaksi atau Referensi (Pencarian Cepat)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaksi_id', 'like', "%{$search}%")
                  ->orWhere('referensi', 'like', "%{$search}%")
                  ->orWhere('metode', 'like', "%{$search}%");
            });
        }

        $pembayarans = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        
        return view('admin.pembayaran.index', compact('pembayarans'));
    }

    public function show($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        return view('admin.pembayaran.show', compact('pembayaran'));
    }

    // Manual reconcile (mark as paid) - admin action
    public function reconcile(Request $request, $id)
    {
        $p = Pembayaran::findOrFail($id);
        $p->jumlah = $request->input('jumlah', $p->jumlah);
        $p->referensi = $request->input('referensi', $p->referensi);
        $p->metode = $request->input('metode', $p->metode);
        $p->save();

        // mark transaksi as selesai if appropriate
        try {
            if ($p->transaksi) {
                $p->transaksi->status = 'selesai';
                $p->transaksi->save();
            }
        } catch (\Throwable $e) {}

        return redirect()->route('pembayaran.show', $p->pembayaran_id)->with('success', 'Pembayaran direkonsiliasi');
    }
}
