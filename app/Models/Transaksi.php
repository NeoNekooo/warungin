<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     * Secara default Laravel akan menggunakan nama jamak 'transaksis'.
     */
    protected $table = 'transaksi';
    protected $primaryKey = 'transaksi_id';
    public $incrementing = true;
    protected $keyType = 'int';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'tanggal',
        'kasir_id',
        'pelanggan_id',
        'total',
        'diskon',
        'pajak',
        'metode_bayar',
        'nominal_bayar',
        'kembalian',
        'status',
        'midtrans_transaction_id',
        'payment_status',
        'midtrans_raw',
    ];

    /**
     * Atribut yang harus diubah menjadi tipe data asli (casting).
     */
    protected $casts = [
        'tanggal' => 'datetime',
        'total' => 'decimal:2',
        'diskon' => 'decimal:2',
        'pajak' => 'decimal:2',
        'nominal_bayar' => 'decimal:2',
        'kembalian' => 'decimal:2',
        'midtrans_raw' => 'array',
    ];

    /**
     * Relasi ke pelanggan.
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id', 'pelanggan_id');
    }

    /**
     * Relasi ke kasir (user).
     */
    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id', 'user_id');
    }

    public function produk(){
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
