<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Item;

class ItemController extends Controller
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
    public function index()
    {
        // $items = DB::table('item')
        //             ->join('category', 'category.id', '=', 'item.category_id')
        //             ->join('allocation', 'allocation.id', '=', 'item.allocation_id')
        //             ->join('item_status', 'item_status.id', '=', 'item.item_status_id')
        //             ->join('sales_status', 'sales_status.id', '=', 'item.sales_status_id')
        //             ->select(
        //                 'item.*',
        //                 'category.description as category_description',
        //                 'allocation.description as allocation_description',
        //                 'item_status.description as item_status_description',
        //                 'sales_status.code as sales_status_code'
        //             )
        //             ->get();
        // dd($items);


        return view('items.index');
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
        $salesstatuses = \App\SalesStatus::all();
        $users = \App\User::all();

        return view('items.create', [
            'stores' => $stores,
            'categories' => $categories,
            'allocations' => $allocations,
            'itemstatuses' => $itemstatuses,
            'salesstatuses' => $salesstatuses,
            'users' => $users
        ]);
    }

    private function getNextItemIncrementId() {
        $id = DB::table('INFORMATION_SCHEMA.TABLES')
            ->select('AUTO_INCREMENT as id')
            ->where('TABLE_SCHEMA', env('DB_DATABASE', ''))
            ->where('TABLE_NAME', 'item')
            ->get();

        return ($id[0]->id);
    }

    private function generateItemNo() {
        $currentYear = date("Y");
        $nextItemIncrementId = $this->getNextItemIncrementId();
        $paddedId = str_pad($nextItemIncrementId, 6, "0", STR_PAD_LEFT);
        $itemNo = $currentYear . '-' . $paddedId;

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
            'category_id' => 'numeric|required',
            'allocation_id' => 'numeric|required',
            'store_id' => 'numeric|required',
            'sales_price' => 'numeric|nullable',
            'sales_at' => 'date|nullable',
            'sales_by' => 'numeric|nullable',
            'sales_status_id' => 'numeric|required',

        ]);
        
        $item = new Item([
            'item_no' => $this->generateItemNo(),
            'item_name' => $request->get('item_name'),
            'item_weight' => $request->get('item_weight'),
            'item_gold_rate' => $request->get('item_gold_rate'),
            'item_status_id' => $request->get('item_status_id'),
            'category_id' => $request->get('category_id'),
            'allocation_id' => $request->get('allocation_id'),
            'store_id' => $request->get('store_id'),
            'sales_price' => $request->get('sales_price'),
            'sales_at' => $request->get('sales_at'),
            'sales_by' => $request->get('sales_by'),
            'sales_status_id' => $request->get('sales_status_id'),
        ]);
        $item->save();

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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
