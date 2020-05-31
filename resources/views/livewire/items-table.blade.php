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

    <div class="row mb-3">
        <div class="col form-inline">
            Advanced Filter: &nbsp;
            <select wire:model="filterStore" class="form-control">
                <option value="">- All Stores -</option>
                @foreach ($stores as $store)
                <option value="{{ $store->id }}">{{ $store->name }}</option>
                @endforeach
            </select>
            &nbsp;
            <select wire:model="filterCategory" class="form-control">
                <option value="">- All Categories -</option>
                @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->description }}</option>
                @endforeach
            </select>
            &nbsp;
            <select wire:model="filterAllocation" class="form-control">
                <option value="">- All Allocations -</option>
                @foreach ($allocations as $allocation)
                <option value="{{ $allocation->id }}">{{ $allocation->description }}</option>
                @endforeach
            </select>
            &nbsp;
            <select wire:model="filterItemStatus" class="form-control">
                <option value="">- All Item Statuses -</option>
                @foreach ($itemstatuses as $itemstatus)
                <option value="{{ $itemstatus->id }}">{{ $itemstatus->description }}</option>
                @endforeach
            </select>
            &nbsp;
            <select wire:model="filterSalesStatus" class="form-control">
                <option value="">- All Sales Statuses -</option>
                @foreach ($salesstatuses as $salesstatus)
                <option value="{{ $salesstatus->id }}">{{ $salesstatus->description }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="clearfix">
        <table class="table table-condensed table-hover table-striped">
            <thead>
                <tr>
                    <th class="col-with-min-width"><a wire:click.prevent="sortBy('store_id')" role="button" href="#">
                        Store
                        @include('includes._sort-icon', ['field' => 'store_id'])
                    </a></th>
                    <th class="col-with-min-width"><a wire:click.prevent="sortBy('item_no')" role="button" href="#">
                        Item No
                        @include('includes._sort-icon', ['field' => 'item_no'])
                    </a></th>
                    <th class="col-with-min-width"><a wire:click.prevent="sortBy('item_name')" role="button" href="#">
                        Item Name
                        @include('includes._sort-icon', ['field' => 'item_name'])
                    </a></th>
                    <th class="col-with-min-width"><a wire:click.prevent="sortBy('category_id')" role="button" href="#">
                        Category
                        @include('includes._sort-icon', ['field' => 'category_id'])
                    </a></th>
                    <th class="col-with-min-width"><a wire:click.prevent="sortBy('allocation_id')" role="button" href="#">
                        Allocation
                        @include('includes._sort-icon', ['field' => 'allocation_id'])
                    </a></th>
                    <th class="col-with-min-width"><a wire:click.prevent="sortBy('item_status_id')" role="button" href="#">
                        Item Status
                        @include('includes._sort-icon', ['field' => 'item_status_id'])
                    </a></th>
                    {{-- <th class="col-with-min-width"><a wire:click.prevent="sortBy('item_weight')" role="button" href="#">
                        Item Weight
                        @include('includes._sort-icon', ['field' => 'item_weight'])
                    </a></th>
                    <th class="col-with-min-width"><a wire:click.prevent="sortBy('item_gold_rate')" role="button" href="#">
                        Gold Rate
                        @include('includes._sort-icon', ['field' => 'item_gold_rate'])
                    </a></th>
                    <th class="col-with-min-width"><a wire:click.prevent="sortBy('created_by')" role="button" href="#">
                        Created By
                        @include('includes._sort-icon', ['field' => 'created_by'])
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
                    </a></th> --}}
                    <th class="col-with-min-width"><a wire:click.prevent="sortBy('sales_status_id')" role="button" href="#">
                        Sales Status
                        @include('includes._sort-icon', ['field' => 'sales_status_id'])
                    </a></th>

                    {{-- <th class="col-with-min-width"><a wire:click.prevent="sortBy('created_at')" role="button" href="#">
                        Created At
                        @include('includes._sort-icon', ['field' => 'created_at'])
                    </a></th>
                    <th class="col-with-min-width"><a wire:click.prevent="sortBy('updated_at')" role="button" href="#">
                        Updated At
                        @include('includes._sort-icon', ['field' => 'updated_at'])
                    </a></th> --}}


                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item->store_name }}</td>
                        <td>{{ $item->item_no }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->category_description }}</td>
                        <td>{{ $item->allocation_description }}</td>
                        <td>{{ $item->item_status_description}}</td>
                        {{-- <td>{{ ($item->item_weight) . " gr" }}</td>
                        <td>{{ ($item->item_gold_rate) . "%" }}</td>
                        <td>{{ $item->created_by == null ? "-" : $item->created_by_name }}</td>
                        <td>{{ $item->sales_price == null ? "-" : ("Rp " . number_format($item->sales_price, 2, ',', '.')) }}</td>
                        <td>{{ $item->sales_at == null ? "-" : Carbon\Carbon::parse($item->sales_at)->format('m/d/Y') }}</td>
                        <td>{{ $item->sales_by == null ? "-" : $item->sales_by_name }}</td> --}}
                        <td>{{ $item->sales_status_description == null ? "-" : $item->sales_status_description }}</td>
                        {{-- <td>{{ Carbon\Carbon::parse($item->created_at)->format('m/d/Y') }}</td>
                        <td>{{ Carbon\Carbon::parse($item->updated_at)->format('m/d/Y') }}</td> --}}

                        
                        <td>
                            <a href="{{ route('items.edit', $item->id) }}"><span><i class="fa fa-edit"></i></span></a>
                            <a href="{{ route('items.delete', $item->id) }}" 
                                onclick="event.preventDefault();
                                    var r = confirm('Are you sure you want to delete this?');
                                    if(r == true) {
                                        document.getElementById('delete-item-form-{{ $item->id }}').submit();
                                    }"><span><i class="fa fa-trash" style="color:red"></i></span></a>

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