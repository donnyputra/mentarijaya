<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Receipts extends Model
{
    protected $fillable = [
        'uuid',
        'receipt_date',
        'customer_name',
        'customer_address',
        'receipt_total',
        'receipt_total_string',
        'store_id',
        'sales_by',
    ];

    protected $casts = [
        'receipt_date' => 'datetime',
        'receipt_total' => 'decimal:2',
    ];

    public static function search($query)
    {
        return empty($query) ? static::query()
            : static::where('uuid', 'like', '%'.$query.'%')
                ->orWhere('customer_name', 'like', '%'.$query.'%')
                ->orWhere('customer_address', 'like', '%'.$query.'%')
                ->orWhere('receipt_date', 'like', '%'.$query.'%');
    }

    public function details()
    {
        return $this->hasMany(ReceiptDetails::class, 'receipt_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function salesUser()
    {
        return $this->belongsTo(User::class, 'sales_by');
    }

    public function isApproved()
    {
        if (!$this->relationLoaded('details')) {
            $this->load('details.item');
        } elseif ($this->details->contains(function ($detail) {
            return !$detail->relationLoaded('item');
        })) {
            $this->load('details.item');
        }

        if ($this->details->isEmpty()) {
            return false;
        }

        return !$this->details->contains(function ($detail) {
            return !$detail->item || $detail->item->sales_approved_at === null;
        });
    }

    public function getShortUuidAttribute()
    {
        if (!$this->uuid) {
            return (string) $this->id;
        }

        return strtoupper(substr(str_replace('-', '', $this->uuid), 0, 8));
    }
}
