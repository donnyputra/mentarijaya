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
        $showServiceFee = $this->shouldShowServiceFee($receipt);
        $receiptCheckUrl = route('receipts.show', ['receipt' => $receipt->id]);

        return view('receipts.show', [
            'receipt' => $receipt,
            'showServiceFee' => $showServiceFee,
            'receiptCheckUrl' => $receiptCheckUrl,
            'receiptQrUrl' => $this->makeReceiptQrUrl($receiptCheckUrl),
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
        $showServiceFee = $this->shouldShowServiceFee($receipt);
        $receiptDetailUrl = route('receipts.show', ['receipt' => $receipt->id]);

        $pdf = PDF::loadView('pdf.receipt', [
            'receipt' => $receipt,
            'showServiceFee' => $showServiceFee,
            'receiptTotalInWords' => $this->convertAmountToBahasa($receipt->receipt_total),
            'receiptQrUrl' => $this->makeReceiptQrUrl($receiptDetailUrl, 90),
        ]);

        return $pdf->setPaper([0, 0, 419.53, 283.46], 'landscape')->stream('receipt-' . $receipt->uuid . '.pdf');
    }

    private function findReceipt($id)
    {
        return Receipts::with([
            'details.item.store',
            'store',
            'salesUser',
        ])->findOrFail($id);
    }

    private function makeReceiptQrUrl($url, $size = 180)
    {
        $size = max(60, (int) $size);

        return 'https://api.qrserver.com/v1/create-qr-code/?size=' . $size . 'x' . $size . '&data=' . urlencode($url);
    }

    private function shouldShowServiceFee(Receipts $receipt)
    {
        return $receipt->details->contains(function ($detail) {
            return (float) ($detail->service_fee ?? 0) > 0;
        });
    }

    private function convertAmountToBahasa($amount)
    {
        $roundedAmount = (int) round((float) $amount);
        if ($roundedAmount <= 0) {
            return 'Nol rupiah';
        }

        return ucfirst(trim($this->spellBahasaNumber($roundedAmount))) . ' rupiah';
    }

    private function spellBahasaNumber($number)
    {
        $number = abs((int) $number);
        $words = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

        if ($number < 12) {
            return ' ' . $words[$number];
        }

        if ($number < 20) {
            return $this->spellBahasaNumber($number - 10) . ' belas';
        }

        if ($number < 100) {
            return $this->spellBahasaNumber((int) floor($number / 10)) . ' puluh' . $this->spellBahasaNumber($number % 10);
        }

        if ($number < 200) {
            return ' seratus' . $this->spellBahasaNumber($number - 100);
        }

        if ($number < 1000) {
            return $this->spellBahasaNumber((int) floor($number / 100)) . ' ratus' . $this->spellBahasaNumber($number % 100);
        }

        if ($number < 2000) {
            return ' seribu' . $this->spellBahasaNumber($number - 1000);
        }

        if ($number < 1000000) {
            return $this->spellBahasaNumber((int) floor($number / 1000)) . ' ribu' . $this->spellBahasaNumber($number % 1000);
        }

        if ($number < 1000000000) {
            return $this->spellBahasaNumber((int) floor($number / 1000000)) . ' juta' . $this->spellBahasaNumber($number % 1000000);
        }

        if ($number < 1000000000000) {
            return $this->spellBahasaNumber((int) floor($number / 1000000000)) . ' miliar' . $this->spellBahasaNumber($number % 1000000000);
        }

        return $this->spellBahasaNumber((int) floor($number / 1000000000000)) . ' triliun' . $this->spellBahasaNumber($number % 1000000000000);
    }
}
