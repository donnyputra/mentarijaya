<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Receipts extends Model
{
    public static function search($query)
    {
        return empty($query) ? static::query()
            : static::where('customer_name', 'like', '%'.$query.'%')
                ->orWhere('receipt_date', 'like', '%'.$query.'%');
    }
}
