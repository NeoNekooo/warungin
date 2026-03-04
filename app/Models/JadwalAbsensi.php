<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalAbsensi extends Model
{
    protected $table = 'jadwal_absensi';

    // Pastikan semua kolom ini ada agar bisa disimpan secara otomatis
    protected $fillable = [
        'id', 
        'jam_masuk', 
        'jam_pulang', 
        'hari_kerja', 
        'tanggal_libur'
    ];
}