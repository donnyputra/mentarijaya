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

    // FILTER
    public $filterStore = '';
    public $filterCategory = '';
    public $filterAllocation = '';
    public $filterItemStatus = '';
    public $filterSalesStatus = '';

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

                    // apply advanced filter
                    ->where(function($queryFilterStoreInput) {
                        return $queryFilterStoreInput->when($this->filterStore, function($queryFilterStoreResult, $filterStoreId){
                            return $queryFilterStoreResult->where('item.store_id', '=', $filterStoreId);
                        });
                    })
                    ->where(function($queryFilterCategoryInput) {
                        return $queryFilterCategoryInput->when($this->filterCategory, function($queryFilterCategoryResult, $filterCategoryId){
                            return $queryFilterCategoryResult->where('item.category_id', '=', $filterCategoryId);
                        });
                    })
                    ->where(function($queryFilterAllocationInput) {
                        return $queryFilterAllocationInput->when($this->filterAllocation, function($queryFilterAllocationResult, $filterAllocationId){
                            return $queryFilterAllocationResult->where('item.allocation_id', '=', $filterAllocationId);
                        });
                    })
                    ->where(function($queryFilterItemStatusInput) {
                        return $queryFilterItemStatusInput->when($this->filterItemStatus, function($queryFilterItemStatusResult, $filterItemStatusId){
                            return $queryFilterItemStatusResult->where('item.item_status_id', '=', $filterItemStatusId);
                        });
                    })
                    ->where(function($queryFilterSalesStatusInput) {
                        return $queryFilterSalesStatusInput->when($this->filterSalesStatus, function($queryFilterSalesStatusResult, $filterSalesStatusId){
                            return $queryFilterSalesStatusResult->where('item.sales_status_id', '=', $filterSalesStatusId);
                        });
                    })

                    // apply search textbox
                    ->where(function($querySearchInput){
                        return $querySearchInput->when($this->search, function($querySearchResult, $searchKeyword) {
                            return $querySearchResult->where('item.item_name', 'like', '%'.$searchKeyword.'%')
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
            'items' => $items,
            'stores' => \App\Store::all(),
            'categories' => \App\Category::all(),
            'allocations' => \App\Allocation::all(),
            'itemstatuses' => \App\ItemStatus::all(),
            'salesstatuses' => \App\SalesStatus::all(),
        ]);
    }
}
