<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'nama' => 'Administrator Sistem',
                'username' => 'admin',
                'email' => 'admin@toko.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'no_hp' => '081234567890',
                'status' => 1,
            ]
        );

        // Create kasir
        User::updateOrCreate(
            ['username' => 'kasir'],
            [
                'nama' => 'Kasir Toko',
                'username' => 'kasir',
                'email' => 'kasir@toko.com',
                'password' => Hash::make('password'),
                'role' => 'kasir',
                'no_hp' => '081122334455',
                'status' => 1,
            ]
        );

        // Create owner
        User::updateOrCreate(
            ['username' => 'owner'],
            [
                'nama' => 'Pemilik Toko',
                'username' => 'owner',
                'email' => 'owner@toko.com',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'no_hp' => '081333444555',
                'status' => 1,
            ]
        );
    }
}
