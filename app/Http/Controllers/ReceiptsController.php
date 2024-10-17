<?php

namespace App\Http\Controllers;

use App\Receipts;
use Illuminate\Http\Request;

class ReceiptsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('receipts.index');
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Receipts  $receipts
     * @return \Illuminate\Http\Response
     */
    public function show(Receipts $receipts)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Receipts  $receipts
     * @return \Illuminate\Http\Response
     */
    public function edit(Receipts $receipts)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Receipts  $receipts
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Receipts $receipts)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Receipts  $receipts
     * @return \Illuminate\Http\Response
     */
    public function destroy(Receipts $receipts)
    {
        //
    }
}
