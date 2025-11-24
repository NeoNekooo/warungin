<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Use raw SQL to modify the column type to VARCHAR to avoid doctrine/dbal dependency
        // This allows adding 'owner' without dealing with ENUM modification portability.
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `users` MODIFY `role` VARCHAR(20) NOT NULL DEFAULT 'kasir'");
        } elseif ($driver === 'sqlite') {
            // sqlite: recreate table would be required; choose a safe no-op to avoid breaking migrations
            // For sqlite testing, perform a schema change via Schema::table when possible
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 20)->default('kasir')->change();
            });
        } else {
            // Fallback to Schema change which may require doctrine/dbal in some drivers
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 20)->default('kasir')->change();
            });
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            // revert to original ENUM with admin, kasir (owner will be lost if present)
            DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','kasir') NOT NULL DEFAULT 'kasir'");
        } else {
            // best-effort revert to string (no-op)
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['admin','kasir'])->default('kasir')->change();
            });
        }
    }
};
