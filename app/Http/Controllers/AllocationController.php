<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Allocation;

class AllocationController extends Controller
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
        return view('allocations.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('allocations.create');
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
            'allocation_code' => 'required',
            'allocation_description' => 'required'
        ]);
        
        $allocation = new Allocation([
            'code' => $request->get('allocation_code'),
            'description' => $request->get('allocation_description'),
        ]);
        $allocation->save();

        return redirect('/allocations')->with('success', __('Allocation has been created.'));
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
            $allocation = Allocation::findOrFail($id);
        } catch (Exception $ex) {
            return redirect()->route('allocations.index')->withError($ex->getMessage());
        }

        return view('allocations.edit')
            ->with('allocation', $allocation);
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
                'allocation_code' => 'required',
                'allocation_description' => 'required'
            ]);

            $allocation = Allocation::findOrFail($id);
            $allocation->code = $request->get("allocation_code");
            $allocation->description = $request->get("allocation_description");
            $allocation->save();

        } catch (Exception $ex) {
            return redirect('/allocations')->with('error', $ex->getMessage());
        }

        return redirect('/allocations')->with('success', __('Allocation has been updated.'));
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
            $allocation = Allocation::findOrFail($id);
            $allocation->delete();
        } catch (Exception $ex) {
            return redirect('/allocations')->with('error', $ex->getMessage());
        }

        return redirect('/allocations')->with('success', __('Allocation has been deleted.'));
    }
}
