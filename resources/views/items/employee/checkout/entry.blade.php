@extends('layouts.admin')

@section('content')
<style>
    .select2-selection {
        height: auto !important;
    }
</style>
@php
    $oldItemIds = old('item_ids', []);
    $oldItemNos = old('item_nos', []);
    $oldItemNames = old('item_names', []);
    $oldItemWeights = old('item_weights', []);
    $oldItemGoldRates = old('item_gold_rates', []);
    $oldRecommendedSalesPrices = old('recommended_sales_prices', []);
    $oldSalesPrices = old('sales_prices', []);
    $oldServiceFees = old('service_fees', []);
    $oldItemNotes = old('item_notes', []);
@endphp
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">{{ __("Checkout") }}</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/">{{ __("Home") }}</a></li>
                                <li class="breadcrumb-item active">{{ __("Checkout") }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content">
                <div class="container-fluid">
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="card-title">{{ __("Find Item") }}</div>
                        </div>
                        <div class="card-body">
                            <div class="form-group row mb-0">
                                <label for="item_picker" class="col-3 col-form-label">Item <span style="color: red">*</span></label>
                                <div class="col-6">
                                    <select id="item_picker" class="form-control">
                                        <option value="">{{ __("--Select--") }}</option>
                                    </select>
                                </div>
                                <div class="col-3">
                                    <button type="button" id="add-item" class="btn btn-primary">{{ __("Add Item") }}</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route($checkoutSubmitRouteName) }}">
                        @csrf

                        <div class="card mb-3">
                            <div class="card-header">
                                <div class="card-title">{{ __("Checkout Information") }}</div>
                            </div>
                            <div class="card-body">
                                <div class="form-group row">
                                    <label for="sales_at" class="col-3 col-form-label">Sales At <span style="color: red">*</span></label>
                                    <div class="col-9">
                                        <input type="text" class="form-control dateselect" id="sales_at" name="sales_at" value="{{ old('sales_at', date('m/d/Y')) }}" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="customer_name" class="col-3 col-form-label">Customer Name</label>
                                    <div class="col-9">
                                        <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" />
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <label for="customer_address" class="col-3 col-form-label">Customer Address</label>
                                    <div class="col-9">
                                        <textarea class="form-control" id="customer_address" name="customer_address" rows="2">{{ old('customer_address') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">{{ __("Cart Items") }}</div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 44px;"></th>
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
                                        <tbody id="checkout-items-body">
                                            @forelse($oldItemIds as $index => $itemId)
                                            <tr data-item-id="{{ $itemId }}">
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-link text-danger p-0 remove-item" title="{{ __('Remove') }}" aria-label="{{ __('Remove') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                                <td>
                                                    {{ $oldItemNos[$index] ?? '-' }}
                                                    <input type="hidden" name="item_ids[]" value="{{ $itemId }}">
                                                    <input type="hidden" name="item_nos[]" value="{{ $oldItemNos[$index] ?? '' }}">
                                                    <input type="hidden" name="item_names[]" value="{{ $oldItemNames[$index] ?? '' }}">
                                                    <input type="hidden" name="item_weights[]" value="{{ $oldItemWeights[$index] ?? '' }}">
                                                    <input type="hidden" name="item_gold_rates[]" value="{{ $oldItemGoldRates[$index] ?? '' }}">
                                                    <input type="hidden" name="recommended_sales_prices[]" value="{{ $oldRecommendedSalesPrices[$index] ?? '' }}">
                                                </td>
                                                <td>{{ $oldItemNames[$index] ?? '-' }}</td>
                                                <td class="text-right item-weight-display">{{ number_format((float) ($oldItemWeights[$index] ?? 0), 2, ',', '.') }} gr</td>
                                                <td class="text-right">{{ number_format((float) ($oldItemGoldRates[$index] ?? 0), 2, ',', '.') }}%</td>
                                                <td class="text-right recommended-sales-price-display">{{ ($oldRecommendedSalesPrices[$index] ?? '') !== '' ? ('Rp ' . number_format((float) $oldRecommendedSalesPrices[$index], 2, ',', '.')) : '-' }}</td>
                                                <td>
                                                    <input type="hidden" class="sales-price-input" name="sales_prices[]" value="{{ $oldSalesPrices[$index] ?? ($oldRecommendedSalesPrices[$index] ?? '') }}">
                                                    <input type="text" inputmode="decimal" class="form-control text-right sales-price-display-input" value="{{ ($oldSalesPrices[$index] ?? ($oldRecommendedSalesPrices[$index] ?? '')) !== '' ? number_format((float) ($oldSalesPrices[$index] ?? ($oldRecommendedSalesPrices[$index] ?? 0)), 2, ',', '.') : '' }}" required>
                                                </td>
                                                <td>
                                                    <input type="hidden" class="service-fee-input" name="service_fees[]" value="{{ $oldServiceFees[$index] ?? 0 }}">
                                                    <input type="text" inputmode="decimal" class="form-control text-right service-fee-display-input" value="{{ number_format((float) ($oldServiceFees[$index] ?? 0), 2, ',', '.') }}">
                                                </td>
                                                <td class="text-right line-total-display">Rp 0,00</td>
                                                <td>
                                                    <textarea class="form-control" name="item_notes[]" rows="2">{{ $oldItemNotes[$index] ?? '' }}</textarea>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr id="checkout-empty-row">
                                                <td colspan="10" class="text-center text-muted">{{ __("No items in cart.") }}</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th colspan="2">Totals</th>
                                                <th class="text-right" id="checkout-total-weight">0,00 gr</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th class="text-right" id="checkout-grand-total">Rp 0,00</th>
                                                <th class="text-center" id="checkout-total-items">0 item(s)</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>

                                <div class="float-right">
                                    <button type="submit" id="checkout-submit" class="btn btn-primary">{{ __("Checkout & Print Receipt") }}</button>
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

    function syncSalesPriceField($input) {
        const $row = $input.closest('tr');
        const parsedValue = parseLocaleNumber($input.val());

        $row.find('.sales-price-input').val(parsedValue > 0 ? parsedValue.toFixed(2) : '');
    }

    function syncServiceFeeField($input) {
        const $row = $input.closest('tr');
        const parsedValue = parseLocaleNumber($input.val());

        $row.find('.service-fee-input').val(parsedValue > 0 ? parsedValue.toFixed(2) : '0.00');
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

    function initializeSalesPriceFields(context) {
        $(context).find('.sales-price-display-input').each(function() {
            const $input = $(this);
            const parsedValue = parseLocaleNumber($input.val());

            if (parsedValue > 0) {
                $input.val(formatLocaleNumber(parsedValue));
            }

            syncSalesPriceField($input);
        });
    }

    function initializeServiceFeeFields(context) {
        $(context).find('.service-fee-display-input').each(function() {
            const $input = $(this);
            const parsedValue = parseLocaleNumber($input.val());

            $input.val(formatLocaleNumber(parsedValue));
            syncServiceFeeField($input);
        });
    }

    function buildCheckoutRow(item) {
        return `
            <tr data-item-id="${item.id}">
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-link text-danger p-0 remove-item" title="{{ __("Remove") }}" aria-label="{{ __("Remove") }}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
                <td>
                    ${item.item_no}
                    <input type="hidden" name="item_ids[]" value="${item.id}">
                    <input type="hidden" name="item_nos[]" value="${item.item_no}">
                    <input type="hidden" name="item_names[]" value="${item.item_name}">
                    <input type="hidden" name="item_weights[]" value="${item.item_weight}">
                    <input type="hidden" name="item_gold_rates[]" value="${item.item_gold_rate}">
                    <input type="hidden" name="recommended_sales_prices[]" value="${item.recommended_sales_price !== null ? item.recommended_sales_price : ''}">
                </td>
                <td>${item.item_name}</td>
                <td class="text-right item-weight-display">${formatDecimal(item.item_weight)} gr</td>
                <td class="text-right">${formatDecimal(item.item_gold_rate)}%</td>
                <td class="text-right recommended-sales-price-display">${item.recommended_sales_price !== null ? formatIdr(item.recommended_sales_price) : '-'}</td>
                <td>
                    <input type="hidden" class="sales-price-input" name="sales_prices[]" value="${item.recommended_sales_price !== null ? item.recommended_sales_price : ''}">
                    <input type="text" inputmode="decimal" class="form-control text-right sales-price-display-input" value="${item.recommended_sales_price !== null ? formatLocaleNumber(item.recommended_sales_price) : ''}" required>
                </td>
                <td>
                    <input type="hidden" class="service-fee-input" name="service_fees[]" value="${item.service_fee !== null ? item.service_fee : 0}">
                    <input type="text" inputmode="decimal" class="form-control text-right service-fee-display-input" value="${formatLocaleNumber(item.service_fee !== null ? item.service_fee : 0)}">
                </td>
                <td class="text-right line-total-display">${formatIdr(0)}</td>
                <td>
                    <textarea class="form-control" name="item_notes[]" rows="2"></textarea>
                </td>
            </tr>
        `;
    }

    function syncCheckoutSummary() {
        let totalWeight = 0;
        let grandTotal = 0;
        let itemCount = 0;

        $('#checkout-items-body tr[data-item-id]').each(function() {
            itemCount++;

            const weight = parseFloat($(this).find('input[name="item_weights[]"]').val() || 0);
            const salesPrice = parseFloat($(this).find('.sales-price-input').val() || 0);
            const serviceFee = parseFloat($(this).find('.service-fee-input').val() || 0);
            const lineTotal = salesPrice + serviceFee;

            totalWeight += weight;
            grandTotal += lineTotal;

            $(this).find('.line-total-display').text(formatIdr(lineTotal));
        });

        if (itemCount === 0) {
            if (!$('#checkout-empty-row').length) {
                $('#checkout-items-body').append('<tr id="checkout-empty-row"><td colspan="10" class="text-center text-muted">{{ __("No items in cart.") }}</td></tr>');
            }
        } else {
            $('#checkout-empty-row').remove();
        }

        $('#checkout-total-weight').text(formatDecimal(totalWeight) + ' gr');
        $('#checkout-grand-total').text(formatIdr(grandTotal));
        $('#checkout-total-items').text(itemCount + ' item(s)');
        $('#checkout-submit').prop('disabled', itemCount === 0);
    }

    $(function() {
        $('#sales_at').datepicker();

        $('#item_picker').select2({
            placeholder: '--Select--',
            minimumInputLength: 1,
            ajax: {
                url: '{{ route("item.simplelist") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term || '',
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results || [],
                        pagination: data.pagination || {
                            more: false
                        }
                    };
                }
            }
        });

        $('#add-item').on('click', function() {
            const selected = $('#item_picker').select2('data');

            if (!selected || !selected.length) {
                alert('Please select an item first.');
                return;
            }

            const item = selected[0];

            if ($('#checkout-items-body tr[data-item-id="' + item.id + '"]').length) {
                alert('Item already exists in cart.');
                return;
            }

            $('#checkout-empty-row').remove();
            $('#checkout-items-body').append(buildCheckoutRow(item));
            $('#item_picker').val(null).trigger('change');
            syncCheckoutSummary();
        });

        $(document).on('focus', '.sales-price-display-input', function() {
            const hiddenValue = $(this).closest('tr').find('.sales-price-input').val();
            const parsedValue = parseLocaleNumber(hiddenValue);
            $(this).val(formatEditableNumber(parsedValue));
        });

        $(document).on('input', '.sales-price-display-input', function() {
            syncSalesPriceField($(this));
            syncCheckoutSummary();
        });

        $(document).on('blur', '.sales-price-display-input', function() {
            const parsedValue = parseLocaleNumber($(this).val());
            $(this).val(parsedValue > 0 ? formatLocaleNumber(parsedValue) : '');
            syncSalesPriceField($(this));
        });

        $(document).on('focus', '.service-fee-display-input', function() {
            const hiddenValue = $(this).closest('tr').find('.service-fee-input').val();
            const parsedValue = parseLocaleNumber(hiddenValue);
            $(this).val(formatEditableNumber(parsedValue));
        });

        $(document).on('input', '.service-fee-display-input', function() {
            syncServiceFeeField($(this));
            syncCheckoutSummary();
        });

        $(document).on('blur', '.service-fee-display-input', function() {
            const parsedValue = parseLocaleNumber($(this).val());
            $(this).val(formatLocaleNumber(parsedValue));
            syncServiceFeeField($(this));
        });

        $(document).on('input', '.service-fee-input', function() {
            syncCheckoutSummary();
        });

        $(document).on('click', '.remove-item', function() {
            $(this).closest('tr').remove();
            syncCheckoutSummary();
        });

        initializeSalesPriceFields(document);
        initializeServiceFeeFields(document);
        syncCheckoutSummary();
    });
</script>
@endsection
