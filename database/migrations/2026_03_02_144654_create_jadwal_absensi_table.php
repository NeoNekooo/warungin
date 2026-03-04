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
        Schema::create('jadwal_absensi', function (Blueprint $table) {
            $table->id(); // Akan selalu ID 1
            $table->time('jam_masuk')->default('08:00');
            $table->time('jam_pulang')->default('17:00');
            $table->text('hari_kerja'); // Format JSON: ["Senin","Selasa",...]
            $table->text('tanggal_libur')->nullable(); // Simpan: 2026-08-17, 2026-12-25
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_absensi');
    }
};
