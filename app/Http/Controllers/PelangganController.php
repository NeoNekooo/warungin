<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;

class PelangganController extends Controller
{
    public function __construct()
    {
        // Only admin may create/update/delete pelanggan
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user || $user->role !== 'admin') return abort(403);
            return $next($request);
        })->only(['create','store','edit','update','destroy']);
    }
     public function index()
    {
        $pelanggans = Pelanggan::orderBy('nama_pelanggan')->paginate(20);
        return view('admin.pelanggan.index', compact('pelanggans'));
    }

    public function create()
    {
        return view('admin.pelanggan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|unique:pelanggans,nama_pelanggan',
            'no_hp' => 'required|string',
            'alamat' => 'nullable|string',
            'email' => 'required|email|max:100',
        ]);

        $pelanggan = Pelanggan::create($validated);

        return redirect()->route('pelanggan.index')->with('success', 'Data Pelanggan berhasil dibuat');
    }

    public function edit($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        return view('admin.pelanggan.edit', compact('pelanggan'));
    }

    public function update(Request $request, $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|unique:pelanggans,nama_pelanggan',
            'no_hp' => 'required|string',
            'alamat' => 'nullable|string',
            'email' => 'required|email|max:100',
        ]);

        $pelanggan->update($validated);

        return redirect()->route('pelanggan.index')->with('success', 'Data Pelanggan berhasil diperbarui');
    }

    public function destroy($id)
    {
        Pelanggan::destroy($id);
        return redirect()->route('pelanggan.index')->with('success', 'Data Pelanggan berhasil dihapus');
    }
}
