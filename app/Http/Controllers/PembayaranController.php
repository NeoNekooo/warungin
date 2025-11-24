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
            $allowed = ['admin', 'kasir'];
            if (!in_array($user->role, $allowed)) return abort(403);
            return $next($request);
        })->only(['index','show']);

        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user || $user->role !== 'admin') return abort(403);
            return $next($request);
        })->only(['reconcile']);
    }
     public function index()
    {
        $pembayarans = Pembayaran::with('transaksi')->orderBy('created_at', 'desc')->paginate(20);
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
