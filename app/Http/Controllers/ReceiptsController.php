<?php

namespace App\Http\Controllers;

use App\Receipts;
use Illuminate\Http\Request;
use PDF;

class ReceiptsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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
    public function show(Request $request, $id)
    {
        $receipt = $this->findReceipt($id);
        $showServiceFee = $request->boolean('show_service_fee', true);

        return view('receipts.show', [
            'receipt' => $receipt,
            'showServiceFee' => $showServiceFee,
        ]);
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

    public function pdf(Request $request, $id)
    {
        $receipt = $this->findReceipt($id);
        $showServiceFee = $request->boolean('show_service_fee', true);

        $pdf = PDF::loadView('pdf.receipt', [
            'receipt' => $receipt,
            'showServiceFee' => $showServiceFee,
        ]);

        return $pdf->setPaper([0, 0, 226.77, 600], 'portrait')->stream('receipt-' . $receipt->uuid . '.pdf');
    }

    private function findReceipt($id)
    {
        return Receipts::with([
            'details.item.store',
            'store',
            'salesUser',
        ])->findOrFail($id);
    }
}
