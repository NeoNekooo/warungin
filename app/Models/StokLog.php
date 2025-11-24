<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokLog extends Model
{
    protected $table = 'stok_log';
    protected $primaryKey = 'stok_id';

    protected $fillable = [
        'produk_id',
        'tanggal',
        'tipe', // masuk/keluar
        'jumlah',
        'sumber',
        'keterangan',
        'user_id'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}