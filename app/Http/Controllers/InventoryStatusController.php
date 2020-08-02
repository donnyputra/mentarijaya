<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\InventoryStatus;

class InventoryStatusController extends Controller
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
        return view('inventorystatuses.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('inventorystatuses.create');
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
            'inventorystatus_code' => 'required',
            'inventorystatus_description' => 'required'
        ]);
        
        $category = new ItemStatus([
            'code' => $request->get('inventorystatus_code'),
            'description' => $request->get('inventorystatus_description'),
        ]);
        $category->save();

        return redirect('/inventorystatuses')->with('success', __('Inventory status has been created.'));
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
            $inventoryStatus = InventoryStatus::findOrFail($id);
        } catch (Exception $ex) {
            return redirect()->route('inventorystatuses.index')->withError($ex->getMessage());
        }

        return view('inventorystatuses.edit')
            ->with('inventorystatus', $inventoryStatus);
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
                'inventorystatus_code' => 'required',
                'inventorystatus_description' => 'required'
            ]);

            $inventoryStatus = ItemStatus::findOrFail($id);
            $inventoryStatus->code = $request->get("inventorystatus_code");
            $inventoryStatus->description = $request->get("inventorystatus_description");
            $inventoryStatus->save();

        } catch (Exception $ex) {
            return redirect('/inventorystatuses')->with('error', $ex->getMessage());
        }

        return redirect('/inventorystatuses')->with('success', __('Inventory status has been updated.'));
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
            $inventoryStatus = InventoryStatus::findOrFail($id);
            $inventoryStatus->delete();
        } catch (Exception $ex) {
            return redirect('/inventorystatuses')->with('error', $ex->getMessage());
        }

        return redirect('/inventorystatuses')->with('success', __('Inventory status has been deleted.'));
    }
}
