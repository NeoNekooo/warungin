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
       Schema::create('absensi', function (Blueprint $table) {
            $table->id('absensi_id');
            $table->foreignId('user_id');
            $table->dateTime('waktu_scan');
            
            // Hapus ->after('waktu_scan'), cukup tulis seperti ini:
            $table->time('jam_pulang')->nullable(); 
            
            $table->enum('status', ['hadir', 'izin', 'terlambat', 'sakit', 'alpa'])->default('hadir');
            $table->text('keterangan')->nullable();
            $table->string('device_id_toko')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
