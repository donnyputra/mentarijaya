@extends('layouts.admin')

@section('content')
<style>
    .receipt-detail-table th,
    .receipt-detail-table td {
        vertical-align: top;
    }

    .receipt-detail-table .col-item-no {
        min-width: 80px;
    }

    .receipt-detail-table .col-item-name {
        min-width: 120px;
    }

    .receipt-detail-table .col-weight,
    .receipt-detail-table .col-gold-rate {
        min-width: 90px;
    }

    .receipt-detail-table .col-average-price,
    .receipt-detail-table .col-recommended,
    .receipt-detail-table .col-sales-price,
    .receipt-detail-table .col-notes,
    .receipt-detail-table .col-service-fee,
    .receipt-detail-table .col-line-total {
        min-width: 150px;
    }

    .receipt-detail-table .col-notes {
        min-width: 220px;
        white-space: normal;
    }

    .receipt-detail-table .money-cell {
        white-space: nowrap;
    }
</style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">{{ __("Receipt Detail") }}</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/">{{ __("Home") }}</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('receipts.index') }}">{{ __("Receipts") }}</a></li>
                                <li class="breadcrumb-item active">{{ __("Receipt Detail") }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5>{{ $receipt->store ? $receipt->store->name : config('app.name') }}</h5>
                                    <div><strong>Receipt ID:</strong> {{ $receipt->uuid }}</div>
                                    <div><strong>Date:</strong> {{ optional($receipt->receipt_date)->format('d M Y H:i') }}</div>
                                    <div><strong>Sales By:</strong> {{ optional($receipt->salesUser)->name ?: '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div><strong>Customer Name:</strong> {{ $receipt->customer_name ?: '-' }}</div>
                                    <div><strong>Customer Address:</strong> {{ $receipt->customer_address ?: '-' }}</div>
                                    <div><strong>Total:</strong> Rp {{ number_format($receipt->receipt_total, 2, ',', '.') }}</div>
                                    <div><strong>Status:</strong> {{ $receiptApproved ? 'Approved' : 'Submitted - waiting for admin approval' }}</div>
                                    <div><strong>Service Fee:</strong> {{ $showServiceFee ? 'Shown automatically' : 'Hidden automatically' }}</div>
                                </div>
                            </div>

                            @if(!$receiptApproved)
                            <div class="alert alert-warning">
                                {{ __("This transaction is still submitted. Receipt printing stays locked until admin approval.") }}
                            </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-striped table-hover receipt-detail-table">
                                    <thead>
                                        <tr>
                                            <th class="col-item-no">Item No</th>
                                            <th class="col-item-name">Name</th>
                                            <th class="text-right col-weight">Weight</th>
                                            <th class="text-right col-gold-rate">Rate</th>
                                            <th class="text-right col-recommended">Reco</th>
                                            <th class="text-right col-average-price">AVG Sales</th>
                                            <th class="text-right col-sales-price">Sales Price</th>
                                            <th class="col-notes">Notes</th>
                                            @if($showServiceFee)
                                            <th class="text-right col-service-fee">Service Fee</th>
                                            @endif
                                            <th class="text-right col-line-total">Line Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($receipt->details as $detail)
                                        @php
                                            $recommendedSalesPrice = null;
                                            if ($detail->item && $detail->item->base_gold_price !== null && $detail->item_weight !== null) {
                                                $recommendedSalesPrice = (float) $detail->item->base_gold_price * (float) $detail->item_weight;
                                            }

                                            $averageSalesPrice = null;
                                            if ($detail->sales_price !== null && (float) $detail->item_weight > 0) {
                                                $averageSalesPrice = (float) $detail->sales_price / (float) $detail->item_weight;
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $detail->item_no ?: optional($detail->item)->item_no ?: '-' }}</td>
                                            <td>{{ $detail->item_name }}</td>
                                            <td class="text-right">{{ number_format($detail->item_weight, 2, ',', '.') }} gr</td>
                                            <td class="text-right">{{ number_format($detail->item_gold_rate, 2, ',', '.') }}%</td>
                                            <td class="text-right money-cell">{{ $recommendedSalesPrice !== null ? ('Rp ' . number_format($recommendedSalesPrice, 2, ',', '.')) : '-' }}</td>
                                            <td class="text-right money-cell">{{ $averageSalesPrice !== null ? ('Rp ' . number_format($averageSalesPrice, 2, ',', '.') . ' / gr') : '-' }}</td>
                                            <td class="text-right money-cell">{{ $detail->sales_price !== null ? ('Rp ' . number_format($detail->sales_price, 2, ',', '.')) : '-' }}</td>
                                            <td>{{ $detail->notes ?: '-' }}</td>
                                            @if($showServiceFee)
                                            <td class="text-right money-cell">{{ $detail->service_fee !== null ? ('Rp ' . number_format($detail->service_fee, 2, ',', '.')) : '-' }}</td>
                                            @endif
                                            <td class="text-right money-cell">{{ $detail->line_total !== null ? ('Rp ' . number_format($detail->line_total, 2, ',', '.')) : '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="{{ $showServiceFee ? 9 : 8 }}" class="text-right">Grand Total</th>
                                            <th class="text-right">Rp {{ number_format($receipt->receipt_total, 2, ',', '.') }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="float-right">
                                <a class="btn btn-secondary" href="{{ route('receipts.index') }}" role="button">{{ __("Back") }}</a>
                                @if(Auth::user()->authRole()->name === 'admin')
                                <a class="btn btn-primary" href="{{ route('receipts.edit', $receipt->id) }}" role="button">{{ __("Edit Transaction") }}</a>
                                @endif
                                @if($receiptApproved)
                                <a class="btn btn-dark" href="{{ route('receipts.pdf', ['receipt' => $receipt->id]) }}" target="_blank" role="button">{{ __("Print Receipt") }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
