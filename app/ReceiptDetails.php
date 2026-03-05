<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReceiptDetails extends Model
{
    protected $fillable = [
        'receipt_date',
        'receipt_id',
        'item_id',
        'item_no',
        'item_name',
        'item_gold_rate',
        'item_weight',
        'sales_price',
        'service_fee',
        'line_total',
    ];

    protected $casts = [
        'receipt_date' => 'datetime',
        'item_gold_rate' => 'decimal:2',
        'item_weight' => 'decimal:2',
        'sales_price' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function receipt()
    {
        return $this->belongsTo(Receipts::class, 'receipt_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
