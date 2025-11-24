<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Promo;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Services\MidtransService;

class PosController extends Controller
{
    protected MidtransService $midtrans;

    public function __construct(MidtransService $midtrans)
    {
        $this->midtrans = $midtrans;
    }

    public function index()
    {
        // Prefer using the Promo model when available
        $promos = collect();
        try {
            if (class_exists(\App\Models\Promo::class)) {
                $now = now();
                $promos = Promo::where('active', true)
                    ->where(function($q) use ($now) {
                        $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                    })
                    ->where(function($q) use ($now) {
                        $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        } catch (\Throwable $e) {
            // fallback to empty collection
            $promos = collect();
        }

        return view('pos.pos', compact('promos'));
    }

    // Simple product search for POS (q param)
    public function search(Request $request)
    {
        $q = $request->query('q', '');
        $products = Produk::where('nama_produk', 'like', "%{$q}%")->limit(20)->get(['produk_id','nama_produk','harga_jual','stok']);
        return response()->json($products);
    }

    // Handle payment: create transaksi + details. For cash, mark selesai and generate invoice; for midtrans return snap token
    public function pay(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|integer',
            'items.*.jumlah' => 'required|integer|min:1',
            'metode_bayar' => 'required|in:tunai,qris,transfer',
            'pelanggan_id' => 'nullable|integer',
        ]);

        $userId = Auth::id();
        $total = 0;
        foreach ($data['items'] as $it) {
            $prod = Produk::find($it['produk_id']);
            $subtotal = $prod ? ($prod->harga_jual * $it['jumlah']) : 0;
            $total += $subtotal;
        }

        // apply promo if present
        $discountAmount = 0;
        if (isset($data['promo_id']) && $data['promo_id']) {
            $promo = Promo::find($data['promo_id']);
            if ($promo) {
                if ($promo->percent) {
                    $discountAmount = round(($promo->percent / 100) * $total);
                } elseif ($promo->discount) {
                    $discountAmount = (float) $promo->discount;
                }
            }
        }

        $totalAfter = max(0, $total - $discountAmount);

        $transaksi = Transaksi::create([
            'tanggal' => now(),
            'kasir_id' => $userId,
            'pelanggan_id' => $data['pelanggan_id'] ?? null,
            'total' => $totalAfter,
            'diskon' => $discountAmount,
            'pajak' => 0,
            'metode_bayar' => $data['metode_bayar'],
            'nominal_bayar' => ($data['metode_bayar'] === 'tunai') ? $totalAfter : 0,
            'kembalian' => ($data['metode_bayar'] === 'tunai') ? 0 : 0,
            'status' => ($data['metode_bayar'] === 'tunai') ? 'selesai' : 'pending',
        ]);

        // Attach promo id (if provided) into midtrans_raw (or promo meta)
        if (isset($data['promo_id']) && $data['promo_id']) {
            $raw = $transaksi->midtrans_raw ?? [];
            $raw = is_array($raw) ? $raw : (is_string($raw) ? json_decode($raw, true) : []);
            $raw['applied_promo_id'] = $data['promo_id'];
            $transaksi->midtrans_raw = $raw;
            $transaksi->save();
        }

        // insert details and reduce stock
        foreach ($data['items'] as $it) {
            $prod = Produk::find($it['produk_id']);
            $harga = $prod ? $prod->harga_jual : 0;
            DB::table('transaksi_detail')->insert([
                'transaksi_id' => $transaksi->transaksi_id,
                'produk_id' => $it['produk_id'],
                'jumlah' => $it['jumlah'],
                'harga_satuan' => $harga,
                'subtotal' => $harga * $it['jumlah'],
                'diskon_item' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // decrement stok if produk exists
            if ($prod) {
                $prod->decrement('stok', $it['jumlah']);
            }
        }

        // If cash, we already marked selesai: generate invoice file
        if ($transaksi->status === 'selesai') {
            $items = DB::table('transaksi_detail')->where('transaksi_id', $transaksi->transaksi_id)->get();
            $html = view('admin.transaksi.invoice', compact('transaksi', 'items'))->render();
            Storage::disk('local')->put("invoices/invoice-{$transaksi->transaksi_id}.html", $html);

            return response()->json(['success' => true, 'transaksi_id' => $transaksi->transaksi_id, 'invoice_url' => route('transaksi.invoice', $transaksi->transaksi_id)]);
        }

        // For Midtrans (qris/transfer) create snap token via MidtransService and return it
        $orderId = 'order-' . $transaksi->transaksi_id . '-' . time();
        // store order_id in midtrans_raw for later matching
        $raw = $transaksi->midtrans_raw ?? [];
        $raw = is_array($raw) ? $raw : (is_string($raw) ? json_decode($raw, true) : []);
        $raw['order_id'] = $orderId;
        $transaksi->midtrans_raw = $raw;
        $transaksi->save();

        $snapToken = $this->midtrans->createSnapToken([
            'order_id' => $orderId,
            'gross_amount' => (float) $transaksi->total,
            'customer' => [],
        ]);

        return response()->json(['success' => true, 'transaksi_id' => $transaksi->transaksi_id, 'snap_token' => $snapToken]);
    }
}
