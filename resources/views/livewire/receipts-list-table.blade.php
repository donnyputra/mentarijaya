<div>
    <div class="row mb-3">
        <div class="col form-inline">
            Per Page: &nbsp;
            <select wire:model="perPage" class="form-control">
            	<option>5</option>
                <option>10</option>
                <option>15</option>
                <option>25</option>
            </select>
        </div>

        <div class="col">
            <input wire:model="search" class="form-control" type="text" placeholder="Search Receipt...">
        </div>
    </div>

    <div class="clearfix">
        <table class="table table-condensed table-hover table-striped">
            <thead>
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center"><a wire:click.prevent="sortBy('receipt_date')" role="button" href="#">
                        Receipt Date
                        @include('includes._sort-icon', ['field' => 'receipt_date'])
                    </a></th>
                    <th class="text-center">Name</th>
                    <th class="text-right">Total</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($receipts as $receipt)
                    <tr>
                        <td class="text-center">{{ $receipt->uuid }}</td>
                        <td class="text-center">{{ $receipt->receipt_date }}</td>
                        <td class="text-center">{{ $receipt->customer_name }}</td>
                        <td class="text-right">Rp. {{ number_format($receipt->receipt_total, 2, ',', '.') }}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-light"><span><i class="fa fa-beat fa-stream"></i></span></button>
                            <button type="button" class="btn btn-sm btn-light"><span><i class="fa fa-beat fa-print"></i></span></button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col">
            {{ $receipts->links() }}
        </div>

        <div class="col text-right text-muted">
            Showing {{ $receipts->firstItem() }} to {{ $receipts->lastItem() }} out of {{ $receipts->total() }} results
        </div>
    </div>
</div>