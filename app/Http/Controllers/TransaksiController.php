<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi; 
use App\Models\Pelanggan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransaksiController extends Controller
{
    /**
     */
    public function index()
    {
        $transaksi = Transaksi::orderBy('tanggal', 'desc')->paginate(20);

        return view('admin.transaksi.index', compact('transaksi'));
    }

    public function __construct()
    {
        // Allow admin, kasir and owner to manage transaksi actions
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user) return abort(403);
            $role = $user->role;
            $allowed = ['admin', 'kasir', 'owner'];
            return $next($request);
        });
    }

    public function create()
    {
        $pelanggan = Pelanggan::orderBy('nama')->get();
        return view('admin.transaksi.create', compact('pelanggan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'pelanggan_id' => 'nullable|exists:pelanggan,pelanggan_id',
            'total' => 'required|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0',
            'pajak' => 'nullable|numeric|min:0',
            'metode_bayar' => 'required|in:tunai,qris,transfer',
            'nominal_bayar' => 'nullable|numeric|min:0',
        ]);

        $kasirId = Auth::id();
        $total = $request->total;
        $nominalBayar = $request->nominal_bayar ?? 0;
        $kembalian = 0;
        if ($nominalBayar > $total) {
            $kembalian = $nominalBayar - $total;
        }

        $transaksi = Transaksi::create([
            'tanggal' => $request->tanggal,
            'kasir_id' => $kasirId,
            'pelanggan_id' => $request->pelanggan_id,
            'total' => $total,
            'diskon' => $request->diskon ?? 0,
            'pajak' => $request->pajak ?? 0,
            'metode_bayar' => $request->metode_bayar,
            'nominal_bayar' => $nominalBayar,
            'kembalian' => $kembalian,
            'status' => ($request->metode_bayar === 'tunai') ? 'selesai' : 'pending',
        ]);

        // If the transaction is completed immediately (tunai), generate invoice HTML file
        if ($transaksi->status === 'selesai') {
            $items = DB::table('transaksi_detail')->where('transaksi_id', $transaksi->transaksi_id)->get();
            $html = view('admin.transaksi.invoice', compact('transaksi', 'items'))->render();
            Storage::disk('local')->put("invoices/invoice-{$transaksi->transaksi_id}.html", $html);
        }

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil ditambahkan!');
    }
    public function destroy(Transaksi $transaksi)
    {
        $transaksi->delete();

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus!');
    }

    /**
     * Show the invoice view for a transaksi (printable receipt style).
     */
    public function invoice(Transaksi $transaksi)
    {
        $items = DB::table('transaksi_detail')->where('transaksi_id', $transaksi->transaksi_id)->get();
        return view('admin.transaksi.invoice', compact('transaksi', 'items'));
    }
}
