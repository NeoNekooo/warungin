<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat pengguna Admin
        User::create([
            'name' => 'Admin Kasir',
            'email' => 'admin@gmail.com',
            'role' => 'kasir',
            // Gunakan password default "password123". Hash::make akan mengacaknya.
            'password' => Hash::make('password123'), 
        ]);
    }
}
