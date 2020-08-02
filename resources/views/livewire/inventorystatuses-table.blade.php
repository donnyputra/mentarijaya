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
            <input wire:model="search" class="form-control" type="text" placeholder="Search Inventory Status...">
        </div>
    </div>

    <div class="clearfix">
        <table class="table table-condensed table-hover table-striped">
            <thead>
                <tr>
                    <th><a wire:click.prevent="sortBy('code')" role="button" href="#">
                        Inventory Status Code
                        @include('includes._sort-icon', ['field' => 'code'])
                    </a></th>
                    <th><a wire:click.prevent="sortBy('description')" role="button" href="#">
                        Inventory Status Description
                        @include('includes._sort-icon', ['field' => 'description'])
                    </a></th>
                    <!-- <th>Action</th> -->
                </tr>
            </thead>
            <tbody>
                @foreach ($inventorystatuses as $inventorystatus)
                    <tr>
                        <td>{{ $inventorystatus->code }}</td>
                        <td>{{ $inventorystatus->description }}</td>
                        <!-- <td>
                        	<a href="{{ route('inventorystatuses.edit', $inventorystatus->id) }}"><span><i class="fa fa-edit"></i></span></a>
                            <a href="{{ route('inventorystatuses.delete', $inventorystatus->id) }}" 
                                onclick="event.preventDefault();
                                    var r = confirm('Are you sure you want to delete this?');
                                    if(r == true) {
                                        document.getElementById('delete-itemstatus-form-{{ $inventorystatus->id }}').submit();
                                    }"><span><i class="fa fa-trash" style="color:red"></i></span></a>

                            <form id="delete-itemstatus-form-{{ $inventorystatus->id }}" method="POST" action="{{ route('inventorystatuses.delete', $inventorystatus->id) }}">
                                @csrf
                                <input type="text" class="form-control" name="id" value="{{ $inventorystatus->id }}" hidden />
                            </form>
                        </td> -->
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col">
            {{ $inventorystatuses->links() }}
        </div>

        <div class="col text-right text-muted">
            Showing {{ $inventorystatuses->firstItem() }} to {{ $inventorystatuses->lastItem() }} out of {{ $inventorystatuses->total() }} results
        </div>
    </div>
</div>