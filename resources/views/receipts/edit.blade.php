@extends('layouts.admin')

@section('content')
@php
    $receiptApproved = $receipt->isApproved();
@endphp
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">{{ __("Edit Transaction") }}</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/">{{ __("Home") }}</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('receipts.index') }}">{{ __("Receipts") }}</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('receipts.show', $receipt->id) }}">{{ __("Receipt Detail") }}</a></li>
                                <li class="breadcrumb-item active">{{ __("Edit Transaction") }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content">
                <div class="container-fluid">
                    <form method="POST" action="{{ route('receipts.update', $receipt->id) }}">
                        @csrf
                        <input type="hidden" name="approval_action" id="approval_action" value="save">

                        <div class="card mb-3">
                            <div class="card-header">
                                <div class="card-title">{{ __("Transaction Information") }}</div>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-3 col-form-label">Receipt ID</label>
                                    <div class="col-9 pt-2">
                                        {{ $receipt->uuid }}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="sales_at" class="col-3 col-form-label">Sales At <span style="color: red">*</span></label>
                                    <div class="col-9">
                                        <input type="text" class="form-control dateselect" id="sales_at" name="sales_at" value="{{ old('sales_at', optional($receipt->receipt_date)->format('m/d/Y')) }}" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="customer_name" class="col-3 col-form-label">Customer Name</label>
                                    <div class="col-9">
                                        <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{ old('customer_name', $receipt->customer_name) }}" />
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <label for="customer_address" class="col-3 col-form-label">Customer Address</label>
                                    <div class="col-9">
                                        <textarea class="form-control" id="customer_address" name="customer_address" rows="2">{{ old('customer_address', $receipt->customer_address) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">{{ __("Transaction Items") }}</div>
                                <div class="float-right">
                                    <span class="badge {{ $receiptApproved ? 'badge-success' : 'badge-warning' }}">
                                        {{ $receiptApproved ? __('Approved') : __('Submitted') }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Item No</th>
                                                <th>Item Name</th>
                                                <th class="text-right">Weight</th>
                                                <th class="text-right">Gold Rate</th>
                                                <th class="text-right" style="min-width: 170px;">Recommended Sales Price</th>
                                                <th class="text-right" style="min-width: 160px;">Sales Price</th>
                                                <th class="text-right" style="min-width: 160px;">Service Fee</th>
                                                <th class="text-right" style="min-width: 170px;">Sales Total</th>
                                                <th style="min-width: 220px;">Notes</th>
                                            </tr>
                                        </thead>
                                        <tbody id="receipt-items-body">
                                            @foreach($receipt->details as $detail)
                                            @php
                                                $recommendedSalesPrice = null;
                                                if ($detail->item && $detail->item->base_gold_price !== null && $detail->item_weight !== null) {
                                                    $recommendedSalesPrice = (float) $detail->item->base_gold_price * (float) $detail->item_weight;
                                                }
                                                $salesPriceValue = old('sales_prices.' . $loop->index, $detail->sales_price);
                                                $serviceFeeValue = old('service_fees.' . $loop->index, $detail->service_fee);
                                            @endphp
                                            <tr data-detail-id="{{ $detail->id }}">
                                                <td>
                                                    {{ $detail->item_no ?: optional($detail->item)->item_no ?: '-' }}
                                                    <input type="hidden" name="detail_ids[]" value="{{ $detail->id }}">
                                                    <input type="hidden" name="item_weights[]" value="{{ $detail->item_weight }}">
                                                </td>
                                                <td>{{ $detail->item_name }}</td>
                                                <td class="text-right">{{ number_format((float) $detail->item_weight, 2, ',', '.') }} gr</td>
                                                <td class="text-right">{{ number_format((float) $detail->item_gold_rate, 2, ',', '.') }}%</td>
                                                <td class="text-right">{{ $recommendedSalesPrice !== null ? ('Rp ' . number_format($recommendedSalesPrice, 2, ',', '.')) : '-' }}</td>
                                                <td>
                                                    <input type="hidden" class="sales-price-input" name="sales_prices[]" value="{{ $salesPriceValue !== null ? number_format((float) $salesPriceValue, 2, '.', '') : '' }}">
                                                    <input type="text" inputmode="decimal" class="form-control text-right sales-price-display-input" value="{{ $salesPriceValue !== null ? number_format((float) $salesPriceValue, 2, ',', '.') : '' }}" required>
                                                </td>
                                                <td>
                                                    <input type="hidden" class="service-fee-input" name="service_fees[]" value="{{ $serviceFeeValue !== null ? number_format((float) $serviceFeeValue, 2, '.', '') : '0.00' }}">
                                                    <input type="text" inputmode="decimal" class="form-control text-right service-fee-display-input" value="{{ number_format((float) $serviceFeeValue, 2, ',', '.') }}">
                                                </td>
                                                <td class="text-right line-total-display">Rp 0,00</td>
                                                <td>
                                                    <textarea class="form-control" name="item_notes[]" rows="2">{{ old('item_notes.' . $loop->index, $detail->notes) }}</textarea>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="2">Totals</th>
                                                <th class="text-right" id="receipt-total-weight">0,00 gr</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th class="text-right" id="receipt-grand-total">Rp 0,00</th>
                                                <th class="text-center" id="receipt-total-items">0 item(s)</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="float-right">
                                    <a class="btn btn-secondary" href="{{ route('receipts.show', $receipt->id) }}" role="button">{{ __("Back") }}</a>
                                    <button type="submit" class="btn btn-primary" data-action="save">{{ __("Save Changes") }}</button>
                                    @if(!$receiptApproved)
                                    <button type="submit" class="btn btn-success" data-action="approve">{{ __("Approve Receipt") }}</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('custom-script')
<script type="text/javascript">
    function formatIdr(value) {
        return 'Rp ' + Number(value || 0).toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function formatDecimal(value) {
        return Number(value || 0).toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function formatLocaleNumber(value) {
        return Number(value || 0).toLocaleString('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function parseLocaleNumber(value) {
        const rawValue = String(value || '').trim();
        let normalized = rawValue;

        if (rawValue.indexOf(',') !== -1) {
            normalized = rawValue.replace(/\./g, '').replace(',', '.');
        } else {
            const dotCount = (rawValue.match(/\./g) || []).length;
            normalized = dotCount > 1 ? rawValue.replace(/\./g, '') : rawValue;
            normalized = normalized.replace(/,/g, '');
        }

        normalized = normalized.replace(/[^0-9.]/g, '');
        const parsed = parseFloat(normalized);

        return Number.isFinite(parsed) ? parsed : 0;
    }

    function formatEditableNumber(value) {
        const numericValue = Number(value || 0);

        if (!Number.isFinite(numericValue) || numericValue <= 0) {
            return '';
        }

        if (Math.floor(numericValue) === numericValue) {
            return String(numericValue);
        }

        return numericValue.toFixed(2).replace(/0+$/, '').replace(/\.$/, '');
    }

    function syncSalesPriceField($input) {
        const parsedValue = parseLocaleNumber($input.val());
        $input.closest('tr').find('.sales-price-input').val(parsedValue > 0 ? parsedValue.toFixed(2) : '');
    }

    function syncServiceFeeField($input) {
        const parsedValue = parseLocaleNumber($input.val());
        $input.closest('tr').find('.service-fee-input').val(parsedValue > 0 ? parsedValue.toFixed(2) : '0.00');
    }

    function initializeCurrencyInputs() {
        $('.sales-price-display-input').each(function() {
            const $input = $(this);
            const parsedValue = parseLocaleNumber($input.val());
            if (parsedValue > 0) {
                $input.val(formatLocaleNumber(parsedValue));
            }
            syncSalesPriceField($input);
        });

        $('.service-fee-display-input').each(function() {
            const $input = $(this);
            const parsedValue = parseLocaleNumber($input.val());
            $input.val(formatLocaleNumber(parsedValue));
            syncServiceFeeField($input);
        });
    }

    function syncReceiptSummary() {
        let totalWeight = 0;
        let grandTotal = 0;
        let itemCount = 0;

        $('#receipt-items-body tr[data-detail-id]').each(function() {
            itemCount++;

            const weight = parseFloat($(this).find('input[name="item_weights[]"]').val() || 0);
            const salesPrice = parseFloat($(this).find('.sales-price-input').val() || 0);
            const serviceFee = parseFloat($(this).find('.service-fee-input').val() || 0);
            const lineTotal = salesPrice + serviceFee;

            totalWeight += weight;
            grandTotal += lineTotal;

            $(this).find('.line-total-display').text(formatIdr(lineTotal));
        });

        $('#receipt-total-weight').text(formatDecimal(totalWeight) + ' gr');
        $('#receipt-grand-total').text(formatIdr(grandTotal));
        $('#receipt-total-items').text(itemCount + ' item(s)');
    }

    $(function() {
        $('#sales_at').datepicker();
        initializeCurrencyInputs();
        syncReceiptSummary();

        $(document).on('focus', '.sales-price-display-input', function() {
            const hiddenValue = $(this).closest('tr').find('.sales-price-input').val();
            $(this).val(formatEditableNumber(parseLocaleNumber(hiddenValue)));
        });

        $(document).on('input', '.sales-price-display-input', function() {
            syncSalesPriceField($(this));
            syncReceiptSummary();
        });

        $(document).on('blur', '.sales-price-display-input', function() {
            const parsedValue = parseLocaleNumber($(this).val());
            $(this).val(parsedValue > 0 ? formatLocaleNumber(parsedValue) : '');
            syncSalesPriceField($(this));
            syncReceiptSummary();
        });

        $(document).on('focus', '.service-fee-display-input', function() {
            const hiddenValue = $(this).closest('tr').find('.service-fee-input').val();
            $(this).val(formatEditableNumber(parseLocaleNumber(hiddenValue)));
        });

        $(document).on('input', '.service-fee-display-input', function() {
            syncServiceFeeField($(this));
            syncReceiptSummary();
        });

        $(document).on('blur', '.service-fee-display-input', function() {
            const parsedValue = parseLocaleNumber($(this).val());
            $(this).val(formatLocaleNumber(parsedValue));
            syncServiceFeeField($(this));
            syncReceiptSummary();
        });

        $('button[data-action]').on('click', function() {
            $('#approval_action').val($(this).data('action'));
        });
    });
</script>
@endsection
