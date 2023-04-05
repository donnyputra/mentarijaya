<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $table = 'item';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'item_no', 'item_name', 'item_weight', 'item_gold_rate', 'sales_price', 'sales_at', 'item_status_id', 'inventory_status_id', 'category_id', 'allocation_id', 'sales_status_id', 'sales_by', 'store_id', 'created_by',
    ];

    public $sortable = [
        'item_no', 'item_name', 'item_weight', 'item_gold_rate', 'sales_price', 'sales_at', 'item_status_id', 'inventory_status_id', 'category_id', 'allocation_id', 'sales_status_id', 'sales_by', 'store_id', 'created_at', 'updated_at', 'created_by',
    ];

    public static function search($query)
    {
        return empty($query) ? static::query()
            : static::where('item_no', 'like', '%'.$query.'%')
                ->orWhere('item_name', 'like', '%'.$query.'%');
    }

    public function itemStatus() {
        return $this->belongsTo('App\ItemStatus');
    }

    public function inventoryStatus() {
        return $this->belongsTo('App\InventoryStatus');
    }
    
    public function category() {
        return $this->belongsTo('App\Category');
    }

    public function allocation() {
        return $this->belongsTo('App\Allocation');
    }

    public function salesStatus() {
        return $this->belongsTo('App\SalesStatus', 'sales_status_id');
    }

    public function store() {
        return $this->belongsTo('App\Store');
    }

    public function createdBy() {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function salesBy() {
        return $this->belongsTo('App\User', 'sales_by');
    }
}
