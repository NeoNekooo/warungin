<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    // 1. Definisi Nama Tabel
    protected $table = 'produk';

    // 2. Definisi Primary Key (WAJIB karena bukan 'id')
    protected $primaryKey = 'produk_id';

    // 3. Field yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'kategori_id',
        'kode_produk',
        'kode_barcode',
        'nama_produk',
        'harga_beli',
        'harga_jual',
        'stok',
        'satuan',
        'deskripsi',
        'gambar_url',
        'status',
    ];

    // Relasi ke Kategori
    // Parameter: (ModelTujuan, ForeignKeyDiTabelIni, PrimaryKeyDiTabelTujuan)
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'kategori_id');
    }

    // Relasi ke Stok Log
    public function stokLog()
    {
        return $this->hasMany(StokLog::class, 'produk_id', 'produk_id');
    }
}