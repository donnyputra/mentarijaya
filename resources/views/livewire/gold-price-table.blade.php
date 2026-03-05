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
    </div>

    <div class="clearfix">
        <table class="table table-condensed table-hover table-striped">
            <thead>
                <tr>
                    <th><a role="button" href="#">
                        Date
                    </a></th>
                    <th><a role="button" href="#">
                        Min Price
                    </a></th>
                    <th><a role="button" href="#">
                        Max Price
                    </a></th>
                    <th><a role="button" href="#">
                        Created by
                    </a></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($goldpricehistories as $goldpricehistory)
                    <tr>
                        <td>{{ date('Y-m-d', strtotime($goldpricehistory->created_at)) }}</td>
                        <td>{{ $goldpricehistory->min_price }}</td>
                        <td>{{ $goldpricehistory->max_price }}</td>
                        <td>{{ $goldpricehistory->created_by }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col">
            {{ $goldpricehistories->links() }}
        </div>

        <div class="col text-right text-muted">
            Showing {{ $goldpricehistories->firstItem() }} to {{ $goldpricehistories->lastItem() }} out of {{ $goldpricehistories->total() }} results
        </div>
    </div>
</div>