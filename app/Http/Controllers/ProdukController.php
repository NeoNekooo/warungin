<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\StokLog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProdukController extends Controller
{
    public function __construct()
    {
        // Restrict create/update/delete (management) to admin and kasir
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user) return abort(403);
            $role = $user->role;
            $allowed = ['admin', 'kasir'];
            // If route is a mutating action, ensure role is allowed
            $action = $request->route()->getActionMethod();
            if (in_array($action, ['store', 'update', 'destroy'])) {
                if (!in_array($role, $allowed)) {
                    return abort(403);
                }
            }
            return $next($request);
        })->only(['store', 'update', 'destroy']);
    }

    /**
     * Generate a unique barcode string for new products (AJAX endpoint).
     */
    public function generateBarcode()
    {
        try {
            // Attempt to generate a numeric-ish barcode and ensure uniqueness
            $attempts = 0;
            do {
                // Compose a human-friendly numeric barcode starting with 'WG' then timestamp + 3 random digits
                $code = 'WG' . strval(time()) . str_pad((string)random_int(0, 999), 3, '0', STR_PAD_LEFT);
                $attempts++;
                if ($attempts > 50) break; // safety fallback
            } while (Produk::where('kode_barcode', $code)->exists());

            if (Produk::where('kode_barcode', $code)->exists()) {
                // unexpected duplicate after many attempts
                \Log::warning('generateBarcode: failed to create a unique kode_barcode after attempts', ['code' => $code, 'attempts' => $attempts]);
                return response()->json(['message' => 'Gagal membuat kode barcode (duplikat). Silakan coba lagi.'], 500);
            }

            return response()->json(['kode_barcode' => $code]);
        } catch (\Throwable $e) {
            \Log::error('generateBarcode: exception while generating barcode', ['err' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Terjadi kesalahan saat membuat kode barcode. ' . $e->getMessage()], 500);
        }
    }
    public function index(Request $request)
    {
        // 1. Ambil kata kunci pencarian
        $search = $request->input('search');

        // 2. Query Dasar
        $query = Produk::with('kategori')->latest();

        // 3. Filter Pencarian
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                  ->orWhere('kode_barcode', 'like', "%{$search}%");
            });
        }

        // 4. Pagination & Append Search query string
        $produks = $query->paginate(10)->appends(['search' => $search]);
        $kategoris = Kategori::all();

        return view('admin.produk.index', compact('produks', 'kategoris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id'   => 'required|exists:kategori,kategori_id',
            'kode_barcode'  => 'nullable|string|unique:produk,kode_barcode',
            'nama_produk'   => 'required|string|max:255',
            'harga_beli'    => 'required|numeric|min:0',
            'harga_jual'    => 'required|numeric|min:0',
            'stok'          => 'nullable|integer|min:0',
            'satuan'        => 'required|string|max:50',
            'deskripsi'     => 'nullable|string',
            'gambar'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'        => 'required|in:aktif,nonaktif',
        ]);

        // Generate Kode Produk Unik
        $validated['kode_produk'] = 'PRD-' . date('ymd') . '-' . strtoupper(Str::random(4));

        // Upload Gambar
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('produk', 'public');
            $validated['gambar_url'] = $path;
        }
        unset($validated['gambar']); // Bersihkan key gambar

        // Create Data
        $produk = Produk::create($validated);

        // Log Stok Awal
        if ($request->stok > 0) {
            StokLog::create([
                'produk_id'  => $produk->produk_id,
                'tanggal'    => now(),
                'tipe'       => 'masuk',
                'jumlah'     => $request->stok,
                'sumber'     => 'Stok Awal',
                'keterangan' => 'Input stok pertama kali',
                'user_id'    => auth()->id(),
            ]);
        }

        return redirect()->route('produk.index')->with('success', 'Produk Berhasil ditambahkan');
    }

    // Note: Method 'edit' tidak dipakai karena kita pakai Modal di index, 
    // tapi dibiarkan ada untuk fallback jika perlu.
    public function edit($id)
    {
        $produk = Produk::findOrFail($id);
        $kategoris = Kategori::all();
        return view('admin.produk.index', compact('produks', 'kategoris')); // Redirect ke index saja
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        $validated = $request->validate([
            'kategori_id'   => 'required|exists:kategori,kategori_id',
            // Unique validation ignore ID saat ini
            'kode_barcode'  => 'nullable|string|unique:produk,kode_barcode,'.$produk->produk_id.',produk_id',
            'nama_produk'   => 'required|string|max:255',
            'harga_beli'    => 'required|numeric|min:0',
            'harga_jual'    => 'required|numeric|min:0',
            'satuan'        => 'required|string|max:50',
            'deskripsi'     => 'nullable|string',
            'gambar'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status'        => 'required|in:aktif,nonaktif',
            // Stok biasanya tidak diupdate langsung di sini untuk menjaga integritas log, 
            // tapi jika simpel diperbolehkan:
            'stok'          => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('gambar')) {
            if ($produk->gambar_url && Storage::disk('public')->exists($produk->gambar_url)) {
                Storage::disk('public')->delete($produk->gambar_url);
            }
            $path = $request->file('gambar')->store('produk', 'public');
            $validated['gambar_url'] = $path;
        }
        unset($validated['gambar']);

        $produk->update($validated);

        return redirect()->route('produk.index')->with('success', 'Data Produk berhasil diperbarui');
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        
        if ($produk->gambar_url && Storage::disk('public')->exists($produk->gambar_url)) {
            Storage::disk('public')->delete($produk->gambar_url);
        }

        $produk->delete();

        return redirect()->route('produk.index')->with('success', 'Data Produk berhasil dihapus');
    }
}