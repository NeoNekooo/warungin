<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Promo;
use App\Models\Transaksi;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\TransaksiController;

class PosController extends Controller
{
    protected MidtransService $midtrans;
    protected TransaksiController $transaksiController;

    public function __construct(MidtransService $midtrans, TransaksiController $transaksiController)
    {
        $this->midtrans = $midtrans;
        $this->transaksiController = $transaksiController;
        // Only allow authenticated users with roles admin/kasir/owner to use POS
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user) return abort(403);
            if (!in_array($user->role, ['admin','kasir','owner'])) return abort(403);
            return $next($request);
        });
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
        $products = Produk::where('nama_produk', 'like', "%{$q}%")->limit(20)->get(['produk_id','nama_produk','harga_jual','stok','gambar_url']);

        // Convert gambar_url to full accessible URL (storage link) for the POS frontend
        $products = $products->map(function($p) {
            $p->gambar_url = $p->gambar_url ? asset('storage/' . $p->gambar_url) : null;
            return $p;
        });

        return response()->json($products);
    }

    // Handle payment: create transaksi + details. For cash, mark selesai and generate invoice; for midtrans return snap token
    public function pay(Request $request)
    {
        Log::info('POS: Payment initiated.', ['request_data' => $request->all()]);

        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|integer',
            'items.*.jumlah' => 'required|integer|min:1',
            'metode_bayar' => 'required|in:tunai,qris,transfer',
            'pelanggan_id' => 'nullable|integer',
            'nominal_bayar' => 'nullable|numeric|min:0',
        ]);

        Log::info('POS: Validation successful.', ['validated_data' => $data]);

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
        Log::info('POS: Totals calculated.', ['total' => $total, 'discount' => $discountAmount, 'total_after' => $totalAfter]);

        // determine nominal bayar and kembalian for cash payments if provided
        $nominalBayar = isset($data['nominal_bayar']) ? (float)$data['nominal_bayar'] : null;
        if ($data['metode_bayar'] === 'tunai' && $nominalBayar === null) {
            // fallback: assume exact payment if caller didn't send nominal_bayar
            $nominalBayar = $totalAfter;
        }
        $kembalian = 0;
        if ($data['metode_bayar'] === 'tunai') {
            $kembalian = max(0, (float)$nominalBayar - $totalAfter);
        }
        Log::info('POS: Nominal Bayar and Kembalian determined.', ['nominal_bayar' => $nominalBayar, 'kembalian' => $kembalian]);


        $transaksi = Transaksi::create([
            'tanggal' => now(),
            'kasir_id' => $userId,
            'pelanggan_id' => $data['pelanggan_id'] ?? null,
            'total' => $totalAfter,
            'diskon' => $discountAmount,
            'pajak' => 0,
            'metode_bayar' => $data['metode_bayar'],
            // store the nominal_bayar supplied by UI (for tunai) or 0 otherwise
            'nominal_bayar' => ($data['metode_bayar'] === 'tunai') ? ($nominalBayar ?? $totalAfter) : 0,
            'kembalian' => ($data['metode_bayar'] === 'tunai') ? $kembalian : 0,
            'status' => ($data['metode_bayar'] === 'tunai') ? 'selesai' : 'pending',
        ]);
        Log::info('POS: Transaksi record created.', ['transaksi_id' => $transaksi->transaksi_id, 'status' => $transaksi->status]);


        // Attach promo id (if provided) into midtrans_raw (or promo meta)
        if (isset($data['promo_id']) && $data['promo_id']) {
            $raw = $transaksi->midtrans_raw ?? [];
            $raw = is_array($raw) ? $raw : (is_string($raw) ? json_decode($raw, true) : []);
            $raw['applied_promo_id'] = $data['promo_id'];
            $transaksi->midtrans_raw = $raw;
            $transaksi->save();
            Log::info('POS: Promo attached to Transaksi.', ['promo_id' => $data['promo_id']]);
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
                Log::info('POS: Stock decremented.', ['produk_id' => $it['produk_id'], 'quantity' => $it['jumlah'], 'new_stock' => $prod->stok]);
            } else {
                Log::warning('POS: Product not found for stock decrement.', ['produk_id' => $it['produk_id']]);
            }
        }
        Log::info('POS: Transaksi details inserted and stock updated.');

        // Generate an order id for gateway payments (and store it in midtrans_raw)
        $orderId = 'order-' . $transaksi->transaksi_id . '-' . time();
        $raw = $transaksi->midtrans_raw ?? [];
        $raw = is_array($raw) ? $raw : (is_string($raw) ? json_decode($raw, true) : []);
        $raw['order_id'] = $orderId;
        $transaksi->midtrans_raw = $raw;
        $transaksi->save();
        Log::info('POS: Order ID generated and saved.', ['order_id' => $orderId]);

        // Create a Pembayaran record for immediate/tunai payments or pending gateway payments
        try {
            if ($transaksi->metode_bayar === 'tunai') {
                Pembayaran::create([
                    'transaksi_id' => $transaksi->transaksi_id,
                    'metode' => 'tunai',
                    'jumlah' => $transaksi->nominal_bayar ?? $transaksi->total,
                    'referensi' => null,
                ]);
                Log::info('POS: Pembayaran record created for Tunai.', ['transaksi_id' => $transaksi->transaksi_id]);
            } else {
                // For gateway payments (midtrans/qris) create a pending pembayaran with order reference
                Pembayaran::create([
                    'transaksi_id' => $transaksi->transaksi_id,
                    'metode' => ($data['metode_bayar'] === 'qris') ? 'qris' : 'midtrans',
                    'jumlah' => 0,
                    'referensi' => $orderId,
                ]);
                Log::info('POS: Pembayaran record created for Midtrans/QRIS (pending).', ['transaksi_id' => $transaksi->transaksi_id, 'order_id' => $orderId]);
            }
        } catch (\Throwable $e) {
            Log::error('POS: Failed to create Pembayaran record.', ['transaksi_id' => $transaksi->transaksi_id, 'error' => $e->getMessage()]);
            // don't break the flow if payment record cannot be created
        }

        // If cash, we already marked selesai: generate invoice file
        if ($transaksi->status === 'selesai') {
            Log::info('POS: Transaction status is Selesai. Generating invoice HTML.', ['transaksi_id' => $transaksi->transaksi_id]);
            $this->transaksiController->generateInvoiceHtml($transaksi);

            $invoiceUrl = route('transaksi.invoice', $transaksi->transaksi_id);
            Log::info('POS: Invoice HTML generated. Returning invoice_url.', ['invoice_url' => $invoiceUrl]);
            return response()->json(['success' => true, 'transaksi_id' => $transaksi->transaksi_id, 'invoice_url' => $invoiceUrl]);
        }

        // For Midtrans (qris/transfer) create snap token via MidtransService and return it
        Log::info('POS: Transaction status is Pending. Creating Midtrans Snap token.', ['transaksi_id' => $transaksi->transaksi_id]);
        $snapToken = $this->midtrans->createSnapToken([
            'order_id' => $orderId,
            'gross_amount' => (float) $transaksi->total,
            'customer' => [],
        ]);
        Log::info('POS: Midtrans Snap token created. Returning snap_token.', ['snap_token' => $snapToken]);

        return response()->json(['success' => true, 'transaksi_id' => $transaksi->transaksi_id, 'snap_token' => $snapToken]);
    }
}
