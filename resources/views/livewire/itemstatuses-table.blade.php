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
            <input wire:model="search" class="form-control" type="text" placeholder="Search Item Status...">
        </div>
    </div>

    <div class="clearfix">
        <table class="table table-condensed table-hover table-striped">
            <thead>
                <tr>
                    <th><a wire:click.prevent="sortBy('code')" role="button" href="#">
                        Item Status Code
                        @include('includes._sort-icon', ['field' => 'code'])
                    </a></th>
                    <th><a wire:click.prevent="sortBy('description')" role="button" href="#">
                        Item Status Description
                        @include('includes._sort-icon', ['field' => 'description'])
                    </a></th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($itemstatuses as $itemstatus)
                    <tr>
                        <td>{{ $itemstatus->code }}</td>
                        <td>{{ $itemstatus->description }}</td>
                        <td>
                        	<a href="{{ route('itemstatuses.edit', $itemstatus->id) }}"><span><i class="fa fa-edit"></i></span></a>
                            <a href="{{ route('itemstatuses.delete', $itemstatus->id) }}" 
                                onclick="event.preventDefault();
                                     document.getElementById('delete-itemstatus-form-{{ $itemstatus->id }}').submit();"><span><i class="fa fa-trash-o" style="color:red"></i></span></a>

                            <form id="delete-itemstatus-form-{{ $itemstatus->id }}" method="POST" action="{{ route('itemstatuses.delete', $itemstatus->id) }}">
                                @csrf
                                <input type="text" class="form-control" name="id" value="{{ $itemstatus->id }}" hidden />
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col">
            {{ $itemstatuses->links() }}
        </div>

        <div class="col text-right text-muted">
            Showing {{ $itemstatuses->firstItem() }} to {{ $itemstatuses->lastItem() }} out of {{ $itemstatuses->total() }} results
        </div>
    </div>
</div>