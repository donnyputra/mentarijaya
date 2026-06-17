<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoldPrice extends Model
{
    protected $fillable = [
        'price_date',
        'gold_rate',
        'inventory_status_id',
        'base_price',
        'service_fee',
        'notes',
        'created_by',
        'created_by_user_id',
    ];

    protected $casts = [
        'gold_rate' => 'decimal:2',
        'base_price' => 'decimal:2',
        'service_fee' => 'decimal:2',
    ];

    public function inventoryStatus()
    {
        return $this->belongsTo(InventoryStatus::class, 'inventory_status_id');
    }
}
