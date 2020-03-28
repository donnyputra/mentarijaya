<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesStatus extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $table = 'sales_status';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'description',
    ];

    public $sortable = [
        'code', 'description', 'created_at', 'updated_at',
    ];

    public static function search($query)
    {
        return empty($query) ? static::query()
            : static::where('code', 'like', '%'.$query.'%')
                ->orWhere('description', 'like', '%'.$query.'%');
    }
}
