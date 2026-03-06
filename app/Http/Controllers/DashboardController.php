<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use DataTables;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Item;
use App\GoldPrice;

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
        if(Auth::id() == 1) {
            $summaryCollection = $this->getSummaryCollection();
            $categorySummaryCollection = $this->getInstockItemCountByCategory();
            $totalWeightSummaryCollection = $this->getInStockTotalItemWeightByCategory();
    
            return view('dashboard.index', [
                'summaryCollection' => $summaryCollection,
                'totalWeightSummaryCollection' => $totalWeightSummaryCollection,
                'itemsCount' => $categorySummaryCollection,
                'todayBaseGoldPriceList' => $this->getTodayBaseGoldPriceList(),
            ]);
        }else{
            return view('home');
        }
    }

    public function summaryDailyDatatable(Request $request) {
        if ($request->ajax()) {
            return $this->getSummaryCollection();
        }
    }

    private function getSummaryCollection() {
        $summaryCollection = DB::table('item')
                            ->select(DB::raw("
                                DATE(item.sales_at) AS 'sales_date', 
                                item.item_gold_rate as item_gold_rate,
                                SUM(item.item_weight) AS total_weight,
                                SUM(item.sales_price) AS total_sales,
                                ROUND(SUM(item.sales_price) / SUM(item.item_weight)) AS average,
                                GROUP_CONCAT(category.CODE) AS item_category_list,
                                MAX(item.id) AS max_item_id
                            "))
                            ->leftJoin('category', 'item.category_id', '=', 'category.id')
                            ->whereNotNull('item.sales_at')
                            ->groupBy('sales_date', 'item_gold_rate');

        return Datatables::of($summaryCollection->get())
            ->addColumn('date_sales', function($row){
                return Carbon::parse($row->sales_date)->format('d-M-Y');
            })
            ->addColumn('gold_rate', function($row){
                return number_format($row->item_gold_rate, 1, ',', '.') . "%";
            })
            ->addColumn('weight', function($row){
                return number_format($row->total_weight, 2, ',', '.') . " gr";
            })
            ->addColumn('sales', function($row){
                if ($row->total_sales == null) {
                    return "-";
                } else {
                    return "Rp. " . number_format($row->total_sales, 0, ',', '.');
                }
            })
            ->addColumn('avg', function($row){
                if ($row->average == null) {
                    return "-";
                } else {
                    return "Rp. " . number_format($row->average, 0, ',', '.');
                }
            })
            ->addColumn('item_count', function($row){
                $temp = '';
                foreach (array_count_values(explode(',', $row->item_category_list)) as $soldKey => $soldValue){
                    $temp .= '<span class="badge badge-pill badge-primary">'.$soldKey.':'.$soldValue.'</span>';
                }
                return $temp;
            })
            ->rawColumns(['item_count'])
            ->make(true);
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
                SELECT
                category.code AS category_code,
                COUNT(item.id) AS item_count
            FROM
                category
            LEFT JOIN item ON category.id = item.category_id AND item.sales_at IS NULL AND item.item_status_id = 2
            WHERE category.deleted_at is null
            GROUP BY
                category_code
            ORDER BY case
            	when category_code = 'A' then 1
            	when category_code = 'CK' then 2
            	when category_code = 'C' then 3
            	when category_code = 'W' then 4
            	when category_code = 'L' then 5
            	when category_code = 'GL' then 6
            	when category_code = 'K' then 7
            	else 8
            	end asc
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
                            SUM(CASE WHEN category.code = 'W' THEN item.item_weight ELSE 0 END) AS W,
                            SUM(item.item_weight) AS TOTAL
                        FROM item
                        JOIN category ON item.category_id = category.id
                        JOIN item_status ON item.item_status_id = item_status.id
                        WHERE item.sales_at IS NULL
                            AND LOWER(item_status.CODE) = 'instock'
                            AND item.deleted_at IS NULL
                        GROUP BY item_gold_rate
                    ");

        return $instockItem;
    }

    private function getTodayBaseGoldPriceList()
    {
        $today = Carbon::today()->format('Y-m-d');
        $hasGoldRateColumn = Schema::hasColumn('gold_prices', 'gold_rate');
        $hasInventoryStatusColumn = Schema::hasColumn('gold_prices', 'inventory_status_id');
        $inventoryStatusMap = [];
        $inventoryStatusOrderMap = [];

        $goldPriceQuery = GoldPrice::query();
        if ($hasInventoryStatusColumn) {
            $goldPriceQuery = $goldPriceQuery->with('inventoryStatus');
            $inventoryStatuses = \App\InventoryStatus::withTrashed()
                ->orderBy('id', 'asc')
                ->get(['id', 'description']);
            foreach ($inventoryStatuses as $index => $inventoryStatus) {
                $inventoryStatusMap[$inventoryStatus->id] = $inventoryStatus->description;
                $inventoryStatusOrderMap[$inventoryStatus->id] = $index;
            }
        }
        if (Schema::hasColumn('gold_prices', 'price_date')) {
            $goldPriceQuery = $goldPriceQuery
                ->whereDate('price_date', '<=', $today)
                ->orderBy('price_date', 'desc');
        } else {
            $goldPriceQuery = $goldPriceQuery
                ->whereDate('created_at', '<=', $today)
                ->orderBy('created_at', 'desc');
        }

        $goldPrices = $goldPriceQuery
            ->orderBy('id', 'desc')
            ->get();

        $result = [];
        $seenPairs = [];
        foreach ($goldPrices as $goldPrice) {
            $displayBasePrice = $goldPrice->base_price ?? $goldPrice->max_price ?? $goldPrice->min_price;
            if ($displayBasePrice === null) {
                continue;
            }

            $displayGoldRate = $hasGoldRateColumn ? $goldPrice->gold_rate : null;
            $displayInventoryStatus = null;
            if ($hasInventoryStatusColumn) {
                $displayInventoryStatus = optional($goldPrice->inventoryStatus)->description;
                if ($displayInventoryStatus === null && $goldPrice->inventory_status_id !== null) {
                    $displayInventoryStatus = $inventoryStatusMap[$goldPrice->inventory_status_id] ?? null;
                }
            }

            if ($hasGoldRateColumn && $displayGoldRate === null) {
                continue;
            }
            if ($hasInventoryStatusColumn && $displayInventoryStatus === null) {
                continue;
            }

            $pairKey = ($hasGoldRateColumn && $displayGoldRate !== null
                    ? number_format((float) $displayGoldRate, 2, '.', '')
                    : 'no_rate')
                . '|' . ($hasInventoryStatusColumn ? (string) ($goldPrice->inventory_status_id ?? 'no_status') : 'no_status');
            if (isset($seenPairs[$pairKey])) {
                continue;
            }
            $seenPairs[$pairKey] = true;

            $result[] = [
                'base_price' => $displayBasePrice,
                'service_fee' => $goldPrice->service_fee ?? 0,
                'gold_rate' => $displayGoldRate,
                'inventory_status' => $displayInventoryStatus,
                'inventory_status_id' => $goldPrice->inventory_status_id,
            ];
        }

        usort($result, function ($left, $right) use ($inventoryStatusOrderMap) {
            $leftRate = $left['gold_rate'] !== null ? (float) $left['gold_rate'] : INF;
            $rightRate = $right['gold_rate'] !== null ? (float) $right['gold_rate'] : INF;
            if ($leftRate !== $rightRate) {
                return $leftRate <=> $rightRate;
            }

            $leftOrder = $inventoryStatusOrderMap[$left['inventory_status_id']] ?? PHP_INT_MAX;
            $rightOrder = $inventoryStatusOrderMap[$right['inventory_status_id']] ?? PHP_INT_MAX;

            return $leftOrder <=> $rightOrder;
        });

        foreach ($result as &$row) {
            unset($row['inventory_status_id']);
        }
        unset($row);

        return $result;
    }

}
