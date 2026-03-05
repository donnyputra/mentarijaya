<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoldPrice extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $table = 'gold_prices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'min_price', 'max_price', 'created_by'
    ];

    public $sortable = [
        'created_at'
    ];
}
