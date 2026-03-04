<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';
    protected $primaryKey = 'absensi_id';
    
    // Tambahkan jam_pulang di sini
    protected $fillable = [
        'user_id', 
        'waktu_scan', 
        'jam_pulang', 
        'status', 
        'keterangan', 
        'device_id_toko'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function admin()
    {
        // Pastikan kolom 'admin_id' ada di tabel 'absensi' Anda
        return $this->belongsTo(User::class, 'admin_id', 'user_id'); 
    }
}