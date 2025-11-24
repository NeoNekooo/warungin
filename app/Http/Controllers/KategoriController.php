<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::orderBy('nama_kategori')->paginate(20);
        return view('admin.kategori.index', compact('kategoris'));
    }

    public function __construct()
    {
        // Only admin can manage categories
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user || $user->role !== 'admin') return abort(403);
            return $next($request);
        })->except(['index']);
    }

    public function create()
    {
        return view('admin.kategori.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Perbaikan: Nama tabel 'kategori', bukan 'kategoris'
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori',
            'deskripsi'     => 'nullable|string',
        ]);

        Kategori::create($validated);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan');
    }

    public function edit($id)
    {
        // findOrFail akan otomatis mencari berdasarkan primary key 'kategori_id' (sesuai Model)
        $kategori = Kategori::findOrFail($id);
        return view('admin.kategori.edit', compact('kategori'));
    }

    public function update(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $validated = $request->validate([
            // Perbaikan Logic Unique Update:
            // Format: unique:nama_tabel,nama_kolom,id_yang_diabaikan,nama_kolom_primary_key
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori,'.$kategori->kategori_id.',kategori_id',
            'deskripsi'     => 'nullable|string',
        ]);

        $kategori->update($validated);

        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil diperbarui');
    }

    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();
        
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus');
    }
}