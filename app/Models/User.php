<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // 1. Definisikan Primary Key Custom
    protected $primaryKey = 'user_id'; 

    // 2. Isi kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'nama',
        'username',
        'email',
        'password',
        'role',
        'no_hp',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}