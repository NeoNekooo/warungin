<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal'); // Sesuai dengan validasi Controller
            $table->string('nama_barang'); // Sesuai dengan validasi Controller
            $table->integer('jumlah'); // Sesuai dengan validasi Controller
            $table->decimal('total', 10, 2); // Menggunakan decimal untuk harga/total
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
