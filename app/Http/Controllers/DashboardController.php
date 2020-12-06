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
        $summaryCollection = $this->getSummaryCollection();
        $categorySummaryCollection = $this->getInstockItemCountByCategory();
        $totalWeightSummaryCollection = $this->getInStockTotalItemWeightByCategory();

        return view('dashboard.index', [
            'summaryCollection' => $summaryCollection->get(),
            'itemCountBarChartLabel' => $categorySummaryCollection[0]->label,
            'itemCountBarChartValue' => $categorySummaryCollection[0]->value,
            'totalWeightSummaryCollection' => $totalWeightSummaryCollection
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

    private function getSummaryCollection() {
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

        return $summaryCollection;
    }

    private function getInstockItemCountByCategory() {
        // $instockItem = DB::table('category')
        //                     ->select(DB::raw("
        //                     category.CODE AS category_code,
        //                     COUNT(*) AS item_count
        //                 "))
        //                 ->leftJoin('item', 'item.category_id', '=', 'category.id')
        //                 ->leftJoin('item_status', 'item.item_status_id', '=', 'item_status.id')
        //                 // ->where('item_status.code', '=', 'instock')
        //                 // ->whereNull('item.sales_at')
        //                 ->groupBy('category_code');

        $instockItem = DB::SELECT("
                        WITH src AS (
                            SELECT 
                                category.code AS category_code,
                                COUNT(item.id) AS item_count
                            FROM category
                            LEFT JOIN item ON category.id = item.category_id AND item.sales_at IS NULL AND item.item_status_id = 2
                            GROUP BY category_code
                        )
                        
                        SELECT
                            GROUP_CONCAT(CONCAT('''',category_code,'''')) AS label,
                            GROUP_CONCAT(item_count) AS value
                        FROM src
                    ");
        
        return $instockItem;
    }

    private function getInStockTotalItemWeightByCategory() {
        $instockItem = DB::SELECT("
                        SELECT
                            item.item_gold_rate,
                            SUM(CASE WHEN category.code = 'A' THEN item.item_weight ELSE 0 END) AS A,
                            SUM(CASE WHEN category.code = 'CK' THEN item.item_weight ELSE 0 END) AS CK,
                            SUM(CASE WHEN category.code = 'C' THEN item.item_weight ELSE 0 END) AS C,
                            SUM(CASE WHEN category.code = 'GL' THEN item.item_weight ELSE 0 END) AS GL,
                            SUM(CASE WHEN category.code = 'K' THEN item.item_weight ELSE 0 END) AS K,
                            SUM(CASE WHEN category.code = 'L' THEN item.item_weight ELSE 0 END) AS L,
                            SUM(CASE WHEN category.code = 'PT' THEN item.item_weight ELSE 0 END) AS PT,
                            SUM(CASE WHEN category.code = 'W' THEN item.item_weight ELSE 0 END) AS W
                        FROM item
                        JOIN category ON item.category_id = category.id
                        JOIN item_status ON item.item_status_id = item_status.id
                        WHERE item.sales_at IS NULL
                            AND item_status.CODE = 'instock'
                        GROUP BY item_gold_rate
                    ");

        return $instockItem;
    }

}