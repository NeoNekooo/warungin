<?php

use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('stores kembalian when paying cash (tunai) with nominal_bayar', function () {
    // create a user and product
    $user = User::factory()->create();

    $product = Produk::create([
        'nama_produk' => 'Tes Produk',
        'harga_jual' => 5000,
        'stok' => 10,
    ]);

    $payload = [
        'items' => [
            ['produk_id' => $product->produk_id, 'jumlah' => 1]
        ],
        'metode_bayar' => 'tunai',
        'nominal_bayar' => 10000,
    ];

    $response = $this->actingAs($user)->postJson(route('pos.pay'), $payload);

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    // check transaksi record created with correct nominal_bayar and kembalian
    $this->assertDatabaseHas('transaksi', [
        'nominal_bayar' => 10000,
        'kembalian' => 5000,
    ]);
});
