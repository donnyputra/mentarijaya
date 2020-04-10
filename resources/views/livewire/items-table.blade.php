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
            <input wire:model="search" class="form-control" type="text" placeholder="Search Item...">
        </div>
    </div>

    <div class="clearfix">
        <table class="table table-condensed table-hover table-striped">
            <thead>
                <tr>
                    <th class="col-with-min-width"><a wire:click.prevent="sortBy('item_no')" role="button" href="#">
                        Item No
                        @include('includes._sort-icon', ['field' => 'item_no'])
                    </a></th>
                    <th class="col-with-min-width"><a wire:click.prevent="sortBy('item_name')" role="button" href="#">
                        Item Name
                        @include('includes._sort-icon', ['field' => 'item_name'])
                    </a></th>
                    <th class="col-with-min-width"><a wire:click.prevent="sortBy('item_weight')" role="button" href="#">
                        Item Weight
                        @include('includes._sort-icon', ['field' => 'item_weight'])
                    </a></th>
                    <th class="col-with-min-width"><a wire:click.prevent="sortBy('item_gold_rate')" role="button" href="#">
                        Gold Rate
                        @include('includes._sort-icon', ['field' => 'item_gold_rate'])
                    </a></th>

                    <th class="col-with-min-width"><a wire:click.prevent="sortBy('sales_price')" role="button" href="#">
                        Sales Price
                        @include('includes._sort-icon', ['field' => 'sales_price'])
                    </a></th>
                    <th class="col-with-min-width"><a wire:click.prevent="sortBy('sales_at')" role="button" href="#">
                        Sales At
                        @include('includes._sort-icon', ['field' => 'sales_at'])
                    </a></th>
                    <th class="col-with-min-width"><a wire:click.prevent="sortBy('sales_by')" role="button" href="#">
                        Sales By
                        @include('includes._sort-icon', ['field' => 'sales_by'])
                    </a></th>
                    <th class="col-with-min-width"><a wire:click.prevent="sortBy('item_status_id')" role="button" href="#">
                        Item Status
                        @include('includes._sort-icon', ['field' => 'item_status_id'])
                    </a></th>
                    <th class="col-with-min-width"><a wire:click.prevent="sortBy('category_id')" role="button" href="#">
                        Category
                        @include('includes._sort-icon', ['field' => 'category_id'])
                    </a></th>
                    <th class="col-with-min-width"><a wire:click.prevent="sortBy('allocation_id')" role="button" href="#">
                        Allocation
                        @include('includes._sort-icon', ['field' => 'allocation_id'])
                    </a></th>
                    <th class="col-with-min-width"><a wire:click.prevent="sortBy('sales_status_id')" role="button" href="#">
                        Sales Status
                        @include('includes._sort-icon', ['field' => 'sales_status_id'])
                    </a></th>
                    <th class="col-with-min-width"><a wire:click.prevent="sortBy('store_id')" role="button" href="#">
                        Store
                        @include('includes._sort-icon', ['field' => 'store_id'])
                    </a></th>

                    <th class="col-with-min-width"><a wire:click.prevent="sortBy('created_at')" role="button" href="#">
                        Created At
                        @include('includes._sort-icon', ['field' => 'created_at'])
                    </a></th>
                    <th class="col-with-min-width"><a wire:click.prevent="sortBy('updated_at')" role="button" href="#">
                        Updated At
                        @include('includes._sort-icon', ['field' => 'updated_at'])
                    </a></th>


                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $items->item_no }}</td>
                        <td>{{ $items->item_name }}</td>
                        <td>{{ $items->item_weight }}</td>
                        <td>{{ $items->item_gold_rate }}</td>
                        <td>{{ $items->sales_price }}</td>
                        <td>{{ $items->sales_at }}</td>
                        <td>{{ $items->sales_by }}</td>
                        <td>{{ $items->item_status_id}}</td>
                        <td>{{ $items->category_id }}</td>
                        <td>{{ $items->allocation_id }}</td>
                        <td>{{ $items->sales_status_id }}</td>
                        <td>{{ $items->store_id }}</td>
                        <td>{{ $items->created_at }}</td>
                        <td>{{ $items->updated_at }}</td>

                        
                        <td>
                        	<a href="{{ route('items.edit', $item->id) }}"><span><i class="fa fa-edit"></i></span></a>
                            <a href="{{ route('items.delete', $item->id) }}" 
                                onclick="event.preventDefault();
                                     document.getElementById('delete-item-form-{{ $item->id }}').submit();"><span><i class="fa fa-trash-o" style="color:red"></i></span></a>

                            <form id="delete-item-form-{{ $item->id }}" method="POST" action="{{ route('items.delete', $item->id) }}">
                                @csrf
                                <input type="text" class="form-control" name="id" value="{{ $item->id }}" hidden />
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col">
            {{ $items->links() }}
        </div>

        <div class="col text-right text-muted">
            Showing {{ $items->firstItem() }} to {{ $items->lastItem() }} out of {{ $items->total() }} results
        </div>
    </div>
</div>