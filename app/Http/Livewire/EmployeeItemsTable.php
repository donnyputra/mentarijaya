<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmployeeItemsTable extends Component
{
    use WithPagination;	

    const INITIAL_SORT_FIELD = 'id';

    public $perPage = 10;
    public $sortField = self::INITIAL_SORT_FIELD;
    public $sortAsc = false;
    public $search = '';

    // FILTER
    public $filterStore = '';
    public $filterCategory = '';
    public $filterAllocation = '';
    public $filterItemStatus = '';
    public $filterSalesStatus = '';
    public $filterInventoryStatus = '';

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
        $items = null;

        $items = DB::table('item')
                ->join('store', 'store.id', '=', 'item.store_id')
                ->join('category', 'category.id', '=', 'item.category_id')
                ->join('allocation', 'allocation.id', '=', 'item.allocation_id')
                ->join('item_status', 'item_status.id', '=', 'item.item_status_id')
                ->join('inventory_status', 'inventory_status.id', '=', 'item.inventory_status_id')
                ->leftJoin('users as create_users', 'create_users.id', '=', 'item.created_by')
                ->leftJoin('users', 'users.id', '=', 'item.sales_by')
                ->leftJoin('sales_status', 'sales_status.id', '=', 'item.sales_status_id')
                ->select(
                    'item.*',
                    'store.code as store_code',
                    'store.name as store_name',
                    'category.description as category_description',
                    'allocation.description as allocation_description',
                    'item_status.description as item_status_description',
                    'inventory_status.description as inventory_status_description',
                    'sales_status.code as sales_status_code',
                    'sales_status.description as sales_status_description',
                    'users.name as sales_by_name',
                    'create_users.name as created_by_name'
                )
                ->where('item.deleted_at', '=', null)
                ->where('item.created_by', '=', Auth::user()->id)                
                ->where(function($query) {
                    $query->whereNull('item.item_approved_at');
                    $query->whereNull('item.sales_approved_at');
                })

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
                ->where(function($queryFilterInventoryStatusInput) {
                    return $queryFilterInventoryStatusInput->when($this->filterInventoryStatus, function($queryFilterInventoryStatusResult, $filterInventoryStatusId){
                        return $queryFilterInventoryStatusResult->where('item.inventory_status_id', '=', $filterInventoryStatusId);
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
                                ->orWhere('inventory_status.description', 'like', '%'.$searchKeyword.'%')
                                ->orWhere('sales_status.description', 'like', '%'.$searchKeyword.'%')
                                ->orWhere('users.name', 'like', '%'.$searchKeyword.'%');
                    });
                })
                ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                ->paginate($this->perPage);

                // dd(DB::getQueryLog());

        return view('livewire.employee-items-table', [
            'items' => $items,
            'stores' => \App\Store::all(),
            'categories' => \App\Category::all(),
            'allocations' => \App\Allocation::all(),
            'itemstatuses' => \App\ItemStatus::all(),
            'inventorystatuses' => \App\InventoryStatus::all(),
            'salesstatuses' => \App\SalesStatus::all(),
        ]);
    }
}
