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
    User::create([
        'nama' => 'Super Admin',
        'username' => 'admin',
        'password' => Hash::make('admin123'),
        'role' => 'admin',
        'email' => 'admin@warungin.com',
        'no_hp' => '08123456789',
        'status' => 1
    ]);

    User::create([
        'nama' => 'Riri Kasir',
        'username' => 'riri',
        'password' => Hash::make('riri123'),
        'role' => 'kasir',
        'email' => 'riri@warungin.com',
        'no_hp' => '08987654321',
        'status' => 1
    ]);
}
}
