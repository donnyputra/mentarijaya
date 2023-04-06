<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Item;

class ItemController extends Controller
{
    const ITEM_NO_SEPARATOR = '-';
    const MAX_ITEM_NO_IN_BOOK = 9999;

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
        $stores = \App\Store::all();
        $categories = \App\Category::all();
        $allocations = \App\Allocation::all();
        $itemstatuses = \App\ItemStatus::all();
        $inventorystatuses = \App\InventoryStatus::all();
        $salesstatuses = \App\SalesStatus::all();
        $users = \App\User::all();

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

        //Implement pagination
        $itemPerPage = 10; // default
        if($request->session()->get('filter.itemperpage') != '')
            $itemPerPage = $request->session()->get('filter.itemperpage');
        $items = $items->paginate($itemPerPage);

        // dd($items->toSql());

        return view('items.index', [
            'stores' => $stores,
            'categories' => $categories,
            'allocations' => $allocations,
            'itemstatuses' => $itemstatuses,
            'inventorystatuses' => $inventorystatuses,
            'salesstatuses' => $salesstatuses,
            'users' => [Auth::user()],
            'items' => $items
        ]);
    }

    public function applyfilter(Request $request) {
        if(count($request->all()) > 0) {
            $request->session()->put('filter', $request->all());
        }

        return redirect()->route('items.index');
    }

    public function clearfilter(Request $request) {
        $request->session()->forget('filter');
        return redirect()->route('items.index');
    }

    public function applysort(Request $request) {
        if(count($request->all()) > 0) {
            $request->session()->put('sort', $request->all());
        }

        return redirect()->route('items.index');
    }

    public function massaction(Request $request) {
        try {
            $itemIds = explode(",", $request->get('mass_action_data'));
            $actionName = strtolower($request->get('mass_action'));
            $approvedAt = \Carbon\Carbon::now()->toDateTimeString();

            switch($actionName) {
                case 'approveitems':
                    $instockItemStatus = \App\ItemStatus::where('code', '=', 'instock')->first();
                    foreach($itemIds as $itemId) {
                        $item = \App\Item::findOrFail($itemId);
                        if($item->item_status_id == $instockItemStatus->id) continue;

                        $item->item_status_id = $instockItemStatus->id;
                        $item->item_approved_at = $approvedAt;
                        $item->save();
                    }
                break;
                case 'approvesales':
                    //retrieve salesstatus
                    $submittedSalesStatus = \App\SalesStatus::where('code', '=', 'submitted')->first();
                    $completedSalesStatus = \App\SalesStatus::where('code', '=', 'completed')->first();

                    //retrieve itemstatus
                    $soldItemStatus = \App\ItemStatus::where('code', '=' , 'sold')->first();

                    //update item
                    foreach($itemIds as $itemId) {
                        $item = \App\Item::findOrFail($itemId);
                        if($item->sales_status_id == $submittedSalesStatus->id) {
                            $item->item_status_id = $soldItemStatus->id;
                            $item->sales_status_id = $completedSalesStatus->id;
                            $item->sales_approved_at = $approvedAt;
                            $item->save();
                        }
                    }
                break;
                case 'refunditems':
                    $soldItemStatus = \App\ItemStatus::where('code', '=' , 'sold')->first();
                    $instockItemStatus = \App\ItemStatus::where('code', '=', 'instock')->first();

                    foreach($itemIds as $itemId) {
                        $item = \App\Item::findOrFail($itemId);
                        if($item->item_status_id == $soldItemStatus->id) {
                            $item->sales_status_id = null;
                            $item->sales_by = null;
                            $item->sales_price = null;
                            $item->sales_at = null;
                            $item->item_status_id = $instockItemStatus->id;
                            $item->save();
                        }
                    }
                default:
            }
        } catch (\Exception $ex) {
            return redirect('/items')->with('error', $ex->getMessage());
        }

        return redirect('/items')->with('success', __('Mass actions has been executed.'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $stores = \App\Store::all();
        $categories = \App\Category::all();
        $allocations = \App\Allocation::all();
        $itemstatuses = \App\ItemStatus::all();
        $inventorystatuses = \App\InventoryStatus::all();
        $salesstatuses = \App\SalesStatus::all();
        $users = \App\User::all();

        return view('items.create', [
            'stores' => $stores,
            'categories' => $categories,
            'allocations' => $allocations,
            'itemstatuses' => $itemstatuses,
            'inventorystatuses' => $inventorystatuses,
            'salesstatuses' => $salesstatuses,
            'users' => [Auth::user()],
        ]);
    }

    private function getCurrentCountGroupByCategoryID() {
        $result = DB::table('item')
                    ->select(DB::raw('category_id, count(*) as cnt'))
                    ->groupBy('category_id')
                    ->orderBy('category_id', 'asc')
                    ->get();

        if($result->count() <= 0)
            return null;

        return $result->keyBy('category_id')->toArray();
    }

    private function generateItemNo($categoryId) {
        // $currentYear = date("Y");
        $category = \App\Category::findOrFail($categoryId);

        $arrCurrentCount = $this->getCurrentCountGroupByCategoryID($categoryId);

        $nextItemIncrementId = 1;
        if($arrCurrentCount != null)
            if(array_key_exists($categoryId, $arrCurrentCount))
                $nextItemIncrementId = (int)$arrCurrentCount[$categoryId]->cnt + 1;

        $itemCount = \App\Item::count();
        $bookNo = ceil(($itemCount + 1) / self::MAX_ITEM_NO_IN_BOOK);

        $paddedId = str_pad($nextItemIncrementId, 4, "0", STR_PAD_LEFT);

        $itemNo = $category->code . self::ITEM_NO_SEPARATOR . $bookNo . self::ITEM_NO_SEPARATOR . $paddedId;

        return $itemNo;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            // 'item_no' => 'required',
            'item_name' => 'required',
            'item_weight' => 'required',
            'item_gold_rate' => 'required',
            'item_status_id' => 'numeric|required',
            'inventory_status_id' => 'numeric|required',
            'category_id' => 'numeric|required',
            'allocation_id' => 'numeric|required',
            'store_id' => 'numeric|required',
            'sales_price' => 'numeric|nullable',
            'sales_at' => 'date|nullable',
            'sales_by' => 'nullable',
            'sales_status_id' => 'nullable',
            'created_by' => 'required',
        ]);

        $item = new Item([
            'item_no' => $this->generateItemNo($request->get('category_id')),
            'item_name' => $request->get('item_name'),
            'item_weight' => $request->get('item_weight'),
            'item_gold_rate' => $request->get('item_gold_rate'),
            'item_status_id' => $request->get('item_status_id'),
            'inventory_status_id' => $request->get('inventory_status_id'),
            'category_id' => $request->get('category_id'),
            'allocation_id' => $request->get('allocation_id'),
            'store_id' => $request->get('store_id'),
            'sales_price' => $request->get('sales_price'),
            'sales_at' => ($request->get('sales_at') != null) ? \Carbon\Carbon::createFromFormat('m/d/Y', $request->get('sales_at'))->format('Y-m-d H:i:s') : null,
            'sales_by' => $request->get('sales_by'),
            'sales_status_id' => $request->get('sales_status_id'),
            'created_by' => $request->get('created_by'),
        ]);
        $item->save();

        if(Auth::user()->authRole()->name == 'employee') {
            return redirect('/employee/items/index')->with('success', __('Item has been created.'));
        }
        
        return redirect('/items')->with('success', __('Item has been created.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $item = Item::findOrFail($id);
        } catch (Exception $ex) {
            return redirect()->route('items.index')->withError($ex->getMessage());
        }

        return view('items.show', [
            'item' => $item
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $item = Item::findOrFail($id);
        } catch (Exception $ex) {
            return redirect()->route('items.index')->withError($ex->getMessage());
        }

        $stores = \App\Store::all();
        $categories = \App\Category::all();
        $allocations = \App\Allocation::all();
        $itemstatuses = \App\ItemStatus::all();
        $inventorystatuses = \App\InventoryStatus::all();
        
        $salesstatuses = null;
        if(Auth::user()->authRole()->name == 'admin') {
            $salesstatuses = \App\SalesStatus::all();
        } else {
            $salesstatuses = \App\SalesStatus::where('code', 'submitted')->get();
        }
        
        $users = null;
        if(Auth::user()->authRole()->name == 'admin') {
            $users = \App\User::all();
        } else {
            $users = [Auth::user()];   
        }

        return view('items.edit', [
            'stores' => $stores,
            'categories' => $categories,
            'allocations' => $allocations,
            'itemstatuses' => $itemstatuses,
            'inventorystatuses' => $inventorystatuses,
            'salesstatuses' => $salesstatuses,
            'users' => $users,
            'item' => $item,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'item_no' => 'required',
                'item_name' => 'required',
                'item_weight' => 'required',
                'item_gold_rate' => 'required',
                'item_status_id' => 'numeric|required',
                'inventory_status_id' => 'numeric|required',
                'category_id' => 'numeric|required',
                'allocation_id' => 'numeric|required',
                'store_id' => 'numeric|required',
                'sales_price' => 'nullable',
                'sales_at' => 'date|nullable',
                'sales_by' => 'nullable',
                'sales_status_id' => 'nullable',
                'created_by' => 'required',
            ]);

            $itemStatusSold = \App\ItemStatus::where('code', '=', 'sold')->first();

            $item = Item::findOrFail($id);
            $item->item_no = $request->get("item_no");
            $item->item_name = $request->get("item_name");
            $item->item_weight = $request->get("item_weight");
            $item->item_gold_rate = $request->get("item_gold_rate");
            $item->inventory_status_id = $request->get("inventory_status_id");
            $item->category_id = $request->get("category_id");
            $item->allocation_id = $request->get("allocation_id");
            $item->store_id = $request->get("store_id");
            $item->sales_price = str_replace(",", "", explode(".", $request->get("sales_price"))[0]);
            $item->sales_at = ($request->get('sales_at') != null) ? \Carbon\Carbon::createFromFormat('m/d/Y', $request->get('sales_at'))->format('Y-m-d H:i:s') : null;
            $item->sales_by = $request->get("sales_by");
            $item->sales_status_id = $request->get("sales_status_id");
            $item->item_status_id = ($request->get("sales_status_id") != NULL) ? $itemStatusSold->id : $request->get("item_status_id");
            $item->created_by = $request->get('created_by');
            $item->save();

        } catch (Exception $ex) {
            return redirect('/items')->with('error', $ex->getMessage());
        }

        if(Auth::user()->authRole()->name == 'employee') {
            return redirect('/employee/items/index')->with('success', __('Item has been updated.'));
        }

        return redirect('/items')->with('success', __('Item has been updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        try {
            $item = Item::findOrFail($id);
            $item->delete();
        } catch (Exception $ex) {
            return redirect('/items')->with('error', $ex->getMessage());
        }

        if(Auth::user()->authRole()->name == 'employee') {
            return redirect('/employee/items/index')->with('success', __('Item has been deleted.'));
        }

        return redirect('/items')->with('success', __('Item has been deleted.'));
    }

    public function bulkupload() {
        return view('items.bulkupload');
    }

    public function downloadCsvTemplate() {
        try {
            // echo "download CSV template";
            $fileName = "items_template.csv";
            $headers = array(
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );

            $columns = [
                'item_no', 
                'item_name', 
                'item_weight', 
                'item_gold_rate', 
                'item_status_code', 
                'inventory_status_code', 
                'category_code', 
                'allocation_code', 
                // 'sales_status_code', 
                // 'sales_price', 
                // 'sales_at', 
                // 'sales_by', 
                'store_code'
            ];

            if(Item::count() > 0) {
                $itemModel = Item::first();
                $item = [
                    $itemModel->item_code,
                    $itemModel->item_name, 
                    $itemModel->item_weight, 
                    $itemModel->item_gold_rate,
                    'new', //$itemModel->item_status_code, 
                    'general', // $itemModel->inventory_status_code, 
                    'K', //$itemModel->category_code, 
                    'storage', //$itemModel->allocation_code, 
                    // 'submitted', // $itemModel->sales_status_code, 
                    // $itemModel->sales_price, 
                    // $itemModel->sales_at, 
                    // $itemModel->sales_by, 
                    \App\Store::where('id', $itemModel->store_id)->first()->code,
                ];
            } else {
                $item = [
                    'sample', // 'item_no', 
                    'sample', // 'item_name', 
                    'sample', // 'item_weight', 
                    'sample', // 'item_gold_rate', 
                    'sample', // 'item_status_code', 
                    'sample', // 'inventory_status_code', 
                    'sample', // 'category_code', 
                    'sample', // 'allocation_code', 
                    // 'sample', // 'sales_status_code', 
                    // 'sample', // 'sales_price', 
                    // 'sample (yyyy-mm-dd)', // 'sales_at', 
                    // 'sample', // 'sales_by', 
                    'sample', // 'store_code'
                ];
            }
            
            $callback = function() use($item, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);
                fputcsv($file, $item);
                fclose($file);
            };
        
        } catch (Exception $ex) {
            return redirect('/items')->with('error', $ex->getMessage());
        }

        return response()->stream($callback, 200, $headers);
    }

    public function importcsv(Request $request) {
        try {
            $request->validate([
                'uploaded_csv' => 'required',
            ]);

            // Read CSV
            if($request->hasFile('uploaded_csv')) {
                $file = $request->file('uploaded_csv');
                $row = 0;

                $arrayItemData = [];

                if (($handle = fopen($file, "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        
                        // skip header
                        if($row > 0) {
                            $content = [
                                'item_no' => $data[0],
                                'item_name' => $data[1],
                                'item_weight' => $data[2],
                                'item_gold_rate' => $data[3],
                                'item_status_code' => $data[4],
                                'inventory_status_code' => $data[5],
                                'category_code' => $data[6],
                                'allocation_code' => $data[7],
                                'store_code' => $data[8],
                            ];
                            array_push($arrayItemData, $content);
                        }

                        $row++;
                    }
                    fclose($handle);
                }

                try {
                    DB::beginTransaction();

                    // processing array data to DB
                    foreach($arrayItemData as $itemData) {
                        $itemStatus = \App\ItemStatus::where('code', $itemData['item_status_code'])->firstOrFail();
                        $inventoryStatus = \App\InventoryStatus::where('code', $itemData['inventory_status_code'])->firstOrFail();
                        $category = \App\Category::where('code', $itemData['category_code'])->firstOrFail();
                        $allocation = \App\Allocation::where('code', $itemData['allocation_code'])->firstOrFail();
                        $store = \App\Store::where('code', $itemData['store_code'])->firstOrFail();

                        if($itemData['item_no'] != '') { //update
                            $item = Item::where('item_no', $itemData['item_no'])->firstOrFail();
                            if($item != null) {
                                $item->item_name = $itemData['item_name'];
                                $item->item_weight = $itemData['item_weight'];
                                $item->item_gold_rate = $itemData['item_gold_rate'];
                                $item->item_status_id = $itemStatus->id;
                                $item->inventory_status_id = $inventoryStatus->id;
                                $item->category_id = $category->id;
                                $item->allocation_id = $allocation->id;
                                $item->store_id = $store->id;
                                // $item->updated_by = auth()->user()->id;
                                $item->save();
                            } else {
                                throw new \Exception(__('Items bulk upload was failed. "Item No is not found."'));
                            }
                        } else { //insert
                            $item = new Item([
                                'item_no' => $this->generateItemNo(\App\Category::where('code', $itemData['category_code'])->first()->id),
                                'item_name' => $itemData['item_name'],
                                'item_weight' => $itemData['item_weight'],
                                'item_gold_rate' => $itemData['item_gold_rate'],
                                'item_status_id' => $itemStatus->id,
                                'inventory_status_id' => $inventoryStatus->id,
                                'category_id' => $category->id,
                                'allocation_id' => $allocation->id,
                                'store_id' => $store->id,
                                'created_by' => auth()->user()->id,
                                // 'updated_by' => auth()->user()->id,
                            ]);
                            $item->save();                            
                        }
                    }
                    
                    DB::commit();
                    return redirect('/items')->with('success', __('Items bulk upload has been processed successfully.'));

                } catch(ModelNotFoundException $ex) {
                    DB::rollback();
                    $message = '';
                    switch(strtolower($ex->getMessage())) {
                        case 'no query results for model [app\item].':
                            $message = 'Item is not found.';
                            break;
                        case 'no query results for model [app\itemstatus].':
                            $message = 'Item status is not found.';
                            break;
                        case 'no query results for model [app\inventorystatus].':
                            $message = 'Inventory status is not found.';
                            break;
                        case 'no query results for model [app\category].':
                            $message = 'Category is not found.';
                            break;
                        case 'no query results for model [app\allocation].':
                            $message = 'Allocation is not found.';
                            break;
                        case 'no query results for model [app\store].':
                            $message = 'Store is not found.';
                            break;
                        default:
                            $ex->getMessage();
                    }
                    throw new \Exception(__('Items bulk upload was failed. ') . $message);
                } catch(\Exception $ex) {
                    DB::rollback();
                    throw new \Exception(__('Items bulk upload was failed. Please check your data in csv.'));
                }
            }

        } catch (\Exception $ex) {
            return redirect()->back()->with('error', $ex->getMessage());
        }
    }

    private function getSummaryItemWeightPerCategoryByEmployee($userId) {
        $result = DB::SELECT("
                        SELECT
                            category.code AS category_code,
                            category.description AS category_name,
                            SUM(item.item_weight) AS sum_weight
                        FROM item
                        LEFT JOIN category ON item.category_id = category.id
                        WHERE created_by = ".$userId."
                        AND item.item_approved_at IS NULL
                        GROUP BY category.code, category.description
                    ");
        
        return $result;
    }

    private function getSummaryItemCountPerCategoryByEmployee($userId) {
        $result = DB::SELECT("
                        SELECT
                            category.code AS category_code,
                            category.description AS category_name,
                            COUNT(item.id) AS count_item
                        FROM item
                        LEFT JOIN category ON item.category_id = category.id
                        WHERE created_by = ".$userId."
                        AND item.item_approved_at IS NULL
                        GROUP BY category.code, category.description
                    ");
        
        return $result;
    }

    public function employeeItemIndex() {
        $data = [
            'summary_total_weight_per_category' => $this->getSummaryItemWeightPerCategoryByEmployee(Auth::user()->id),
            'summary_total_count_per_category' => $this->getSummaryItemCountPerCategoryByEmployee(Auth::user()->id),
        ];

        // dd($data);

        return view('items.employee.item.index', $data);
    }

    public function employeeItemCreate() {
        $stores = \App\Store::all();
        $categories = \App\Category::all();
        $allocations = \App\Allocation::all();
        $itemstatuses = \App\ItemStatus::all();
        $inventorystatuses = \App\InventoryStatus::all();
        $users = \App\User::all();

        return view('items.employee.item.create', [
            'stores' => $stores,
            'categories' => $categories,
            'allocations' => $allocations,
            'itemstatuses' => $itemstatuses,
            'inventorystatuses' => $inventorystatuses,
            'users' => [Auth::user()],
        ]);
    }

    public function employeeItemStore(Request $request) {
        $request->validate([
            'item_name' => 'required',
            'item_weight' => 'required',
            'item_gold_rate' => 'required',
            'item_status_id' => 'numeric|required',
            'inventory_status_id' => 'numeric|required',
            'category_id' => 'numeric|required',
            'allocation_id' => 'numeric|required',
            'store_id' => 'numeric|required',
            'created_by' => 'required',
        ]);

        $item = new Item([
            'item_no' => $this->generateItemNo($request->get('category_id')),
            'item_name' => $request->get('item_name'),
            'item_weight' => $request->get('item_weight'),
            'item_gold_rate' => $request->get('item_gold_rate'),
            'item_status_id' => $request->get('item_status_id'),
            'inventory_status_id' => $request->get('inventory_status_id'),
            'category_id' => $request->get('category_id'),
            'allocation_id' => $request->get('allocation_id'),
            'store_id' => $request->get('store_id'),
            'created_by' => $request->get('created_by'),
        ]);
        $item->save();

        if($request->get('action') == 'save entry sales') {
            return redirect('/employee/sales/form/' . $item->id)->with('success', __('Item has been created.'));
        }
        
        return redirect('/employee/items/index')->with('success', __('Item has been created.'));
    }

    private function getSummarySoldItemWeightPerCategoryByEmployee($userId) {
        $result = DB::SELECT("
                        SELECT
                            category.code AS category_code,
                            category.description AS category_name,
                            SUM(item.item_weight) AS sum_weight
                        FROM item
                        LEFT JOIN category ON item.category_id = category.id
                        WHERE sales_by = ".$userId."
                        AND item.sales_status_id = 1
                        AND item.sales_approved_at IS NULL
                        GROUP BY category.code, category.description
                    ");
        
        return $result;
    }

    private function getSummaryTotalSalesPricePerCategoryByEmployee($userId) {
        $result = DB::SELECT("
                        SELECT
                            category.code AS category_code,
                            category.description AS category_name,
                            SUM(item.sales_price) AS total_sales
                        FROM item
                        LEFT JOIN category ON item.category_id = category.id
                        WHERE sales_by = ".$userId."
                        AND item.sales_status_id = 1
                        AND item.sales_approved_at IS NULL
                        GROUP BY category.code, category.description
                    ");
        
        return $result;
    }

    private function getSummaryTotalCountSoldItemPerCategoryByEmployee($userId) {
        $today = \Carbon\Carbon::now()->format('Y-m-d');
        $result = DB::SELECT("
                        SELECT
                            category.code AS category_code,
                            category.description AS category_name,
                            COUNT(item.id) AS item_count
                        FROM item
                        LEFT JOIN category ON item.category_id = category.id
                        WHERE sales_by = ".$userId."
                        AND item.sales_status_id = 1
                        AND DATE(sales_at) = '".$today."'
                        AND item.sales_approved_at IS NULL
                        GROUP BY category.code, category.description
                    ");
        
        return $result;
    }

    private function getEmployeeTodaySales($userId) {
        $today = \Carbon\Carbon::now()->format('Y-m-d');
        $query = "SELECT
                    DATE_FORMAT(DATE(sales_at), '%d-%b-%Y') AS sales_at,
                    item_no,
                    item_weight,
                    sales_price
                FROM item
                WHERE sales_by = ".$userId."
                    AND DATE(sales_at) = '".$today."'
                    AND sales_approved_at IS NULL
                ORDER BY sales_at DESC";
        $result = DB::SELECT($query);
        
        return $result;
    }

    public function employeeSalesEntry() {
        $data = [
            // 'summary_total_weight_per_category' => $this->getSummarySoldItemWeightPerCategoryByEmployee(Auth::user()->id),
            // 'summary_total_count_per_category' => $this->getSummaryTotalSalesPricePerCategoryByEmployee(Auth::user()->id),
            'total_count_sold_items' => $this->getSummaryTotalCountSoldItemPerCategoryByEmployee(Auth::user()->id),
            'today_list' => $this->getEmployeeTodaySales(Auth::user()->id),
        ];
        
        return view('items.employee.sales.entry', $data);
    }

    public function employeeItemFind(Request $request) {
        $itemNo = $request->get('item_no');
        $completedSalesStatus = \App\SalesStatus::where('code', 'completed')->first();

        $item = \App\Item::where('item_no', $itemNo)
                ->whereNull('sales_approved_at')
                ->first();

        if($item) {
            return redirect('/employee/sales/form/' . $item->id);
        }

        return redirect()->back()->with('error', __('Item No ' . $itemNo . ' is not searchable.'));
    }

    public function employeeSalesForm($itemId) {
        $salesstatus = \App\SalesStatus::where('code', 'submitted')->first();

        $item = \App\Item::where('id', $itemId)->first();

        return view('items.employee.sales.form', [
            'item' => $item,
            'salesstatus' => $salesstatus,
        ]);
    }

    public function employeeSalesFormSave(Request $request) {
        $itemId = $request->get('item_id');

        try {
            $request->validate([
                'sales_price' => 'numeric|required',
                'sales_at' => 'date|required',
                'sales_by_id' => 'required',
                'sales_status_id' => 'required',
            ]);

            //retrieve item status
            $soldItemStatus = \App\ItemStatus::where('code', '=', 'sold')->first();

            $item = \App\Item::where('id', $itemId)->first();
            $item->sales_price = $request->get("sales_price");
            $item->sales_at = ($request->get('sales_at') != null) ? \Carbon\Carbon::createFromFormat('m/d/Y', $request->get('sales_at'))->format('Y-m-d H:i:s') : null;
            $item->sales_by = $request->get("sales_by_id");
            $item->sales_status_id = $request->get("sales_status_id");
            $item->item_status_id = $soldItemStatus->id;
            $item->save();

        } catch (Exception $ex) {
            if(Auth::user()->authRole()->name == 'employee') {
                return redirect('/employee/items/index')->with('success', __('Item has been updated.'));
            }

            return redirect('/items')->with('error', $ex->getMessage());
        }

        if(Auth::user()->authRole()->name == 'employee') {
            return redirect('/employee/items/index')->with('success', __('Item has been updated.'));
        }

        return redirect('/items')->with('success', __('Item has been updated.'));
    }
}