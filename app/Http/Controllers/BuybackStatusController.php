<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BuybackStatus;

class BuybackStatusController extends Controller
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
        return view('buybackstatuses.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('buybackstatuses.create');
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
            'buybackstatus_code' => 'required',
            'buybackstatus_description' => 'required'
        ]);
        
        $category = new BuybackStatus([
            'code' => $request->get('buybackstatus_code'),
            'description' => $request->get('buybackstatus_description'),
        ]);
        $category->save();

        return redirect('/buybackstatuses')->with('success', __('Sales status has been created.'));
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
            $buybackstatus = BuybackStatus::findOrFail($id);
        } catch (Exception $ex) {
            return redirect()->route('buybackstatuses.index')->withError($ex->getMessage());
        }

        return view('buybackstatuses.edit')
            ->with('buybackstatus', $buybackstatus);
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
                'buybackstatus_code' => 'required',
                'buybackstatus_description' => 'required'
            ]);

            $buybackstatus = BuybackStatus::findOrFail($id);
            $buybackstatus->code = $request->get("buybackstatus_code");
            $buybackstatus->description = $request->get("buybackstatus_description");
            $buybackstatus->save();

        } catch (Exception $ex) {
            return redirect('/buybackstatuses')->with('error', $ex->getMessage());
        }

        return redirect('/buybackstatuses')->with('success', __('Buyback status has been updated.'));
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
            $buybackstatus = BuybackStatus::findOrFail($id);
            $buybackstatus->delete();
        } catch (Exception $ex) {
            return redirect('/buybackstatuses')->with('error', $ex->getMessage());
        }

        return redirect('/buybackstatuses')->with('success', __('Buyback status has been deleted.'));
    }
}
