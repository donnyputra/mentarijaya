<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Item;

class PdfController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function itemsPdf(Request $request) {
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
                        'allocation.code as allocation_code',
                        'item_status.description as item_status_description',
                        'inventory_status.description as inventory_status_description',
                        'sales_status.code as sales_status_code',
                        'sales_status.description as sales_status_description',
                        'users.name as sales_by_name',
                        'create_users.name as created_by_name'
                    )
                    ->where('item.deleted_at', '=', null);
        
        // Implement search by item name
        if($request->session()->get('filter.item_name') != '')
            $items = $items->whereRaw('LOWER(item.item_name) like ?', ['%'.strtolower($request->session()->get('filter.item_name')).'%']);
        
        // Implement search by item no
        if($request->session()->get('filter.item_no') != '')
            $items = $items->whereRaw('LOWER(item.item_no) like ?', ['%'.strtolower($request->session()->get('filter.item_no')).'%']);

        // Implement advanced filters
        if($request->session()->get('filter.rangedate') != ''){
            $exploded = explode(" - ", $request->session()->get('filter.rangedate'));
            $items = $items->whereDate('item.created_at', '>=', \Carbon\Carbon::parse($exploded[0])->format('Y-m-d') );
            $items = $items->whereDate('item.created_at', '<=', \Carbon\Carbon::parse($exploded[1])->format('Y-m-d') );
        }
        if($request->session()->get('filter.rangesalesdate') != null){
            $exploded = explode(" - ", $request->session()->get('filter.rangesalesdate'));
            $items = $items->whereDate('item.sales_at', '>=', \Carbon\Carbon::parse($exploded[0])->format('Y-m-d') );
            $items = $items->whereDate('item.sales_at', '<=', \Carbon\Carbon::parse($exploded[1])->format('Y-m-d') );
        }
        if($request->session()->get('filter.store') != '')
            $items = $items->where('item.store_id', '=', $request->session()->get('filter.store'));
        if($request->session()->get('filter.category') != '')
            $items = $items->where('item.category_id', '=', $request->session()->get('filter.category'));
        if($request->session()->get('filter.allocation') != '')
            $items = $items->where('item.allocation_id', '=', $request->session()->get('filter.allocation'));
        if($request->session()->get('filter.item_status') != '')
            $items = $items->where('item.item_status_id', '=', $request->session()->get('filter.item_status'));
        if($request->session()->get('filter.inventory_status') != '')
            $items = $items->where('item.inventory_status_id', '=', $request->session()->get('filter.inventory_status'));
        if($request->session()->get('filter.sales_status') != '')
            $items = $items->where('item.sales_status_id', '=', $request->session()->get('filter.sales_status'));

        //Implement sort
        $sortBy = '';
        switch($request->session()->get('sort.sort_by')) {
            case 'item_id':
                $sortBy = 'item.id';
                break;
            case 'item_no':
                $sortBy = 'item.item_no';
                break;
            case 'item_name':
                $sortBy = 'item.item_name';
                break;
            case 'item_weight':
                $sortBy = 'item.item_weight';
                break;
            case 'sales_price':
                $sortBy = 'item.sales_price';
                break;
            default:
                $sortBy = 'item.id';
        }

        $sortDirection = '';
        if($request->session()->has('sort.sort_direction')) {
            $sortDirection = $request->session()->get('sort.sort_direction');
        } else {
            $sortDirection = 'desc'; //default direction
        }

        $items = $items->orderBy($sortBy, $sortDirection);

        $printed = explode(",", $request->get('printed'));

        //Implement pagination
        $itemPerPage = 10; // default
        if($request->get('itemperpage') != '')
            $itemPerPage = $request->get('itemperpage');
        $items = $items->paginate($itemPerPage);

        $total_weight = 0;
        $total_price = 0;
        $item_count = 0;
        $storage_item_count = 0;
        foreach($items as $item) {
            $total_weight = $total_weight + $item->item_weight;
            $total_price = $total_price + $item->sales_price;
            if($item->allocation_id == 2) {
                $storage_item_count++;
            }
            $item_count++;
        }

        $pdf = PDF::loadView('pdf.items', [
            'items' => $items,
            'printed' => $printed,
            'total_weight' => $total_weight,
            'total_price' => $total_price,
            'item_count' => $item_count,
            'storage_item_count' => $storage_item_count
        ]);

        if(count($printed)>10) {
            return $pdf->setPaper('a4', 'landscape')->stream();
        }
        return $pdf->setPaper('a4', 'portrait')->stream();
    }
}
