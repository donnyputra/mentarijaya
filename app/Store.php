<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
	use SoftDeletes;

    protected $guarded = [];

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

    public static function search($query)
    {
        return empty($query) ? static::query()
            : static::where('code', 'like', '%'.$query.'%')
                ->orWhere('name', 'like', '%'.$query.'%')
                ->orWhere('phone_no', 'like', '%'.$query.'%')
                ->orWhere('address', 'like', '%'.$query.'%');
    }
}
