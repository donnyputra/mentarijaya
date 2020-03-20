<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Store extends Model
{
	use SoftDeletes, Sortable;

    protected $table = 'store';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'name', 'address', 'phone_no',
    ];

    public $sortable = [
        'code', 'name', 'address', 'phone_no', 'created_at', 'updated_at',
    ];
}
