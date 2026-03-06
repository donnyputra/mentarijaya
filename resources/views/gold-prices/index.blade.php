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
                                    @php
                                        $groupedTodayBasePriceList = collect($todayBasePriceList)->groupBy(function ($todayBasePrice) {
                                            return $todayBasePrice['gold_rate'] !== null
                                                ? number_format((float) $todayBasePrice['gold_rate'], 2, '.', '')
                                                : 'unknown';
                                        });
                                    @endphp
                                    @foreach($groupedTodayBasePriceList as $goldRateKey => $todayBasePriceRows)
                                        <div class="mb-1">
                                            <span class="badge badge-secondary mr-1 mb-1">
                                                {{ $goldRateKey !== 'unknown' ? number_format((float) $goldRateKey, 2, ',', '.') . '%' : '-' }}
                                            </span>
                                            @foreach($todayBasePriceRows as $todayBasePrice)
                                                <span class="badge badge-light mr-1 mb-1">
                                                    {{ $todayBasePrice['inventory_status'] ?? '-' }}
                                                    :
                                                    Rp {{ number_format($todayBasePrice['base_price'], 2, ',', '.') }}
                                                    + Fee Rp {{ number_format($todayBasePrice['service_fee'] ?? 0, 2, ',', '.') }}
                                                </span>
                                            @endforeach
                                        </div>
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
                                    <div class="card-title">{{ __("Add Daily Gold Price") }}</div>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('gold-prices.store') }}">
                                        @csrf

                                        <div class="form-group row">
                                            <label class="col-3 col-form-label" for="price_date">Price Date <span style="color: red">*</span></label>
                                            <div class="col-9">
                                                <input type="date" class="form-control" id="price_date" name="price_date" value="{{ old('price_date', $selectedPriceDate) }}" required />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-3 col-form-label">Daily Price Matrix <span style="color: red">*</span></label>
                                            <div class="col-9">
                                                <div class="table-responsive matrix-scroll-container">
                                                    <table class="table table-bordered table-sm matrix-table">
                                                        <thead>
                                                            <tr>
                                                                <th class="align-middle matrix-sticky-col">Status / Type</th>
                                                                @foreach($rateColumns as $rateColumn)
                                                                <th class="text-center">{{ $rateColumn['label'] }}%</th>
                                                                @endforeach
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($inventoryStatuses as $inventoryStatus)
                                                            <tr>
                                                                <td class="align-middle matrix-sticky-col">
                                                                    <strong>{{ $inventoryStatus->description }}</strong>
                                                                    <div class="text-muted small">Base Price</div>
                                                                </td>
                                                                @foreach($rateColumns as $rateColumn)
                                                                <td>
                                                                    @php
                                                                        $defaultBasePrice = $matrixDefaults[$inventoryStatus->id][$rateColumn['key']]['base_price'] ?? null;
                                                                        $defaultBasePriceDisplay = $defaultBasePrice !== null ? number_format((float) $defaultBasePrice, 2, ',', '.') : '';
                                                                    @endphp
                                                                    <input
                                                                        type="text"
                                                                        inputmode="decimal"
                                                                        class="form-control form-control-sm rupiah-input matrix-input text-right"
                                                                        name="matrix[{{ $inventoryStatus->id }}][{{ $rateColumn['key'] }}][base_price]"
                                                                        value="{{ old('matrix.' . $inventoryStatus->id . '.' . $rateColumn['key'] . '.base_price', $defaultBasePriceDisplay) }}"
                                                                    />
                                                                </td>
                                                                @endforeach
                                                            </tr>
                                                            <tr>
                                                                <td class="align-middle text-muted small matrix-sticky-col">Service Fee</td>
                                                                @foreach($rateColumns as $rateColumn)
                                                                <td>
                                                                    @php
                                                                        $defaultServiceFee = $matrixDefaults[$inventoryStatus->id][$rateColumn['key']]['service_fee'] ?? null;
                                                                        $defaultServiceFeeDisplay = $defaultServiceFee !== null ? number_format((float) $defaultServiceFee, 2, ',', '.') : '';
                                                                    @endphp
                                                                    <input
                                                                        type="text"
                                                                        inputmode="decimal"
                                                                        class="form-control form-control-sm rupiah-input matrix-input text-right"
                                                                        name="matrix[{{ $inventoryStatus->id }}][{{ $rateColumn['key'] }}][service_fee]"
                                                                        value="{{ old('matrix.' . $inventoryStatus->id . '.' . $rateColumn['key'] . '.service_fee', $defaultServiceFeeDisplay) }}"
                                                                    />
                                                                </td>
                                                                @endforeach
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                @error('matrix')
                                                <small class="text-danger">{{ $message }}</small>
                                                @enderror
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
                                    @forelse($historyMatrices as $historyMatrix)
                                    <div class="card mb-3 border">
                                        <div class="card-header bg-light">
                                            <strong>{{ \Carbon\Carbon::parse($historyMatrix['price_date'])->format('d-M-Y') }}</strong>
                                            <span class="text-muted ml-2">By: {{ $historyMatrix['created_by'] ?? '-' }}</span>
                                            <span class="text-muted ml-2">At: {{ optional($historyMatrix['created_at'])->format('d-M-Y H:i') }}</span>
                                            @if(!empty($historyMatrix['notes']))
                                            <div class="small text-muted mt-1">Notes: {{ $historyMatrix['notes'] }}</div>
                                            @endif
                                        </div>
                                        <div class="card-body p-2">
                                            <div class="table-responsive matrix-scroll-container">
                                                <table class="table table-bordered table-sm mb-0 matrix-table">
                                                    <thead>
                                                        <tr>
                                                            <th class="align-middle matrix-sticky-col">Status / Type</th>
                                                            @foreach($rateColumns as $rateColumn)
                                                            <th class="text-center">{{ $rateColumn['label'] }}%</th>
                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($inventoryStatuses as $inventoryStatus)
                                                        <tr>
                                                            <td class="align-middle matrix-sticky-col">
                                                                <strong>{{ $inventoryStatus->description }}</strong>
                                                                <div class="text-muted small">Base Price</div>
                                                            </td>
                                                            @foreach($rateColumns as $rateColumn)
                                                            @php
                                                                $historyBasePrice = $historyMatrix['matrix'][$inventoryStatus->id][$rateColumn['key']]['base_price'] ?? null;
                                                            @endphp
                                                            <td class="text-right">
                                                                {{ $historyBasePrice !== null ? ('Rp ' . number_format((float) $historyBasePrice, 2, ',', '.')) : '-' }}
                                                            </td>
                                                            @endforeach
                                                        </tr>
                                                        <tr>
                                                            <td class="align-middle text-muted small matrix-sticky-col">Service Fee</td>
                                                            @foreach($rateColumns as $rateColumn)
                                                            @php
                                                                $historyServiceFee = $historyMatrix['matrix'][$inventoryStatus->id][$rateColumn['key']]['service_fee'] ?? null;
                                                            @endphp
                                                            <td class="text-right">
                                                                {{ $historyServiceFee !== null ? ('Rp ' . number_format((float) $historyServiceFee, 2, ',', '.')) : '-' }}
                                                            </td>
                                                            @endforeach
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="text-center text-muted">{{ __("No gold price history yet.") }}</div>
                                    @endforelse

                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            {{ $historyPaginator->links() }}
                                        </div>
                                        <div class="col-xs-12 col-md-6 text-right text-muted">
                                            Showing {{ $historyPaginator->firstItem() ?: 0 }} to {{ $historyPaginator->lastItem() ?: 0 }} out of {{ $historyPaginator->total() }} dates
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
<style>
    .matrix-scroll-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding-bottom: .25rem;
    }

    .matrix-table {
        width: max-content;
        min-width: 100%;
        table-layout: auto;
    }

    .matrix-table th,
    .matrix-table td {
        white-space: nowrap;
        vertical-align: middle;
    }

    .matrix-table .matrix-sticky-col {
        position: sticky;
        left: 0;
        background: #fff;
        z-index: 3;
        min-width: 170px;
        box-shadow: 2px 0 0 rgba(0, 0, 0, .05);
    }

    .matrix-table thead .matrix-sticky-col {
        background: #f8f9fa;
        z-index: 4;
    }

    .matrix-table .matrix-input {
        min-width: 150px;
    }

    @media (max-width: 767.98px) {
        .matrix-table .matrix-sticky-col {
            min-width: 150px;
        }

        .matrix-table .matrix-input {
            min-width: 140px;
            font-size: .95rem;
        }
    }
</style>
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
