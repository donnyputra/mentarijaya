<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;

use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Events\AdminTransactionCreated;
use App\Item;
use App\ItemNumber;
use App\Notifications\EmployeeTransactionSubmittedNotification;
use App\ReceiptDetails;
use App\Receipts;

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
        $availableGoldRates = DB::table('item')
            ->whereNull('deleted_at')
            ->whereNotNull('item_gold_rate')
            ->select('item_gold_rate')
            ->distinct()
            ->orderBy('item_gold_rate', 'asc')
            ->pluck('item_gold_rate')
            ->map(function ($goldRate) {
                return number_format((float) $goldRate, 2, '.', '');
            })
            ->values()
            ->all();

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
        $selectedGoldRates = $request->session()->get('filter.gold_rates', []);
        if (!is_array($selectedGoldRates)) {
            $selectedGoldRates = [$selectedGoldRates];
        }
        $selectedGoldRates = array_values(array_filter(array_map(function ($goldRate) {
            if ($goldRate === null || $goldRate === '') {
                return null;
            }

            if (!is_numeric($goldRate)) {
                return null;
            }

            return number_format((float) $goldRate, 2, '.', '');
        }, $selectedGoldRates)));
        if (count($selectedGoldRates) > 0) {
            $items = $items->whereIn('item.item_gold_rate', $selectedGoldRates);
        }

        //Implement sort
        $sortBy = '';
        switch($request->session()->get('sort.sort_by')) {
            case 'item_id':
                $sortBy = 'item.id';
                break;
            case 'item_no':
                $sortBy = "CASE WHEN LOCATE('-', item.item_no) > 0 THEN 0 ELSE 1 END, CAST(REGEXP_SUBSTR(item.item_no, '[0-9]+') AS UNSIGNED), CASE WHEN LOCATE('-', item.item_no) > 0 THEN CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(item.item_no, '-', -1), '-', 1) AS UNSIGNED) ELSE NULL END";
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

        $items = $items->orderByRaw($sortBy . ' ' . $sortDirection);

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
            'items' => $items,
            'availableGoldRates' => $availableGoldRates,
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
                    $soldItemStatus = \App\ItemStatus::where('code', '=', 'sold')->first();
                    foreach($itemIds as $itemId) {
                        $item = \App\Item::findOrFail($itemId);
                        if($item->item_status_id == $instockItemStatus->id) continue;

                        if($item->item_status_id != $soldItemStatus->id){
                            $item->item_status_id = $instockItemStatus->id;
                        }
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
                            $this->syncReceiptSnapshotForItem($item);
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
                            $item->base_gold_price = null;
                            if (Schema::hasColumn('item', 'base_service_fee')) {
                                $item->base_service_fee = null;
                            }
                            if (Schema::hasColumn('item', 'service_fee')) {
                                $item->service_fee = null;
                            }
                            $item->sales_at = null;
                            $item->item_status_id = $instockItemStatus->id;
                            $item->sales_approved_at = null;
                            $item->save();
                            $this->removeReceiptSnapshotForItem($item);
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
        $category = \App\Category::findOrFail($categoryId);

        $itemNumber = ItemNumber::firstOrCreate(
            ['category_id' => $categoryId],
            ['category_code' => $category->code, 'number' => 0]
        );
        $number = $itemNumber->number+1;

        $itemNo = $category->code . $number;
        $itemNumber->number = $number;
        $itemNumber->save();
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

        if ($request->images){
            $last_item_id = $item->id;
            foreach($request->images as $key => $image)
            {
                $imageName = Str::uuid().'.'.$image->extension();  
                $image->move(public_path('img'), $imageName);
            
                $images[]['img_url'] = $imageName;
            }

            foreach ($images as $key => $image) {
                $image['item_id'] = $last_item_id;
                \App\Photos::create($image);
            }
        }
        

        if(Auth::user()->authRole()->name == 'employee') {
            return redirect('/employee/sales/form/' . $item->id)->with('success', __('Item has been created.'));
        }
        
        return redirect('/admin/sales/form/' . $item->id)->with('success', __('Item has been created.'));
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
            $photos = \App\Photos::where('item_id', $id)->get();
            $receiptDetail = $this->findReceiptDetailForItem($id);
        } catch (\Exception $ex) {
            return redirect()->route('items.index')->withError($ex->getMessage());
        }

        return view('items.show', [
            'item' => $item,
            'photos' => $photos,
            'receiptDetail' => $receiptDetail,
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
            $receiptDetail = $this->findReceiptDetailForItem($id);
        } catch (\Exception $ex) {
            return redirect()->route('items.index')->withError($ex->getMessage());
        }

        $stores = \App\Store::all();
        $categories = \App\Category::all();
        $allocations = \App\Allocation::all();
        $itemstatuses = \App\ItemStatus::all();
        $inventorystatuses = \App\InventoryStatus::all();
        $photos = \App\Photos::where('item_id', $id)->get();
        
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
            'photos' => $photos,
            'receiptDetail' => $receiptDetail,
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
                'service_fee' => 'nullable|numeric|min:0',
                'item_note' => 'nullable|string|max:1000',
                'customer_name' => 'nullable|string|max:255',
                'customer_address' => 'nullable|string|max:255',
            ]);

            $itemStatusSold = \App\ItemStatus::where('code', '=', 'sold')->first();
            $submittedSalesStatus = \App\SalesStatus::where('code', '=', 'submitted')->first();
            $completedSalesStatus = \App\SalesStatus::where('code', '=', 'completed')->first();

            $item = Item::findOrFail($id);
            $isAdmin = Auth::user()->authRole()->name == 'admin';
            $item->item_no = $request->get("item_no");
            $item->item_name = $request->get("item_name");
            $item->item_weight = $request->get("item_weight");
            $item->item_gold_rate = $request->get("item_gold_rate");
            $item->inventory_status_id = $request->get("inventory_status_id");
            $item->category_id = $request->get("category_id");
            $item->allocation_id = $request->get("allocation_id");
            $item->store_id = $request->get("store_id");
            if ($isAdmin) {
                $item->sales_price = ($request->get('sales_price') != null) ? $request->get("sales_price") : null;
                if (Schema::hasColumn('item', 'service_fee')) {
                    $item->service_fee = ($request->get('service_fee') != null) ? $request->get("service_fee") : null;
                }
                $item->sales_at = ($request->get('sales_at') != null) ? \Carbon\Carbon::createFromFormat('m/d/Y', $request->get('sales_at'))->format('Y-m-d H:i:s') : null;
                $item->sales_by = $request->get("sales_by");
                $item->sales_status_id = $request->get("sales_status_id");
                if ($item->sales_status_id === null || ($submittedSalesStatus && (int) $item->sales_status_id === (int) $submittedSalesStatus->id)) {
                    $item->sales_approved_at = null;
                }
                if ($completedSalesStatus && (int) $item->sales_status_id === (int) $completedSalesStatus->id) {
                    $item->sales_approved_at = \Carbon\Carbon::now()->toDateTimeString();
                }
                $item->item_status_id = ($request->get("sales_status_id") != NULL) ? $itemStatusSold->id : $request->get("item_status_id");
                if ($item->sales_status_id !== null) {
                    $todayGoldPriceSetting = $this->getTodayGoldPriceSetting($item->item_gold_rate, $item->inventory_status_id);
                    $item->base_gold_price = $todayGoldPriceSetting['base_price'] ?? null;
                    if (Schema::hasColumn('item', 'base_service_fee')) {
                        $item->base_service_fee = $todayGoldPriceSetting['service_fee'] ?? null;
                    }
                } else {
                    $item->base_gold_price = null;
                    if (Schema::hasColumn('item', 'base_service_fee')) {
                        $item->base_service_fee = null;
                    }
                    if (Schema::hasColumn('item', 'service_fee')) {
                        $item->service_fee = null;
                    }
                }
            } else {
                $item->item_status_id = ($item->sales_status_id != NULL) ? $itemStatusSold->id : $request->get("item_status_id");
            }
            $item->created_by = $request->get('created_by');
            $item->save();

            if ($item->sales_status_id !== null && $item->sales_at !== null) {
                $this->syncReceiptSnapshotForItem($item, [
                    'notes' => $request->get('item_note'),
                    'customer_name' => $request->get('customer_name'),
                    'customer_address' => $request->get('customer_address'),
                ]);
            } else {
                $this->removeReceiptSnapshotForItem($item);
            }

        } catch (\Exception $ex) {
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
            $itemStatusSold = \App\ItemStatus::where('code', '=', 'sold')->first();
            $item->item_status_id = $itemStatusSold->id;
            $item->save();
            $item->delete();
        } catch (\Exception $ex) {
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
        
        } catch (\Exception $ex) {
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

        if ($request->images){
            $last_item_id = $item->id;
            foreach($request->images as $key => $image)
            {
                $imageName = Str::uuid().'.'.$image->extension();  
                $image->move(public_path('img'), $imageName);
            
                $images[]['img_url'] = $imageName;
            }

            foreach ($images as $key => $image) {
                $image['item_id'] = $last_item_id;
                \App\Photos::create($image);
            }
        }

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
            'todayGoldPriceList' => $this->getTodayGoldPriceList(),
            'salesEntryRouteName' => $this->getSalesEntryRouteName(),
            'salesFindRouteName' => $this->getSalesFindRouteName(),
            'salesSearchItemsRouteName' => $this->getSalesSearchItemsRouteName(),
        ];
        
        return view('items.employee.sales.entry', $data);
    }

    public function employeeItemFind(Request $request) {
        $itemNo = trim((string) $request->get('item_no'));

        $item = $this->getEmployeeSalesSearchItemsQuery()
                ->where('item_no', $itemNo)
                ->first();

        if($item) {
            return redirect()->route($this->getSalesFormRouteName(), ['itemId' => $item->id]);
        }

        return redirect()->back()->with('error', __('Item No ' . $itemNo . ' is not searchable.'));
    }

    public function employeeSearchAvailableItems(Request $request)
    {
        $term = trim((string) $request->get('term', ''));

        $query = $this->getEmployeeSalesSearchItemsQuery();

        if ($term !== '') {
            $query->where('item_no', 'like', '%' . $term . '%');
        }

        $items = $query
            ->orderBy('item_no', 'asc')
            ->limit(20)
            ->get(['item_no', 'item_name', 'item_weight']);

        $results = $items->map(function ($item) {
            return [
                'id' => $item->item_no,
                'text' => $item->item_no . ' - ' . $item->item_name . ' (' . number_format($item->item_weight, 2, ',', '.') . ' gr)',
            ];
        })->values();

        return response()->json([
            'results' => $results,
        ]);
    }

    public function employeeSalesForm($itemId) {
        $salesstatus = \App\SalesStatus::where('code', 'submitted')->first();

        $item = \App\Item::where('id', $itemId)->first();
        $selectedSalesStatus = $salesstatus;
        if ($item && $item->sales_status_id) {
            $selectedSalesStatus = \App\SalesStatus::where('id', $item->sales_status_id)->first() ?? $salesstatus;
        }

        $salesById = Auth::id();
        $salesByName = Auth::user()->name;
        if ($item && $item->sales_by) {
            $existingSalesBy = \App\User::where('id', $item->sales_by)->first();
            if ($existingSalesBy) {
                $salesById = $existingSalesBy->id;
                $salesByName = $existingSalesBy->name;
            }
        }

        $todayGoldPriceSetting = $item
            ? $this->getTodayGoldPriceSetting($item->item_gold_rate, $item->inventory_status_id)
            : null;
        $todayBaseGoldPrice = $todayGoldPriceSetting['base_price'] ?? null;
        $todayServiceFee = $todayGoldPriceSetting['service_fee'] ?? null;
        $recommendedItemPrice = null;
        $recommendedServiceFee = null;
        $recommendedSalesPrice = null;

        if ($item) {
            if ($todayBaseGoldPrice !== null) {
                $recommendedItemPrice = round((float) $item->item_weight * (float) $todayBaseGoldPrice, 2);
            }
            if ($todayServiceFee !== null) {
                $recommendedServiceFee = round((float) $item->item_weight * (float) $todayServiceFee, 2);
            }
            if ($recommendedItemPrice !== null && $recommendedServiceFee !== null) {
                $recommendedSalesPrice = max(0, round($recommendedItemPrice + $recommendedServiceFee, 2));
            }
        }

        $defaultSalesPrice = $item && $item->sales_price !== null
            ? round((float) $item->sales_price, 2)
            : $recommendedItemPrice;
        $defaultServiceFee = (Schema::hasColumn('item', 'service_fee') && $item && $item->service_fee !== null)
            ? round((float) $item->service_fee, 2)
            : $recommendedServiceFee;

        return view('items.employee.sales.form', [
            'item' => $item,
            'salesstatus' => $selectedSalesStatus,
            'salesById' => $salesById,
            'salesByName' => $salesByName,
            'todayBaseGoldPrice' => $todayBaseGoldPrice,
            'todayServiceFee' => $todayServiceFee,
            'recommendedItemPrice' => $recommendedItemPrice,
            'recommendedServiceFee' => $recommendedServiceFee,
            'recommendedSalesPrice' => $recommendedSalesPrice,
            'receiptDetail' => $item ? $this->findReceiptDetailForItem($item->id) : null,
            'salesEntryRouteName' => $this->getSalesEntryRouteName(),
            'salesFormSaveRouteName' => $this->getSalesFormSaveRouteName(),
            'defaultSalesPrice' => $defaultSalesPrice,
            'defaultServiceFee' => $defaultServiceFee,
        ]);
    }

    public function checkoutEntry()
    {
        return view('items.employee.checkout.entry', [
            'checkoutSubmitRouteName' => $this->getCheckoutSubmitRouteName(),
            'checkoutCreateItemRouteName' => $this->getCheckoutCreateItemRouteName(),
            'checkoutCreateItemData' => $this->getCheckoutCreateItemData(),
        ]);
    }

    public function checkoutCreateItem(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'item_weight' => 'required',
            'item_gold_rate' => 'required',
            'inventory_status_id' => 'required|numeric|exists:inventory_status,id',
            'category_id' => 'required|numeric|exists:category,id',
            'store_id' => 'required|numeric|exists:store,id',
            'images.*' => 'nullable|image',
        ]);

        $itemWeight = $this->normalizeLocalizedPrice($request->get('item_weight'));
        $itemGoldRate = $this->normalizeLocalizedPrice($request->get('item_gold_rate'));

        if ($itemWeight === null || !is_numeric($itemWeight) || (float) $itemWeight <= 0) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'item_weight' => __('Item weight format is invalid.'),
            ]);
        }

        if ($itemGoldRate === null || !is_numeric($itemGoldRate) || (float) $itemGoldRate <= 0) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'item_gold_rate' => __('Gold rate format is invalid.'),
            ]);
        }

        $dalamAllocation = $this->getCheckoutDalamAllocation();
        $newItemStatus = \App\ItemStatus::where('code', 'new')->firstOrFail();

        $item = DB::transaction(function () use ($request, $itemWeight, $itemGoldRate, $dalamAllocation, $newItemStatus) {
            $item = new Item([
                'item_no' => $this->generateItemNo($request->get('category_id')),
                'item_name' => $request->get('item_name'),
                'item_weight' => round((float) $itemWeight, 2),
                'item_gold_rate' => round((float) $itemGoldRate, 2),
                'item_status_id' => $newItemStatus->id,
                'inventory_status_id' => $request->get('inventory_status_id'),
                'category_id' => $request->get('category_id'),
                'allocation_id' => $dalamAllocation->id,
                'store_id' => $request->get('store_id'),
                'created_by' => Auth::id(),
            ]);
            $item->save();

            $this->storeItemPhotos($item, $request->file('images', []));

            return $item;
        });

        return response()->json([
            'status' => 'ok',
            'message' => __('Item has been created.'),
            'item' => $this->buildCheckoutSelectableItemPayload($item),
        ]);
    }

    public function getItemsPagination(Request $request)
    {
        $term = trim((string) $request->get('term', ''));
        $page = max((int) $request->get('page', 1), 1);
        $perPage = 20;

        $query = \App\Item::query()
            ->whereNull('sales_status_id')
            ->whereNull('sales_approved_at')
            ->whereNull('deleted_at');

        if ($term !== '') {
            $query->where(function ($builder) use ($term) {
                $builder->where('item_no', 'like', $term . '%')
                    ->orWhere('item_name', 'like', $term . '%');
            });
        }

        $paginator = $query->orderBy('item_no')
            ->simplePaginate($perPage, ['id', 'item_no', 'item_name', 'item_weight', 'item_gold_rate', 'store_id', 'inventory_status_id'], 'page', $page);

        $goldPriceSettings = $this->getTodayGoldPriceSettingsForItems($paginator->getCollection());

        $results = $paginator->getCollection()->map(function ($item) use ($goldPriceSettings) {
            return $this->buildCheckoutSelectableItemPayload($item, $goldPriceSettings);
        })->values();

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $paginator->hasMorePages(),
            ],
        ]);
    }

    public function checkoutSubmit(Request $request)
    {
        $this->assertReceiptSnapshotSchema();

        $request->validate([
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'required|integer',
            'sales_prices' => 'required|array|min:1',
            'sales_prices.*' => 'required|numeric|min:0',
            'service_fees' => 'nullable|array',
            'service_fees.*' => 'nullable|numeric|min:0',
            'item_notes' => 'nullable|array',
            'item_notes.*' => 'nullable|string|max:1000',
            'sales_at' => 'required|date',
            'customer_name' => 'nullable|string|max:255',
            'customer_address' => 'nullable|string|max:255',
        ]);

        $itemIds = array_values(array_filter($request->get('item_ids', []), function ($id) {
            return $id !== null && $id !== '';
        }));
        $salesPrices = array_values($request->get('sales_prices', []));
        $serviceFees = array_values($request->get('service_fees', []));
        $itemNotes = array_values($request->get('item_notes', []));

        if (count($itemIds) !== count($salesPrices)) {
            return redirect()->back()->withInput()->with('error', __('Checkout data is not aligned.'));
        }

        if (count(array_unique($itemIds)) !== count($itemIds)) {
            return redirect()->back()->withInput()->with('error', __('Duplicate item detected in cart.'));
        }

        try {
            $transactionResult = DB::transaction(function () use ($itemIds, $salesPrices, $serviceFees, $itemNotes, $request) {
                $submittedSalesStatus = \App\SalesStatus::where('code', 'submitted')->firstOrFail();
                $soldItemStatus = \App\ItemStatus::where('code', 'sold')->firstOrFail();
                $salesAt = \Carbon\Carbon::createFromFormat('m/d/Y', $request->get('sales_at'))->format('Y-m-d H:i:s');

                $items = \App\Item::whereIn('id', $itemIds)
                    ->whereNull('sales_status_id')
                    ->whereNull('sales_approved_at')
                    ->whereNull('deleted_at')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                if ($items->count() !== count($itemIds)) {
                    throw new \RuntimeException(__('One or more items are no longer available for checkout.'));
                }

                $payload = [];

                foreach ($itemIds as $index => $itemId) {
                    $item = $items->get((int) $itemId);

                    if (!$item) {
                        throw new \RuntimeException(__('Item data mismatch during checkout.'));
                    }

                    $salesPrice = (float) $salesPrices[$index];
                    $serviceFee = (float) ($serviceFees[$index] ?? 0);
                    $todayGoldPriceSetting = $this->getTodayGoldPriceSetting($item->item_gold_rate, $item->inventory_status_id);

                    $item->sales_price = $salesPrice;
                    $item->base_gold_price = $todayGoldPriceSetting['base_price'] ?? null;
                    if (Schema::hasColumn('item', 'base_service_fee')) {
                        $item->base_service_fee = $todayGoldPriceSetting['service_fee'] ?? null;
                    }
                    $item->sales_at = $salesAt;
                    $item->sales_by = Auth::id();
                    $item->sales_status_id = $submittedSalesStatus->id;
                    $item->service_fee = $serviceFee;
                    $item->item_status_id = $soldItemStatus->id;
                    $item->save();

                    $payload[] = [
                        'item' => $item,
                        'sales_price' => $salesPrice,
                        'service_fee' => $serviceFee,
                        'notes' => trim((string) ($itemNotes[$index] ?? '')),
                    ];
                }

                return [
                    'items' => array_map(function ($row) {
                        return $row['item'];
                    }, $payload),
                    'receipt' => $this->createReceiptForItems(
                        $payload,
                        $salesAt,
                        $request->get('customer_name'),
                        $request->get('customer_address')
                    ),
                ];
            });
        } catch (\Exception $ex) {
            return redirect()->back()->withInput()->with('error', $ex->getMessage());
        }

        $receipt = $transactionResult['receipt'];
        $this->notifyAdminsAboutSubmittedTransaction($receipt, $transactionResult['items']);

        return redirect()->route($this->getCheckoutEntryRouteName())
            ->with('success', __('Transaction submitted and waiting for admin approval.'));
    }

    public function employeeSalesFormSave(Request $request) {
        $itemId = $request->get('item_id');
        $receipt = null;

        try {
            $this->assertReceiptSnapshotSchema();
            if (!Schema::hasColumn('item', 'service_fee') || !Schema::hasColumn('item', 'base_service_fee')) {
                $missingColumns = [];
                if (!Schema::hasColumn('item', 'service_fee')) {
                    $missingColumns[] = 'service_fee';
                }
                if (!Schema::hasColumn('item', 'base_service_fee')) {
                    $missingColumns[] = 'base_service_fee';
                }

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', __('Item table is missing columns: :columns. Please run migration first.', [
                        'columns' => implode(', ', $missingColumns),
                    ]));
            }

            $normalizedSalesPrice = $this->normalizeLocalizedPrice($request->get('sales_price'));
            $normalizedServiceFee = $this->normalizeLocalizedPrice($request->get('service_fee'));

            if ($normalizedSalesPrice === null || !is_numeric($normalizedSalesPrice) || (float) $normalizedSalesPrice < 0) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors([
                        'sales_price' => __('Sales price format is invalid.'),
                    ]);
            }

            if ($normalizedServiceFee === null || !is_numeric($normalizedServiceFee) || (float) $normalizedServiceFee < 0) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors([
                        'service_fee' => __('Service fee format is invalid.'),
                    ]);
            }

            $request->validate([
                'sales_price' => 'required',
                'service_fee' => 'required',
                'sales_at' => 'date|required',
                'sales_by_id' => 'required',
                'sales_status_id' => 'required',
                'item_note' => 'nullable|string|max:1000',
                'customer_name' => 'nullable|string|max:255',
                'customer_address' => 'nullable|string|max:255',
            ]);

            $transactionResult = DB::transaction(function () use ($itemId, $request, $normalizedSalesPrice, $normalizedServiceFee) {
                $soldItemStatus = \App\ItemStatus::where('code', '=', 'sold')->first();
                $submittedSalesStatus = \App\SalesStatus::where('code', 'submitted')->first();
                $item = \App\Item::where('id', $itemId)->lockForUpdate()->firstOrFail();
                $originalSalesStatusId = $item->sales_status_id;
                $todayGoldPriceSetting = $this->getTodayGoldPriceSetting($item->item_gold_rate, $item->inventory_status_id);

                $item->sales_price = round((float) $normalizedSalesPrice, 2);
                $item->base_gold_price = $todayGoldPriceSetting['base_price'] ?? null;
                if (Schema::hasColumn('item', 'base_service_fee')) {
                    $item->base_service_fee = $todayGoldPriceSetting['service_fee'] ?? null;
                }
                if (Schema::hasColumn('item', 'service_fee')) {
                    $item->service_fee = round((float) $normalizedServiceFee, 2);
                }
                $item->sales_at = ($request->get('sales_at') != null) ? \Carbon\Carbon::createFromFormat('m/d/Y', $request->get('sales_at'))->format('Y-m-d H:i:s') : null;
                $item->sales_by = $request->get('sales_by_id');
                $item->sales_status_id = $request->get('sales_status_id');
                $item->item_status_id = $soldItemStatus->id;
                $item->save();

                return [
                    'item' => $item,
                    'receipt' => $this->upsertReceiptForItem($item, $request),
                    'should_notify' => $this->shouldNotifyAdminsAboutSubmittedSale(
                        $originalSalesStatusId,
                        $item->sales_status_id,
                        optional($submittedSalesStatus)->id
                    ),
                ];
            });

            $receipt = $transactionResult['receipt'];
            if ($transactionResult['should_notify']) {
                $this->notifyAdminsAboutSubmittedTransaction($receipt, [$transactionResult['item']]);
            }

        } catch (\Exception $ex) {
            if(Auth::user()->authRole()->name == 'employee') {
                return redirect('/employee/items/index')->with('error', $ex->getMessage());
            }

            return redirect('/items')->with('error', $ex->getMessage());
        }

        if ($receipt) {
            return redirect()->route($this->getSalesEntryRouteName())
                ->with('success', __('Transaction submitted and waiting for admin approval.'));
        }

        return redirect('/items')->with('success', __('Item has been updated.'));
    }

    private function upsertReceiptForItem(Item $item, Request $request)
    {
        $receiptDetail = $this->findReceiptDetailForItem($item->id);
        $receipt = $receiptDetail ? $receiptDetail->receipt : new Receipts();

        $serviceFee = (float) ($item->service_fee ?: 0);
        $salesPrice = (float) $item->sales_price;
        $lineTotal = $salesPrice + $serviceFee;

        $receipt->receipt_date = $item->sales_at;
        if (Schema::hasColumn('receipts', 'store_id')) {
            $receipt->store_id = $item->store_id;
        }

        if (Schema::hasColumn('receipts', 'sales_by')) {
            $receipt->sales_by = $item->sales_by;
        }

        $receipt->customer_name = $request->get('customer_name');
        $receipt->customer_address = $request->get('customer_address');
        $receipt->receipt_total = $lineTotal;
        $receipt->receipt_total_string = 'Rp ' . number_format($lineTotal, 2, ',', '.');
        $receipt->save();

        if (!$receiptDetail) {
            $receiptDetail = new ReceiptDetails();
        }

        $receiptDetail->receipt_date = $item->sales_at;
        $receiptDetail->receipt_id = $receipt->id;
        $receiptDetail->item_name = $item->item_name;
        $receiptDetail->item_gold_rate = $item->item_gold_rate;
        $receiptDetail->item_weight = $item->item_weight;
        $receiptDetail->service_fee = $serviceFee;
        if (Schema::hasColumn('receipt_details', 'notes')) {
            $itemNote = trim((string) $request->get('item_note'));
            $receiptDetail->notes = $itemNote !== '' ? $itemNote : null;
        }

        if (Schema::hasColumn('receipt_details', 'item_id')) {
            $receiptDetail->item_id = $item->id;
        }

        if (Schema::hasColumn('receipt_details', 'item_no')) {
            $receiptDetail->item_no = $item->item_no;
        }

        if (Schema::hasColumn('receipt_details', 'sales_price')) {
            $receiptDetail->sales_price = $salesPrice;
        }

        if (Schema::hasColumn('receipt_details', 'line_total')) {
            $receiptDetail->line_total = $lineTotal;
        }

        $receiptDetail->save();

        return $receipt;
    }

    private function createReceiptForItems(array $payload, $salesAt, $customerName = null, $customerAddress = null)
    {
        if (count($payload) <= 0) {
            throw new \RuntimeException(__('Cannot create an empty receipt.'));
        }

        $receipt = new Receipts();
        $receipt->receipt_date = $salesAt;

        $storeIds = array_values(array_unique(array_map(function ($row) {
            return $row['item']->store_id;
        }, $payload)));

        if (Schema::hasColumn('receipts', 'store_id')) {
            $receipt->store_id = count($storeIds) === 1 ? $storeIds[0] : null;
        }

        if (Schema::hasColumn('receipts', 'sales_by')) {
            $receipt->sales_by = Auth::id();
        }

        $receipt->customer_name = $customerName;
        $receipt->customer_address = $customerAddress;

        $receiptTotal = 0;
        foreach ($payload as $row) {
            $receiptTotal += ((float) $row['sales_price']) + ((float) $row['service_fee']);
        }

        $receipt->receipt_total = $receiptTotal;
        $receipt->receipt_total_string = 'Rp ' . number_format($receiptTotal, 2, ',', '.');
        $receipt->save();

        foreach ($payload as $row) {
            $item = $row['item'];
            $salesPrice = (float) $row['sales_price'];
            $serviceFee = (float) $row['service_fee'];
            $lineTotal = $salesPrice + $serviceFee;
            $itemNote = trim((string) ($row['notes'] ?? ''));

            $receiptDetail = new ReceiptDetails();
            $receiptDetail->receipt_date = $salesAt;
            $receiptDetail->receipt_id = $receipt->id;
            $receiptDetail->item_name = $item->item_name;
            $receiptDetail->item_gold_rate = $item->item_gold_rate;
            $receiptDetail->item_weight = $item->item_weight;
            $receiptDetail->service_fee = $serviceFee;
            if (Schema::hasColumn('receipt_details', 'notes')) {
                $receiptDetail->notes = $itemNote !== '' ? $itemNote : null;
            }

            if (Schema::hasColumn('receipt_details', 'item_id')) {
                $receiptDetail->item_id = $item->id;
            }

            if (Schema::hasColumn('receipt_details', 'item_no')) {
                $receiptDetail->item_no = $item->item_no;
            }

            if (Schema::hasColumn('receipt_details', 'sales_price')) {
                $receiptDetail->sales_price = $salesPrice;
            }

            if (Schema::hasColumn('receipt_details', 'line_total')) {
                $receiptDetail->line_total = $lineTotal;
            }

            $receiptDetail->save();
        }

        return $receipt;
    }

    private function syncReceiptSnapshotForItem(Item $item, array $attributes = [])
    {
        $receiptDetail = $this->findReceiptDetailForItem($item->id);
        $receipt = $receiptDetail ? $receiptDetail->receipt : new Receipts();
        $serviceFee = (float) ($item->service_fee ?: 0);
        $salesPrice = (float) ($item->sales_price ?: 0);
        $lineTotal = $salesPrice + $serviceFee;

        $receipt->receipt_date = $item->sales_at;
        if (Schema::hasColumn('receipts', 'store_id')) {
            $receipt->store_id = $item->store_id;
        }

        if (Schema::hasColumn('receipts', 'sales_by')) {
            $receipt->sales_by = $item->sales_by;
        }

        if (array_key_exists('customer_name', $attributes)) {
            $receipt->customer_name = $attributes['customer_name'];
        }

        if (array_key_exists('customer_address', $attributes)) {
            $receipt->customer_address = $attributes['customer_address'];
        }

        $receipt->receipt_total = $lineTotal;
        $receipt->receipt_total_string = 'Rp ' . number_format($lineTotal, 2, ',', '.');
        $receipt->save();

        if (!$receiptDetail) {
            $receiptDetail = new ReceiptDetails();
        }

        $receiptDetail->receipt_date = $item->sales_at;
        $receiptDetail->receipt_id = $receipt->id;
        $receiptDetail->item_name = $item->item_name;
        $receiptDetail->item_gold_rate = $item->item_gold_rate;
        $receiptDetail->item_weight = $item->item_weight;
        $receiptDetail->service_fee = $serviceFee;
        if (Schema::hasColumn('receipt_details', 'notes') && array_key_exists('notes', $attributes)) {
            $itemNote = trim((string) $attributes['notes']);
            $receiptDetail->notes = $itemNote !== '' ? $itemNote : null;
        }

        if (Schema::hasColumn('receipt_details', 'item_id')) {
            $receiptDetail->item_id = $item->id;
        }

        if (Schema::hasColumn('receipt_details', 'item_no')) {
            $receiptDetail->item_no = $item->item_no;
        }

        if (Schema::hasColumn('receipt_details', 'sales_price')) {
            $receiptDetail->sales_price = $salesPrice;
        }

        if (Schema::hasColumn('receipt_details', 'line_total')) {
            $receiptDetail->line_total = $lineTotal;
        }

        $receiptDetail->save();

        $this->syncReceiptHeaderFromDetails($receipt);

        return $receipt;
    }

    private function removeReceiptSnapshotForItem(Item $item)
    {
        $receiptDetail = $this->findReceiptDetailForItem($item->id);
        if (!$receiptDetail) {
            return;
        }

        $receipt = $receiptDetail->receipt;
        $receiptDetail->delete();

        if (!$receipt) {
            return;
        }

        if (!$receipt->details()->exists()) {
            $receipt->delete();
            return;
        }

        $this->syncReceiptHeaderFromDetails($receipt);
    }

    private function syncReceiptHeaderFromDetails(Receipts $receipt)
    {
        $details = $receipt->details()->orderBy('id')->get();
        if ($details->isEmpty()) {
            $receipt->delete();
            return null;
        }

        $receipt->receipt_total = (float) $details->sum(function ($detail) {
            return (float) ($detail->line_total ?? 0);
        });
        $receipt->receipt_total_string = 'Rp ' . number_format((float) $receipt->receipt_total, 2, ',', '.');

        $firstDetail = $details->first();
        if ($firstDetail && $firstDetail->receipt_date) {
            $receipt->receipt_date = $firstDetail->receipt_date;
        }

        $receipt->save();

        return $receipt;
    }

    private function assertReceiptSnapshotSchema()
    {
        $missingColumns = [];

        $required = [
            'receipts' => ['store_id', 'sales_by'],
            'receipt_details' => ['item_id', 'item_no', 'sales_price', 'line_total'],
        ];

        foreach ($required as $table => $columns) {
            foreach ($columns as $column) {
                if (!Schema::hasColumn($table, $column)) {
                    $missingColumns[] = $table . '.' . $column;
                }
            }
        }

        if (count($missingColumns) > 0) {
            throw new \RuntimeException(
                'Receipt schema is incomplete. Run migration 2026_03_04_180000_alter_receipts_for_printable_item_receipts.php first.'
            );
        }
    }

    private function findReceiptDetailForItem($itemId)
    {
        if (!Schema::hasColumn('receipt_details', 'item_id')) {
            return null;
        }

        return ReceiptDetails::with('receipt')->where('item_id', $itemId)->latest('id')->first();
    }

    private function shouldNotifyAdminsAboutSubmittedSale($originalSalesStatusId, $currentSalesStatusId, $submittedSalesStatusId)
    {
        if (!Auth::check() || Auth::user()->authRole()->name !== 'employee') {
            return false;
        }

        if (!$submittedSalesStatusId) {
            return false;
        }

        return $originalSalesStatusId === null && (int) $currentSalesStatusId === (int) $submittedSalesStatusId;
    }

    private function notifyAdminsAboutSubmittedTransaction(Receipts $receipt, array $items)
    {
        if (!Auth::check() || Auth::user()->authRole()->name !== 'employee') {
            return;
        }

        $admins = $this->getAdminUsers();
        if ($admins->isEmpty()) {
            \Log::warning('Unable to send admin transaction notification: no admin users found.');
            return;
        }

        $payload = $this->buildAdminTransactionNotificationPayload($receipt, $items);
        $notificationsTableReady = Schema::hasTable('notifications');

        if (!$notificationsTableReady) {
            \Log::warning('Unable to persist admin transaction notification: notifications table is missing.', [
                'receipt_id' => $receipt->id,
            ]);
        }

        if ($notificationsTableReady) {
            try {
                Notification::send($admins, new EmployeeTransactionSubmittedNotification($payload));
            } catch (\Throwable $exception) {
                \Log::warning('Unable to persist admin transaction notification.', [
                    'receipt_id' => $receipt->id,
                    'message' => $exception->getMessage(),
                    'notifications_table_ready' => $notificationsTableReady,
                ]);
            }
        }

        try {
            event(new AdminTransactionCreated($admins->pluck('id')->all(), $payload));
        } catch (\Throwable $exception) {
            \Log::warning('Unable to broadcast admin transaction notification.', [
                'receipt_id' => $receipt->id,
                'message' => $exception->getMessage(),
                'broadcast_driver' => config('broadcasting.default'),
                'pusher_key_configured' => !empty(config('broadcasting.connections.pusher.key')),
                'admin_user_ids' => $admins->pluck('id')->all(),
            ]);
        }
    }

    private function getAdminUsers()
    {
        return \App\User::query()
            ->select('users.*')
            ->join('user_roles', 'user_roles.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('roles.name', 'admin')
            ->distinct()
            ->get();
    }

    private function buildAdminTransactionNotificationPayload(Receipts $receipt, array $items)
    {
        $itemsCollection = collect($items)->filter();
        $firstItem = $itemsCollection->first();
        $itemCount = $itemsCollection->count();
        $itemNumbers = $itemsCollection
            ->pluck('item_no')
            ->filter()
            ->values()
            ->all();

        $message = $itemCount > 0
            ? 'New sales for ' . $this->formatNotificationItemNumbers($itemNumbers)
            : 'New sales transaction submitted';

        return [
            'title' => __('New Sales Transaction'),
            'message' => $message . '.',
            'employee_name' => Auth::user()->name,
            'receipt_id' => $receipt->id,
            'item_id' => $firstItem ? $firstItem->id : null,
            'item_no' => $firstItem ? $firstItem->item_no : null,
            'item_count' => $itemCount,
            'receipt_total' => (float) $receipt->receipt_total,
            'receipt_total_string' => 'Rp ' . number_format((float) $receipt->receipt_total, 2, ',', '.'),
            'url' => route('receipts.show', $receipt->id),
        ];
    }

    private function formatNotificationItemNumbers(array $itemNumbers)
    {
        $itemNumbers = array_values(array_filter($itemNumbers));
        $count = count($itemNumbers);

        if ($count === 0) {
            return __('transaction');
        }

        if ($count === 1) {
            return $itemNumbers[0];
        }

        if ($count === 2) {
            return $itemNumbers[0] . ' and ' . $itemNumbers[1];
        }

        $lastItemNumber = array_pop($itemNumbers);

        return implode(', ', $itemNumbers) . ', and ' . $lastItemNumber;
    }

    private function getTodayBaseGoldPrice($goldRate = null, $inventoryStatusId = null)
    {
        $todayGoldPriceSetting = $this->getTodayGoldPriceSetting($goldRate, $inventoryStatusId);

        return $todayGoldPriceSetting['base_price'];
    }

    private function getTodayGoldPriceSetting($goldRate = null, $inventoryStatusId = null)
    {
        $today = \Carbon\Carbon::today()->format('Y-m-d');

        $goldPriceQuery = \App\GoldPrice::query();
        if (Schema::hasColumn('gold_prices', 'price_date')) {
            $goldPriceQuery = $goldPriceQuery
                ->whereDate('price_date', '<=', $today)
                ->orderBy('price_date', 'desc');
        } else {
            $goldPriceQuery = $goldPriceQuery
                ->whereDate('created_at', '<=', $today)
                ->orderBy('created_at', 'desc');
        }

        if (Schema::hasColumn('gold_prices', 'gold_rate') && $goldRate !== null) {
            $goldPriceQuery = $goldPriceQuery->where('gold_rate', (float) $goldRate);
        }

        if (Schema::hasColumn('gold_prices', 'inventory_status_id') && $inventoryStatusId !== null) {
            $goldPriceQuery = $goldPriceQuery->where('inventory_status_id', (int) $inventoryStatusId);
        }

        $goldPrice = $goldPriceQuery->orderBy('id', 'desc')->first();

        if (!$goldPrice) {
            return [
                'base_price' => null,
                'service_fee' => null,
            ];
        }

        $basePrice = null;
        if (Schema::hasColumn('gold_prices', 'base_price') && $goldPrice->base_price !== null) {
            $basePrice = $goldPrice->base_price;
        }

        $serviceFee = null;
        if (Schema::hasColumn('gold_prices', 'service_fee') && $goldPrice->service_fee !== null) {
            $serviceFee = $goldPrice->service_fee;
        }

        return [
            'base_price' => $basePrice,
            'service_fee' => $serviceFee,
        ];
    }

    private function getTodayGoldPriceSettingsForItems($items)
    {
        $items = collect($items)->filter();
        if ($items->isEmpty()) {
            return [];
        }

        $today = \Carbon\Carbon::today()->format('Y-m-d');
        $hasGoldRateColumn = Schema::hasColumn('gold_prices', 'gold_rate');
        $hasInventoryStatusColumn = Schema::hasColumn('gold_prices', 'inventory_status_id');
        $hasPriceDateColumn = Schema::hasColumn('gold_prices', 'price_date');

        $query = \App\GoldPrice::query();

        if ($hasPriceDateColumn) {
            $query->whereDate('price_date', '<=', $today)
                ->orderBy('price_date', 'desc');
        } else {
            $query->whereDate('created_at', '<=', $today)
                ->orderBy('created_at', 'desc');
        }

        if ($hasGoldRateColumn) {
            $goldRates = $items->pluck('item_gold_rate')
                ->filter(function ($value) {
                    return $value !== null;
                })
                ->unique()
                ->values()
                ->all();

            if (count($goldRates) > 0) {
                $query->whereIn('gold_rate', $goldRates);
            }
        }

        if ($hasInventoryStatusColumn) {
            $inventoryStatusIds = $items->pluck('inventory_status_id')
                ->filter(function ($value) {
                    return $value !== null;
                })
                ->unique()
                ->values()
                ->all();

            if (count($inventoryStatusIds) > 0) {
                $query->whereIn('inventory_status_id', $inventoryStatusIds);
            }
        }

        $settings = [];
        foreach ($query->orderBy('id', 'desc')->get() as $goldPrice) {
            $cacheKey = $this->makeGoldPriceSettingKey(
                $hasGoldRateColumn ? $goldPrice->gold_rate : null,
                $hasInventoryStatusColumn ? $goldPrice->inventory_status_id : null
            );

            if (isset($settings[$cacheKey])) {
                continue;
            }

            $basePrice = null;
            if (Schema::hasColumn('gold_prices', 'base_price') && $goldPrice->base_price !== null) {
                $basePrice = $goldPrice->base_price;
            }

            $settings[$cacheKey] = [
                'base_price' => $basePrice,
                'service_fee' => $goldPrice->service_fee ?? 0,
            ];
        }

        return $settings;
    }

    private function makeGoldPriceSettingKey($goldRate, $inventoryStatusId)
    {
        $goldRatePart = $goldRate !== null ? number_format((float) $goldRate, 2, '.', '') : 'null';
        $inventoryStatusPart = $inventoryStatusId !== null ? (string) (int) $inventoryStatusId : 'null';

        return $goldRatePart . '|' . $inventoryStatusPart;
    }

    private function buildCheckoutSelectableItemPayload(Item $item, array $goldPriceSettings = [])
    {
        $cacheKey = $this->makeGoldPriceSettingKey($item->item_gold_rate, $item->inventory_status_id);
        $todayGoldPriceSetting = $goldPriceSettings[$cacheKey] ?? $this->getTodayGoldPriceSetting($item->item_gold_rate, $item->inventory_status_id);
        $recommendedItemPrice = null;
        $recommendedSalesPrice = null;
        $recommendedServiceFee = 0;

        if (($todayGoldPriceSetting['base_price'] ?? null) !== null && $item->item_weight !== null) {
            $recommendedItemPrice = round((float) $todayGoldPriceSetting['base_price'] * (float) $item->item_weight, 2);
            $recommendedServiceFee = round((float) ($todayGoldPriceSetting['service_fee'] ?? 0) * (float) $item->item_weight, 2);
            $recommendedSalesPrice = max(0, round($recommendedItemPrice + $recommendedServiceFee, 2));
        }

        return [
            'id' => $item->id,
            'text' => $item->item_no . ' - ' . $item->item_name,
            'item_no' => $item->item_no,
            'item_name' => $item->item_name,
            'item_weight' => $item->item_weight,
            'item_gold_rate' => $item->item_gold_rate,
            'store_id' => $item->store_id,
            'sales_price' => $recommendedItemPrice,
            'recommended_sales_price' => $recommendedSalesPrice,
            'service_fee' => $recommendedServiceFee,
        ];
    }

    private function getCheckoutCreateItemData()
    {
        $dalamAllocation = $this->getCheckoutDalamAllocation();
        $newItemStatus = \App\ItemStatus::where('code', 'new')->firstOrFail();

        return [
            'stores' => \App\Store::orderBy('name', 'asc')->get(['id', 'name', 'code']),
            'categories' => \App\Category::orderBy('description', 'asc')->get(['id', 'description', 'code']),
            'inventorystatuses' => \App\InventoryStatus::orderBy('description', 'asc')->get(['id', 'description']),
            'allocation' => $dalamAllocation,
            'item_status' => $newItemStatus,
        ];
    }

    private function getCheckoutDalamAllocation()
    {
        return \App\Allocation::query()
            ->where(function ($query) {
                $query->where('code', 'STORAGE')
                    ->orWhere('description', 'Penyimpanan Dalam');
            })
            ->orderBy('id', 'asc')
            ->firstOrFail();
    }

    private function storeItemPhotos(Item $item, array $images = [])
    {
        foreach ($images as $image) {
            if (!$image) {
                continue;
            }

            $imageName = Str::uuid() . '.' . $image->extension();
            $image->move(public_path('img'), $imageName);

            \App\Photos::create([
                'item_id' => $item->id,
                'img_url' => $imageName,
            ]);
        }
    }

    private function getEmployeeSalesSearchItemsQuery()
    {
        return \App\Item::query();
    }

    private function getSalesRoutePrefix()
    {
        if (Auth::check() && Auth::user()->authRole()->name === 'admin') {
            return 'sales.admin';
        }

        return 'sales.employee';
    }

    private function getSalesEntryRouteName()
    {
        return $this->getSalesRoutePrefix() . '.entry';
    }

    private function getSalesFindRouteName()
    {
        return $this->getSalesRoutePrefix() . '.find';
    }

    private function getSalesSearchItemsRouteName()
    {
        return $this->getSalesRoutePrefix() . '.search-items';
    }

    private function getSalesFormRouteName()
    {
        return $this->getSalesRoutePrefix() . '.form';
    }

    private function getSalesFormSaveRouteName()
    {
        return $this->getSalesRoutePrefix() . '.form.save';
    }

    private function getCheckoutRoutePrefix()
    {
        if (Auth::check() && Auth::user()->authRole()->name === 'admin') {
            return 'checkout.admin';
        }

        return 'checkout.employee';
    }

    private function getCheckoutEntryRouteName()
    {
        return $this->getCheckoutRoutePrefix() . '.entry';
    }

    private function getCheckoutSubmitRouteName()
    {
        return $this->getCheckoutRoutePrefix() . '.submit';
    }

    private function getCheckoutCreateItemRouteName()
    {
        return $this->getCheckoutRoutePrefix() . '.create-item';
    }

    private function normalizeLocalizedPrice($value)
    {
        $value = trim((string) $value);
        $value = str_ireplace('rp', '', $value);
        $value = preg_replace('/\s+/', '', $value);

        if ($value === '') {
            return null;
        }

        if (strpos($value, ',') !== false) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (preg_match('/^\d{1,3}(\.\d{3})+$/', $value)) {
            $value = str_replace('.', '', $value);
        }

        return $value;
    }

    private function getTodayGoldPriceList()
    {
        $today = \Carbon\Carbon::today()->format('Y-m-d');
        $hasGoldRateColumn = Schema::hasColumn('gold_prices', 'gold_rate');
        $hasInventoryStatusColumn = Schema::hasColumn('gold_prices', 'inventory_status_id');
        $inventoryStatusMap = [];
        $inventoryStatusOrderMap = [];

        $goldPriceQuery = \App\GoldPrice::query();
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
            $displayBasePrice = $goldPrice->base_price;
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
