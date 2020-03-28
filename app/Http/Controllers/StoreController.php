<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Store;

class StoreController extends Controller
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
        return view('stores.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('stores.create');
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
            'store_code' => 'required',
            'store_name' => 'required',
            'store_address' => 'required',
            'store_phone' => 'required'
        ]);
        
        $store = new Store([
            'code' => $request->get('store_code'),
            'name' => $request->get('store_name'),
            'address' => $request->get('store_address'),
            'phone_no' => $request->get('store_phone'),
        ]);
        $store->save();

        return redirect('/stores')->with('success', __('Store has been created.'));
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
            $store = Store::findOrFail($id);
        } catch (Exception $ex) {
            return redirect()->route('stores.index')->withError($ex->getMessage());
        }

        return view('stores.edit')
            ->with('store', $store);
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
                'store_code' => 'required',
                'store_name' => 'required',
                'store_address' => 'required',
                'store_phone' => 'required'
            ]);

            $store = Store::findOrFail($id);
            $store->code = $request->get("store_code");
            $store->name = $request->get("store_name");
            $store->address = $request->get("store_address");
            $store->phone_no = $request->get("store_phone");
            $store->save();

        } catch (Exception $ex) {
            return redirect('/stores')->with('error', $ex->getMessage());
        }

        return redirect('/stores')->with('success', __('Store has been updated.'));
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
            $store = Store::findOrFail($id);
            $store->delete();
        } catch (Exception $ex) {
            return redirect('/stores')->with('error', $ex->getMessage());
        }

        return redirect('/stores')->with('success', __('Store has been deleted.'));
    }
}
