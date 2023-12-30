<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemNumber extends Model
{
    protected $fillable = [
        'category_id', 'category_code', 'number'
    ];
}
