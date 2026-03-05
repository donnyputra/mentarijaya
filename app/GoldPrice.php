<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoldPrice extends Model
{
    protected $fillable = [
        'min_price',
        'max_price',
        'created_by',
        'price_date',
        'base_price',
        'notes',
        'created_by_user_id',
    ];

    protected $casts = [
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
        'base_price' => 'decimal:2',
    ];
}
