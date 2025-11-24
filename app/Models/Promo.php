<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;

    protected $table = 'promos';

    protected $fillable = [
        'name', 'title', 'code', 'description', 'discount', 'percent', 'type', 'active', 'starts_at', 'ends_at', 'meta'
    ];

    protected $casts = [
        'discount' => 'float',
        'percent' => 'integer',
        'active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'meta' => 'array',
    ];
}
