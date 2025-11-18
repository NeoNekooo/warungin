<?php

namespace Database\Seeders;

// Gunakan model User dan Hash untuk membuat password
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil UserSeeder untuk mengisi data admin
        $this->call([
            UserSeeder::class, // <-- Pastikan ini memanggil UserSeeder
        ]);
    }
}
