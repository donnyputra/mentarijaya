<?php

namespace App\Http\Controllers;

use App\Receipts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $receiptApproved = $receipt->isApproved();
        $this->ensureReceiptCanBeViewed($receiptApproved);
        $showServiceFee = $this->shouldShowServiceFee($receipt);

        return view('receipts.show', [
            'receipt' => $receipt,
            'receiptApproved' => $receiptApproved,
            'showServiceFee' => $showServiceFee,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Receipts  $receipts
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->ensureAdminAccess();

        $receipt = $this->findReceipt($id);

        return view('receipts.edit', [
            'receipt' => $receipt,
            'showServiceFee' => $this->shouldShowServiceFee($receipt),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Receipts  $receipts
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->ensureAdminAccess();

        $request->validate([
            'sales_at' => 'required|date',
            'customer_name' => 'nullable|string|max:255',
            'customer_address' => 'nullable|string|max:255',
            'detail_ids' => 'required|array|min:1',
            'detail_ids.*' => 'required|integer',
            'sales_prices' => 'required|array|min:1',
            'sales_prices.*' => 'required|numeric|min:0',
            'service_fees' => 'nullable|array',
            'service_fees.*' => 'nullable|numeric|min:0',
            'item_notes' => 'nullable|array',
            'item_notes.*' => 'nullable|string|max:1000',
            'approval_action' => 'nullable|in:save,approve',
        ]);

        $detailIds = array_values($request->get('detail_ids', []));
        $salesPrices = array_values($request->get('sales_prices', []));
        $serviceFees = array_values($request->get('service_fees', []));
        $itemNotes = array_values($request->get('item_notes', []));

        if (count($detailIds) !== count($salesPrices)) {
            return redirect()->back()->withInput()->with('error', __('Receipt data is not aligned.'));
        }

        $shouldApprove = $request->get('approval_action') === 'approve';

        try {
            $receipt = DB::transaction(function () use ($id, $request, $detailIds, $salesPrices, $serviceFees, $itemNotes, $shouldApprove) {
                $receipt = Receipts::with('details.item')->lockForUpdate()->findOrFail($id);
                $details = $receipt->details->keyBy('id');

                if ($details->count() !== count($detailIds)) {
                    throw new \RuntimeException(__('Receipt items are no longer aligned.'));
                }

                $salesAt = \Carbon\Carbon::createFromFormat('m/d/Y', $request->get('sales_at'))->format('Y-m-d H:i:s');
                $completedSalesStatus = \App\SalesStatus::where('code', 'completed')->first();
                $submittedSalesStatus = \App\SalesStatus::where('code', 'submitted')->first();
                $soldItemStatus = \App\ItemStatus::where('code', 'sold')->firstOrFail();
                $approvedAt = \Carbon\Carbon::now()->toDateTimeString();
                $receiptTotal = 0;

                foreach ($detailIds as $index => $detailId) {
                    $detail = $details->get((int) $detailId);
                    if (!$detail) {
                        throw new \RuntimeException(__('Receipt item was not found.'));
                    }

                    $item = \App\Item::where('id', $detail->item_id)->lockForUpdate()->firstOrFail();
                    $salesPrice = (float) $salesPrices[$index];
                    $serviceFee = (float) ($serviceFees[$index] ?? 0);
                    $note = trim((string) ($itemNotes[$index] ?? ''));
                    $lineTotal = $salesPrice + $serviceFee;

                    $item->sales_price = $salesPrice;
                    if (\Illuminate\Support\Facades\Schema::hasColumn('item', 'service_fee')) {
                        $item->service_fee = $serviceFee;
                    }
                    $item->sales_at = $salesAt;
                    $item->item_status_id = $soldItemStatus->id;

                    if ($shouldApprove) {
                        if ($completedSalesStatus) {
                            $item->sales_status_id = $completedSalesStatus->id;
                        }
                        $item->sales_approved_at = $approvedAt;
                    } elseif ($item->sales_approved_at === null && $submittedSalesStatus) {
                        $item->sales_status_id = $submittedSalesStatus->id;
                    }

                    $item->save();

                    $detail->receipt_date = $salesAt;
                    $detail->item_name = $item->item_name;
                    $detail->item_gold_rate = $item->item_gold_rate;
                    $detail->item_weight = $item->item_weight;
                    $detail->sales_price = $salesPrice;
                    $detail->service_fee = $serviceFee;
                    $detail->line_total = $lineTotal;
                    if (\Illuminate\Support\Facades\Schema::hasColumn('receipt_details', 'notes')) {
                        $detail->notes = $note !== '' ? $note : null;
                    }
                    $detail->save();

                    $receiptTotal += $lineTotal;
                }

                $receipt->receipt_date = $salesAt;
                $receipt->customer_name = $request->get('customer_name');
                $receipt->customer_address = $request->get('customer_address');
                $receipt->receipt_total = $receiptTotal;
                $receipt->receipt_total_string = 'Rp ' . number_format($receiptTotal, 2, ',', '.');
                $receipt->save();

                return $receipt;
            });
        } catch (\Throwable $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->route('receipts.show', $receipt->id)->with(
            'success',
            $shouldApprove ? __('Receipt has been approved.') : __('Receipt changes have been saved.')
        );
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
        $receiptApproved = $receipt->isApproved();
        $this->ensureReceiptCanBePrinted($receiptApproved);
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

    private function ensureAdminAccess()
    {
        abort_unless(auth()->check() && auth()->user()->authRole()->name === 'admin', 403);
    }

    private function ensureReceiptCanBeViewed($receiptApproved)
    {
        if (auth()->user()->authRole()->name === 'admin') {
            return;
        }

        if (!$receiptApproved) {
            abort(403, 'Receipt is still waiting for admin approval.');
        }
    }

    private function ensureReceiptCanBePrinted($receiptApproved)
    {
        if (!$receiptApproved) {
            abort(403, 'Receipt is still waiting for admin approval.');
        }
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
