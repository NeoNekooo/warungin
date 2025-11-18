<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi; 

class TransaksiController extends Controller
{
    /**
     */
    public function index()
    {
        $transaksi = Transaksi::orderBy('tanggal', 'desc')->get(); 

        return view('kasir.transaksi');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nama_barang' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'total' => 'required|numeric|min:0',
        ]);

        Transaksi::create([
            'tanggal' => $request->tanggal,
            'nama_barang' => $request->nama_barang,
            'jumlah' => $request->jumlah,
            'total' => $request->total,
        ]);

        return redirect()->route('transaksi')->with('success', 'Transaksi berhasil ditambahkan!');
    }
    public function destroy(Transaksi $transaksi)
    {
        $transaksi->delete();

        return redirect()->route('transaksi')->with('success', 'Transaksi berhasil dihapus!');
    }
}
