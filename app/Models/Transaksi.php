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
    protected $table = 'transaksis'; 

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'tanggal',
        'nama_barang',
        'jumlah',
        'total',
    ];

    /**
     * Atribut yang harus diubah menjadi tipe data asli (casting).
     */
    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'integer',
        'total' => 'integer', // Asumsi total disimpan sebagai integer (tanpa desimal)
    ];
}
