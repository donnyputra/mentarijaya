@extends('layouts.admin')

@section('content')
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
                                    <div><strong>Print Mode:</strong> {{ $showServiceFee ? 'Show Service Fee' : 'Hide Service Fee' }}</div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Item No</th>
                                            <th>Item Name</th>
                                            <th class="text-right">Weight</th>
                                            <th class="text-right">Gold Rate</th>
                                            <th class="text-right">Sales Price</th>
                                            @if($showServiceFee)
                                            <th class="text-right">Service Fee</th>
                                            @endif
                                            <th class="text-right">Line Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($receipt->details as $detail)
                                        <tr>
                                            <td>{{ $detail->item_no ?: optional($detail->item)->item_no ?: '-' }}</td>
                                            <td>{{ $detail->item_name }}</td>
                                            <td class="text-right">{{ number_format($detail->item_weight, 2, ',', '.') }} gr</td>
                                            <td class="text-right">{{ number_format($detail->item_gold_rate, 2, ',', '.') }}%</td>
                                            <td class="text-right">{{ $detail->sales_price !== null ? ('Rp ' . number_format($detail->sales_price, 2, ',', '.')) : '-' }}</td>
                                            @if($showServiceFee)
                                            <td class="text-right">{{ $detail->service_fee !== null ? ('Rp ' . number_format($detail->service_fee, 2, ',', '.')) : '-' }}</td>
                                            @endif
                                            <td class="text-right">{{ $detail->line_total !== null ? ('Rp ' . number_format($detail->line_total, 2, ',', '.')) : '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="{{ $showServiceFee ? 6 : 5 }}" class="text-right">Grand Total</th>
                                            <th class="text-right">Rp {{ number_format($receipt->receipt_total, 2, ',', '.') }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="float-right">
                                <a class="btn btn-secondary" href="{{ route('receipts.index') }}" role="button">{{ __("Back") }}</a>
                                <a class="btn btn-light" href="{{ route('receipts.show', ['receipt' => $receipt->id, 'show_service_fee' => 0]) }}" role="button">{{ __("Preview Without Fee") }}</a>
                                <a class="btn btn-info" href="{{ route('receipts.show', ['receipt' => $receipt->id, 'show_service_fee' => 1]) }}" role="button">{{ __("Preview With Fee") }}</a>
                                <a class="btn btn-secondary" href="{{ route('receipts.pdf', ['receipt' => $receipt->id, 'show_service_fee' => 0]) }}" target="_blank" role="button">{{ __("Print Without Fee") }}</a>
                                <a class="btn btn-dark" href="{{ route('receipts.pdf', ['receipt' => $receipt->id, 'show_service_fee' => 1]) }}" target="_blank" role="button">{{ __("Print With Fee") }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
