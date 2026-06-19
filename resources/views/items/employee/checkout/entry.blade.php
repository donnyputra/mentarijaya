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
    $checkoutCreateStores = $checkoutCreateItemData['stores'];
    $checkoutCreateCategories = $checkoutCreateItemData['categories'];
    $checkoutCreateInventoryStatuses = $checkoutCreateItemData['inventorystatuses'];
    $checkoutCreateAllocation = $checkoutCreateItemData['allocation'];
    $checkoutCreateItemStatus = $checkoutCreateItemData['item_status'];
    $checkoutCreateItemEnabled = $checkoutCreateItemData['can_create'];
    $checkoutCreateItemDisabledReason = $checkoutCreateItemData['disabled_reason'];
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

                        <div class="card mb-3">
                            <div class="card-header">
                                <div class="card-title">{{ __("Find Item") }}</div>
                            </div>
                            <div class="card-body">
                                <div class="form-group row mb-0">
                                    <label for="item_picker" class="col-2 col-form-label">Item <span style="color: red">*</span></label>
                                    <div class="col-6">
                                        <select id="item_picker" class="form-control">
                                            <option value="">{{ __("--Select--") }}</option>
                                        </select>
                                    </div>
                                    <div class="col-4 d-flex">
                                        <button type="button" id="add-item" class="btn btn-primary mr-2">{{ __("Add Item") }}</button>
                                        <button
                                            type="button"
                                            id="open-create-item-modal"
                                            class="btn btn-outline-secondary"
                                            {{ $checkoutCreateItemEnabled ? '' : 'disabled' }}
                                            title="{{ $checkoutCreateItemDisabledReason ?: __('New Item') }}"
                                        >{{ __("New Item") }}</button>
                                    </div>
                                </div>
                                @if(!$checkoutCreateItemEnabled)
                                <div class="alert alert-warning mt-3 mb-0">
                                    {{ $checkoutCreateItemDisabledReason }}
                                </div>
                                @endif
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
                                                    <input type="hidden" class="sales-price-input" name="sales_prices[]" value="{{ $oldSalesPrices[$index] ?? '' }}">
                                                    <input type="text" inputmode="decimal" class="form-control text-right sales-price-display-input" value="{{ ($oldSalesPrices[$index] ?? '') !== '' ? number_format((float) ($oldSalesPrices[$index] ?? 0), 2, ',', '.') : '' }}" required>
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
                                    <button type="submit" id="checkout-submit" class="btn btn-primary">{{ __("Submit For Approval") }}</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <form id="checkout-create-item-form" enctype="multipart/form-data">
                        @csrf
                        <div class="modal fade" id="checkout-create-item-modal" tabindex="-1" role="dialog" aria-labelledby="checkout-create-item-modal-label" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="checkout-create-item-modal-label">{{ __("New Item") }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div id="checkout-create-item-errors" class="alert alert-danger d-none mb-3"></div>

                                        <div class="form-group row">
                                            <label for="checkout_create_store_id" class="col-sm-3 col-form-label">Store Name <span style="color: red">*</span></label>
                                            <div class="col-sm-9">
                                                <select class="form-control" id="checkout_create_store_id" name="store_id">
                                                    @foreach ($checkoutCreateStores as $store)
                                                    <option value="{{ $store->id }}">{{ $store->name }} ({{ $store->code }})</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback" data-field-error="store_id"></div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Item No</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" value="{{ __('Auto generated by system') }}" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="checkout_create_item_name" class="col-sm-3 col-form-label">Item Name <span style="color: red">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="checkout_create_item_name" name="item_name">
                                                <div class="invalid-feedback" data-field-error="item_name"></div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="checkout_create_item_weight" class="col-sm-3 col-form-label">Item Weight <span style="color: red">*</span></label>
                                            <div class="col-sm-9">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="checkout_create_item_weight" name="item_weight" placeholder="Ex: 50,45">
                                                    <div class="input-group-append">
                                                        <div class="input-group-text">gr</div>
                                                    </div>
                                                </div>
                                                <div class="invalid-feedback d-block" data-field-error="item_weight"></div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="checkout_create_category_id" class="col-sm-3 col-form-label">Category <span style="color: red">*</span></label>
                                            <div class="col-sm-9">
                                                <select class="form-control" id="checkout_create_category_id" name="category_id">
                                                    @foreach ($checkoutCreateCategories as $category)
                                                    <option value="{{ $category->id }}" data-code="{{ $category->code }}">{{ $category->description }} ({{ $category->code }})</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback" data-field-error="category_id"></div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="checkout_create_item_gold_rate" class="col-sm-3 col-form-label">Gold Rate <span style="color: red">*</span></label>
                                            <div class="col-sm-9">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="checkout_create_item_gold_rate" name="item_gold_rate" placeholder="Ex: 37,5">
                                                    <div class="input-group-append">
                                                        <div class="input-group-text">%</div>
                                                    </div>
                                                </div>
                                                <div class="invalid-feedback d-block" data-field-error="item_gold_rate"></div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Allocation</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" value="{{ $checkoutCreateAllocation->description }}" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Item Status</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" value="{{ $checkoutCreateItemStatus->description }}" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="checkout_create_inventory_status_id" class="col-sm-3 col-form-label">Inv Status <span style="color: red">*</span></label>
                                            <div class="col-sm-9">
                                                <select class="form-control" id="checkout_create_inventory_status_id" name="inventory_status_id">
                                                    @foreach ($checkoutCreateInventoryStatuses as $inventoryStatus)
                                                    <option value="{{ $inventoryStatus->id }}">{{ $inventoryStatus->description }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback" data-field-error="inventory_status_id"></div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Created By</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                                            </div>
                                        </div>

                                        <div class="form-group row mb-0">
                                            <label for="checkout_create_images" class="col-sm-3 col-form-label">Photos</label>
                                            <div class="col-sm-9">
                                                <input type="file" class="form-control-file" id="checkout_create_images" name="images[]" accept="image/*" multiple>
                                                <small class="form-text text-muted" id="checkout-create-item-photo-list">{{ __('No photos selected.') }}</small>
                                                <div class="invalid-feedback d-block" data-field-error="images"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __("Close") }}</button>
                                        <button type="submit" id="checkout-create-item-submit" class="btn btn-primary">{{ __("Save Item") }}</button>
                                    </div>
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
    const checkoutCreateItemUrl = '{{ route($checkoutCreateItemRouteName) }}';

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

    function appendCheckoutItem(item) {
        if ($('#checkout-items-body tr[data-item-id="' + item.id + '"]').length) {
            toastr.warning('{{ __("Item already exists in cart.") }}');
            return false;
        }

        $('#checkout-empty-row').remove();
        $('#checkout-items-body').append(buildCheckoutRow(item));
        syncCheckoutSummary();

        return true;
    }

    function canAutoAddCreatedItemToCart() {
        return $.trim($('#sales_at').val()) !== '';
    }

    function resetCheckoutCreateItemForm() {
        const $form = $('#checkout-create-item-form');
        $form[0].reset();
        $('#checkout_create_item_gold_rate').val('37.5');
        $('#checkout-create-item-photo-list').text('{{ __("No photos selected.") }}');
        resetCheckoutCreateItemFormErrors();
    }

    function resetCheckoutCreateItemFormErrors() {
        const $form = $('#checkout-create-item-form');
        $('#checkout-create-item-errors').addClass('d-none').empty();
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('[data-field-error]').empty().removeClass('d-block');
    }

    function showCheckoutCreateItemFormErrors(errors) {
        const $errorBox = $('#checkout-create-item-errors');
        const messages = [];

        resetCheckoutCreateItemFormErrors();

        $.each(errors || {}, function(field, fieldMessages) {
            const messagesForField = $.isArray(fieldMessages) ? fieldMessages : [fieldMessages];
            const messageText = messagesForField.join(' ');
            const $field = $('#checkout-create-item-form').find('[name="' + field + '"]');
            const $feedback = $('#checkout-create-item-form').find('[data-field-error="' + field + '"]');

            if ($field.length) {
                $field.addClass('is-invalid');
            }

            if ($feedback.length) {
                $feedback.text(messageText).addClass('d-block');
            }

            messages.push(messageText);
        });

        if (messages.length > 0) {
            $errorBox.removeClass('d-none').html(messages.map(function(message) {
                return '<div>' + $('<div>').text(message).html() + '</div>';
            }).join(''));
        }
    }

    function syncCheckoutCreateGoldRate() {
        const categoryCode = ($('#checkout_create_category_id option:selected').data('code') || '').toString().toUpperCase();
        $('#checkout_create_item_gold_rate').val(categoryCode === 'K' ? '42.0' : '37.5');
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
                    <input type="hidden" class="sales-price-input" name="sales_prices[]" value="${item.sales_price !== null ? item.sales_price : ''}">
                    <input type="text" inputmode="decimal" class="form-control text-right sales-price-display-input" value="${item.sales_price !== null ? formatLocaleNumber(item.sales_price) : ''}" required>
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
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

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

            appendCheckoutItem(selected[0]);
            $('#item_picker').val(null).trigger('change');
        });

        $('#open-create-item-modal').on('click', function() {
            resetCheckoutCreateItemForm();
            syncCheckoutCreateGoldRate();
            $('#checkout-create-item-modal').modal('show');
        });

        $('#checkout_create_category_id').on('change', function() {
            syncCheckoutCreateGoldRate();
        });

        $('#checkout_create_images').on('change', function() {
            const files = Array.prototype.slice.call(this.files || []);
            if (files.length === 0) {
                $('#checkout-create-item-photo-list').text('{{ __("No photos selected.") }}');
                return;
            }

            $('#checkout-create-item-photo-list').text(files.map(function(file) {
                return file.name;
            }).join(', '));
        });

        $('#checkout-create-item-modal').on('hidden.bs.modal', function() {
            resetCheckoutCreateItemForm();
        });

        $('#checkout-create-item-form').on('submit', function(event) {
            event.preventDefault();

            const $submitButton = $('#checkout-create-item-submit');
            const formData = new FormData(this);

            resetCheckoutCreateItemFormErrors();
            $submitButton.prop('disabled', true);

            $.ajax({
                url: checkoutCreateItemUrl,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            }).done(function(response) {
                const item = response.item || null;
                const itemAdded = item && canAutoAddCreatedItemToCart() ? appendCheckoutItem(item) : false;

                $('#checkout-create-item-modal').modal('hide');

                if (itemAdded) {
                    toastr.success(response.message || '{{ __("Item has been created.") }}');
                    return;
                }

                toastr.success(response.message || '{{ __("Item has been created.") }}');
                if (item && !canAutoAddCreatedItemToCart()) {
                    toastr.info('{{ __("Fill Sales At first if you want the new item to be added into cart automatically.") }}');
                }
            }).fail(function(xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showCheckoutCreateItemFormErrors(xhr.responseJSON.errors);
                    return;
                }

                toastr.error('{{ __("Unable to create item.") }}');
            }).always(function() {
                $submitButton.prop('disabled', false);
            });
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
