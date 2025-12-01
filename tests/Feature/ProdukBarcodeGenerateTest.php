<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('returns a unique barcode string for product creation', function () {
    $user = User::factory()->create([ 'role' => 'admin' ]);

    $this->actingAs($user)->getJson(route('produk.generateBarcode'))
        ->assertStatus(200)
        ->assertJsonStructure(['kode_barcode'])
        ->assertJson(fn($json) => $json->whereType('kode_barcode', 'string'));
});

it('generates different codes on subsequent requests', function () {
    $user = User::factory()->create([ 'role' => 'admin' ]);

    $first = $this->actingAs($user)->getJson(route('produk.generateBarcode'))->json('kode_barcode');
    $second = $this->actingAs($user)->getJson(route('produk.generateBarcode'))->json('kode_barcode');

    expect($first)->not()->toBe($second);
});

it('redirects unauthenticated users to login for the endpoint', function () {
    // unauthenticated call should be redirected to login (web middleware)
    $this->get(route('produk.generateBarcode'))
        ->assertStatus(302);
});
