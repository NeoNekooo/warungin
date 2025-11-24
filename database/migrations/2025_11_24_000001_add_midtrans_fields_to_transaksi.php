<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->string('midtrans_transaction_id')->nullable()->after('nominal_bayar');
            $table->string('payment_status')->default('pending')->after('midtrans_transaction_id');
            $table->json('midtrans_raw')->nullable()->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn(['midtrans_transaction_id', 'payment_status', 'midtrans_raw']);
        });
    }
};
