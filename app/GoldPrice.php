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
        'gold_rate',
        'inventory_status_id',
        'base_price',
        'service_fee',
        'notes',
        'created_by_user_id',
    ];

    protected $casts = [
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
        'gold_rate' => 'decimal:2',
        'base_price' => 'decimal:2',
        'service_fee' => 'decimal:2',
    ];

    public function inventoryStatus()
    {
        return $this->belongsTo(InventoryStatus::class, 'inventory_status_id');
    }
}
