<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ItemStatus;

class ItemStatusController extends Controller
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
        return view('itemstatuses.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('itemstatuses.create');
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
            'itemstatus_code' => 'required',
            'itemstatus_description' => 'required'
        ]);
        
        $category = new ItemStatus([
            'code' => $request->get('itemstatus_code'),
            'description' => $request->get('itemstatus_description'),
        ]);
        $category->save();

        return redirect('/itemstatuses')->with('success', __('Item status has been created.'));
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
            $itemstatus = ItemStatus::findOrFail($id);
        } catch (Exception $ex) {
            return redirect()->route('itemstatuses.index')->withError($ex->getMessage());
        }

        return view('itemstatuses.edit')
            ->with('itemstatus', $itemstatus);
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
                'itemstatus_code' => 'required',
                'itemstatus_description' => 'required'
            ]);

            $itemstatus = ItemStatus::findOrFail($id);
            $itemstatus->code = $request->get("itemstatus_code");
            $itemstatus->description = $request->get("itemstatus_description");
            $itemstatus->save();

        } catch (Exception $ex) {
            return redirect('/itemstatuses')->with('error', $ex->getMessage());
        }

        return redirect('/itemstatuses')->with('success', __('Item status has been updated.'));
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
            $itemstatus = ItemStatus::findOrFail($id);
            $itemstatus->delete();
        } catch (Exception $ex) {
            return redirect('/itemstatuses')->with('error', $ex->getMessage());
        }

        return redirect('/itemstatuses')->with('success', __('Item status has been deleted.'));
    }
}
