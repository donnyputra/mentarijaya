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
        if($request->get('search') != '')
            $items = $items->whereRaw('LOWER(item.item_name) like ?', ['%'.strtolower($request->get('search')).'%']);
        
        // Implement search by item no
        if($request->get('searchitemno') != '')
            $items = $items->whereRaw('LOWER(item.item_no) like ?', ['%'.strtolower($request->get('searchitemno')).'%']);

        // Implement advanced filters
        if($request->get('rangedate') != ''){
            $exploded = explode(" - ", $request->get('rangedate'));
            $items = $items->whereDate('item.created_at', '>=', \Carbon\Carbon::parse($exploded[0])->format('Y-m-d') );
            $items = $items->whereDate('item.created_at', '<=', \Carbon\Carbon::parse($exploded[1])->format('Y-m-d') );
        }
        if($request->get('rangesalesdate') != null){
            $exploded = explode(" - ", $request->get('rangesalesdate'));
            $items = $items->whereDate('item.sales_at', '>=', \Carbon\Carbon::parse($exploded[0])->format('Y-m-d') );
            $items = $items->whereDate('item.sales_at', '<=', \Carbon\Carbon::parse($exploded[1])->format('Y-m-d') );
        }
        if($request->get('store') != '')
            $items = $items->where('item.store_id', '=', $request->get('store'));
        if($request->get('category') != '')
            $items = $items->where('item.category_id', '=', $request->get('category'));
        if($request->get('allocation') != '')
            $items = $items->where('item.allocation_id', '=', $request->get('allocation'));
        if($request->get('itemstatus') != '')
            $items = $items->where('item.item_status_id', '=', $request->get('itemstatus'));
        if($request->get('inventorystatus') != '')
            $items = $items->where('item.inventory_status_id', '=', $request->get('inventorystatus'));
        if($request->get('salesstatus') != '')
            $items = $items->where('item.sales_status_id', '=', $request->get('salesstatus'));

        //Implement sort
        $items = $items->orderBy('item.id', 'desc');

        $printed = explode(",", $request->get('printed'));

        //Implement pagination
        $itemPerPage = 10; // default
        if($request->get('itemperpage') != '')
            $itemPerPage = $request->get('itemperpage');
        $items = $items->paginate($itemPerPage);

        $pdf = PDF::loadView('pdf.items', [
            'items' => $items,
            'printed' => $printed,
        ]);

        if(count($printed)>10) {
            return $pdf->setPaper('a4', 'landscape')->stream();
        }
        return $pdf->setPaper('a4', 'portrait')->stream();
    }
}
