@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 text-dark">{{ __("Gold Price History") }}</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/">{{ __("Home") }}</a></li>
                                <li class="breadcrumb-item active">{{ __("Gold Price History") }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content">
                <div class="container-fluid">
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                <strong>Today's Base Price List:</strong>
                                @if(count($todayBasePriceList) > 0)
                                    @foreach($todayBasePriceList as $todayBasePrice)
                                        <span class="badge badge-light mr-1 mb-1">
                                            {{ $todayBasePrice['gold_rate'] !== null ? number_format($todayBasePrice['gold_rate'], 2, ',', '.') . '%' : '-' }}
                                            /
                                            {{ $todayBasePrice['inventory_status'] ?? '-' }}
                                            :
                                            Rp {{ number_format($todayBasePrice['base_price'], 2, ',', '.') }}
                                            + Fee Rp {{ number_format($todayBasePrice['service_fee'] ?? 0, 2, ',', '.') }}
                                        </span>
                                    @endforeach
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">{{ __("Add New Base Price") }}</div>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('gold-prices.store') }}">
                                        @csrf

                                        <div class="form-group row">
                                            <label class="col-3 col-form-label" for="price_date">Price Date <span style="color: red">*</span></label>
                                            <div class="col-9">
                                                <input type="date" class="form-control" id="price_date" name="price_date" value="{{ old('price_date', date('Y-m-d')) }}" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-3 col-form-label" for="base_price">Base Price (per gram) <span style="color: red">*</span></label>
                                            <div class="col-9">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="text" inputmode="decimal" class="form-control" id="base_price" name="base_price" value="{{ old('base_price') }}" placeholder="10.000,00" required />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-3 col-form-label" for="gold_rate">Gold Rate <span style="color: red">*</span></label>
                                            <div class="col-9">
                                                <input type="number" step="0.01" min="0" class="form-control" id="gold_rate" name="gold_rate" value="{{ old('gold_rate') }}" placeholder="Ex: 37.50" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-3 col-form-label" for="service_fee">Service Fee <span style="color: red">*</span></label>
                                            <div class="col-9">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Rp</span>
                                                    </div>
                                                    <input type="text" inputmode="decimal" class="form-control" id="service_fee" name="service_fee" value="{{ old('service_fee') }}" placeholder="2.000,00" required />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-3 col-form-label" for="inventory_status_id">Inventory Status <span style="color: red">*</span></label>
                                            <div class="col-9">
                                                <select class="form-control" id="inventory_status_id" name="inventory_status_id" required>
                                                    <option value="">Select inventory status</option>
                                                    @foreach($inventoryStatuses as $inventoryStatus)
                                                    <option value="{{ $inventoryStatus->id }}" {{ (string) old('inventory_status_id') === (string) $inventoryStatus->id ? 'selected' : '' }}>
                                                        {{ $inventoryStatus->description }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-3 col-form-label" for="notes">Notes</label>
                                            <div class="col-9">
                                                <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                                            </div>
                                        </div>

                                        <div class="float-right">
                                            <button type="submit" class="btn btn-primary">{{ __("Save Base Price") }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">{{ __("Change History") }}</div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th class="text-right">Gold Rate</th>
                                                    <th>Inventory Status</th>
                                                    <th class="text-right">Base Price</th>
                                                    <th class="text-right">Service Fee</th>
                                                    <th>Notes</th>
                                                    <th>Created By</th>
                                                    <th>Created At</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($goldPrices as $goldPrice)
                                                @php
                                                    $displayBasePrice = $goldPrice->base_price ?? $goldPrice->max_price ?? $goldPrice->min_price;
                                                    $displayPriceDate = $goldPrice->price_date ?? optional($goldPrice->created_at)->toDateString();
                                                    $displayGoldRate = $goldPrice->gold_rate ?? '-';
                                                    $displayInventoryStatus = optional($goldPrice->inventoryStatus)->description ?? '-';
                                                    $displayServiceFee = $goldPrice->service_fee ?? 0;
                                                    $displayNotes = $goldPrice->notes ?? '-';
                                                    $displayCreatedBy = $goldPrice->created_by ?? '-';
                                                @endphp
                                                <tr>
                                                    <td>{{ $displayPriceDate ? \Carbon\Carbon::parse($displayPriceDate)->format('d-M-Y') : '-' }}</td>
                                                    <td class="text-right">{{ is_numeric($displayGoldRate) ? number_format($displayGoldRate, 2, ',', '.') . '%' : '-' }}</td>
                                                    <td>{{ $displayInventoryStatus }}</td>
                                                    <td class="text-right">{{ $displayBasePrice !== null ? ('Rp ' . number_format($displayBasePrice, 2, ',', '.')) : '-' }}</td>
                                                    <td class="text-right">{{ 'Rp ' . number_format($displayServiceFee, 2, ',', '.') }}</td>
                                                    <td>{{ $displayNotes }}</td>
                                                    <td>{{ $displayCreatedBy }}</td>
                                                    <td>{{ optional($goldPrice->created_at)->format('d-M-Y H:i') }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted">{{ __("No gold price history yet.") }}</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            {{ $goldPrices->links() }}
                                        </div>
                                        <div class="col-xs-12 col-md-6 text-right text-muted">
                                            Showing {{ $goldPrices->firstItem() ?: 0 }} to {{ $goldPrices->lastItem() ?: 0 }} out of {{ $goldPrices->total() }} results
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('custom-script')
<script type="text/javascript">
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
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        var basePriceInput = document.getElementById('base_price');
        var serviceFeeInput = document.getElementById('service_fee');
        if (!basePriceInput && !serviceFeeInput) {
            return;
        }

        [basePriceInput, serviceFeeInput].forEach(function(input) {
            if (!input) {
                return;
            }

            input.value = formatRupiah(input.value);
            input.addEventListener('blur', function() {
                input.value = formatRupiah(input.value);
            });
        });
    })();
</script>
@endsection
