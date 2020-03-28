<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SalesStatus;

class SalesStatusController extends Controller
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
        return view('salesstatuses.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('salesstatuses.create');
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
            'salesstatus_code' => 'required',
            'salesstatus_description' => 'required'
        ]);
        
        $category = new SalesStatus([
            'code' => $request->get('salesstatus_code'),
            'description' => $request->get('salesstatus_description'),
        ]);
        $category->save();

        return redirect('/salesstatuses')->with('success', __('Sales status has been created.'));
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
            $salesstatus = SalesStatus::findOrFail($id);
        } catch (Exception $ex) {
            return redirect()->route('salesstatuses.index')->withError($ex->getMessage());
        }

        return view('salesstatuses.edit')
            ->with('salesstatus', $salesstatus);
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
                'salesstatus_code' => 'required',
                'salesstatus_description' => 'required'
            ]);

            $salesstatus = SalesStatus::findOrFail($id);
            $salesstatus->code = $request->get("salesstatus_code");
            $salesstatus->description = $request->get("salesstatus_description");
            $salesstatus->save();

        } catch (Exception $ex) {
            return redirect('/salesstatuses')->with('error', $ex->getMessage());
        }

        return redirect('/salesstatuses')->with('success', __('Sales status has been updated.'));
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
            $salesstatus = SalesStatus::findOrFail($id);
            $salesstatus->delete();
        } catch (Exception $ex) {
            return redirect('/salesstatuses')->with('error', $ex->getMessage());
        }

        return redirect('/salesstatuses')->with('success', __('Sales status has been deleted.'));
    }
}
