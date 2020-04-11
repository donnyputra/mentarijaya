<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class ItemsTable extends Component
{
	use WithPagination;	

    const INITIAL_SORT_FIELD = 'item_no';

    public $perPage = 10;
    public $sortField = self::INITIAL_SORT_FIELD;
    public $sortAsc = true;
    public $search = '';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }

    public function render()
    {
        // DB::enableQueryLog();

        $items = DB::table('item')
                    ->join('store', 'store.id', '=', 'item.store_id')
                    ->join('category', 'category.id', '=', 'item.category_id')
                    ->join('allocation', 'allocation.id', '=', 'item.allocation_id')
                    ->join('item_status', 'item_status.id', '=', 'item.item_status_id')
                    ->leftJoin('users', 'users.id', '=', 'item.sales_by')
                    ->leftJoin('sales_status', 'sales_status.id', '=', 'item.sales_status_id')
                    ->select(
                        'item.*',
                        'store.code as store_code',
                        'store.name as store_name',
                        'category.description as category_description',
                        'allocation.description as allocation_description',
                        'item_status.description as item_status_description',
                        'sales_status.code as sales_status_code',
                        'users.name as sales_by_name'
                    )
                    ->where('item.deleted_at', '=', null)
                    ->where(function($query1){
                        return $query1->when($this->search, function($query2, $searchKeyword) {
                            return $query2->where('item.item_name', 'like', '%'.$searchKeyword.'%')
                                    ->orWhere('store.name', 'like', '%'.$searchKeyword.'%')
                                    ->orWhere('category.description', 'like', '%'.$searchKeyword.'%')
                                    ->orWhere('allocation.description', 'like', '%'.$searchKeyword.'%')
                                    ->orWhere('item_status.description', 'like', '%'.$searchKeyword.'%')
                                    ->orWhere('sales_status.description', 'like', '%'.$searchKeyword.'%')
                                    ->orWhere('users.name', 'like', '%'.$searchKeyword.'%');
                        });
                    })
                    ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                    ->paginate($this->perPage);

                    // dd(DB::getQueryLog());

        return view('livewire.items-table', [
            'items' => $items
        ]);
    }
}
