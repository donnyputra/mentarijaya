@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

			<div class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1 class="m-0 text-dark">{{ __("Entry Sales") }}</h1>
						</div><!-- /.col -->
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="/">{{ __("Home") }}</a></li>
								<li class="breadcrumb-item"><a href="{{ route($salesEntryRouteName) }}">{{ __("Sales") }}</a></li>
								<li class="breadcrumb-item active">{{ __("Entry Sales") }}</li>
							</ol>
						</div><!-- /.col -->
					</div><!-- /.row -->
				</div><!-- /.container-fluid -->
			</div>

			<div class="content">
				<div class="container-fluid">
					<div class="row">
						<div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">{{ __("Sales Information") }}</div>
                                    <div class="card-tools text-muted" style="font-size: 0.9rem;">
                                        Base Price: {{ $todayBaseGoldPrice !== null ? ('Rp ' . number_format($todayBaseGoldPrice, 0, ',', '.')) : '-' }}
                                        |
                                        Service Fee: {{ $todayServiceFee !== null ? ('Rp ' . number_format($todayServiceFee, 0, ',', '.')) : '-' }}
                                    </div>
                                </div>
                                <div class="card-body">
									<form method="POST" action="{{ route($salesFormSaveRouteName) }}">
                                        @php
                                            $existingReceipt = optional($receiptDetail)->receipt;
                                        @endphp
                                        @csrf
                                        <div class="form-group row" hidden>
                                            <label class="col-3 col-form-label">Item ID</label>
                                            <div class="col-9">
                                                <input type="text" class="form-control dateselect" id="item_id" name="item_id" value="{{ $item->id }}" />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-3 col-form-label">Item No</label>
                                            <div class="col-9">
                                                {{ $item->item_no }}
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-3 col-form-label">Item Name</label>
                                            <div class="col-9">
                                                {{ $item->item_name }}
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-3 col-form-label">Item Weight</label>
                                            <div class="col-9">
                                                {{ $item->item_weight }} gr
                                            </div>
                                        </div>
	                                        <div class="form-group row">
	                                            <label class="col-3 col-form-label">Recommended Item Price</label>
	                                            <div class="col-9">
                                                {{ $recommendedItemPrice !== null ? ('Rp ' . number_format($recommendedItemPrice, 0, ',', '.')) : '-' }}
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-3 col-form-label">Recommended Service Fee</label>
                                            <div class="col-9">
                                                {{ $recommendedServiceFee !== null ? ('Rp ' . number_format($recommendedServiceFee, 0, ',', '.')) : '-' }}
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-3 col-form-label">Recommended Sales Price</label>
                                            <div class="col-9">
                                                {{ $recommendedSalesPrice !== null ? ('Rp ' . number_format($recommendedSalesPrice, 0, ',', '.')) : '-' }}
                                                <small class="form-text text-muted">Recommendation only. You can still change the service fee and sales price.</small>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="service_fee" class="col-3 col-form-label">Service Fee</label>
                                            <div class="col-9">
                                                @php
                                                    $defaultServiceFeeInput = $defaultServiceFee !== null
                                                        ? number_format((float) $defaultServiceFee, 0, ',', '.')
                                                        : '';
                                                @endphp
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                            <div class="input-group-text">Rp</div>
                                                    </div>
                                                    <input type="text" inputmode="numeric" class="form-control rupiah-input" id="service_fee" name="service_fee" value="{{ old('service_fee', $defaultServiceFeeInput) }}" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="sales_price" class="col-3 col-form-label">Sales Price</label>
                                            <div class="col-9">
                                                @php
                                                    $defaultSalesPriceInput = $defaultSalesPrice !== null
                                                        ? number_format((float) $defaultSalesPrice, 0, ',', '.')
                                                        : '';
                                                @endphp
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                            <div class="input-group-text">Rp</div>
                                                    </div>
                                                    <input type="text" inputmode="numeric" class="form-control rupiah-input" id="sales_price" name="sales_price" value="{{ old('sales_price', $defaultSalesPriceInput) }}" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="sales_at" class="col-3 col-form-label">Sales At</label>
                                            <div class="col-9">
                                                <div class="input-group">
                                                    <input type="text" class="form-control dateselect" id="sales_at" name="sales_at" placeholder="select date" value="{{ date('m/d/Y') }}" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="sales_status_id" class="col-3 col-form-label">Sales Status</label>
                                            <div class="col-9">
                                                <input type="text" class="form-control" id="sales_status_id" name="sales_status_id" value="{{ $salesstatus->id }}" hidden />
                                                <input type="text" class="form-control" id="sales_status_text" name="sales_status_text" value="{{ $salesstatus->code }}" disabled />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="sales_by" class="col-3 col-form-label">Sales By</label>
                                            <div class="col-9">
                                                <div class="input-group">
                                                    <input type="text" class="form-control dateselect" id="sales_by_id" name="sales_by_id" value="{{ old('sales_by_id', $salesById) }}" hidden />
                                                    <input type="text" class="form-control dateselect" id="sales_by_text" name="sales_by_text" value="{{ old('sales_by_text', $salesByName) }}" disabled />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="item_note" class="col-3 col-form-label">Item Note</label>
                                            <div class="col-9">
                                                <textarea class="form-control" id="item_note" name="item_note" rows="2">{{ old('item_note', optional($receiptDetail)->notes) }}</textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="customer_name" class="col-3 col-form-label">Customer Name</label>
                                            <div class="col-9">
                                                <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{ old('customer_name', optional($existingReceipt)->customer_name) }}" />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="customer_address" class="col-3 col-form-label">Customer Address</label>
                                            <div class="col-9">
                                                <textarea class="form-control" id="customer_address" name="customer_address" rows="2">{{ old('customer_address', optional($existingReceipt)->customer_address) }}</textarea>
                                            </div>
                                        </div>
                
                                        <div class="float-right">
                                            <a class="btn btn-secondary" href="{{ url()->previous() }}" role="button">{{ __("Back") }}</a>
                                            <button type="submit" class="btn btn-primary">{{ __("Submit For Approval") }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
						</div>
					</div>
					<!-- /.row -->
				</div><!-- /.container-fluid -->
			</div>

        </div>
    </div>
</div>
@endsection

@section('custom-script')
<script type="text/javascript">
	$(function() {
        $('#sales_at').datepicker();
    });

    (function() {
        function formatRupiah(value) {
            if (!value) {
                return '';
            }

            var cleaned = String(value).replace(/rp/ig, '').replace(/\s+/g, '');
            if (cleaned.indexOf(',') !== -1) {
                cleaned = cleaned.replace(/\./g, '');
                cleaned = cleaned.replace(',', '.');
            } else if (/^\d{1,3}(\.\d{3})+$/.test(cleaned)) {
                cleaned = cleaned.replace(/\./g, '');
            }

            var numeric = Number(cleaned);
            if (!isFinite(numeric)) {
                return value;
            }

            return numeric.toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

        var inputs = document.querySelectorAll('.rupiah-input');
        if (!inputs || inputs.length === 0) {
            return;
        }

        Array.prototype.forEach.call(inputs, function(input) {
            input.value = formatRupiah(input.value);
            input.addEventListener('blur', function() {
                input.value = formatRupiah(input.value);
            });
        });
    })();
</script>
@endsection
