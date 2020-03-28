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
            <input wire:model="search" class="form-control" type="text" placeholder="Search Buyback Status...">
        </div>
    </div>

    <div class="clearfix">
        <table class="table table-condensed table-hover table-striped">
            <thead>
                <tr>
                    <th><a wire:click.prevent="sortBy('code')" role="button" href="#">
                        Buyback Status Code
                        @include('includes._sort-icon', ['field' => 'code'])
                    </a></th>
                    <th><a wire:click.prevent="sortBy('description')" role="button" href="#">
                        Buyback Status Description
                        @include('includes._sort-icon', ['field' => 'description'])
                    </a></th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($buybackstatuses as $buybackstatus)
                    <tr>
                        <td>{{ $buybackstatus->code }}</td>
                        <td>{{ $buybackstatus->description }}</td>
                        <td>
                        	<a href="{{ route('buybackstatuses.edit', $buybackstatus->id) }}"><span><i class="fa fa-edit"></i></span></a>
                            <a href="{{ route('buybackstatuses.delete', $buybackstatus->id) }}" 
                                onclick="event.preventDefault();
                                     document.getElementById('delete-buybackstatus-form-{{ $buybackstatus->id }}').submit();"><span><i class="fa fa-trash-o" style="color:red"></i></span></a>

                            <form id="delete-buybackstatus-form-{{ $buybackstatus->id }}" method="POST" action="{{ route('buybackstatuses.delete', $buybackstatus->id) }}">
                                @csrf
                                <input type="text" class="form-control" name="id" value="{{ $buybackstatus->id }}" hidden />
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col">
            {{ $buybackstatuses->links() }}
        </div>

        <div class="col text-right text-muted">
            Showing {{ $buybackstatuses->firstItem() }} to {{ $buybackstatuses->lastItem() }} out of {{ $buybackstatuses->total() }} results
        </div>
    </div>
</div>