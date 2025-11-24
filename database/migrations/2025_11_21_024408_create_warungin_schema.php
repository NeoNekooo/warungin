<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. USERS (Master)
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id'); // Custom PK
            $table->string('nama');
            $table->string('username')->unique();
            $table->string('password'); // Laravel default auth column
            $table->enum('role', ['admin', 'kasir']);
            $table->string('email')->nullable()->unique();
            $table->string('no_hp')->nullable();
            $table->tinyInteger('status')->default(1); // 1: Aktif, 0: Nonaktif
            $table->timestamps();
        });

        // Tabel Wajib Laravel (Session & Reset Password)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // 2. KATEGORI
        Schema::create('kategori', function (Blueprint $table) {
            $table->id('kategori_id');
            $table->string('nama_kategori');
            $table->text('deskripsi')->nullable();
            $table->timestamps(); // Opsional jika butuh created_at
        });

        // 3. PRODUK
        Schema::create('produk', function (Blueprint $table) {
            $table->id('produk_id');
            $table->string('nama_produk');
            $table->string('kode_produk')->unique();
            $table->string('kode_barcode')->unique()->nullable();
            // Relasi ke kategori_id
            $table->foreignId('kategori_id')->constrained('kategori', 'kategori_id');
            $table->decimal('harga_beli', 15, 2);
            $table->decimal('harga_jual', 15, 2);
            $table->integer('stok')->default(0);
            $table->string('satuan')->default('pcs');
            $table->text('deskripsi')->nullable();
            $table->string('gambar_url')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });

        // 4. PELANGGAN
        Schema::create('pelanggan', function (Blueprint $table) {
            $table->id('pelanggan_id');
            $table->string('nama');
            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();
            $table->string('email')->nullable();
            $table->enum('member_level', ['regular', 'silver', 'gold'])->default('regular');
            $table->integer('poin')->default(0);
            $table->timestamps();
        });

        // 5. PENGATURAN TOKO
        Schema::create('pengaturan_toko', function (Blueprint $table) {
            $table->id('toko_id');
            $table->string('nama_toko');
            $table->text('alamat')->nullable();
            $table->string('no_telp')->nullable();
            $table->string('email')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('npwp')->nullable();
            $table->string('qris_code')->nullable();
            $table->timestamps();
        });

        // 6. TRANSAKSI (Header)
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('transaksi_id');
            $table->dateTime('tanggal');
            // Relasi Kasir ke users.user_id
            $table->foreignId('kasir_id')->constrained('users', 'user_id');
            $table->foreignId('pelanggan_id')->nullable()->constrained('pelanggan', 'pelanggan_id');
            $table->decimal('total', 15, 2);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('pajak', 15, 2)->default(0);
            $table->enum('metode_bayar', ['tunai', 'qris', 'transfer']);
            $table->decimal('nominal_bayar', 15, 2);
            $table->decimal('kembalian', 15, 2);
            $table->enum('status', ['selesai', 'pending', 'batal'])->default('pending');
            $table->timestamps();
        });

        // 7. TRANSAKSI DETAIL
        Schema::create('transaksi_detail', function (Blueprint $table) {
            $table->id('detail_id');
            $table->foreignId('transaksi_id')->constrained('transaksi', 'transaksi_id')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produk', 'produk_id');
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->decimal('diskon_item', 15, 2)->default(0);
            $table->timestamps();
        });

        // 8. PEMBAYARAN (Opsional jika detail split payment)
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id('pembayaran_id');
            $table->foreignId('transaksi_id')->constrained('transaksi', 'transaksi_id')->onDelete('cascade');
            $table->string('metode');
            $table->decimal('jumlah', 15, 2);
            $table->string('referensi')->nullable();
            $table->timestamps();
        });

        // 9. STOK LOG
        Schema::create('stok_log', function (Blueprint $table) {
            $table->id('stok_id');
            $table->foreignId('produk_id')->constrained('produk', 'produk_id');
            $table->dateTime('tanggal');
            $table->enum('tipe', ['masuk', 'keluar']);
            $table->integer('jumlah');
            $table->string('sumber')->nullable(); // ex: 'Supplier A' atau 'Transaksi #1'
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->constrained('users', 'user_id'); // Siapa yang input
            $table->timestamps();
        });

        // 10. LAPORAN
        Schema::create('laporan', function (Blueprint $table) {
            $table->id('laporan_id');
            $table->string('periode'); // Bulanan/Harian
            $table->decimal('total', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan');
        Schema::dropIfExists('stok_log');
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('transaksi_detail');
        Schema::dropIfExists('transaksi');
        Schema::dropIfExists('pengaturan_toko');
        Schema::dropIfExists('pelanggan');
        Schema::dropIfExists('produk');
        Schema::dropIfExists('kategori');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
