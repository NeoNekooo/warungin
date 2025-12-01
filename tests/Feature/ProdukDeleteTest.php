<?php

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('allows admin to delete a product via destroy route', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $kategori = Kategori::create(['nama_kategori' => 'Umum', 'deskripsi' => null]);

    $produk = Produk::create([
        'nama_produk' => 'Produk Hapus',
        'kode_produk' => 'PRD-TEST',
        'kategori_id' => $kategori->kategori_id,
        'harga_beli' => 1000,
        'harga_jual' => 2000,
        'stok' => 5,
        'satuan' => 'pcs',
        'status' => 'aktif',
    ]);

    $this->actingAs($user)->delete(route('produk.destroy', $produk->produk_id))
        ->assertRedirect(route('produk.index'));

    $this->assertDatabaseMissing('produk', ['produk_id' => $produk->produk_id]);
});

it('requires authentication to delete product (guest redirect)', function () {
    $this->delete(route('produk.destroy', 999))->assertStatus(302);
});
