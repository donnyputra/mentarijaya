<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Item;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $querySummary = <<<EOT
        //     WITH item_dtl AS (
        //         SELECT
        //             DATE(sales_at) AS sales_date,
        //             item_gold_rate,
        //             GROUP_CONCAT(category.CODE) AS item_category_list
        //         FROM item
        //         LEFT JOIN category ON item.category_id = category.id
        //         WHERE item.sales_at IS NOT NULL
        //         GROUP BY 1, 2
        //     ),
            
        //     summary AS (
        //         SELECT
        //             DATE(item.sales_at) AS sales_date,
        //             item.item_gold_rate,
        //             SUM(item.item_weight) AS total_weight,
        //             SUM(item.sales_price) AS total_sales,
        //             ROUND(SUM(item.sales_price) / SUM(item.item_weight)) AS average
        //         FROM item
        //         WHERE item.sales_at IS NOT NULL
        //         GROUP BY 1, 2
        //     )
            
        //     SELECT
        //         summary.*,
        //         item_dtl.item_category_list
        //     FROM summary
        //     JOIN item_dtl ON summary.sales_date = item_dtl.sales_date AND summary.item_gold_rate = item_dtl.item_gold_rate	
        // EOT;
        // $summaryCollection = DB::select($querySummary);
        $summaryCollection = DB::table('item')
                            ->select(DB::raw("
                                DATE(item.sales_at) AS 'sales_date', 
                                item.item_gold_rate,
                                SUM(item.item_weight) AS total_weight,
                                SUM(item.sales_price) AS total_sales,
                                ROUND(SUM(item.sales_price) / SUM(item.item_weight)) AS average,
                                GROUP_CONCAT(category.CODE) AS item_category_list,
                                MAX(item.id) AS max_item_id
                            "))
                            ->leftJoin('category', 'item.category_id', '=', 'category.id')
                            ->whereNotNull('item.sales_at')
                            ->groupBy('sales_date', 'item_gold_rate');

        //Implement sort
        $summaryCollection->orderBy('sales_date', 'desc');

        // dd($items->toSql());

        //Implement pagination
        $itemPerPage = 10; // default
        // $summaryCollection = $summaryCollection->paginate($itemPerPage);

        return view('dashboard.index', [
            'summaryCollection' => $summaryCollection->get()
        ]);
        
        
        // // Implement search by item name
        // if($request->get('search') != '')
        //     $items = $items->whereRaw('LOWER(item.item_name) like ?', ['%'.strtolower($request->get('search')).'%']);
        
        // // Implement search by item no
        // if($request->get('searchitemno') != '')
        //     $items = $items->whereRaw('LOWER(item.item_no) like ?', ['%'.strtolower($request->get('searchitemno')).'%']);

        // // Implement advanced filters
        // if($request->get('startdate') != '')
        //     $items = $items->whereDate('item.created_at', '>=', \Carbon\Carbon::parse($request->get('startdate'))->format('Y-m-d') );
        // if($request->get('enddate') != '')
        //     $items = $items->whereDate('item.created_at', '<=', \Carbon\Carbon::parse($request->get('enddate'))->format('Y-m-d') );
        // if($request->get('salesstartdate') != '')
        //     $items = $items->whereDate('item.sales_at', '>=', \Carbon\Carbon::parse($request->get('salesstartdate'))->format('Y-m-d') );
        // if($request->get('salesenddate') != '')
        //     $items = $items->whereDate('item.sales_at', '<=', \Carbon\Carbon::parse($request->get('salesenddate'))->format('Y-m-d') );
        // if($request->get('store') != '')
        //     $items = $items->where('item.store_id', '=', $request->get('store'));
        // if($request->get('category') != '')
        //     $items = $items->where('item.category_id', '=', $request->get('category'));
        // if($request->get('allocation') != '')
        //     $items = $items->where('item.allocation_id', '=', $request->get('allocation'));
        // if($request->get('itemstatus') != '')
        //     $items = $items->where('item.item_status_id', '=', $request->get('itemstatus'));
        // if($request->get('inventorystatus') != '')
        //     $items = $items->where('item.inventory_status_id', '=', $request->get('inventorystatus'));
        // if($request->get('salesstatus') != '')
        //     $items = $items->where('item.sales_status_id', '=', $request->get('salesstatus'));

    }

}